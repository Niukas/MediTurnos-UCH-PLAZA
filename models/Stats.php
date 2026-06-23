<?php

// Clase para los stats

class Stats
{

    private \PDO $db;

    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }

    // Metodo para traer el numero total de pacientes que hay en el sistema
    public function getTotalPacientes()
    {

        try {
            $sql = "SELECT COUNT(*) FROM Paciente";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $total = $stmt->fetchColumn();

            return $total;
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            return false;
        }
    }

    // Metodo para traer un numero total de los medicos cargados en el sistema
    public function getTotalMedicos()
    {
        try {
            $sql = "SELECT COUNT(*) FROM Medico";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $total = $stmt->fetchColumn();

            return $total;
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            return false;
        }
    }

    // Metodo para traer la cantidad de turnos que hay en el dia
    public function getTurnosHoy()
    {
        try {
            $sql = "SELECT COUNT(*) FROM Turno WHERE fecha = CURDATE()";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $total = $stmt->fetchColumn();

            return $total;
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            return false;
        }
    }

    /**
     * Metodo que trae los turnos por estado y su cantidad
     * pendiente  → 3
     * confirmado → 5
     * cancelado  → 1
     * realizado  → 12
     */
    public function getTurnosPorEstado()
    {
        try {
            $sql = "SELECT estado, COUNT(*) as total 
                    FROM Turno 
                    GROUP BY estado";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $estados = $stmt->fetchAll();

            return $estados;
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            return false;
        }
    }

    // Metodo que trae el médico con más turnos — devuelve un array con nombre, apellido y total_turnos
    public function getMedicoMasTurnos()
    {
        $sql = "SELECT m.nombre, m.apellido, COUNT(t.id_turno) AS total_turnos
            FROM Medico m
            JOIN Turno t ON m.matricula = t.matricula
            GROUP BY m.matricula
            ORDER BY total_turnos DESC
            LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Metodo que trae el listado de obras sociales ordenadas por cantidad de turnos — devuelve array de arrays
    public function getTurnosPorObraSocial()
    {
        $sql = "SELECT os.nombre AS obra_social, COUNT(t.id_turno) AS total_turnos
            FROM Turno t
            JOIN Plan_OS p ON t.id_plan = p.id_plan
            JOIN Obra_Social os ON p.id_obra_social = os.id_obra_social
            GROUP BY os.id_obra_social
            ORDER BY total_turnos DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Metodo que trae la especialidad más solicitada — devuelve un array con nombre y total_turnos
    public function getEspecialidadMasDemandada()
    {
        $sql = "SELECT e.nombre, COUNT(t.id_turno) AS total_turnos
            FROM Especialidad e
            JOIN Turno t ON e.id_especialidad = t.id_especialidad
            GROUP BY e.id_especialidad
            ORDER BY total_turnos DESC
            LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Metodo que trae la cantidad de pacientes que nunca tuvieron un turno no cancelado — devuelve un número
    public function getPacientesSinTurnos()
    {
        $sql = "SELECT COUNT(*) FROM Paciente
            WHERE dni NOT IN (
                SELECT DISTINCT dni FROM Turno
                WHERE estado != 'cancelado'
            )";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}
