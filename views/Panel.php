<?php
define('SECCION', 'misTurnos');
require '../controllers/PacienteController.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mis turnos — MediTurnos</title>
</head>

<body>

    <h1>Mis turnos</h1>

    <!-- MENSAJES -->
    <?php
    $mensajes = [
        'exitoso' => ['color' => 'green', 'texto' => 'Turno cancelado correctamente.'],
        'error'   => ['color' => 'red',   'texto' => 'Hubo un error al cancelar el turno.'],
    ];
    $registro = $_GET['registro'] ?? null;
    if ($registro && isset($mensajes[$registro])): ?>
        <p style="color:<?= $mensajes[$registro]['color'] ?>"><?= $mensajes[$registro]['texto'] ?></p>
    <?php endif; ?>

    <?php require '../views/layout/menuPaciente.php'; ?>

    <!-- FILTROS -->
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
                    <th>Médico</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($turnos as $t): ?>
                    <tr>
                        <td><?= $t['id_turno'] ?></td>
                        <td><?= $t['fecha'] ?></td>
                        <td><?= $t['hora_inicio'] ?></td>
                        <td><?= $t['medico_nombre'] . ' ' . $t['medico_apellido'] ?></td>
                        <td><?= ucfirst($t['estado']) ?></td>
                        <td>
                            <?php if ($t['estado'] === 'pendiente' || $t['estado'] === 'confirmado'): ?>
                                <form method="POST" action="Panel.php">
                                    <input type="hidden" name="accion" value="cancelarTurno">
                                    <input type="hidden" name="id_turno" value="<?= $t['id_turno'] ?>">
                                    <button type="submit">Cancelar</button>
                                </form>
                            <?php else: ?>
                                —
                            <?php endif; ?>
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