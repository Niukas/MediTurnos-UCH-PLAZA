<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Verificacion si el usuario esta logueado y es paciente
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'paciente') {
    header('Location: ../views/login.php');
    exit;
}

require_once '../config/db.php';
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

if (defined('SECCION') && SECCION === 'misTurnos') {
    $especialidadFiltro = filter_var($_GET['especialidad'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS) ?: null;
    $periodo            = filter_var($_GET['periodo']      ?? 'todos', FILTER_SANITIZE_SPECIAL_CHARS);
    $paginaActual       = filter_var($_GET['pagina']       ?? 1, FILTER_SANITIZE_NUMBER_INT);

    $totalTurnos        = $turno->getTotalByFiltros($especialidadFiltro, $periodo, $idPaciente);
    $totalPaginas       = ceil($totalTurnos / 20);
    $listadoTurnos      = $turno->getByFiltros($especialidadFiltro, $periodo, $paginaActual, 20, $idPaciente);

    require_once '../models/Pago.php';
    $pagoModel = new Pago($pdo);

    // Recorremos los turnos y le inyectamos la info del pago a cada uno
    foreach ($listadoTurnos as $key => $t) {
        $pagoInfo = $pagoModel->getByTurno($t['id_turno']);

        if ($pagoInfo) {
            $listadoTurnos[$key]['estado_pago'] = $pagoInfo['estado_pago'];
            $listadoTurnos[$key]['monto_pago']  = $pagoInfo['monto'];
        } else {
            $listadoTurnos[$key]['estado_pago'] = null;
            $listadoTurnos[$key]['monto_pago']  = null;
        }
    }
}

if (defined('SECCION') && SECCION === 'sacarTurno') {
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
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    // 1. Procesar la creación de un turno nuevo
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
            require_once '../models/Pago.php';
            require_once '../models/Plan.php';

            $pago = new Pago($pdo);
            $plan = new Plan($pdo);

            $espData  = $especialidad->getById((int)$datos['id_especialidad']);
            $planData = $plan->getById((int)$datos['id_plan']);

            $precioBase  = $espData['precio'];
            $cobertura   = $planData['porcentaje_cobertura'];
            $descuento   = $precioBase * ($cobertura / 100);
            $montofinal  = $precioBase - $descuento;

            $pago->crear((int)$resultado, $montofinal);

            header('Location: ../views/pagarTurno.php?id_turno=' . $resultado . '&monto=' . $montofinal . '&cobertura=' . $cobertura . '&precio_base=' . $precioBase);
            exit;
        }
    }

    // 2. Procesar el pago de la pasarela de simulacion
    if ($accion === 'confirmarPago') {
        $id_turno = filter_var($_POST['id_turno'] ?? 0, FILTER_SANITIZE_NUMBER_INT);

        if ($id_turno) {
            require_once '../models/Pago.php';
            $pagoModel = new Pago($pdo);

            if ($pagoModel->confirmar($id_turno)) {
                $turno->cambiarEstado($id_turno, 'Confirmado');

                // Redirigimos al panel con un mensaje de éxito
                header('Location: ../views/Panel.php?pago=exitoso');
                exit;
            } else {
                header('Location: ../views/Panel.php?pago=error');
                exit;
            }
        }
    }

    // 3. Procesar la cancelación de turnos
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
    
    // Procesar el clic de "Pagar" desde el Panel
    if ($accion === 'prepararPago') {
        $id_turno = filter_var($_POST['id_turno'] ?? 0, FILTER_SANITIZE_NUMBER_INT);

        if ($id_turno) {
            require_once '../models/Pago.php';
            require_once '../models/Plan.php';

            $pagoModel = new Pago($pdo);
            $planModel = new Plan($pdo);

            // 1. Buscamos el turno completo en la DB
            $turnoData = $turno->getById($id_turno);
            $pagoData = $pagoModel->getByTurno($id_turno);

            if ($turnoData && $pagoData) {
                // 2. Buscamos el precio base real de esa especialidad
                $espData = $especialidad->getById((int)$turnoData['id_especialidad']);
                $precioBase = $espData ? (float)$espData['precio'] : (float)$pagoData['monto'];

                // 3. Buscamos la cobertura del plan usando el Modelo
                $cobertura = 0;
                if (!empty($turnoData['id_plan'])) {
                    $planData = $planModel->getById((int)$turnoData['id_plan']);
                    $cobertura = $planData ? (float)$planData['porcentaje_cobertura'] : 0;
                }

                $montoFinal = (float)$pagoData['monto'];

                // 4. Redirigimos a pagarTurno.php con la info fresca de la Base de Datos
                header('Location: ../views/pagarTurno.php?id_turno=' . $id_turno . '&monto=' . $montoFinal . '&cobertura=' . $cobertura . '&precio_base=' . $precioBase);
                exit;
            } else {
                header('Location: ../views/Panel.php?registro=error');
                exit;
            }
        }
    }
}
