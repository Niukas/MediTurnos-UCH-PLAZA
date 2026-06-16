<?php
class Plan
{
    private \PDO $db;

    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function getById(int $id_plan)
    {
        $sql  = "SELECT * FROM Plan_OS WHERE id_plan = :id_plan";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_plan' => $id_plan]);
        return $stmt->fetch();
    }
}