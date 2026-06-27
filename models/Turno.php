<?php
class Turno
{

    private \PDO $db;

    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }

    // Metodo para saber la cantidad de turnos que hay en la db
    public function getTotalTurnos()
    {
        $sql = "SELECT COUNT(*) FROM Turno";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // Metodo para traer una vista de la db un listado de todos los turnos segun la pagina que se consulta en el front
    public function getAll($pagina = 1, $porPagina = 20)
    {
        $offset = ($pagina - 1) * $porPagina;
        $sql = "SELECT * FROM vista_turnos LIMIT :limite OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // metodo para traer los turnos por medio de filtros
    public function getByFiltros($especialidad = null, $periodo = 'todos', $pagina = 1, $porPagina = 20, $dni = null)
    {
        $where = [];
        $params = [];

        if ($dni) {
            $where[] = "dni = :dni";
            $params[':dni'] = $dni;
        }

        if ($especialidad) {
            $where[] = "especialidad = :especialidad";
            $params[':especialidad'] = $especialidad;
        }

        if ($periodo === 'dia') {
            $where[] = "fecha = CURDATE()";
        } elseif ($periodo === 'semana') {
            $where[] = "YEARWEEK(fecha, 1) = YEARWEEK(CURDATE(), 1)";
        } elseif ($periodo === 'mes') {
            $where[] = "MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())";
        }

        $sqlWhere = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';
        $offset   = ($pagina - 1) * $porPagina;
        $sql      = "SELECT * FROM vista_turnos $sqlWhere LIMIT :limite OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limite', $porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,    PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getTotalByFiltros($especialidad = null, $periodo = 'todos', $dni = null)
    {
        $where  = [];
        $params = [];

        if ($dni) {
            $where[] = "dni = :dni";
            $params[':dni'] = $dni;
        }

        if ($especialidad) {
            $where[] = "especialidad = :especialidad";
            $params[':especialidad'] = $especialidad;
        }

        if ($periodo === 'dia') {
            $where[] = "fecha = CURDATE()";
        } elseif ($periodo === 'semana') {
            $where[] = "YEARWEEK(fecha, 1) = YEARWEEK(CURDATE(), 1)";
        } elseif ($periodo === 'mes') {
            $where[] = "MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())";
        }

        $sqlWhere = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';
        $sql      = "SELECT COUNT(*) FROM vista_turnos $sqlWhere";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function cambiarEstado(int $id_turno, string $estado)
    {
        try {
            $sql = "UPDATE Turno 
                SET estado = :estado 
                WHERE id_turno = :id_turno";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':estado'   => $estado,
                ':id_turno' => $id_turno
            ]);

            return true;
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            return false;
        }
    }

    // Metodo de creacion de un turno
    public function crear($datos)
    {
        try {
            // 1. Iniciamos la transacción (tx) que tenías originalmente
            $this->db->beginTransaction();

            $sql = "CALL sp_crear_turno(
                :fecha, 
                :hora_inicio, 
                :dni, 
                :matricula, 
                :id_especialidad, 
                :id_consultorio, 
                :id_plan, 
                :nro_afiliado, 
                :observacion, 
                @p_resultado, 
                @p_id_turno
            )";

            $stmt = $this->db->prepare($sql);

            // Bindeamos explícitamente para asegurarnos de que el NULL se envíe bien si es Particular
            $stmt->bindValue(':fecha',           $datos['fecha']);
            $stmt->bindValue(':hora_inicio',     $datos['hora_inicio']);
            $stmt->bindValue(':dni',             $datos['dni'], PDO::PARAM_INT);
            $stmt->bindValue(':matricula',       $datos['matricula']);
            $stmt->bindValue(':id_especialidad', $datos['id_especialidad'], PDO::PARAM_INT);
            $stmt->bindValue(':id_consultorio',  $datos['id_consultorio'], PDO::PARAM_INT);
            $stmt->bindValue(':id_plan',         $datos['id_plan'], is_null($datos['id_plan']) ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindValue(':nro_afiliado',    $datos['nro_afiliado'], is_null($datos['nro_afiliado']) ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':observacion',     $datos['observacion']);

            $stmt->execute();

            // FIX VITAL: Liberar el cursor. Si no hacemos esto, el próximo SELECT hace explotar a PDO (Commands out of sync)
            $stmt->closeCursor();

            // 2. Traemos las variables OUT de MySQL
            $stmtOut = $this->db->query("SELECT @p_resultado AS resultado, @p_id_turno AS id_turno");
            $res = $stmtOut->fetch(PDO::FETCH_ASSOC);

            // 3. Evaluamos qué nos respondió el Procedure
            if ($res && $res['resultado'] == 1) {
                // Todo salió bien, impactamos la base de datos
                $this->db->commit();
                return $res['id_turno'];
            } else {
                // El turno ya existía o hubo conflicto lógico en el SP
                $this->db->rollBack();
                return false;
            }
        } catch (\Throwable $th) {
            var_dump($th);
            exit;
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Error en Turno->crear(): " . $th->getMessage());
            return false;
        }
    }


    public function getByFiltrosMedico(string $matricula, $especialidad = null, $periodo = 'todos', $pagina = 1, $porPagina = 20, $id_paciente = null)
    {
        $where   = ["matricula = :matricula"];
        $params  = [':matricula' => $matricula];

        if ($id_paciente) {
            $where[] = "id_paciente = :id_paciente";
            $params[':id_paciente'] = $id_paciente;
        }

        if ($especialidad) {
            $where[] = "especialidad = :especialidad";
            $params[':especialidad'] = $especialidad;
        }

        if ($periodo === 'dia') {
            $where[] = "fecha = CURDATE()";
        } elseif ($periodo === 'semana') {
            $where[] = "YEARWEEK(fecha, 1) = YEARWEEK(CURDATE(), 1)";
        } elseif ($periodo === 'mes') {
            $where[] = "MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())";
        }

        $sqlWhere = 'WHERE ' . implode(' AND ', $where);
        $offset   = ($pagina - 1) * $porPagina;
        $sql      = "SELECT * FROM vista_turnos $sqlWhere LIMIT :limite OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limite', $porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,    PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getTotalByFiltrosMedico($matricula, $especialidad = null, $periodo = 'todos')
    {
        $where  = ["matricula = :matricula"];
        $params = [':matricula' => $matricula];

        if ($especialidad) {
            $where[] = "especialidad = :especialidad";
            $params[':especialidad'] = $especialidad;
        }

        if ($periodo === 'dia') {
            $where[] = "fecha = CURDATE()";
        } elseif ($periodo === 'semana') {
            $where[] = "YEARWEEK(fecha, 1) = YEARWEEK(CURDATE(), 1)";
        } elseif ($periodo === 'mes') {
            $where[] = "MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())";
        }

        $sqlWhere = 'WHERE ' . implode(' AND ', $where);
        $sql      = "SELECT COUNT(*) FROM vista_turnos $sqlWhere";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function actualizarEstadoObservacion(int $id_turno, string $estado, ?string $observacion)
    {
        try {
            $sql = "UPDATE Turno 
                SET estado = :estado, observacion = :observacion
                WHERE id_turno = :id_turno";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':estado'      => $estado,
                ':observacion' => $observacion,
                ':id_turno'    => $id_turno
            ]);
            return true;
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            return false;
        }
    }

    public function getById(int $id_turno)
    {
        $sql = "SELECT * FROM Turno WHERE id_turno = :id_turno";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_turno' => $id_turno]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Metodo para cancelar masivamente los turnos cuando un médico bloquea su agenda
    public function cancelarTurnosPorBloqueo($matricula, $fecha, $motivo)
    {
        try {
            // Armamos un mensaje para que el paciente lo vea en su panel
            $obs_medico = $motivo ? " Motivo: " . $motivo . "." : "";
            $mensaje_cancelacion = "Turno cancelado por el profesional." . $obs_medico . " Por favor, vuelva a agendar su cita desde la sección 'Agendar Cita'.";

            $sql = "UPDATE Turno 
                    SET estado = 'cancelado', 
                        observacion = :observacion 
                    WHERE matricula = :matricula 
                    AND fecha = :fecha 
                    AND estado IN ('pendiente', 'confirmado')";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':observacion' => $mensaje_cancelacion,
                ':matricula'   => $matricula,
                ':fecha'       => $fecha
            ]);
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            return false;
        }
    }
}
