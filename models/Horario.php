<?php
class Horario
{
    private \PDO $db;

    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }

    // Metodo para traer los turnos dispobles en base a el medico, su especialidad, la fecha y Duracion
    public function getDisponibles(string $matricula, int $id_especialidad, string $fecha, int $duracion_min)
    {
        try {
            // Traer el rango horario del médico para esa especialidad y día de la semana
            $diaSemana = date('l', strtotime($fecha)); // día en inglés
            $diasMap = [
                'Monday'    => 'Lunes',
                'Tuesday'   => 'Martes',
                'Wednesday' => 'Miercoles',
                'Thursday'  => 'Jueves',
                'Friday'    => 'Viernes',
                'Saturday'  => 'Sabado'
            ];
            $diaSemanaES = $diasMap[$diaSemana] ?? null;

            if (!$diaSemanaES) return [];

            // Verificar si esta bloqueado
            $sqlBloqueo = "SELECT COUNT(*) FROM Bloqueo_Horario 
               WHERE matricula = :matricula AND fecha = :fecha";
            $stmtBloqueo = $this->db->prepare($sqlBloqueo);
            $stmtBloqueo->execute([':matricula' => $matricula, ':fecha' => $fecha]);
            if ($stmtBloqueo->fetchColumn() > 0) return [];

            $sql = "SELECT h.hora_inicio, h.hora_fin, h.id_consultorio,
                       c.numero AS consultorio_nro, c.piso
                FROM Horario_Atencion h
                JOIN Consultorio c ON h.id_consultorio = c.id_consultorio
                WHERE h.matricula       = :matricula
                AND   h.id_especialidad = :id_especialidad
                AND   h.dia_semana      = :dia_semana";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':matricula'       => $matricula,
                ':id_especialidad' => $id_especialidad,
                ':dia_semana'      => $diaSemanaES
            ]);
            $horario = $stmt->fetch();

            if (!$horario) return [];

            // Traer los slots ya ocupados en esa fecha
            $sqlOcupados = "SELECT hora_inicio FROM Turno
                        WHERE matricula   = :matricula
                        AND   fecha       = :fecha
                        AND   estado     != 'cancelado'";

            $stmtOcupados = $this->db->prepare($sqlOcupados);
            $stmtOcupados->execute([
                ':matricula' => $matricula,
                ':fecha'     => $fecha
            ]);
            $ocupados = $stmtOcupados->fetchAll(PDO::FETCH_COLUMN);

            // Generar slots cada duracion_min entre hora_inicio y hora_fin
            $slots = [];
            $inicio = strtotime($horario['hora_inicio']);
            $fin    = strtotime($horario['hora_fin']);

            while ($inicio < $fin) {
                $horaSlot = date('H:i:s', $inicio);

                // Solo agregar si no está ocupado
                if (!in_array($horaSlot, $ocupados)) {
                    $slots[] = [
                        'hora_inicio'     => $horaSlot,
                        'id_consultorio'  => $horario['id_consultorio'],
                        'consultorio_nro' => $horario['consultorio_nro'],
                        'piso'            => $horario['piso'],
                    ];
                }

                $inicio += $duracion_min * 60;
            }

            return $slots;
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            return [];
        }
    }

    // Metodo para bloquear un dia en especifico
    public function bloquearDia(string $matricula, string $fecha, ?string $motivo = null)
    {
        try {
            // Verificar si ya existe un bloqueo para esa fecha
            $sqlCheck = "SELECT COUNT(*) FROM Bloqueo_Horario 
                     WHERE matricula = :matricula AND fecha = :fecha";
            $stmtCheck = $this->db->prepare($sqlCheck);
            $stmtCheck->execute([':matricula' => $matricula, ':fecha' => $fecha]);
            if ($stmtCheck->fetchColumn() > 0) return false;

            $sql = "INSERT INTO Bloqueo_Horario (matricula, fecha, motivo)
                VALUES (:matricula, :fecha, :motivo)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':matricula' => $matricula,
                ':fecha'     => $fecha,
                ':motivo'    => $motivo
            ]);
            return true;
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            return false;
        }
    }

    // Metodo para desbloquear el Dia ya bloqueado
    public function desbloquearDia(string $matricula, string $fecha)
    {
        try {
            $sql = "DELETE FROM Bloqueo_Horario 
                WHERE matricula = :matricula AND fecha = :fecha";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':matricula' => $matricula, ':fecha' => $fecha]);
            return true;
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            return false;
        }
    }

    // Metodo para traer todos los bloqueos existentes
    public function getBloqueos(string $matricula)
    {
        $sql = "SELECT * FROM Bloqueo_Horario 
            WHERE matricula = :matricula 
            AND fecha >= CURDATE()
            ORDER BY fecha ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':matricula' => $matricula]);
        return $stmt->fetchAll();
    }

    // Método para traer la agenda regular (horarios de atención) de un médico
    public function getHorariosMedico(string $matricula)
    {
        $sql = "SELECT h.*, e.nombre AS especialidad, c.numero AS consultorio_nro 
                FROM Horario_Atencion h
                JOIN Especialidad e ON h.id_especialidad = e.id_especialidad
                JOIN Consultorio c ON h.id_consultorio = c.id_consultorio
                WHERE h.matricula = :matricula
                ORDER BY FIELD(h.dia_semana, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'), h.hora_inicio ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':matricula' => $matricula]);
        return $stmt->fetchAll();
    }

    // Método para agregar un nuevo bloque de horario de atención (Con validación de superposición)
    public function agregarHorario(array $datos)
    {
        try {
            // Verificamos si ya existe un horario que se superponga ese mismo día
            // La fórmula de superposición es: (InicioA < FinB) AND (FinA > InicioB)
            $sqlCheck = "SELECT COUNT(*) FROM Horario_Atencion 
                         WHERE matricula = :matricula 
                         AND dia_semana = :dia_semana 
                         AND (hora_inicio < :hora_fin AND hora_fin > :hora_inicio)";
                         
            $stmtCheck = $this->db->prepare($sqlCheck);
            $stmtCheck->execute([
                ':matricula'   => $datos['matricula'],
                ':dia_semana'  => $datos['dia_semana'],
                ':hora_inicio' => $datos['hora_inicio'],
                ':hora_fin'    => $datos['hora_fin']
            ]);

            // Si encuentra al menos 1 registro que se pise, aborta y avisa
            if ($stmtCheck->fetchColumn() > 0) {
                return 'superpuesto';
            }

            // Si no hay choques de horario, lo insertamos
            $sql = "INSERT INTO Horario_Atencion 
                    (matricula, id_especialidad, dia_semana, hora_inicio, hora_fin, id_consultorio)
                    VALUES (:matricula, :id_especialidad, :dia_semana, :hora_inicio, :hora_fin, :id_consultorio)";
                    
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':matricula'       => $datos['matricula'],
                ':id_especialidad' => $datos['id_especialidad'],
                ':dia_semana'      => $datos['dia_semana'],
                ':hora_inicio'     => $datos['hora_inicio'],
                ':hora_fin'        => $datos['hora_fin'],
                ':id_consultorio'  => $datos['id_consultorio']
            ]);
            return true;
            
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            return false;
        }
    }

    // Método para eliminar un bloque de horario de atención
    public function eliminarHorario(int $id_horario)
    {
        try {
            $sql = "DELETE FROM Horario_Atencion WHERE id_horario = :id_horario";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id_horario' => $id_horario]);
            return true;
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            return false;
        }
    }
}
