<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Login — MediTurnos</title>
</head>

<body>

    <h2>Iniciar sesión</h2>

    <?php
    $mensajes = [
        'errorLogin' => ['color' => 'red',   'texto' => 'Usuario o contraseña incorrectos.'],
        'error'      => ['color' => 'red',   'texto' => 'Hubo un error en el registro, intentá de nuevo.'],
        'registro'   => ['color' => 'green', 'texto' => 'Cuenta creada correctamente. Ya podés iniciar sesión.'],
    ];

    foreach ($mensajes as $key => $msg):
        if (isset($_GET[$key])): ?>
            <p style="color:<?= $msg['color'] ?>"><?= $msg['texto'] ?></p>
    <?php endif;
    endforeach;
    ?>

    <form method="POST" action="../controllers/AuthController.php">
        <input type="hidden" name="accion" value="login">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Ingresar</button>
    </form>

</body>

</html>