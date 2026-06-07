<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'medico') {
    header('Location: ../views/login.php');
    exit;
}

require '../config/db.php';
require_once '../models/Usuario.php';
require_once '../models/Turno.php';

$usuario = new Usuario($pdo);
$turno   = new Turno($pdo);

// Obtener la matrícula del médico logueado
$matriculaMedico = $usuario->getMatricula($_SESSION['usuario_id']);

if (SECCION === 'misTurnos') {
    $periodo      = $_GET['periodo'] ?? 'dia'; // por defecto muestra hoy
    $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    $totalTurnos  = $turno->getTotalByFiltrosMedico($matriculaMedico, null, $periodo);
    $totalPaginas = ceil($totalTurnos / 20);
    $listadoTurnos = $turno->getByFiltrosMedico($matriculaMedico, null, $periodo, $paginaActual);

    $accion = $_POST['accion'] ?? '';

    if ($accion === 'cambiarEstado') {
        $idTurno   = (int)$_POST['id_turno'];
        $estado    = $_POST['estado'];
        $resultado = $turno->cambiarEstado($idTurno, $estado);

        if ($resultado) {
            header('Location: ../views/panelMedico.php?registro=exitoso');
            exit;
        } else {
            header('Location: ../views/panelMedico.php?registro=error');
            exit;
        }
    }
}
