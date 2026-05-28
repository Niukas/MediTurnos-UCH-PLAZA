<?php
session_start();

// Verificacion si el usuario esta logueado o si esta logueado y su rol es admin

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
    header('Location: ../views/login.php');
    exit;
}

require '../config/db.php';
require_once '../models/Stats.php';

// Instanciar la clase stats
$stats = new Stats($pdo);

$pacientesTotales = $stats->getTotalPacientes();

$medicosTotales = $stats->getTotalMedicos();

$turnosHoy = $stats->getTurnosHoy();

$turnosPorEstado = $stats->getTurnosPorEstado();

?>