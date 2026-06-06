<?php
class Horario {
    private \PDO $db;

    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }

    // Metodo para traer los turnos dispobles en base a el medico, su especialidad y la fecha
    public function getDisponibles(string $matricula, int $id_especialidad, string $fecha){
        $sql = "SELECT h.id_horario, h.dia_semana, h.hora_inicio, h.hora_fin, h.id_consultorio,
                c.numero AS consultorio_nro, c.piso
                FROM Horario_Atencion h
                JOIN Consultorio c ON h.id_consultorio = c.id_consultorio
                WHERE h.matricula = :matricula
                AND h.id_especialidad = :id_especialidad
                AND h.hora_inicio NOT IN (
                    SELECT hora_inicio FROM Turno
                    WHERE matricula = :matricula2
                    AND fecha = :fecha
                    AND estado != 'cancelado'
                )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':matricula' => $matricula,
            ':id_especialidad' => $id_especialidad,
            ':matricula2' => $matricula,
            ':fecha' => $fecha
        ]);
        return $stmt->fetchAll();
    }
}
?>