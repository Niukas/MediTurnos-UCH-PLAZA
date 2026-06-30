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
require_once '../models/Paciente.php';

// Instanciar las clases
$stats        = new Stats($pdo);
$rol          = new Rol($pdo);
$usuario      = new Usuario($pdo);
$medico       = new Medico($pdo);
$especialidad = new Especialidad($pdo);
$turno        = new Turno($pdo);
$paciente     = new Paciente($pdo);

$listadoRoles = $rol->getAll();

// llamado a metodos de estadisticas

if (defined('SECCION') && SECCION === 'stats') {
    $pacientesTotales       = $stats->getTotalPacientes();
    $medicosTotales         = $stats->getTotalMedicos();
    $turnosHoy              = $stats->getTurnosHoy();
    $turnosPorEstado        = $stats->getTurnosPorEstado();
    $medicoConMasTurnos     = $stats->getMedicoMasTurnos();
    $listadoObraSocialTurnos = $stats->getTurnosPorObraSocial();
    $especialidadDemandada  = $stats->getEspecialidadMasDemandada();
    $pacientesSinTurno      = $stats->getPacientesSinTurnos();
}

// Dashboard Usuarios

if (defined('SECCION') && SECCION === 'usuarios') {
    $busqueda = filter_var($_GET['busqueda'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
    $rol = filter_var($_GET['rol'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);

    $filtros = [
        'busqueda' => $busqueda,
        'rol' => $rol
    ];

    if (!empty($busqueda) || !empty($rol)) {
        $listadoUsuarios = $usuario->buscar($filtros);
        $totalPaginas = 1; // No hay paginación en la búsqueda
        $paginaActual = 1;
    } else {
        $paginaActual  = filter_var($_GET['pagina'] ?? 1, FILTER_SANITIZE_NUMBER_INT);
        $totalUsuarios = $usuario->getTotalUsuarios($filtros);
        $totalPaginas  = ceil($totalUsuarios / 20);
        $listadoUsuarios = $usuario->getAll($filtros, $paginaActual);
    }
}

// dashboard Medicos

if (defined('SECCION') && SECCION === 'medicos') {
    $busqueda = filter_var($_GET['busqueda'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
    $especialidadFiltro = filter_var($_GET['especialidad'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
    
    $filtros = [
        'busqueda' => $busqueda,
        'especialidad' => $especialidadFiltro
    ];

    $listadoMedicos = $medico->getAll($filtros);
    $listadoEspecialidad = $especialidad->getAll();
}

// Dashboard de turnos

if (defined('SECCION') && SECCION === 'turnos') {
    $listadoEspecialidad = $especialidad->getAll();
    
    // Recoger todos los filtros del GET
    $q = filter_var($_GET['q'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
    $especialidadFiltro = filter_var($_GET['especialidad'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
    $estado = filter_var($_GET['estado'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
    $periodo = filter_var($_GET['periodo'] ?? 'todos', FILTER_SANITIZE_SPECIAL_CHARS);
    $paginaActual = filter_var($_GET['pagina'] ?? 1, FILTER_SANITIZE_NUMBER_INT);
    
    // Construir un array de filtros para pasar al modelo
    $filtros = [
        'q' => $q,
        'especialidad' => $especialidadFiltro,
        'estado' => $estado,
        'periodo' => $periodo
    ];

    $totalTurnos = $turno->getTotalByFiltros($filtros);
    $totalPaginas = ceil($totalTurnos / 20);
    $listadoTurnos = $turno->getByFiltros($filtros, $paginaActual);
}

// Buscar Pacientes

if (defined('SECCION') && SECCION === 'buscarPaciente') {
    $busqueda   = filter_var($_GET['busqueda'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
    $resultados = [];

    if (!empty($busqueda)) {
        $resultados = $paciente->buscar($busqueda);
    }

    // Si se seleccionó un paciente específico — cargás sus turnos
    $turnosPaciente = [];
    $dniVer = filter_var($_GET['dni'] ?? null, FILTER_SANITIZE_NUMBER_INT) ?: null;
    if ($dniVer) {
        $filtros = ['dni' => $dniVer];
        $turnosPaciente = $turno->getByFiltros($filtros, 1, 100);
    }
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
            header('Location: ../views/dashboardAdminUsuarios.php?registro=rol_actualizado');
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
            header('Location: ../views/dashboardAdminMedicos.php?registro=creado');
            exit;
        } else {
            header('Location: ../views/dashboardAdminMedicos.php?registro=error');
            exit;
        }
    }

    // Eliminar médico
    if ($accion === 'eliminarMedico') {
        $matricula = filter_var($_POST['matricula'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $resultado = $medico->eliminar($matricula);

        if ($resultado) {
            header('Location: ../views/dashboardAdminMedicos.php?registro=eliminado');
            exit;
        } else {
            header('Location: ../views/dashboardAdminMedicos.php?registro=error');
            exit;
        }
    }

    // Editar médico
    if ($accion === 'editarMedico') {
        $datos = [
            'matricula' => filter_var($_POST['matricula'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            'nombre'    => filter_var($_POST['nombre']    ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            'apellido'  => filter_var($_POST['apellido']  ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            'telefono'  => filter_var($_POST['telefono']  ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            'email'     => filter_var($_POST['email']     ?? '', FILTER_SANITIZE_EMAIL),
        ];
        $resultado = $medico->editar($datos);

        if ($resultado) {
            header('Location: ../views/dashboardAdminMedicos.php?registro=editado');
            exit;
        } else {
            header('Location: ../views/dashboardAdminMedicos.php?registro=error');
            exit;
        }
    }

    // Eliminar usuario
    if ($accion === 'eliminarUsuario') {
        $idUsuario = filter_var($_POST['id_usuario'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
        $resultado = $usuario->eliminar($idUsuario);

        if ($resultado) {
            header('Location: ../views/dashboardAdminUsuarios.php?registro=eliminado');
            exit;
        } else {
            header('Location: ../views/dashboardAdminUsuarios.php?registro=error');
            exit;
        }
    }

    // Editar usuario
    if ($accion === 'editarUsuario') {
        $datos = [
            'id_usuario' => filter_var($_POST['id_usuario'] ?? 0,  FILTER_SANITIZE_NUMBER_INT),
            'nombre'     => filter_var($_POST['nombre']     ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            'apellido'   => filter_var($_POST['apellido']   ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            'email'      => filter_var($_POST['email']      ?? '', FILTER_SANITIZE_EMAIL),
        ];
        $resultado = $usuario->editar($datos);

        if ($resultado) {
            header('Location: ../views/dashboardAdminUsuarios.php?registro=editado');
            exit;
        } else {
            header('Location: ../views/dashboardAdminUsuarios.php?registro=error');
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

    // POST — Editar paciente
    if ($accion === 'editarPaciente') {
        $datos = [
            'dni'      => filter_var($_POST['dni']      ?? 0,  FILTER_SANITIZE_NUMBER_INT),
            'nombre'   => filter_var($_POST['nombre']   ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            'apellido' => filter_var($_POST['apellido'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            'telefono' => filter_var($_POST['telefono'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            'email'    => filter_var($_POST['email']    ?? '', FILTER_SANITIZE_EMAIL),
        ];
        $resultado = $paciente->editar($datos);
        $redirect  = $_POST['busqueda'] ?? '';

        if ($resultado) {
            header("Location: ../views/buscarPacienteAdmin.php?busqueda=$redirect&registro=editado");
        } else {
            header("Location: ../views/buscarPacienteAdmin.php?busqueda=$redirect&registro=error");
        }
        exit;
    }
}
