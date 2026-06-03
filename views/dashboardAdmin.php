<?php 
require '../controllers/AdminController.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin — MediTurnos</title>
</head>
<body>

<!-- MENSAJES -->
<?php if (isset($_GET['registro']) && $_GET['registro'] === 'exitoso'): ?>
    <p style="color:green">Rol actualizado correctamente.</p>
<?php endif; ?>
<?php if (isset($_GET['registro']) && $_GET['registro'] === 'error'): ?>
    <p style="color:red">Hubo un error al actualizar el rol.</p>
<?php endif; ?>

<!-- NAV -->
<nav>
    <span>MediTurnos — Admin | Hola, <?= $_SESSION['usuario_nombre'] ?></span>
    <form method="POST" action="../controllers/AuthController.php">
        <input type="hidden" name="accion" value="logout">
        <button type="submit">Cerrar sesión</button>
    </form>
</nav>

<!-- STATS GENERALES -->
<h2>Stats generales</h2>
<ul>
    <li>Pacientes: <?= $pacientesTotales ?></li>
    <li>Médicos: <?= $medicosTotales ?></li>
    <li>Turnos hoy: <?= $turnosHoy ?></li>
    <li>Usuarios en el sistema: <?= count($listadoUsuarios) ?></li>
</ul>

<!-- TURNOS POR ESTADO -->
<h2>Turnos por estado</h2>
<ul>
    <?php foreach ($turnosPorEstado as $e): ?>
        <li><?= ucfirst($e['estado']) ?>: <?= $e['total'] ?></li>
    <?php endforeach; ?>
</ul>

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