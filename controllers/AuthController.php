<?php
session_start();

require '../config/db.php';
require_once '../models/Usuario.php';

// Instanciar la clase Usuario
$usuario = new Usuario($pdo);

// Verificar si viene de post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Guardar en una variable el tipo de formulario que llegó
    $accion = $_POST['accion'] ?? '';

    // Logica del Login
    if ($accion === 'login') {

        $email = $_POST['email'];
        $password = $_POST['password'];

        // Guardo los valores de la consulta que se realiza por un metodo de la clase por medio del modelo
        $resultado = $usuario->login($email, $password);

        // Si el resultado contiene los valores se carga todo en la session
        if ($resultado) {

            session_regenerate_id(true);

            $_SESSION['usuario_id'] = $resultado['id_usuario'];
            $_SESSION['usuario_nombre'] = $resultado['nombre'];
            $_SESSION['usuario_email'] = $resultado['email'];
            $_SESSION['usuario_rol'] = $resultado['rol'];

            // Redireccion segun el ROL
            $destino = match ($resultado['rol']) {
                'paciente' => '../views/panel.php',
                'medico' => '../views/portal_medico.php',
                'admin' => '../views/dashboardAdmin.php',
                default => '../views/inicio.php',
            };
            header("Location: $destino");
            exit;
        } else {
            $error = "Email o contraseña incorrectos.";
            header("Location: ../views/Login.php");
        }
    }

    // ----------------
    // Logica de logout
    if ($accion === 'logout') {
        
        // Vacio todos los datos de la variable session
        session_unset();

        // Destruir la session
        session_destroy();

        header('Location: ../public/Index.php');
    }

    if ($accion === 'registrar') {

        // Recibimos los datos del post y lo guardo en un array
        $datos = [
            'nombre' => $_POST['nombre'],
            'apellido' => $_POST['apellido'],
            'dni' => $_POST['dni'],
            'fecha_nac' => $_POST['fecha_nac'],
            'telefono' => $_POST['telefono'],
            'email' => $_POST['email'],
            'password' => $_POST['password']
            ];
        
        $resultado = $usuario->registrar($datos);

        if ($resultado) {
            header('Location: ../views/Login.php?registro=exitoso');
            exit;
        }else {
            header('Location: ../views/Login.php?error=1');
            exit;
        }
    }

}
