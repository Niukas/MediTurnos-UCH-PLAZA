<?php
// api/historial_turno.php

// 1. Configuración y Seguridad Básica
header('Content-Type: application/json');
require_once '../config/db.php';
require_once '../models/HistorialTurno.php';

// 2. Validar que se reciba un ID de turno
$id_turno = filter_var($_GET['id_turno'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
if (!$id_turno) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'ID de turno no válido o no proporcionado.']);
    exit;
}

// 3. Iniciar sesión para verificar permisos (opcional pero recomendado)
session_start();
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['usuario_rol'], ['admin', 'medico', 'paciente'])) {
    http_response_code(403); // Forbidden
    echo json_encode(['error' => 'Acceso no autorizado.']);
    exit;
}

// 4. Consultar el modelo y devolver los datos
try {
    $historialTurno = new HistorialTurno($pdo);
    $historial = $historialTurno->getHistoryForTurno($id_turno);

    echo json_encode($historial);

} catch (\Throwable $th) {
    http_response_code(500); // Internal Server Error
    error_log("Error en API historial_turno.php: " . $th->getMessage());
    echo json_encode(['error' => 'Ocurrió un error en el servidor al consultar el historial.']);
}
