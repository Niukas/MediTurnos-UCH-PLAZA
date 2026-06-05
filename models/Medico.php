<?php
class Medico {
    private \PDO $db;
    
    public function __construct(\PDO $pdo) {
        $this->db = $pdo;
    }

    // Metodo para traer una vista de la db con todos los datos de los medicos
    public function getAll(){
        $sql = "SELECT * FROM vista_medicos";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $todosMedicos = $stmt->fetchAll();
        return $todosMedicos;
    }

    // Metodo para crear un medico nuevo en la db
    public function crearMedico(array $datos) {
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
}
?>