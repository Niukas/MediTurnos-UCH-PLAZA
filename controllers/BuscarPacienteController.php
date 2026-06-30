<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 1. Verificación de Seguridad: Asegurarse de que el usuario esté logueado y sea admin o medico.
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['usuario_rol'], ['admin', 'medico'])) {
    header('Location: ../views/login.php');
    exit;
}

// 2. Carga de Dependencias (Modelos)
require_once '../config/db.php';
require_once '../models/Usuario.php';
require_once '../models/Turno.php';
require_once '../models/Paciente.php';

// 3. Instanciación de Modelos
$usuario = new Usuario($pdo);
$turno   = new Turno($pdo);
$paciente = new Paciente($pdo);

// 4. Lógica de Búsqueda y Obtención de Datos
$busqueda = filter_var($_GET['busqueda'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
$resultados = [];

if (!empty($busqueda)) {
    $resultados = $paciente->buscar($busqueda);
}

// Lógica para obtener los turnos de un paciente específico si se selecciona
$turnosPaciente = [];
$dniVer = filter_var($_GET['dni'] ?? null, FILTER_SANITIZE_NUMBER_INT) ?: null;

if ($dniVer) {
    $filtros = ['dni' => $dniVer];

    // Si el usuario es un médico, la búsqueda de turnos del paciente se limita a los turnos con ese médico.
    if ($_SESSION['usuario_rol'] === 'medico') {
        $filtros['matricula'] = $usuario->getMatricula($_SESSION['usuario_id']);
    }

    // Se obtienen los turnos con un límite alto (100) para simular "todos" los turnos históricos.
    $turnosPaciente = $turno->getByFiltros($filtros, 1, 100);
}


// 5. Lógica de Formularios (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    // Acción para que un admin edite datos de un paciente
    if ($accion === 'editarPaciente' && $_SESSION['usuario_rol'] === 'admin') {
        $datos = [
            'dni'      => filter_var($_POST['dni']      ?? 0,  FILTER_SANITIZE_NUMBER_INT),
            'nombre'   => filter_var($_POST['nombre']   ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            'apellido' => filter_var($_POST['apellido'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            'telefono' => filter_var($_POST['telefono'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            'email'    => filter_var($_POST['email']    ?? '', FILTER_SANITIZE_EMAIL),
        ];
        $resultado = $paciente->editar($datos);
        $redirectBusqueda  = $_POST['busqueda'] ?? '';
        $redirectDni = $datos['dni'] ?? '';

        if ($resultado) {
            header("Location: ../views/buscarPaciente.php?busqueda=$redirectBusqueda&dni=$redirectDni&registro=editado");
        } else {
            header("Location: ../views/buscarPaciente.php?busqueda=$redirectBusqueda&dni=$redirectDni&registro=error");
        }
        exit;
    }

    // Acción para que un médico actualice la ficha (diagnóstico/estado) de un turno
    if ($accion === 'cambiarEstado' && $_SESSION['usuario_rol'] === 'medico') {
        $idTurno     = filter_var($_POST['id_turno']    ?? 0,  FILTER_SANITIZE_NUMBER_INT);
        $estado      = filter_var($_POST['estado']      ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $observacion = filter_var($_POST['observacion'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS) ?: null;

        $resultado   = $turno->actualizarEstadoObservacion($idTurno, $estado, $observacion);

        $redirectBusqueda = filter_var($_POST['busqueda'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $redirectDni      = filter_var($_POST['dni'] ?? '', FILTER_SANITIZE_NUMBER_INT);

        if ($resultado) {
            header("Location: ../views/buscarPaciente.php?busqueda=$redirectBusqueda&dni=$redirectDni&registro=exitoso");
        } else {
            header("Location: ../views/buscarPaciente.php?busqueda=$redirectBusqueda&dni=$redirectDni&registro=error");
        }
        exit;
    }
}
