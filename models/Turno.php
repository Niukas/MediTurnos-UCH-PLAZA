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
    public function getByFiltros($especialidad = null, $periodo = 'todos', $pagina = 1, $porPagina = 20, $id_paciente = null)
    {
        $where = [];
        $params = [];

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
            $where[] = "fecha BETWEEN DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND CURDATE()";
        } elseif ($periodo === 'mes') {
            $where[] = "fecha BETWEEN DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND CURDATE()";
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


    public function getTotalByFiltros($especialidad = null, $periodo = 'todos', $id_paciente = null)
    {
        $where = [];
        $params = [];

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
            $where[] = "fecha BETWEEN DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND CURDATE()";
        } elseif ($periodo === 'mes') {
            $where[] = "fecha BETWEEN DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND CURDATE()";
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
}
