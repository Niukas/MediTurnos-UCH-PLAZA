<?php 
define('SECCION', 'turnos');
require '../controllers/AdminController.php';
require 'layout/menuAdmin.php';
require '../config/helpers.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Turnos — MediTurnos</title>
</head>

<body>

    <h1>Gestión de turnos</h1>

    <!-- FILTROS -->
    <form method="GET" action="">

        <!-- Filtro período -->
        <span>Período:</span>
        <?php
        $periodos = ['dia' => 'Hoy', 'semana' => 'Semana', 'mes' => 'Mes', 'todos' => 'Todos'];
        foreach ($periodos as $valor => $label):
        ?>
            <a href="?periodo=<?= $valor ?>&especialidad=<?= $especialidadFiltro ?>">
                <?php if ($periodo === $valor): ?>
                    <strong><?= $label ?></strong>
                <?php else: ?>
                    <?= $label ?>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>

        <!-- Filtro especialidad -->
        <select name="especialidad" onchange="this.form.submit()">
            <option value="">Todas las especialidades</option>
            <?php foreach ($listadoEspecialidad as $e): ?>
                <option value="<?= h($e['nombre']) ?>"
                    <?= $especialidad === $e['nombre'] ? 'selected' : '' ?>>
                    <?= h($e['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <input type="hidden" name="periodo" value="<?= $periodo ?>">

    </form>

    <p>Total: <?= $totalTurnos ?> turnos | Página <?= $paginaActual ?> de <?= $totalPaginas ?></p>

    <!-- TURNOS AGRUPADOS POR ESPECIALIDAD -->
    <?php
    $turnosPorEspecialidad = [];
    foreach ($listadoTurnos as $t) {
        $turnosPorEspecialidad[$t['especialidad']][] = $t;
    }
    ?>

    <?php foreach ($turnosPorEspecialidad as $nombreEspecialidad => $turnos): ?>

        <h2><?= $nombreEspecialidad ?> (<?= count($turnos) ?>)</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Paciente</th>
                    <th>Médico</th>
                    <th>Estado / Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($turnos as $t): ?>
                    <tr>
                        <td><?= $t['id_turno'] ?></td>
                        <td><?= $t['fecha'] ?></td>
                        <td><?= $t['hora_inicio'] ?></td>
                        <td><?= h($t['paciente_nombre']) . ' ' . h($t['paciente_apellido']) ?></td>
                        <td><?= h($t['medico_nombre']) . ' ' . h($t['medico_apellido']) ?></td>
                        <td>
                            <form method="POST" action="dashboardAdminTurnos.php">
                                <input type="hidden" name="accion" value="cambiarEstado">
                                <input type="hidden" name="id_turno" value="<?= $t['id_turno'] ?>">
                                <select name="estado">
                                    <?php
                                    $estados = ['pendiente', 'confirmado', 'cancelado', 'realizado'];
                                    foreach ($estados as $e):
                                    ?>
                                        <option value="<?= $e ?>" <?= $t['estado'] === $e ? 'selected' : '' ?>>
                                            <?= ucfirst($e) ?>
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

    <?php endforeach; ?>

    <!-- PAGINACION -->
    <nav>
        <?php if ($paginaActual > 1): ?>
            <a href="?pagina=<?= $paginaActual - 1 ?>&periodo=<?= $periodo ?>&especialidad=<?= $especialidadFiltro ?>">← Anterior</a>
        <?php endif; ?>

        <?php
        // Mostrar solo 5 páginas alrededor de la actual
        $inicio = max(1, $paginaActual - 2);
        $fin    = min($totalPaginas, $paginaActual + 2);
        ?>

        <?php if ($inicio > 1): ?>
            <a href="?pagina=1&periodo=<?= $periodo ?>&especialidad=<?= $especialidadFiltro ?>">1</a>
            <?php if ($inicio > 2): ?> ... <?php endif; ?>
        <?php endif; ?>

        <?php for ($i = $inicio; $i <= $fin; $i++): ?>
            <?php if ($i == $paginaActual): ?>
                <strong><?= $i ?></strong>
            <?php else: ?>
                <a href="?pagina=<?= $i ?>&periodo=<?= $periodo ?>&especialidad=<?= $especialidadFiltro ?>"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($fin < $totalPaginas): ?>
            <?php if ($fin < $totalPaginas - 1): ?> ... <?php endif; ?>
            <a href="?pagina=<?= $totalPaginas ?>&periodo=<?= $periodo ?>&especialidad=<?= $especialidadFiltro ?>"><?= $totalPaginas ?></a>
        <?php endif; ?>

        <?php if ($paginaActual < $totalPaginas): ?>
            <a href="?pagina=<?= $paginaActual + 1 ?>&periodo=<?= $periodo ?>&especialidad=<?= $especialidadFiltro ?>">Siguiente →</a>
        <?php endif; ?>
    </nav>

</body>

</html>