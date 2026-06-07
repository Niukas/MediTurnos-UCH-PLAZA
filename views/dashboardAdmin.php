<?php
define('SECCION', 'stats');
require '../controllers/AdminController.php';
require 'layout/menuAdmin.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Dashboard — MediTurnos</title>
</head>

<body>

    <h1>Dashboard</h1>

    <!-- MENSAJES -->
    <?php
    $mensajes = [
        'exitoso' => ['color' => 'green', 'texto' => 'Operación realizada correctamente.'],
        'error'   => ['color' => 'red',   'texto' => 'Hubo un error.'],
    ];
    $registro = $_GET['registro'] ?? null;
    if ($registro && isset($mensajes[$registro])): ?>
        <p style="color:<?= $mensajes[$registro]['color'] ?>"><?= $mensajes[$registro]['texto'] ?></p>
    <?php endif; ?>

    <!-- STATS GENERALES -->
    <h2>Resumen general</h2>
    <ul>
        <li>Pacientes registrados: <?= $pacientesTotales ?></li>
        <li>Médicos activos: <?= $medicosTotales ?></li>
        <li>Turnos hoy: <?= $turnosHoy ?></li>
        <li>Pacientes sin turnos: <?= $pacientesSinTurno ?></li>
    </ul>

    <!-- TURNOS POR ESTADO -->
    <h2>Turnos por estado</h2>
    <ul>
        <?php foreach ($turnosPorEstado as $e): ?>
            <li><?= ucfirst($e['estado']) ?>: <?= $e['total'] ?></li>
        <?php endforeach; ?>
    </ul>

    <!-- MÉDICO CON MÁS TURNOS -->
    <h2>Médico con más turnos</h2>
    <p><?= $medicoConMasTurnos['nombre'] . ' ' . $medicoConMasTurnos['apellido'] ?> — <?= $medicoConMasTurnos['total_turnos'] ?> turnos</p>

    <!-- ESPECIALIDAD MÁS DEMANDADA -->
    <h2>Especialidad más demandada</h2>
    <p><?= $especialidadDemandada['nombre'] ?> — <?= $especialidadDemandada['total_turnos'] ?> turnos</p>

    <!-- TURNOS POR OBRA SOCIAL -->
    <h2>Turnos por obra social</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Obra Social</th>
                <th>Total turnos</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($listadoObraSocialTurnos as $os): ?>
                <tr>
                    <td><?= $os['obra_social'] ?></td>
                    <td><?= $os['total_turnos'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>

</html>