<?php
session_start();

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

// Instanciar las clases
$stats = new Stats($pdo);
$rol = new Rol($pdo);
$usuario = new Usuario($pdo);
$medico = new Medico($pdo);
$especialidad = new Especialidad($pdo);

$listadoRoles = $rol->getAll();

// llamado a metodos de estadisticas

$pacientesTotales = $stats->getTotalPacientes();

$medicosTotales = $stats->getTotalMedicos();

$turnosHoy = $stats->getTurnosHoy();

$turnosPorEstado = $stats->getTurnosPorEstado();

$listadoUsuarios = $usuario->getAll();

// dashboard Medicos

$listadoMedicos = $medico->getAll();
$listadoEspecialidad = $especialidad->getAll();

// Accion de formularios

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $accion = $_POST['accion'] ?? '';

    // Form de cambiar roles de usuario dentro de dashboardAdmin.php
    if ($accion === 'cambiarRol') {

        $idUsuario = $_POST['id_Usuario'];
        $idRol = $_POST['id_rol'];

        $resultado = $usuario->cambiarRol($idUsuario, $idRol);

        if ($resultado) {
            header("Location: ../views/dashboardAdmin.php?registro=exitoso");
            exit;
        }else {
            header("Location: ../views/dashboardAdmin.php?registro=error");
            exit;
        }
    }

    // Form de crear un medico dentro de medicosAdminDashboard.php
    if ($accion === 'crearMedico') {
        $datos = [
            'matricula'       => $_POST['matricula'],
            'nombre'          => $_POST['nombre'],
            'apellido'        => $_POST['apellido'],
            'telefono'        => $_POST['telefono'],
            'email'           => $_POST['email'],
            'id_especialidad' => $_POST['id_especialidad']
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

}
?>