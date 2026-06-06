<?php

class Especialidad {
    private \PDO $db;
    
    public function __construct(\PDO $pdo) {
        $this->db = $pdo;
    }

    public function getAll(){
        $sql = "SELECT id_especialidad, nombre, duracion_turno_min FROM Especialidad ORDER BY nombre ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $especialidades = $stmt->fetchAll();
        return $especialidades;
    }
}
?>