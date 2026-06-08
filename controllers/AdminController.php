<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Verificacion si el usuario esta logueado o si esta logueado y su rol es admin

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
    header('Location: ../views/login.php');
    exit;
}

require '../config/db.php';
require_once '../models/Stats.php';
require_once '../models/Rol.php';
require_once '../models/Usuario.php';
require_once '../models/Medico.php';
require_once '../models/Especialidad.php';
require_once '../models/Turno.php';

// Instanciar las clases
$stats = new Stats($pdo);
$rol = new Rol($pdo);
$usuario = new Usuario($pdo);
$medico = new Medico($pdo);
$especialidad = new Especialidad($pdo);
$turno = new Turno($pdo);

$listadoRoles = $rol->getAll();

// llamado a metodos de estadisticas

if (SECCION === 'stats') {
    $pacientesTotales = $stats->getTotalPacientes();
    $medicosTotales   = $stats->getTotalMedicos();
    $turnosHoy        = $stats->getTurnosHoy();
    $turnosPorEstado  = $stats->getTurnosPorEstado();
    $medicoConMasTurnos = $stats->getMedicoMasTurnos();
    $listadoObraSocialTurnos = $stats->getTurnosPorObraSocial();
    $especialidadDemandada = $stats->getEspecialidadMasDemandada();
    $pacientesSinTurno = $stats->getPacientesSinTurnos();
}

// Dashboard Usuarios

if (SECCION === 'usuarios') {
    $listadoUsuarios  = $usuario->getAll();
}

// dashboard Medicos

if (SECCION === 'medicos') {
    $listadoMedicos      = $medico->getAll();
    $listadoEspecialidad = $especialidad->getAll();
}

// Dashboard de turnos

if (SECCION === 'turnos') {
    $listadoEspecialidad = $especialidad->getAll();
    $especialidadFiltro = filter_var($_GET['especialidad'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS) ?: null;
    $periodo            = filter_var($_GET['periodo']      ?? 'todos', FILTER_SANITIZE_SPECIAL_CHARS);
    $paginaActual       = filter_var($_GET['pagina']       ?? 1, FILTER_SANITIZE_NUMBER_INT);
    $totalTurnos        = $turno->getTotalByFiltros($especialidadFiltro, $periodo);
    $totalPaginas       = ceil($totalTurnos / 20);
    $listadoTurnos      = $turno->getByFiltros($especialidadFiltro, $periodo, $paginaActual);
}

// Accion de formularios

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $accion = $_POST['accion'] ?? '';

    // Form de cambiar roles de usuario dentro de dashboardAdmin.php
    if ($accion === 'cambiarRol') {

        $idUsuario = filter_var($_POST['id_Usuario'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
        $idRol     = filter_var($_POST['id_rol']     ?? 0, FILTER_SANITIZE_NUMBER_INT);

        $resultado = $usuario->cambiarRol($idUsuario, $idRol);

        if ($resultado) {
            header("Location: ../views/dashboardAdmin.php?registro=exitoso");
            exit;
        } else {
            header("Location: ../views/dashboardAdmin.php?registro=error");
            exit;
        }
    }

    // Form de crear un medico dentro de medicosAdminDashboard.php
    if ($accion === 'crearMedico') {

        $datos = [
            'matricula'       => filter_var($_POST['matricula']       ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            'nombre'          => filter_var($_POST['nombre']          ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            'apellido'        => filter_var($_POST['apellido']        ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            'telefono'        => filter_var($_POST['telefono']        ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            'email'           => filter_var($_POST['email']           ?? '', FILTER_SANITIZE_EMAIL),
            'id_especialidad' => filter_var($_POST['id_especialidad'] ?? 0,  FILTER_SANITIZE_NUMBER_INT),
        ];

        $resultado = $medico->crearMedico($datos);

        if ($resultado) {
            header('Location: ../views/dashboardAdminMedicos.php?registro=exitoso');
            exit;
        } else {
            header('Location: ../views/dashboardAdminMedicos.php?registro=error');
            exit;
        }
    }

    // Form que actualiza el estado de un turno en la db
    if ($accion === 'cambiarEstado') {

        $idTurno = filter_var($_POST['id_turno'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
        $estado  = filter_var($_POST['estado']   ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

        $resultado = $turno->cambiarEstado($idTurno, $estado);

        if ($resultado) {
            header('Location: ../views/dashboardAdminTurnos.php?registro=exitoso');
            exit;
        } else {
            header('Location: ../views/dashboardAdminTurnos.php?registro=error');
            exit;
        }
    }
}
