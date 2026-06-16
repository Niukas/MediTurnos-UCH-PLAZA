<?php
class Pago
{
    private \PDO $db;

    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }

    // Crea el pago en estado pendiente cuando se registra el turno
    public function crear(int $id_turno, float $monto)
    {
        try {
            $sql = "INSERT INTO Pago (id_turno, monto, estado_pago)
                    VALUES (:id_turno, :monto, 'pendiente')";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id_turno' => $id_turno,
                ':monto'    => $monto
            ]);
            return true;
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            return false;
        }
    }

    // Confirma el pago y actualiza la fecha
    public function confirmar(int $id_turno)
    {
        try {
            $sql = "UPDATE Pago 
                    SET estado_pago = 'pagado', fecha_pago = NOW()
                    WHERE id_turno = :id_turno";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id_turno' => $id_turno]);
            return true;
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            return false;
        }
    }

    // Trae el pago de un turno específico
    public function getByTurno(int $id_turno)
    {
        try {
            $sql  = "SELECT * FROM Pago WHERE id_turno = :id_turno";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id_turno' => $id_turno]);
            return $stmt->fetch();
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            return null;
        }
    }
}
