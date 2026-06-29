<?php
class Plan
{
    private \PDO $db;

    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function getAll()
    {
        $sql = "SELECT p.id_plan, p.nombre_plan, p.id_obra_social, p.porcentaje_cobertura, os.nombre AS obra_social
                FROM Plan_OS p
                JOIN Obra_Social os ON p.id_obra_social = os.id_obra_social
                ORDER BY os.nombre, p.nombre_plan";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllObraSociales()
    {
        $sql = "SELECT id_obra_social, nombre FROM Obra_Social ORDER BY nombre ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id_plan)
    {
        $sql  = "SELECT * FROM Plan_OS WHERE id_plan = :id_plan";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_plan' => $id_plan]);
        return $stmt->fetch();
    }
}