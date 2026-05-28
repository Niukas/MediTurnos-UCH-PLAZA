<?php
require '../controllers/AdminController.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Dashboard</title>
</head>

<body>
    <table>
        <thead>
            <tr>
                <th>Pacientes</th>
                <th>Medicos</th>
                <th>Turnos de Hoy</th>
                <th>Estados</th>
            </tr>
        </thead>
        <tbody>
            <td><?= $pacientesTotales ?></td>
            <td><?= $medicosTotales ?></td>
            <td><?= $turnosHoy ?></td>
            <?php foreach ($turnosPorEstado as $estado): ?>
                <tr>
                    <td><?= $estado['estado'] ?></td>
                    <td><?= $estado['total'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>