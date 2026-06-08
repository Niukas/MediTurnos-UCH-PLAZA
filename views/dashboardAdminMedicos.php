<?php
define('SECCION', 'medicos');
require '../controllers/AdminController.php';
require 'layout/menuAdmin.php';
require '../config/helpers.php';
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
        'creado'   => ['color' => 'green', 'texto' => 'Médico creado correctamente.'],
        'editado'  => ['color' => 'green', 'texto' => 'Médico actualizado correctamente.'],
        'eliminado' => ['color' => 'green', 'texto' => 'Médico eliminado correctamente.'],
        'error'    => ['color' => 'red',   'texto' => 'Hubo un error al realizar la operación.'],
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
                <option value="<?= h($e['id_especialidad']) ?>"><?= h($e['nombre']) ?></option>
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
                <th>Horarios</th>
                <th>Eliminar</th>
                <th>Editar</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($listadoMedicos as $m): ?>
                <tr>
                    <td><?= h($m['matricula']) ?></td>
                    <td><?= h($m['nombre']) . ' ' . h($m['apellido']) ?></td>
                    <td><?= h($m['email']) ?></td>
                    <td><?= h($m['telefono']) ?></td>
                    <td><?= h($m['especialidades']) ?></td>
                    <td><?= h($m['horarios']) ?></td>

                    <!-- Eliminar -->
                    <td>
                        <form method="POST" action="dashboardAdminMedicos.php"
                            onsubmit="return confirm('¿Seguro que querés eliminar este médico?')">
                            <input type="hidden" name="accion" value="eliminarMedico">
                            <input type="hidden" name="matricula" value="<?= h($m['matricula']) ?>">
                            <button type="submit">Eliminar</button>
                        </form>
                    </td>

                    <!-- Editar -->
                    <!-- Editar -->
                    <td>
                        <button onclick="document.getElementById('editar-<?= $m['matricula'] ?>').style.display = 
        document.getElementById('editar-<?= $m['matricula'] ?>').style.display === 'none' ? 'block' : 'none'">
                            Editar
                        </button>
                        <div id="editar-<?= $m['matricula'] ?>" style="display:none">
                            <form method="POST" action="dashboardAdminMedicos.php">
                                <input type="hidden" name="accion" value="editarMedico">
                                <input type="hidden" name="matricula" value="<?= h($m['matricula']) ?>">
                                <input type="text" name="nombre" value="<?= h($m['nombre']) ?>" required>
                                <input type="text" name="apellido" value="<?= h($m['apellido']) ?>" required>
                                <input type="text" name="telefono" value="<?= h($m['telefono']) ?>">
                                <input type="email" name="email" value="<?= h($m['email']) ?>">
                                <button type="submit">Guardar</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>

</html>