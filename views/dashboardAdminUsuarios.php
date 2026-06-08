<?php
define('SECCION', 'usuarios');
require '../controllers/AdminController.php';
require '../config/helpers.php';
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
        'rol_actualizado' => ['color' => 'green', 'texto' => 'Rol actualizado correctamente.'],
        'editado'         => ['color' => 'green', 'texto' => 'Usuario actualizado correctamente.'],
        'eliminado'       => ['color' => 'green', 'texto' => 'Usuario eliminado correctamente.'],
        'error'           => ['color' => 'red',   'texto' => 'Hubo un error al realizar la operación.'],
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
                <th>Editar</th>
                <th>Eliminar</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($listadoUsuarios as $u): ?>
                <tr>
                    <td><?= $u['id_usuario'] ?></td>
                    <td><?= h($u['nombre']) . ' ' . h($u['apellido']) ?></td>
                    <td><?= h($u['email']) ?></td>
                    <td><span><?= ucfirst(h($u['rol'])) ?></span></td>

                    <!-- Cambiar rol -->
                    <td>
                        <form method="POST" action="dashboardAdminUsuarios.php">
                            <input type="hidden" name="accion" value="cambiarRol">
                            <input type="hidden" name="id_Usuario" value="<?= $u['id_usuario'] ?>">
                            <select name="id_rol">
                                <?php foreach ($listadoRoles as $rol): ?>
                                    <option value="<?= $rol['id_rol'] ?>"
                                        <?= $rol['id_rol'] == $u['id_rol'] ? 'selected' : '' ?>>
                                        <?= ucfirst(h($rol['nombre'])) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit">Guardar</button>
                        </form>
                    </td>

                    <!-- Editar -->
                    <td>
                        <button onclick="document.getElementById('editar-<?= $u['id_usuario'] ?>').style.display = 
        document.getElementById('editar-<?= $u['id_usuario'] ?>').style.display === 'none' ? 'block' : 'none'">
                            Editar
                        </button>
                        <div id="editar-<?= $u['id_usuario'] ?>" style="display:none">
                            <form method="POST" action="dashboardAdminUsuarios.php">
                                <input type="hidden" name="accion" value="editarUsuario">
                                <input type="hidden" name="id_usuario" value="<?= $u['id_usuario'] ?>">
                                <input type="text" name="nombre" value="<?= h($u['nombre']) ?>" required>
                                <input type="text" name="apellido" value="<?= h($u['apellido']) ?>" required>
                                <input type="email" name="email" value="<?= h($u['email']) ?>">
                                <button type="submit">Guardar</button>
                            </form>
                        </div>
                    </td>

                    <!-- Eliminar -->
                    <td>
                        <form method="POST" action="dashboardAdminUsuarios.php"
                            onsubmit="return confirm('¿Seguro que querés eliminar este usuario?')">
                            <input type="hidden" name="accion" value="eliminarUsuario">
                            <input type="hidden" name="id_usuario" value="<?= $u['id_usuario'] ?>">
                            <button type="submit">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <nav>
        <?php if ($paginaActual > 1): ?>
            <a href="?pagina=<?= $paginaActual - 1 ?>">← Anterior</a>
        <?php endif; ?>

        <?php
        $inicio = max(1, $paginaActual - 2);
        $fin    = min($totalPaginas, $paginaActual + 2);
        ?>

        <?php if ($inicio > 1): ?>
            <a href="?pagina=1">1</a>
            <?php if ($inicio > 2): ?> ... <?php endif; ?>
        <?php endif; ?>

        <?php for ($i = $inicio; $i <= $fin; $i++): ?>
            <?php if ($i == $paginaActual): ?>
                <strong><?= $i ?></strong>
            <?php else: ?>
                <a href="?pagina=<?= $i ?>"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($fin < $totalPaginas): ?>
            <?php if ($fin < $totalPaginas - 1): ?> ... <?php endif; ?>
            <a href="?pagina=<?= $totalPaginas ?>"><?= $totalPaginas ?></a>
        <?php endif; ?>

        <?php if ($paginaActual < $totalPaginas): ?>
            <a href="?pagina=<?= $paginaActual + 1 ?>">Siguiente →</a>
        <?php endif; ?>
    </nav>
</body>

</html>