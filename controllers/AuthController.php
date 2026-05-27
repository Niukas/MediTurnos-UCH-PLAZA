<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../config/db.php';
require_once '../models/Usuario.php';

$usuario = new Usuario($pdo);

// Verificar si viene de post
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Guardo los valores de la consulta que se realiza por un metodo de la clase por medio del modelo
    $resultado = $usuario->login($email,$password);

    // Si el resultado contiene los valores se carga todo en la session
    if ($resultado) {

        session_regenerate_id(true);
        
        $_SESSION['usuario_id'] = $resultado['id_usuario'];
        $_SESSION['usuario_nombre'] = $resultado['nombre'];
        $_SESSION['usuario_email'] = $resultado['email'];
        $_SESSION['usuario_rol'] = $resultado['rol'];

        // Redireccion segun el ROL
        $destino = match ($resultado['rol']) {
            'paciente' => './views/panel.php',
            'medico' => './views/portal_medico.php',
            'recepcionista' => './views/recepcionista.php',
            'admin' => './views/dashboard.php',
            default => './views/inicio.php',
        };
        header("Location: $destino");
        exit;

    }else {
        $error = "Email o contraseña incorrectos.";
    }
}

?>