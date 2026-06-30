<?php
class Medico
{
    private \PDO $db;

    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }

    // Metodo para traer una vista de la db con todos los datos de los medicos
    public function getAll($filtros = [], $pagina = 1, $porPagina = 20)
    {
        $where = [];
        $params = [];

        if (!empty($filtros['especialidad'])) {
            // Usamos LIKE porque 'especialidades' es una lista de texto: "Cardiología, Pediatría"
            $where[] = 'especialidades LIKE :especialidad';
            $params[':especialidad'] = '%' . $filtros['especialidad'] . '%';
        }

        if (!empty($filtros['busqueda'])) {
            $where[] = '(nombre LIKE :busqueda OR apellido LIKE :busqueda)';
            $params[':busqueda'] = '%' . $filtros['busqueda'] . '%';
        }
        
        $sqlWhere = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $offset = ($pagina - 1) * $porPagina;

        $sql = "SELECT * FROM vista_medicos
                $sqlWhere
                ORDER BY apellido, nombre
                LIMIT :limite OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getTotalMedicos($filtros = []) {
        $where = [];
        $params = [];

        if (!empty($filtros['especialidad'])) {
            $where[] = 'especialidades LIKE :especialidad';
            $params[':especialidad'] = '%' . $filtros['especialidad'] . '%';
        }

        if (!empty($filtros['busqueda'])) {
            $where[] = '(nombre LIKE :busqueda OR apellido LIKE :busqueda)';
            $params[':busqueda'] = '%' . $filtros['busqueda'] . '%';
        }

        $sqlWhere = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "SELECT COUNT(*) FROM vista_medicos $sqlWhere";
        
        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        return $stmt->fetchColumn();
    }


    // Metodo para crear un medico nuevo en la db
    public function crearMedico(array $datos)
    {
        try {
            $this->db->beginTransaction();

            // insert en medico

            $sql1 = "INSERT INTO Medico (matricula, nombre, apellido, telefono, email)
                 VALUES (:matricula, :nombre, :apellido, :telefono, :email)";
            $stmt1 = $this->db->prepare($sql1);
            $stmt1->execute([
                ':matricula' => $datos['matricula'],
                ':nombre'    => $datos['nombre'],
                ':apellido'  => $datos['apellido'],
                ':telefono'  => $datos['telefono'],
                ':email'     => $datos['email']
            ]);

            // insert en Medico_Especialidad

            $sql2 = "INSERT INTO Medico_Especialidad (matricula, id_especialidad)
                 VALUES (:matricula, :id_especialidad)";
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute([
                ':matricula'       => $datos['matricula'],
                ':id_especialidad' => $datos['id_especialidad']
            ]);

            $this->db->commit();
            return true;
        } catch (\Throwable $th) {
            $this->db->rollBack();
            error_log($th->getMessage());
            return false;
        }
    }

    // Editar médico
    public function editar(array $datos)
    {
        try {
            $sql = "UPDATE Medico 
                SET nombre = :nombre, apellido = :apellido, 
                    telefono = :telefono, email = :email
                WHERE matricula = :matricula";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':nombre'    => $datos['nombre'],
                ':apellido'  => $datos['apellido'],
                ':telefono'  => $datos['telefono'],
                ':email'     => $datos['email'],
                ':matricula' => $datos['matricula']
            ]);
            return true;
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            return false;
        }
    }

    // Eliminar médico
    public function eliminar(string $matricula)
    {
        try {
            $sql  = "DELETE FROM Medico WHERE matricula = :matricula";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':matricula' => $matricula]);
            return true;
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            return false;
        }
    }

    // Metodo que trae medico segun la especialidad
    public function getByEspecialidad(int $idEspecialidad)
    {
        $sql = "SELECT m.matricula, m.nombre, m.apellido
                FROM Medico m
                JOIN Medico_Especialidad me ON m.matricula = me.matricula
                WHERE me.id_especialidad = :id_especialidad";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_especialidad' => $idEspecialidad]);
        return $stmt->fetchAll();
    }
}
