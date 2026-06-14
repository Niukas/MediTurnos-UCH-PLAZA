<?php
class Paciente
{
    private \PDO $db;

    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }

    // Trae los planes del paciente con nombre de obra social y cobertura
    public function getByPaciente(int $id_paciente)
    {
        $sql = "SELECT pp.nro_afiliado,
                       pl.id_plan,
                       pl.nombre_plan,
                       pl.porcentaje_cobertura,
                       os.nombre AS obra_social
                FROM Paciente_Plan pp
                JOIN Plan_OS pl ON pp.id_plan = pl.id_plan
                JOIN Obra_Social os ON pl.id_obra_social = os.id_obra_social
                WHERE pp.id_paciente = :id_paciente";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_paciente' => $id_paciente]);
        return $stmt->fetchAll();
    }

    // Metodo para buscar por nombre, apellido o dni
    public function buscar(string $busqueda)
    {
        $sql = "SELECT p.*, u.email
            FROM Paciente p
            LEFT JOIN Usuario u ON u.id_paciente = p.id_paciente
            WHERE p.nombre   LIKE :busqueda
            OR p.apellido    LIKE :busqueda
            OR p.dni         LIKE :busqueda
            ORDER BY p.apellido ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':busqueda' => '%' . $busqueda . '%']);
        return $stmt->fetchAll();
    }

    // Metodo para editar un paciente 
    public function editar(array $datos) {
    try {
        $sql = "UPDATE Paciente 
                SET nombre = :nombre, apellido = :apellido,
                    telefono = :telefono, email = :email
                WHERE id_paciente = :id_paciente";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nombre'      => $datos['nombre'],
            ':apellido'    => $datos['apellido'],
            ':telefono'    => $datos['telefono'],
            ':email'       => $datos['email'],
            ':id_paciente' => $datos['id_paciente'],
        ]);
        return true;
    } catch (\Throwable $th) {
        error_log($th->getMessage());
        return false;
    }
}
}
