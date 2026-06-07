<?php
session_start();
$logueado = isset($_SESSION['usuario_id']);
$rol = $_SESSION['usuario_rol'] ?? null;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediTurnos</title>
</head>

<body>

    <h1>MediTurnos</h1>
    <p>Sistema de gestión de turnos médicos</p>

    <nav>
        <a href="../views/login.php">Iniciar sesión</a>
        <a href="../views/registro.php">Registrarme</a>
    </nav>

    <hr>

    <h2>Especialidades disponibles</h2>
    <ul>
        <li>Cardiología</li>
        <li>Clínica General</li>
        <li>Pediatría</li>
        <li>Dermatología</li>
        <li>Traumatología</li>
    </ul>

    <?php if ($logueado && $rol === 'paciente'): ?>
        <a href="../views/SacarTurno.php">Buscar turno</a>
    <?php else: ?>
        <a href="../views/Login.php">Buscar turno</a>
    <?php endif; ?>

    <a href="../views/Medicos.php">Ver médicos</a>

</body>
</html>