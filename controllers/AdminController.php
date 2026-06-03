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

// Instanciar las clases
$stats = new Stats($pdo);
$rol = new Rol($pdo);
$usuario = new Usuario($pdo);

$listadoRoles = $rol->getAll();

// llamado a metodos de estadisticas

$pacientesTotales = $stats->getTotalPacientes();

$medicosTotales = $stats->getTotalMedicos();

$turnosHoy = $stats->getTurnosHoy();

$turnosPorEstado = $stats->getTurnosPorEstado();

$listadoUsuarios = $usuario->getAll();

// Accion de formularios

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $accion = $_POST['accion'] ?? '';

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
}
?>