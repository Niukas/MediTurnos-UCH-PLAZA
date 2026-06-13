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
require_once '../models/Horario.php';

$usuario = new Usuario($pdo);
$turno   = new Turno($pdo);
$horario = new Horario($pdo);

// Obtener la matrícula del médico logueado
$matriculaMedico = $usuario->getMatricula($_SESSION['usuario_id']);
$listadoBloqueos = $horario->getBloqueos($matriculaMedico);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'cambiarEstado') {
        $idTurno     = filter_var($_POST['id_turno']    ?? 0,  FILTER_SANITIZE_NUMBER_INT);
        $estado      = filter_var($_POST['estado']      ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $observacion = filter_var($_POST['observacion'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS) ?: null;
        $resultado   = $turno->actualizarEstadoObservacion($idTurno, $estado, $observacion);

        if ($resultado) {
            header('Location: ../views/panelMedico.php?registro=exitoso');
            exit;
        } else {
            header('Location: ../views/panelMedico.php?registro=error');
            exit;
        }
    }

    if ($accion === 'bloquearDia') {
        $fecha     = filter_var($_POST['fecha']  ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $motivo    = filter_var($_POST['motivo'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS) ?: null;
        $resultado = $horario->bloquearDia($matriculaMedico, $fecha, $motivo);

        if ($resultado) {
            header('Location: ../views/panelMedico.php?registro=bloqueado');
            exit;
        } else {
            header('Location: ../views/panelMedico.php?registro=error');
            exit;
        }
    }

    if ($accion === 'desbloquearDia') {
        $fecha     = filter_var($_POST['fecha'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $resultado = $horario->desbloquearDia($matriculaMedico, $fecha);

        if ($resultado) {
            header('Location: ../views/panelMedico.php?registro=desbloqueado');
            exit;
        } else {
            header('Location: ../views/panelMedico.php?registro=error');
            exit;
        }
    }
}

if (SECCION === 'misTurnos') {
    $periodo      = filter_var($_GET['periodo'] ?? 'dia', FILTER_SANITIZE_SPECIAL_CHARS);
    $paginaActual = filter_var($_GET['pagina']  ?? 1,     FILTER_SANITIZE_NUMBER_INT);
    $totalTurnos  = $turno->getTotalByFiltrosMedico($matriculaMedico, null, $periodo);
    $totalPaginas = ceil($totalTurnos / 20);
    $listadoTurnos = $turno->getByFiltrosMedico($matriculaMedico, null, $periodo, $paginaActual);
}
