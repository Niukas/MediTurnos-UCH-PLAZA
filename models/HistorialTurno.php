<?php
class HistorialTurno
{
    private \PDO $db;

    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }

    /**
     * Obtiene las últimas N actividades de la tabla de historial.
     * Une las tablas para obtener nombres de paciente y médico.
     * @param int $limit El número de registros a obtener.
     * @return array
     */
    public function getRecentActivity(int $limit = 15): array
    {
        try {
            $sql = "SELECT 
                        h.id_hist, 
                        h.id_turno, 
                        h.estado_anterior, 
                        h.estado_nuevo, 
                        h.fecha_cambio,
                        p.nombre as paciente_nombre, 
                        p.apellido as paciente_apellido,
                        m.nombre as medico_nombre, 
                        m.apellido as medico_apellido
                    FROM historial_turno h
                    JOIN turno t ON h.id_turno = t.id_turno
                    JOIN paciente p ON t.dni = p.dni
                    JOIN medico m ON t.matricula = m.matricula
                    ORDER BY h.fecha_cambio DESC
                    LIMIT :limit";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();

        } catch (\Throwable $th) {
            error_log("Error en HistorialTurno->getRecentActivity(): " . $th->getMessage());
            return [];
        }
    }

    /**
     * Obtiene el historial de cambios para un turno específico.
     * @param int $id_turno El ID del turno a consultar.
     * @return array
     */
    public function getHistoryForTurno(int $id_turno): array
    {
        try {
            $sql = "SELECT * FROM historial_turno 
                    WHERE id_turno = :id_turno 
                    ORDER BY fecha_cambio DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id_turno' => $id_turno]);
            return $stmt->fetchAll();

        } catch (\Throwable $th) {
            error_log("Error en HistorialTurno->getHistoryForTurno(): " . $th->getMessage());
            return [];
        }
    }
}
