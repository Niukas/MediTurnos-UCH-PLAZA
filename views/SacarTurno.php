<?php 
define('SECCION', 'sacarTurno');
require '../controllers/PacienteController.php';
require 'layout/menuPaciente.php'; 
require '../config/helpers.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Sacar turno — MediTurnos</title>
</head>

<body>

    <h1>Sacar turno</h1>

    <!-- MENSAJES -->
    <?php
    $mensajes = [
        'exitoso' => ['color' => 'green', 'texto' => 'Turno registrado correctamente.'],
        'error'   => ['color' => 'red',   'texto' => 'Hubo un error al registrar el turno.'],
    ];
    $registro = $_GET['registro'] ?? null;
    if ($registro && isset($mensajes[$registro])): ?>
        <p style="color:<?= $mensajes[$registro]['color'] ?>"><?= $mensajes[$registro]['texto'] ?></p>
    <?php endif; ?>

    <!-- PASO 1 — ELEGIR ESPECIALIDAD -->
    <h2>Paso 1 — Elegí una especialidad</h2>
    <form method="GET" action="">
        <select name="id_especialidad" onchange="this.form.submit()">
            <option value="">Seleccioná una especialidad</option>
            <?php foreach ($listadoEspecialidades as $e): ?>
                <option value="<?= $e['id_especialidad'] ?>"
                    <?= isset($_GET['id_especialidad']) && $_GET['id_especialidad'] == $e['id_especialidad'] ? 'selected' : '' ?>>
                    <?= h($e['nombre']) ?> (<?= $e['duracion_turno_min'] ?> min)
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <!-- PASO 2 — ELEGIR MÉDICO -->
    <?php if (!empty($listadoMedicos)): ?>
        <h2>Paso 2 — Elegí un médico</h2>
        <form method="GET" action="">
            <input type="hidden" name="id_especialidad" value="<?= $_GET['id_especialidad'] ?>">
            <table border="1">
                <thead>
                    <tr>
                        <th>Médico</th>
                        <th>Seleccionar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($listadoMedicos as $m): ?>
                        <tr>
                            <td><?= h($m['nombre']) . ' ' . h($m['apellido']) ?></td>
                            <td>
                                <button type="submit" name="matricula" value="<?= $m['matricula'] ?>">Elegir</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </form>
    <?php endif; ?>

    <!-- PASO 3 — ELEGIR FECHA Y HORARIO -->
    <?php if (isset($_GET['matricula'])): ?>
        <h2>Paso 3 — Elegí una fecha y horario</h2>
        <form method="GET" action="">
            <input type="hidden" name="id_especialidad" value="<?= $_GET['id_especialidad'] ?>">
            <input type="hidden" name="matricula" value="<?= $_GET['matricula'] ?>">
            <input type="date" name="fecha" value="<?= $_GET['fecha'] ?? '' ?>"
                min="<?= date('Y-m-d') ?>" onchange="this.form.submit()">
        </form>

        <?php if (!empty($listadoHorarios)): ?>
            <table border="1">
                <thead>
                    <tr>
                        <th>Hora</th>
                        <th>Consultorio</th>
                        <th>Seleccionar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($listadoHorarios as $h): ?>
                        <tr>
                            <td><?= $h['hora_inicio'] ?></td>
                            <td>Nº <?= $h['consultorio_nro'] ?> · Piso <?= $h['piso'] ?></td>
                            <td>
                                <form method="GET" action="">
                                    <input type="hidden" name="id_especialidad" value="<?= $_GET['id_especialidad'] ?>">
                                    <input type="hidden" name="matricula" value="<?= $_GET['matricula'] ?>">
                                    <input type="hidden" name="fecha" value="<?= $_GET['fecha'] ?>">
                                    <input type="hidden" name="hora_inicio" value="<?= $h['hora_inicio'] ?>">
                                    <input type="hidden" name="id_consultorio" value="<?= $h['id_consultorio'] ?>">
                                    <button type="submit">Elegir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif (isset($_GET['fecha'])): ?>
            <p>No hay horarios disponibles para esa fecha.</p>
        <?php endif; ?>
    <?php endif; ?>

    <!-- PASO 4 — CONFIRMAR TURNO -->
    <?php if (isset($_GET['hora_inicio'])): ?>
        <h2>Paso 4 — Confirmá tu turno</h2>
        <form method="POST" action="SacarTurno.php">
            <input type="hidden" name="accion" value="crearTurno">
            <input type="hidden" name="fecha" value="<?= $_GET['fecha'] ?>">
            <input type="hidden" name="hora_inicio" value="<?= $_GET['hora_inicio'] ?>">
            <input type="hidden" name="matricula" value="<?= $_GET['matricula'] ?>">
            <input type="hidden" name="id_especialidad" value="<?= $_GET['id_especialidad'] ?>">
            <input type="hidden" name="id_consultorio" value="<?= $_GET['id_consultorio'] ?>">

            <p><strong>Fecha:</strong> <?= $_GET['fecha'] ?></p>
            <p><strong>Hora:</strong> <?= $_GET['hora_inicio'] ?></p>

            <label>Obra social y plan:</label>
            <select name="id_plan" onchange="
        this.form.nro_afiliado.value =
        this.options[this.selectedIndex].dataset.afiliado">
                <option value="">Seleccioná un plan</option>
                <?php foreach ($listadoPlanes as $pl): ?>
                    <option value="<?= $pl['id_plan'] ?>"
                        data-afiliado="<?= h($pl['nro_afiliado']) ?>">
                        <?= h($pl['obra_social']) ?> — <?= h($pl['nombre_plan']) ?>
                        (<?= $pl['porcentaje_cobertura'] ?>% cobertura)
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="hidden" name="nro_afiliado" value="">

            <label>Observación (opcional):</label>
            <input type="text" name="observacion" placeholder="Motivo de consulta...">

            <button type="submit">Confirmar turno</button>
        </form>
    <?php endif; ?>

</body>

</html>