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
        $sql = "SELECT * FROM vista_usuarios LIMIT :limite OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,    PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getTotalUsuarios()
    {
        $sql  = "SELECT COUNT(*) FROM Usuario";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // Metodo para verificar un login y devuelve un array con id, nombre, email y rol
    public function login(string $email, string $password)
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
            } else {
                return false;
            }
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            return false;
        }
    }

    // Metodo para registrar un usuario
    public function registrar(array $datos)
    {
        $passwordHasheada = password_hash($datos['password'], PASSWORD_DEFAULT);

        try {

            // Inicio de Transaccion

            $this->db->beginTransaction();

            // Insertar en la tabla usuario con rol de paciente por default

            $sql1 = "INSERT INTO Paciente (nombre, apellido, email, dni, fecha_nac, telefono)
                    VALUES (:nombre, :apellido, :email, :dni, :fecha_nac, :telefono)";
            $stmt1 = $this->db->prepare($sql1);
            $stmt1->execute([
                ':nombre'    => $datos['nombre'],
                ':apellido'  => $datos['apellido'],
                ':email'     => $datos['email'],
                ':dni'       => $datos['dni'],
                ':fecha_nac' => $datos['fecha_nac'],
                ':telefono'  => $datos['telefono']
            ]);

            // Obtengo el id del usuario que acabo de insertar
            $idPaciente = $this->db->lastInsertId();

            // Insertar en la tabla de paciente el usuario que acabo de insertar


            $sql2 = "INSERT INTO Usuario (nombre, apellido, email, password, id_paciente)
                    VALUES (:nombre, :apellido, :email, :password, :id_paciente)";
            $stmt2 = $this->db->prepare($sql2);

            // se carga un array con todos los parametros
            $stmt2->execute([
                ':nombre' => $datos['nombre'],
                ':apellido' => $datos['apellido'],
                ':email' => $datos['email'],
                ':password' => $passwordHasheada,
                ':id_paciente' => $idPaciente
            ]);

            // Commit de la transaccion
            $this->db->commit();
            return true;
        } catch (\Throwable $th) {

            // Si la tx falla, se hace un rollback
            $this->db->rollback();
            error_log($th->getMessage());
            return false;
        }
    }

    // Metodo que selecciona el id de paciente segun el usuario que esta logueado
    public function getIdPaciente(int $id_usuario)
    {
        $sql  = "SELECT id_paciente FROM Usuario WHERE id_usuario = :id_usuario";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);
        return $stmt->fetchColumn();
    }

    // Eliminar usuario
    public function eliminar(int $id_usuario)
    {
        try {
            $sql  = "DELETE FROM Usuario WHERE id_usuario = :id_usuario";
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
