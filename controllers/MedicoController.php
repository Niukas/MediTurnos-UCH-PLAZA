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
require_once '../models/Paciente.php';

$usuario = new Usuario($pdo);
$turno   = new Turno($pdo);
$horario = new Horario($pdo);
$paciente = new Paciente($pdo);

// Obtener la matrícula del médico logueado
$matriculaMedico = $usuario->getMatricula($_SESSION['usuario_id']);
$listadoBloqueos = $horario->getBloqueos($matriculaMedico);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'cambiarEstado') {
        $idTurno     = filter_var($_POST['id_turno']    ?? 0,  FILTER_SANITIZE_NUMBER_INT);
        $estado      = filter_var($_POST['estado']      ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $observacion = filter_var($_POST['observacion'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS) ?: null;
        $origen      = filter_var($_POST['origen']      ?? 'panel', FILTER_SANITIZE_SPECIAL_CHARS);

        $resultado   = $turno->actualizarEstadoObservacion($idTurno, $estado, $observacion);

        $idPaciente = filter_var($_POST['id_paciente'], FILTER_SANITIZE_NUMBER_INT);
        $busqueda   = filter_var($_POST['busqueda'], FILTER_SANITIZE_SPECIAL_CHARS);

        if ($resultado) {
            if ($origen === 'buscar') {
                header("Location: ../views/buscarPacienteMedico.php?busqueda=$busqueda&id_paciente=$idPaciente&registro=exitoso");
            } else {
                header('Location: ../views/panelMedico.php?registro=exitoso');
            }
            exit;
        } else {
            if ($origen === 'buscar') {
                header("Location: ../views/buscarPacienteMedico.php?busqueda=$busqueda&id_paciente=$idPaciente&registro=error");
            } else {
                header('Location: ../views/panelMedico.php?registro=error');
            }
        }
        exit;
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

if (SECCION === 'buscarPaciente') {
    $busqueda      = filter_var($_GET['busqueda'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
    $resultados    = [];
    if (!empty($busqueda)) {
        $resultados = $paciente->buscar($busqueda);
    }
    // Si se seleccionó un paciente específico — cargás sus turnos
    $turnosPaciente = [];
    $idPacienteVer  = filter_var($_GET['id_paciente'] ?? null, FILTER_SANITIZE_NUMBER_INT) ?: null;
    if ($idPacienteVer) {
        $turnosPaciente = $turno->getByFiltrosMedico($matriculaMedico, null, 'todos', 1, 100, $idPacienteVer);
    }
}
