<?php

class Rol {
    private \PDO $db;
    
    public function __construct(\PDO $pdo) {
        $this->db = $pdo;
    }
    
    // Metodo para traer Roles de DB
    public function getAll() {
        
        $sql = "SELECT * FROM Rol";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $roles = $stmt->fetchAll();
        return $roles;
    }
}
?>