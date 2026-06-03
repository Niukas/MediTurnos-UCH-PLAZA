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
     *  pendiente  → 3
     *  confirmado → 5
     *  cancelado  → 1
     *  realizado  → 12
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
}
