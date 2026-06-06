<?php
class Paciente
{
    private \PDO $db;

    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }

    // Trae los planes del paciente con nombre de obra social y cobertura
    public function getByPaciente(int $id_paciente)
    {
        $sql = "SELECT pp.nro_afiliado,
                       pl.id_plan,
                       pl.nombre_plan,
                       pl.porcentaje_cobertura,
                       os.nombre AS obra_social
                FROM Paciente_Plan pp
                JOIN Plan_OS pl ON pp.id_plan = pl.id_plan
                JOIN Obra_Social os ON pl.id_obra_social = os.id_obra_social
                WHERE pp.id_paciente = :id_paciente";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_paciente' => $id_paciente]);
        return $stmt->fetchAll();
    }
}
