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
    $periodo      = filter_var($_GET['periodo'] ?? 'dia', FILTER_SANITIZE_SPECIAL_CHARS);
    $paginaActual = filter_var($_GET['pagina']  ?? 1, FILTER_SANITIZE_NUMBER_INT);
    $totalTurnos  = $turno->getTotalByFiltrosMedico($matriculaMedico, null, $periodo);
    $totalPaginas = ceil($totalTurnos / 20);
    $listadoTurnos = $turno->getByFiltrosMedico($matriculaMedico, null, $periodo, $paginaActual);

    $accion = $_POST['accion'] ?? '';

    if ($accion === 'cambiarEstado') {
        $idTurno     = filter_var($_POST['id_turno']     ?? 0,  FILTER_SANITIZE_NUMBER_INT);
        $estado      = filter_var($_POST['estado']       ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $observacion = filter_var($_POST['observacion']  ?? '', FILTER_SANITIZE_SPECIAL_CHARS) ?: null;
        $resultado = $turno->cambiarEstado($idTurno, $estado);
        $resultado = $turno->actualizarEstadoObservacion($idTurno, $estado, $observacion);

        if ($resultado) {
            header('Location: ../views/panelMedico.php?registro=exitoso');
            exit;
        } else {
            header('Location: ../views/panelMedico.php?registro=error');
            exit;
        }
    }
}
