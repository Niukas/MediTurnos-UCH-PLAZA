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

        $email    = filter_var($_POST['email']    ?? '', FILTER_SANITIZE_EMAIL);
        $password = filter_var($_POST['password'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

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
                'paciente' => '../views/Panel.php',
                'medico' => '../views/PanelMedico.php',
                'admin' => '../views/dashboardAdmin.php',
                default => '../views/inicio.php',
            };
            header("Location: $destino");
            exit;
        } else {
            header("Location: ../views/Login.php?errorLogin");
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
            'nombre'    => filter_var($_POST['nombre']    ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            'apellido'  => filter_var($_POST['apellido']  ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            'dni'       => filter_var($_POST['dni']       ?? '', FILTER_SANITIZE_NUMBER_INT),
            'fecha_nac' => filter_var($_POST['fecha_nac'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            'telefono'  => filter_var($_POST['telefono']  ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            'email'     => filter_var($_POST['email']     ?? '', FILTER_SANITIZE_EMAIL),
            'password'  => filter_var($_POST['password']  ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
        ];

        // Validaciones
        if (empty($datos['nombre']) || empty($datos['apellido']) || empty($datos['email']) || empty($datos['password'])) {
            header('Location: ../views/registro.php?error=campos_vacios');
            exit;
        }

        if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            header('Location: ../views/registro.php?error=email_invalido');
            exit;
        }

        if (strlen($datos['password']) <= 6) {
            header('Location: ../views/registro.php?error=password_corta');
            exit;
        }

        if (empty($datos['dni'])) {
            header('Location: ../views/registro.php?error=dni_invalido');
            exit;
        }

        $resultado = $usuario->registrar($datos);

        if ($resultado === 'dni_duplicado') {
            header('Location: ../views/registro.php?error=dni_duplicado');
            exit;
        } elseif ($resultado) {
            header('Location: ../views/Login.php?registro=exitoso');
            exit;
        } else {
            header('Location: ../views/Login.php?error=1');
            exit;
        }
    }
     if ($accion === 'recuperar') {
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $dni = filter_var($_POST['dni'] ?? '', FILTER_SANITIZE_NUMBER_INT);

        if (empty($email) || empty($dni)) {
            header('Location: ../views/Recuperar.php?error=datos_invalidos');
            exit;
        }

        $resultado = $usuario->verificarIdentidad($email, $dni);

        if ($resultado) {
            $_SESSION['usuario_id_para_reset'] = $resultado['id_usuario'];
            header('Location: ../views/Reset.php');
            exit;
        } else {
            header('Location: ../views/Recuperar.php?error=no_coincide');
            exit;
        }
    }

    if ($accion === 'reset') {
        if (!isset($_SESSION['usuario_id_para_reset'])) {
            header('Location: ../views/Login.php');
            exit;
        }

        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        if (strlen($password) < 6) {
            header('Location: ../views/Reset.php?error=password_corta');
            exit;
        }

        if ($password !== $password_confirm) {
            header('Location: ../views/Reset.php?error=no_coinciden');
            exit;
        }

        $id_usuario = $_SESSION['usuario_id_para_reset'];
        $resultado = $usuario->resetearPassword($id_usuario, $password);

        if ($resultado) {
            unset($_SESSION['usuario_id_para_reset']);
            header('Location: ../views/Login.php?exito=password_actualizada');
            exit;
        } else {
            header('Location: ../views/Reset.php?error=desconocido');
            exit;
        }
    }
}
