<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro — MediTurnos</title>
</head>
<body>

    <h2>Registrarse</h2>

<?php if (isset($_GET['error'])): ?>
    <p style="color:red">Hubo un error en el registro, intentá de nuevo.</p>
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