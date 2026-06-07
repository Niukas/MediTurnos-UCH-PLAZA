<?php

class Especialidad
{
    private \PDO $db;

    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }

    // Metodo para traer las especialidades de la db
    public function getAll()
    {
        $sql = "SELECT id_especialidad, nombre, duracion_turno_min FROM Especialidad ORDER BY nombre ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $especialidades = $stmt->fetchAll();
        return $especialidades;
    }

    // Traer una especialidad segun el id
    public function getById(int $id)
    {
        $sql  = "SELECT * FROM Especialidad WHERE id_especialidad = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
}
