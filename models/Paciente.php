<?php
class Paciente
{
    private \PDO $db;

    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }

    // Trae los planes del paciente con nombre de obra social y cobertura
    public function getByPaciente($dni)
    {
        $sql = "SELECT pp.nro_afiliado,
                   pl.id_plan,
                   pl.nombre_plan,
                   pl.porcentaje_cobertura,
                   os.nombre AS obra_social
            FROM Paciente_Plan pp
            JOIN Plan_OS pl ON pp.id_plan = pl.id_plan
            JOIN Obra_Social os ON pl.id_obra_social = os.id_obra_social
            WHERE pp.dni = :dni";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':dni' => $dni]);
        return $stmt->fetchAll();
    }

    // Metodo para buscar por nombre, apellido o dni
    public function buscar(string $busqueda)
    {
        $sql = "SELECT p.*, u.email
            FROM Paciente p
            LEFT JOIN Usuario u ON u.dni = p.dni
            WHERE p.nombre   LIKE :busqueda
            OR p.apellido    LIKE :busqueda
            OR p.dni         LIKE :busqueda
            ORDER BY p.apellido ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':busqueda' => '%' . $busqueda . '%']);
        return $stmt->fetchAll();
    }

    // Metodo para editar un paciente 
    public function editar($datos)
    {
        try {
            $sql = "UPDATE Paciente 
                SET nombre = :nombre, apellido = :apellido,
                    telefono = :telefono, email = :email
                WHERE dni = :dni";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':nombre'   => $datos['nombre'],
                ':apellido' => $datos['apellido'],
                ':telefono' => $datos['telefono'],
                ':email'    => $datos['email'],
                ':dni'      => $datos['dni'],
            ]);
            return true;
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            return false;
        }
    }
}
