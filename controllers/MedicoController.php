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

        // REEMPLAZO CLAVE: Capturamos DNI en vez de id_paciente para redireccionar limpio
        $dni      = filter_var($_POST['dni'] ?? '', FILTER_SANITIZE_NUMBER_INT);
        $busqueda = filter_var($_POST['busqueda'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

        if ($resultado) {
            if ($origen === 'buscar') {
                header("Location: ../views/buscarPacienteMedico.php?busqueda=$busqueda&dni=$dni&registro=exitoso");
            } else {
                header('Location: ../views/panelMedico.php?registro=exitoso');
            }
            exit;
        } else {
            if ($origen === 'buscar') {
                header("Location: ../views/buscarPacienteMedico.php?busqueda=$busqueda&dni=$dni&registro=error");
            } else {
                header('Location: ../views/panelMedico.php?registro=error');
            }
        }
        exit;
    }

    if ($accion === 'bloquearDia') {
        $fecha     = filter_var($_POST['fecha']  ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $motivo    = filter_var($_POST['motivo'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS) ?: null;

        // 1. Bloqueamos el día en la tabla de bloqueos/horarios
        $resultado = $horario->bloquearDia($matriculaMedico, $fecha, $motivo);

        if ($resultado) {
            // 2. Si se bloqueó con éxito, cancelamos todos los turnos afectados
            $turno->cancelarTurnosPorBloqueo($matriculaMedico, $fecha, $motivo);

            header('Location: ../views/configurarHorarios.php?registro=bloqueado');
            exit;
        } else {
            header('Location: ../views/configurarHorarios.php?registro=error');
            exit;
        }
    }

    if ($accion === 'desbloquearDia') {
        $fecha     = filter_var($_POST['fecha'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $resultado = $horario->desbloquearDia($matriculaMedico, $fecha);

        if ($resultado) {
            header('Location: ../views/configurarHorarios.php?registro=desbloqueado');
            exit;
        } else {
            header('Location: ../views/configurarHorarios.php?registro=error');
            exit;
        }
    }

    // AGREGAR NUEVO HORARIO DE ATENCIÓN
    if ($accion === 'agregarHorario') {
        $horaInicio = filter_var($_POST['hora_inicio'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $horaFin    = filter_var($_POST['hora_fin'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

        // VALIDACIÓN LÓGICA: Que no estén vacías y que el fin sea después del inicio
        if (empty($horaInicio) || empty($horaFin) || $horaInicio >= $horaFin) {
            header('Location: ../views/configurarHorarios.php?registro=error_hora');
            exit;
        }

        $datos = [
            'matricula'       => $matriculaMedico,
            'id_especialidad' => filter_var($_POST['id_especialidad'] ?? 0, FILTER_SANITIZE_NUMBER_INT),
            'dia_semana'      => filter_var($_POST['dia_semana'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            'hora_inicio'     => $horaInicio,
            'hora_fin'        => $horaFin,
            'id_consultorio'  => filter_var($_POST['id_consultorio'] ?? 0, FILTER_SANITIZE_NUMBER_INT)
        ];

        $resultado = $horario->agregarHorario($datos);

        if ($resultado === true) {
            header('Location: ../views/configurarHorarios.php?registro=agregado');
        } elseif ($resultado === 'superpuesto') {
            header('Location: ../views/configurarHorarios.php?registro=error_superpuesto');
        } else {
            header('Location: ../views/configurarHorarios.php?registro=error');
        }
        exit;
    }

    // ELIMINAR HORARIO DE ATENCIÓN
    if ($accion === 'eliminarHorario') {
        // El (int) fuerza a que PHP trate el dato estrictamente como número, evitando fallos en PDO
        $idHorario = (int) filter_var($_POST['id_horario'] ?? 0, FILTER_SANITIZE_NUMBER_INT);

        $resultado = $horario->eliminarHorario($idHorario);

        if ($resultado) {
            header('Location: ../views/configurarHorarios.php?registro=eliminado');
        } else {
            header('Location: ../views/configurarHorarios.php?registro=error');
        }
        exit;
    }
}

if (SECCION === 'misTurnos') {
    // Recoger todos los filtros del GET
    $q = filter_var($_GET['q'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
    $estado = filter_var($_GET['estado'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
    $periodo = filter_var($_GET['periodo'] ?? 'dia', FILTER_SANITIZE_SPECIAL_CHARS);
    $paginaActual = filter_var($_GET['pagina']  ?? 1,     FILTER_SANITIZE_NUMBER_INT);
    
    // Construir un array de filtros para pasar al modelo
    $filtros = [
        'q' => $q,
        'estado' => $estado,
        'periodo' => $periodo,
        'matricula' => $matriculaMedico
    ];

    $totalTurnos  = $turno->getTotalByFiltros($filtros);
    $totalPaginas = ceil($totalTurnos / 20);
    $listadoTurnos = $turno->getByFiltros($filtros, $paginaActual);
}



if (SECCION === 'configurarHorarios') {

    // Traer la agenda actual que ya tiene configurada el médico
    $misHorarios = $horario->getHorariosMedico($matriculaMedico);

    // Traer SÓLO las especialidades de este médico (para el select del form)
    $sqlEsp = "SELECT e.id_especialidad, e.nombre 
               FROM Medico_Especialidad me
               JOIN Especialidad e ON me.id_especialidad = e.id_especialidad
               WHERE me.matricula = :matricula";
    $stmtEsp = $pdo->prepare($sqlEsp);
    $stmtEsp->execute([':matricula' => $matriculaMedico]);
    $misEspecialidades = $stmtEsp->fetchAll();

    // Traer todos los consultorios de la clínica (para el select del form)
    $sqlCons = "SELECT id_consultorio, numero, piso FROM Consultorio ORDER BY numero ASC";
    $stmtCons = $pdo->query($sqlCons);
    $listadoConsultorios = $stmtCons->fetchAll();

    // Traer ocupación de consultorios (de OTROS médicos) para filtrar en el front
    $sqlBusy = "SELECT dia_semana, id_consultorio FROM Horario_Atencion WHERE matricula != :matricula";
    $stmtBusy = $pdo->prepare($sqlBusy);
    $stmtBusy->execute([':matricula' => $matriculaMedico]);
    $ocupacionConsultorios = [];
    foreach ($stmtBusy->fetchAll() as $row) {
        $ocupacionConsultorios[$row['dia_semana']][] = $row['id_consultorio'];
    }
}
