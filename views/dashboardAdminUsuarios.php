<?php
define('SECCION', 'usuarios');
require '../controllers/AdminController.php'; 
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin — MediTurnos</title>
</head>

<body>

    <!-- MENSAJES -->
    <?php
    $mensajes = [
        'exitoso' => ['color' => 'green', 'texto' => 'Rol actualizado correctamente.'],
        'error'   => ['color' => 'red',   'texto' => 'Hubo un error al actualizar el rol.'],
    ];

    $registro = $_GET['registro'] ?? null;

    if ($registro && isset($mensajes[$registro])): ?>
        <p style="color:<?= $mensajes[$registro]['color'] ?>"><?= $mensajes[$registro]['texto'] ?></p>
    <?php endif; ?>

    <!-- NAV -->
    <?php require '../views/layout/menuAdmin.php'; ?>

    <!-- TABLA USUARIOS -->
    <h2>Gestión de usuarios</h2>
    <table border="1">
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol actual</th>
                <th>Cambiar rol</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($listadoUsuarios as $u): ?>
                <tr>
                    <td><?= $u['id_usuario'] ?></td>
                    <td><?= $u['nombre'] . ' ' . $u['apellido'] ?></td>
                    <td><?= $u['email'] ?></td>
                    <td><?= ucfirst($u['rol']) ?></td>
                    <td>
                        <form method="POST" action="../controllers/AdminController.php">
                            <input type="hidden" name="accion" value="cambiarRol">
                            <input type="hidden" name="id_Usuario" value="<?= $u['id_usuario'] ?>">
                            <select name="id_rol">
                                <?php foreach ($listadoRoles as $rol): ?>
                                    <option value="<?= $rol['id_rol'] ?>"
                                        <?= $rol['id_rol'] == $u['id_rol'] ? 'selected' : '' ?>>
                                        <?= ucfirst($rol['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit">Guardar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>

</html>