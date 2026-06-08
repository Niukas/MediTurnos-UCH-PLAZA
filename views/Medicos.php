<?php 
require '../controllers/MedicosPublicController.php';
require '../config/helpers.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Médicos — MediTurnos</title>
</head>
<body>

<h1>Nuestros Medicos</h1>

<!-- FILTRO POR ESPECIALIDAD -->
<form method="GET" action="">
    <select name="especialidad" onchange="this.form.submit()">
        <option value="">Todas las especialidades</option>
        <?php foreach ($listadoEspecialidad as $e): ?>
            <option value="<?= $e['id_especialidad'] ?>"
                <?= isset($_GET['especialidad']) && $_GET['especialidad'] == $e['id_especialidad'] ? 'selected' : '' ?>>
                <?= h($e['nombre']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<!-- LISTADO -->
<table border="1">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Especialidades</th>
            <th>Horarios</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($listadoMedicos as $m): ?>
        <tr>
            <td><?= h($m['nombre']) . ' ' . h($m['apellido']) ?></td>
            <td><?= h($m['especialidades']) ?></td>
            <td><?= $m['horarios'] ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<a href="../public/index.php">← Volver al inicio</a>

</body>
</html>