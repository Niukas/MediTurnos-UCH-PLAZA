<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro — MediTurnos</title>
</head>

<body>

    <h2>Registrarse</h2>

    <?php
    $errores = [
        'campos_vacios'  => 'Completá todos los campos obligatorios.',
        'email_invalido' => 'El email no tiene un formato válido.',
        'password_corta' => 'La contraseña debe tener al menos 6 caracteres.',
        'dni_invalido'   => 'El DNI ingresado no es válido.',
    ];
    $error = $_GET['error'] ?? null;
    if ($error && isset($errores[$error])): ?>
        <p style="color:red"><?= $errores[$error] ?></p>
    <?php endif; ?>

    <form method="POST" action="../controllers/AuthController.php">
        <input type="hidden" name="accion" value="registrar">
        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="text" name="apellido" placeholder="Apellido" required>
        <input type="text" name="dni" placeholder="44444444" required>
        <input type="date" name="fecha_nac" required>
        <input type="text" name="telefono" placeholder="2616859565" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">registrar</button>
    </form>

</body>

</html>