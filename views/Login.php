<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login — MediTurnos</title>
</head>
<body>

    <h2>Iniciar sesión</h2>

    <?php if (!empty($error)): ?>
        <p style="color:red"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST" action="../controllers/AuthController.php">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Ingresar</button>
    </form>

</body>
</html>