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
}
