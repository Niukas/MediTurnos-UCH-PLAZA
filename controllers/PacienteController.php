<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
// Verificacion si el usuario esta logueado o si esta logueado y su rol es paciente

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'paciente') {
    header('Location: ../views/login.php');
    exit;
}

require '../config/db.php';
require_once '../models/Usuario.php';
require_once '../models/Turno.php';
require_once '../models/Especialidad.php';
require_once '../models/Medico.php';
require_once '../models/Horario.php';
require_once '../models/Paciente.php';

$usuario = new Usuario($pdo);
$turno = new Turno($pdo);
$especialidad = new Especialidad($pdo);
$medico       = new Medico($pdo);
$horario      = new Horario($pdo);
$paciente     = new Paciente($pdo);

$idPaciente = $usuario->getIdPaciente($_SESSION['usuario_id']);

if (SECCION === 'misTurnos') {
    $especialidadFiltro = filter_var($_GET['especialidad'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS) ?: null;
    $periodo            = filter_var($_GET['periodo']      ?? 'todos', FILTER_SANITIZE_SPECIAL_CHARS);
    $paginaActual       = filter_var($_GET['pagina']       ?? 1, FILTER_SANITIZE_NUMBER_INT);
    $totalTurnos        = $turno->getTotalByFiltros($especialidadFiltro, $periodo, $idPaciente);
    $totalPaginas       = ceil($totalTurnos / 20);
    $listadoTurnos      = $turno->getByFiltros($especialidadFiltro, $periodo, $paginaActual, 20, $idPaciente);
}


if (SECCION === 'sacarTurno') {
    $listadoEspecialidades = $especialidad->getAll();
    $listadoPlanes         = $paciente->getByPaciente($idPaciente);
    $listadoMedicos        = [];
    $listadoHorarios       = [];

    $idEspecialidad = filter_var($_GET['id_especialidad'] ?? null, FILTER_SANITIZE_NUMBER_INT) ?: null;
    $matricula      = filter_var($_GET['matricula']       ?? null, FILTER_SANITIZE_SPECIAL_CHARS) ?: null;
    $fecha          = filter_var($_GET['fecha']           ?? null, FILTER_SANITIZE_SPECIAL_CHARS) ?: null;

    if ($idEspecialidad) {
        $listadoMedicos = $medico->getByEspecialidad($idEspecialidad);
    }

    if ($matricula && $fecha && $idEspecialidad) {
        $espData = $especialidad->getById($idEspecialidad);
        if ($espData) {
            $duracionMin     = $espData['duracion_turno_min'];
            $listadoHorarios = $horario->getDisponibles($matricula, $idEspecialidad, $fecha, $duracionMin);
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $accion = $_POST['accion'] ?? '';

        if ($accion === 'crearTurno') {
            $datos = [
                'fecha'           => filter_var($_POST['fecha']           ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
                'hora_inicio'     => filter_var($_POST['hora_inicio']     ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
                'id_paciente'     => filter_var($idPaciente,                    FILTER_SANITIZE_NUMBER_INT),
                'matricula'       => filter_var($_POST['matricula']       ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
                'id_especialidad' => filter_var($_POST['id_especialidad'] ?? 0,  FILTER_SANITIZE_NUMBER_INT),
                'id_consultorio'  => filter_var($_POST['id_consultorio']  ?? 0,  FILTER_SANITIZE_NUMBER_INT),
                'id_plan'         => filter_var($_POST['id_plan']         ?? 0,  FILTER_SANITIZE_NUMBER_INT),
                'nro_afiliado'    => filter_var($_POST['nro_afiliado']    ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
                'observacion'     => filter_var($_POST['observacion']     ?? '', FILTER_SANITIZE_SPECIAL_CHARS) ?: null,
            ];

            $resultado = $turno->crear($datos);

            if ($resultado) {
                header('Location: ../views/SacarTurno.php?registro=exitoso');
                exit;
            } else {
                header('Location: ../views/SacarTurno.php?registro=error');
                exit;
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'cancelarTurno') {
        $idTurno = filter_var($_POST['id_turno'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
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
