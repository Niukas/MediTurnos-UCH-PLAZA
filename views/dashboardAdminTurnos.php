<?php
define('SECCION', 'turnos');
require_once '../controllers/AdminController.php';
require_once 'layout/menuAdmin.php';
require_once '../config/helpers.php';
$titulo = 'Gestión de Turnos — MediTurnos';
?>
<!DOCTYPE html>
<html lang="es">

<?php require_once 'layout/head.php'; ?>

<body class="bg-[#F8FAFC] font-sans text-charcoal min-h-screen flex flex-col">

    <main class="flex-grow p-6 sm:p-10 max-w-7xl mx-auto w-full">

        <div class="mb-8">
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-white border border-gray-200 text-slate text-[0.65rem] font-bold uppercase tracking-widest mb-3 shadow-sm">
                <svg class="w-3.5 h-3.5 text-slate" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Reserva General
            </div>
            <h1 class="font-serif text-3xl md:text-4xl text-charcoal tracking-tight mb-1">Control de Turnos</h1>
            <p class="text-slate text-sm">Historial de citas médicas, cancelaciones y monitoreo de la agenda global.</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/80 p-5 mb-8 shadow-sm">
            <form method="GET" action="" class="m-0 flex flex-wrap items-center gap-4">
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <span class="text-[0.65rem] font-bold text-slate uppercase tracking-widest hidden md:block">Especialidad:</span>
                    <div class="relative w-full sm:w-64">
                        <select name="especialidad" onchange="this.form.submit()"
                            class="w-full pl-4 pr-8 py-2 bg-[#F8FAFC] border border-gray-200/80 rounded-xl text-sm font-semibold text-charcoal focus:outline-none focus:border-slate appearance-none cursor-pointer transition-colors shadow-sm">
                            <option value="">Todas las especialidades</option>
                            <?php
                            // Verificamos que el listado de especialidades exista antes del loop
                            if (!empty($listadoEspecialidad)):
                                foreach ($listadoEspecialidad as $e):
                                    // Adaptá si tu controlador filtra por ID (ej: $e['id_especialidad']) o por nombre
                                    $valorFiltro = $e['nombre'] ?? $e['id_especialidad'];
                                    $estaSeleccionado = (isset($_GET['especialidad']) && $_GET['especialidad'] == $valorFiltro) ? 'selected' : '';
                            ?>
                                    <option value="<?= h($valorFiltro) ?>" <?= $estaSeleccionado ?>>
                                        <?= h($e['nombre']) ?>
                                    </option>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/80 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 bg-[#F8FAFC]">
                            <th class="py-4 px-6 text-[0.65rem] font-bold text-slate uppercase tracking-widest">Fecha y Hora</th>
                            <th class="py-4 px-6 text-[0.65rem] font-bold text-slate uppercase tracking-widest">Paciente</th>
                            <th class="py-4 px-6 text-[0.65rem] font-bold text-slate uppercase tracking-widest">Médico</th>
                            <th class="py-4 px-6 text-[0.65rem] font-bold text-slate uppercase tracking-widest">Especialidad</th>
                            <th class="py-4 px-6 text-[0.65rem] font-bold text-slate uppercase tracking-widest text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 bg-white">
                        <?php
                        if (!isset($listadoTurnos) || empty($listadoTurnos)):
                        ?>
                            <tr>
                                <td colspan="5" class="py-10 px-6 text-center text-sm text-slate italic font-medium">No hay turnos agendados con los criterios seleccionados.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($listadoTurnos as $t): ?>
                                <tr class="hover:bg-ghost/30 transition-colors group">

                                    <td class="py-4 px-6 align-middle">
                                        <div class="text-sm font-bold text-charcoal"><?= date('d/m/Y', strtotime($t['fecha'])) ?></div>
                                        <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-ghost border border-gray-200 text-[0.7rem] font-mono text-slate mt-1">
                                            <svg class="w-3 h-3 text-slate" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <?= isset($t['hora_inicio']) ? substr($t['hora_inicio'], 0, 5) : '00:00' ?> hs
                                        </div>
                                    </td>

                                    <td class="py-4 px-6 align-middle">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full bg-slate/10 text-slate flex items-center justify-center font-bold text-[0.65rem] uppercase">
                                                <?= substr(h($t['paciente_nombre'] ?? 'P'), 0, 1) . substr(h($t['paciente_apellido'] ?? 'A'), 0, 1) ?>
                                            </div>
                                            <span class="text-sm font-bold text-charcoal"><?= h(($t['paciente_nombre'] ?? '') . ' ' . ($t['paciente_apellido'] ?? '')) ?></span>
                                        </div>
                                    </td>

                                    <td class="py-4 px-6 text-sm text-slate font-medium align-middle">
                                        Dr/a. <?= h(($t['medico_nombre'] ?? '') . ' ' . ($t['medico_apellido'] ?? '')) ?>
                                    </td>

                                    <td class="py-4 px-6 align-middle">
                                        <span class="bg-ghost border border-gray-200 text-charcoal text-[0.65rem] font-bold px-2 py-0.5 rounded uppercase tracking-wider">
                                            <?= h($t['especialidad'] ?? 'General') ?>
                                        </span>
                                    </td>

                                    <td class="py-4 px-6 align-middle text-right">
                                        <form method="POST" action="dashboardAdminTurnos.php" class="m-0 flex items-center justify-end gap-2">
                                            <input type="hidden" name="accion" value="cambiarEstado">
                                            <input type="hidden" name="id_turno" value="<?= $t['id_turno'] ?>">

                                            <?php
                                            $estadoActual = $t['estado'] ?? 'pendiente';
                                            $selectColors = match ($estadoActual) {
                                                'pendiente'  => 'bg-amber-50 text-amber-700 border-amber-200 focus:ring-amber-500/20',
                                                'confirmado' => 'bg-emerald-50 text-emerald-700 border-emerald-200 focus:ring-emerald-500/20',
                                                'cancelado'  => 'bg-rose-50 text-rose-700 border-rose-200 focus:ring-rose-500/20',
                                                'realizado'  => 'bg-blue-50 text-blue-700 border-blue-200 focus:ring-blue-500/20',
                                                default      => 'bg-gray-50 text-gray-700 border-gray-200 focus:ring-gray-500/20'
                                            };
                                            ?>

                                            <div class="relative">
                                                <select name="estado" class="pl-3 pr-8 py-1.5 rounded-lg text-[0.7rem] font-bold uppercase tracking-wider border <?= $selectColors ?> outline-none focus:ring-2 appearance-none cursor-pointer transition-all shadow-sm">
                                                    <?php foreach (['pendiente', 'confirmado', 'cancelado', 'realizado'] as $e): ?>
                                                        <option value="<?= $e ?>" <?= $estadoActual === $e ? 'selected' : '' ?> class="bg-white text-charcoal normal-case tracking-normal">
                                                            <?= ucfirst($e) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none opacity-50 text-slate">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </div>
                                            </div>

                                            <button type="submit" class="bg-white border border-gray-200 text-slate hover:border-charcoal hover:text-charcoal hover:shadow-sm w-7 h-7 rounded-lg flex items-center justify-center transition-all" title="Guardar estado">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

</body>

</html>