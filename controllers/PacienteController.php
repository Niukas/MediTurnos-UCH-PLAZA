<?php
session_start();

// Verificacion si el usuario esta logueado o si esta logueado y su rol es paciente

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'paciente') {
    header('Location: ../views/login.php');
    exit;
}

require '../config/db.php';
require_once '../models/Usuario.php';
require_once '../models/Turno.php';

$usuario = new Usuario($pdo);
$turno = new Turno($pdo);


if (SECCION === 'misTurnos') {
    $idPaciente         = $usuario->getIdPaciente($_SESSION['usuario_id']);
    $especialidadFiltro = $_GET['especialidad'] ?? null;
    $periodo            = $_GET['periodo']      ?? 'todos';
    $paginaActual       = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    $totalTurnos        = $turno->getTotalByFiltros($especialidadFiltro, $periodo, $idPaciente);
    $totalPaginas       = ceil($totalTurnos / 20);
    $listadoTurnos      = $turno->getByFiltros($especialidadFiltro, $periodo, $paginaActual, 20, $idPaciente);
}

if (SECCION === 'sacarTurno') {
    
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'cancelarTurno') {
        $idTurno   = (int)$_POST['id_turno'];
        $resultado = $turno->cambiarEstado($idTurno, 'cancelado');

        if ($resultado) {
            header('Location: ../views/Panel.php?registro=exitoso');
            exit;
        } else {
            header('Location: ../views/Panel.php?registro=error');
            exit;
        }
    }
}
