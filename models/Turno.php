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
    public function crear(array $datos)
    {
        try {
            $this->db->beginTransaction();

            $sql = "CALL sp_crear_turno(
                    :fecha, :hora_inicio, :dni, :matricula,
                    :id_especialidad, :id_consultorio, :id_plan,
                    :nro_afiliado, :observacion, @resultado, @p_id_turno)";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':fecha'           => $datos['fecha'],
                ':hora_inicio'     => $datos['hora_inicio'],
                ':dni'             => $datos['dni'],
                ':matricula'       => $datos['matricula'],
                ':id_especialidad' => $datos['id_especialidad'],
                ':id_consultorio'  => $datos['id_consultorio'],
                ':id_plan'         => $datos['id_plan'],
                ':nro_afiliado'    => $datos['nro_afiliado'],
                ':observacion'     => $datos['observacion'] ?? null
            ]);

            $resultado = $this->db->query("SELECT @resultado AS resultado, @p_id_turno AS id_turno")->fetch();

            $this->db->commit();

            if ($resultado['resultado'] == 1) {
                return $resultado['id_turno'];
            }
            return false;
        } catch (\Throwable $th) {
            $this->db->rollBack();
            error_log($th->getMessage());
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
}
