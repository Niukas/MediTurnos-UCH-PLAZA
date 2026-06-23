<?php

// Clase Usuario

class Usuario
{
    // db contiene pdo para todos los metodos de la clase
    private \PDO $db;

    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }

    // Metodo para traer todos los usuarios y unida con la tabla de Rol
    public function getAll($pagina = 1, $porPagina = 20)
    {
        $offset = ($pagina - 1) * $porPagina;
        $sql = "SELECT * FROM vista_usuarios WHERE activo = 1 LIMIT :limite OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,    PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getTotalUsuarios()
    {
        $sql  = "SELECT COUNT(*) FROM Usuario WHERE activo = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // Metodo para verificar un login y devuelve un array con id, nombre, email y rol
    public function login(string $email, string $password)
    {
        try {
            $sql  = "SELECT u.id_usuario, u.password, u.nombre, u.email, r.nombre AS rol
                 FROM Usuario u
                 JOIN Rol r ON u.id_rol = r.id_rol
                 WHERE u.email = :email AND u.activo = 1
                 LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':email' => $email]);
            $usuario = $stmt->fetch();

            if ($usuario && password_verify($password, $usuario['password'])) {
                return $usuario;
            }
            return false;
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            return false;
        }
    }

    // Metodo para registrar un usuario
    public function registrar($datos)
    {
        try {
            // Verificar si el DNI ya existe
            $sqlCheck  = "SELECT COUNT(*) FROM Paciente WHERE dni = :dni";
            $stmtCheck = $this->db->prepare($sqlCheck);
            $stmtCheck->execute([':dni' => $datos['dni']]);
            if ($stmtCheck->fetchColumn() > 0) {
                return 'dni_duplicado';
            }

            $passwordHasheada = password_hash($datos['password'], PASSWORD_DEFAULT);

            $this->db->beginTransaction();

            // INSERT en Paciente con dni como PK directa
            $sql1 = "INSERT INTO Paciente (dni, nombre, apellido, email, fecha_nac, telefono)
                 VALUES (:dni, :nombre, :apellido, :email, :fecha_nac, :telefono)";
            $stmt1 = $this->db->prepare($sql1);
            $stmt1->execute([
                ':dni'       => $datos['dni'],
                ':nombre'    => $datos['nombre'],
                ':apellido'  => $datos['apellido'],
                ':email'     => $datos['email'],
                ':fecha_nac' => $datos['fecha_nac'],
                ':telefono'  => $datos['telefono']
            ]);

            // INSERT en Usuario usando el mismo dni (ya no hay lastInsertId)
            $sql2 = "INSERT INTO Usuario (nombre, apellido, email, password, dni)
                 VALUES (:nombre, :apellido, :email, :password, :dni)";
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute([
                ':nombre'   => $datos['nombre'],
                ':apellido' => $datos['apellido'],
                ':email'    => $datos['email'],
                ':password' => $passwordHasheada,
                ':dni'      => $datos['dni']
            ]);

            $this->db->commit();
            return true;
        } catch (\Throwable $th) {
            $this->db->rollback();
            error_log($th->getMessage());
            return false;
        }
    }

    // Metodo que selecciona el dni de paciente segun el usuario que esta logueado
    public function getDni($id_usuario)
    {
        $sql  = "SELECT dni FROM Usuario WHERE id_usuario = :id_usuario";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);
        return $stmt->fetchColumn();
    }

    // Eliminar usuario
    public function eliminar(int $id_usuario)
    {
        try {
            $sql  = "UPDATE Usuario SET activo = 0 WHERE id_usuario = :id_usuario";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id_usuario' => $id_usuario]);
            return true;
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            return false;
        }
    }

    // Editar usuario
    public function editar(array $datos)
    {
        try {
            $sql = "UPDATE Usuario 
                SET nombre = :nombre, apellido = :apellido, email = :email
                WHERE id_usuario = :id_usuario";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':nombre'     => $datos['nombre'],
                ':apellido'   => $datos['apellido'],
                ':email'      => $datos['email'],
                ':id_usuario' => $datos['id_usuario']
            ]);
            return true;
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            return false;
        }
    }

    // Metodo que sirve para cambiar el rol de un usuario x 
    public function cambiarRol(int $idUsuario, int $idRol)
    {
        try {
            $sql = "UPDATE Usuario 
                SET id_rol = :id_rol 
                WHERE id_usuario = :id_usuario";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                ':id_rol' => $idRol,
                ':id_usuario' => $idUsuario
            ]);

            return true;
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            return false;
        }
    }

    public function getMatricula(int $id_usuario)
    {
        $sql  = "SELECT matricula FROM Usuario WHERE id_usuario = :id_usuario";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);
        return $stmt->fetchColumn();
    }

    // Método para buscar usuarios por nombre, apellido, email o nombre completo
    public function buscar(string $busqueda)
    {
        $sql = "SELECT * FROM vista_usuarios 
                WHERE nombre LIKE :busqueda 
                OR apellido LIKE :busqueda 
                OR email LIKE :busqueda 
                OR CONCAT(nombre, ' ', apellido) LIKE :busqueda
                OR CONCAT(apellido, ' ', nombre) LIKE :busqueda
                ORDER BY apellido ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':busqueda' => '%' . $busqueda . '%']);
        return $stmt->fetchAll();
    }
}
