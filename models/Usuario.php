<?php

// Clase Usuario

class Usuario
{
    // db contiene pdo para todos los metodos de la clase
    private $db;

    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    // Metodo para verificar un login y devuelve un array con id, nombre, email y rol
    public function login($email, $password)
    {
        try {

            $sql  = "SELECT u.id_usuario,
            u.password,
            u.nombre,
            u.email,
            r.nombre AS rol
            FROM   Usuario u
            JOIN   Rol r ON u.id_rol = r.id_rol
            WHERE  u.email = :email
            LIMIT  1";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([':email' => $email]);
            $usuario = $stmt->fetch();

            if ($usuario && password_verify($password, $usuario['password'])) {
                return $usuario;
            }else {
                return false;
            }

        } catch (\Throwable $th) {
            error_log($th->getMessage());
            return false;
        }
    }

}
