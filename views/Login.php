<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login — MediTurnos</title>
</head>
<body>

    <h2>Iniciar sesión</h2>

<?php if (isset($_GET['error'])): ?>
    <p style="color:red">Hubo un error en el registro, intentá de nuevo.</p>
<?php endif; ?>

    <?php if (isset($_GET['registro'])): ?>
    <p style="color:green">Cuenta creada correctamente. Ya podés iniciar sesión.</p>
    <?php endif; ?>

    <form method="POST" action="../controllers/AuthController.php">
        <input type="hidden" name="accion" value="login">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Ingresar</button>
    </form>

</body>
</html>