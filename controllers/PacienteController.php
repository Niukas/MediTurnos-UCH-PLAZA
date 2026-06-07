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
    $especialidadFiltro = $_GET['especialidad'] ?? null;
    $periodo            = $_GET['periodo']      ?? 'todos';
    $paginaActual       = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    $totalTurnos        = $turno->getTotalByFiltros($especialidadFiltro, $periodo, $idPaciente);
    $totalPaginas       = ceil($totalTurnos / 20);
    $listadoTurnos      = $turno->getByFiltros($especialidadFiltro, $periodo, $paginaActual, 20, $idPaciente);
}


if (SECCION === 'sacarTurno') {
    $listadoEspecialidades = $especialidad->getAll();
    $listadoPlanes         = $paciente->getByPaciente($idPaciente);
    $listadoMedicos        = [];
    $listadoHorarios       = [];

    if (isset($_GET['id_especialidad'])) {
        $listadoMedicos = $medico->getByEspecialidad((int)$_GET['id_especialidad']);
    }

    if (isset($_GET['matricula'], $_GET['fecha'], $_GET['id_especialidad'])) {
        $espData = $especialidad->getById((int)$_GET['id_especialidad']);
        if ($espData) {
            $duracionMin     = $espData['duracion_turno_min'];
            $listadoHorarios = $horario->getDisponibles(
                $_GET['matricula'],
                (int)$_GET['id_especialidad'],
                $_GET['fecha'],
                $duracionMin
            );
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $accion = $_POST['accion'] ?? '';

        if ($accion === 'crearTurno') {
            $datos = [
                'fecha'           => $_POST['fecha'],
                'hora_inicio'     => $_POST['hora_inicio'],
                'observacion'     => $_POST['observacion'] ?? null,
                'id_paciente'     => $idPaciente,
                'matricula'       => $_POST['matricula'],
                'id_especialidad' => $_POST['id_especialidad'],
                'id_consultorio'  => $_POST['id_consultorio'],
                'id_plan'         => $_POST['id_plan'],
                'nro_afiliado'    => $_POST['nro_afiliado']
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
