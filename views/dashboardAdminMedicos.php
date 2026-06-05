<?php
require '../controllers/AdminController.php';
require 'layout/menuAdmin.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Médicos — MediTurnos</title>
</head>

<body>

    <h1>Gestión de médicos</h1>

    <!-- MENSAJES -->
    <?php
    $mensajes = [
        'exitoso' => ['color' => 'green', 'texto' => 'Médico creado correctamente.'],
        'error'   => ['color' => 'red',   'texto' => 'Hubo un error al crear el médico.'],
    ];
    $registro = $_GET['registro'] ?? null;
    if ($registro && isset($mensajes[$registro])): ?>
        <p style="color:<?= $mensajes[$registro]['color'] ?>"><?= $mensajes[$registro]['texto'] ?></p>
    <?php endif; ?>

    <!-- FORMULARIO AGREGAR MÉDICO -->
    <h2>Agregar médico</h2>
    <form method="POST" action="../controllers/AdminController.php">
        <input type="hidden" name="accion" value="crearMedico">
        <input type="text" name="matricula" placeholder="Matrícula" required>
        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="text" name="apellido" placeholder="Apellido" required>
        <input type="text" name="telefono" placeholder="Teléfono">
        <input type="email" name="email" placeholder="Email">
        <select name="id_especialidad" required>
            <option value="">Seleccioná una especialidad</option>
            <?php foreach ($listadoEspecialidad as $e): ?>
                <option value="<?= $e['id_especialidad'] ?>"><?= $e['nombre'] ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Agregar médico</button>
    </form>

    <hr>

    <!-- LISTADO DE MÉDICOS -->
    <h2>Médicos registrados</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Matrícula</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Especialidades</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($listadoMedicos as $m): ?>
                <tr>
                    <td><?= $m['matricula'] ?></td>
                    <td><?= $m['nombre'] . ' ' . $m['apellido'] ?></td>
                    <td><?= $m['email'] ?></td>
                    <td><?= $m['telefono'] ?></td>
                    <td><?= $m['especialidades'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>

</html>