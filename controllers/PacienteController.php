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

$usuario      = new Usuario($pdo);
$turno        = new Turno($pdo);
$especialidad = new Especialidad($pdo);
$medico       = new Medico($pdo);
$horario      = new Horario($pdo);
$paciente     = new Paciente($pdo);

$dni = $usuario->getDni($_SESSION['usuario_id']);
error_log("DEBUG: Usuario ID: " . $_SESSION['usuario_id'] . ", DNI obtenido: " . var_export($dni, true));

if (defined('SECCION') && SECCION === 'misTurnos') {
    $especialidadFiltro = filter_var($_GET['especialidad'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS) ?: null;
    $periodo            = filter_var($_GET['periodo']      ?? 'todos', FILTER_SANITIZE_SPECIAL_CHARS);
    $paginaActual       = filter_var($_GET['pagina']       ?? 1, FILTER_SANITIZE_NUMBER_INT);

    $totalTurnos        = $turno->getTotalByFiltros($especialidadFiltro, $periodo, $dni);
    $totalPaginas       = ceil($totalTurnos / 20);
    $listadoTurnos      = $turno->getByFiltros($especialidadFiltro, $periodo, $paginaActual, 20, $dni);

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
    $listadoPlanes         = $paciente->getByPaciente($dni);
    $listadoMedicos        = [];
    $listadoHorarios       = [];


    $todosLosMedicos       = $medico->getAll();
    $especialidadesDelMedico = [];

    $idEspecialidad = filter_var($_GET['id_especialidad'] ?? null, FILTER_SANITIZE_NUMBER_INT) ?: null;
    $matricula      = filter_var($_GET['matricula']       ?? null, FILTER_SANITIZE_SPECIAL_CHARS) ?: null;
    $fecha          = filter_var($_GET['fecha']           ?? null, FILTER_SANITIZE_SPECIAL_CHARS) ?: null;


    if ($matricula && !$idEspecialidad) {
        $sqlEsp = "SELECT e.id_especialidad, e.nombre, e.duracion_turno_min 
                   FROM Medico_Especialidad me
                   JOIN Especialidad e ON me.id_especialidad = e.id_especialidad
                   WHERE me.matricula = :matricula";
        $stmtEsp = $pdo->prepare($sqlEsp);
        $stmtEsp->execute([':matricula' => $matricula]);
        $especialidadesDelMedico = $stmtEsp->fetchAll();

        // Si el médico atiende una sola especialidad, autocompletamos y recargamos
        if (count($especialidadesDelMedico) === 1) {
            $idAuto = $especialidadesDelMedico[0]['id_especialidad'];
            header("Location: SacarTurno.php?id_especialidad=$idAuto&matricula=$matricula");
            exit;
        }
    }

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

        if (empty($dni)) {
            error_log("DEBUG: Intento de crear turno sin DNI para usuario ID: " . $_SESSION['usuario_id']);
            header('Location: ../views/SacarTurno.php?registro=error');
            exit;
        }

        // Obtenemos el ID del plan. Si es "Particular", llegará un 0.
        $idPlan = filter_var($_POST['id_plan'] ?? 0,  FILTER_SANITIZE_NUMBER_INT);

        $datos = [
            'fecha'           => filter_var($_POST['fecha']           ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            'hora_inicio'     => filter_var($_POST['hora_inicio']     ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            'dni'             => $dni,
            'matricula'       => filter_var($_POST['matricula']       ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            'id_especialidad' => filter_var($_POST['id_especialidad'] ?? 0,  FILTER_SANITIZE_NUMBER_INT),
            'id_consultorio'  => filter_var($_POST['id_consultorio']  ?? 0,  FILTER_SANITIZE_NUMBER_INT),

            'id_plan'         => ($idPlan > 0) ? $idPlan : null,
            'nro_afiliado'    => ($idPlan > 0) ? filter_var($_POST['nro_afiliado'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS) : null,

            'observacion'     => filter_var($_POST['observacion']     ?? '', FILTER_SANITIZE_SPECIAL_CHARS) ?: null,
        ];

        $resultado = $turno->crear($datos);

        if ($resultado) {
            require_once '../models/Pago.php';
            require_once '../models/Plan.php';

            $pago = new Pago($pdo);
            $plan = new Plan($pdo);

            $espData  = $especialidad->getById((int)$datos['id_especialidad']);
            $precioBase  = $espData['precio'];

            // Verificamos si eligió "Particular" o una Obra Social real
            if (!empty($datos['id_plan'])) {
                $planData = $plan->getById((int)$datos['id_plan']);
                $cobertura = $planData ? $planData['porcentaje_cobertura'] : 0;
            } else {
                $cobertura = 0;
            }

            $descuento   = $precioBase * ($cobertura / 100);
            $montofinal  = $precioBase - $descuento;

            $pago->crear((int)$resultado, $montofinal);

            header('Location: ../views/pagarTurno.php?id_turno=' . $resultado . '&monto=' . $montofinal . '&cobertura=' . $cobertura . '&precio_base=' . $precioBase);
            exit;
        } else {
            header('Location: ../views/SacarTurno.php?registro=error');
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
                $turno->cambiarEstado($id_turno, 'confirmado');

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
        $idTurno   = filter_var($_POST['id_turno'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
        $resultado = $turno->cambiarEstado($idTurno, 'cancelado');

        if ($resultado) {
            header('Location: ../views/Panel.php?registro=exitoso');
            exit;
        } else {
            header('Location: ../views/Panel.php?registro=error');
            exit;
        }
    }

    // 4. Procesar el clic de "Pagar" desde el Panel
    if ($accion === 'prepararPago') {
        $id_turno = filter_var($_POST['id_turno'] ?? 0, FILTER_SANITIZE_NUMBER_INT);

        if ($id_turno) {
            require_once '../models/Pago.php';
            require_once '../models/Plan.php';

            $pagoModel = new Pago($pdo);
            $planModel = new Plan($pdo);

            $turnoData = $turno->getById($id_turno);
            $pagoData  = $pagoModel->getByTurno($id_turno);

            if ($turnoData && $pagoData) {
                $espData    = $especialidad->getById((int)$turnoData['id_especialidad']);
                $precioBase = $espData ? (float)$espData['precio'] : (float)$pagoData['monto'];

                $cobertura = 0;
                if (!empty($turnoData['id_plan'])) {
                    $planData  = $planModel->getById((int)$turnoData['id_plan']);
                    $cobertura = $planData ? (float)$planData['porcentaje_cobertura'] : 0;
                }

                $montoFinal = (float)$pagoData['monto'];

                header('Location: ../views/pagarTurno.php?id_turno=' . $id_turno . '&monto=' . $montoFinal . '&cobertura=' . $cobertura . '&precio_base=' . $precioBase);
                exit;
            } else {
                header('Location: ../views/Panel.php?registro=error');
                exit;
            }
        }
    }
}
