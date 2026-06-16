<?php
define('SECCION', 'misTurnos');
require '../controllers/PacienteController.php';
require '../config/helpers.php';
$titulo = 'Mis turnos — MediTurnos';
?>
<!DOCTYPE html>
<html lang="es">

<?php require 'layout/head.php'; ?>

<body class="bg-[#F8FAFC] font-sans text-charcoal min-h-screen flex flex-col">

    <?php require '../views/layout/menuPaciente.php'; ?>

    <main class="flex-grow p-6 sm:p-10 max-w-7xl mx-auto w-full animate-fadeIn">

        <div class="mb-8 md:mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-white border border-gray-200 text-slate text-[0.65rem] font-bold uppercase tracking-widest mb-3 shadow-sm">
                    <svg class="w-3.5 h-3.5 text-lightblue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Portal del Paciente
                </div>
                <h1 class="font-serif text-3xl md:text-4xl text-charcoal tracking-tight mb-1">Mis turnos</h1>
                <p class="text-slate text-sm">Gestioná tus citas médicas, revisá el historial y cancelá si es necesario.</p>
            </div>
        </div>

        <?php
        $mensajes = [
            'exitoso' => ['bg' => 'bg-green-500/10', 'border' => 'border-green-500/20', 'text' => 'text-green-700', 'texto' => 'Turno cancelado correctamente.', 'icon' => 'M5 13l4 4L19 7'],
            'error'   => ['bg' => 'bg-red-500/10',   'border' => 'border-red-500/20',   'text' => 'text-red-700',   'texto' => 'Hubo un error al cancelar el turno.', 'icon' => 'M6 18L18 6M6 6l12 12'],
        ];
        $registro = $_GET['registro'] ?? null;
        if ($registro && isset($mensajes[$registro])): ?>
            <div class="<?= $mensajes[$registro]['bg'] ?> <?= $mensajes[$registro]['border'] ?> border rounded-xl px-4 py-3 mb-8 text-sm font-medium <?= $mensajes[$registro]['text'] ?> flex items-center gap-3 shadow-sm">
                <div class="p-1 rounded-full bg-white/50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $mensajes[$registro]['icon'] ?>"></path>
                    </svg>
                </div>
                <span><?= $mensajes[$registro]['texto'] ?></span>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl border border-gray-200/80 p-4 mb-8 shadow-[0_4px_20px_rgba(0,0,0,0.02)] flex flex-col md:flex-row items-start md:items-center justify-between gap-4">

            <div class="flex items-center gap-3 w-full md:w-auto">
                <span class="text-[0.7rem] font-bold text-slate uppercase tracking-widest hidden sm:block">Período:</span>
                <div class="flex bg-[#F8FAFC] rounded-xl p-1 border border-gray-200/60 w-full sm:w-auto overflow-x-auto">
                    <?php
                    $periodos = ['dia' => 'Hoy', 'semana' => 'Semana', 'mes' => 'Mes', 'todos' => 'Todos'];
                    foreach ($periodos as $valor => $label):
                        $isActive = ($periodo === $valor);
                    ?>
                        <a href="?periodo=<?= $valor ?>&especialidad=<?= $especialidadFiltro ?>"
                            class="flex-1 sm:flex-none text-center px-4 py-1.5 text-xs font-semibold rounded-lg transition-all duration-200 <?= $isActive ? 'bg-white text-charcoal shadow-sm border border-gray-200/50' : 'text-slate hover:text-charcoal hover:bg-gray-50' ?>">
                            <?= $label ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="text-[0.8rem] text-slate font-medium px-2 md:px-0">
                <span class="bg-ghost px-2.5 py-1 rounded-md border border-gray-200 mr-1 text-charcoal font-bold"><?= $totalTurnos ?></span> turnos en total
                <span class="mx-2 text-gray-300">|</span>
                Pág <strong class="text-charcoal"><?= $paginaActual ?></strong> de <strong class="text-charcoal"><?= $totalPaginas ?></strong>
            </div>

        </div>

        <?php
        $turnosPorEspecialidad = [];
        foreach ($listadoTurnos as $t) {
            $turnosPorEspecialidad[$t['especialidad']][] = $t;
        }

        if (empty($turnosPorEspecialidad)): ?>
            <div class="bg-white rounded-2xl border border-gray-200/80 p-12 text-center shadow-[0_4px_20px_rgba(0,0,0,0.02)] flex flex-col items-center">
                <div class="w-16 h-16 bg-ghost rounded-full flex items-center justify-center mb-4 text-slate">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-charcoal mb-1">No hay turnos registrados</h3>
                <p class="text-slate text-sm">No encontramos citas para el período seleccionado.</p>
            </div>
        <?php else: ?>
            <div class="space-y-8">
                <?php foreach ($turnosPorEspecialidad as $nombreEspecialidad => $turnos): ?>
                    <div class="bg-white rounded-2xl border border-gray-200/80 overflow-hidden shadow-[0_4px_20px_rgba(0,0,0,0.02)]">

                        <div class="bg-[#F8FAFC] px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-lightblue">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                </div>
                                <h2 class="font-serif text-xl text-charcoal"><?= h($nombreEspecialidad) ?></h2>
                            </div>
                            <span class="bg-white border border-gray-200 text-charcoal text-[0.7rem] font-bold px-3 py-1 rounded-full shadow-sm">
                                <?= count($turnos) ?> <?= count($turnos) === 1 ? 'cita' : 'citas' ?>
                            </span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-gray-100">
                                        <th class="py-3.5 px-6 text-[0.65rem] font-bold text-slate uppercase tracking-widest bg-white">ID</th>
                                        <th class="py-3.5 px-6 text-[0.65rem] font-bold text-slate uppercase tracking-widest bg-white">Fecha / Hora</th>
                                        <th class="py-3.5 px-6 text-[0.65rem] font-bold text-slate uppercase tracking-widest bg-white">Profesional</th>
                                        <th class="py-3.5 px-6 text-[0.65rem] font-bold text-slate uppercase tracking-widest bg-white">Estado</th>
                                        <th class="py-3.5 px-6 text-[0.65rem] font-bold text-slate uppercase tracking-widest bg-white text-right">Gestión</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    <?php foreach ($turnos as $t): ?>
                                        <tr class="hover:bg-[#F8FAFC]/80 transition-colors group">
                                            <td class="py-4 px-6 text-xs text-slate/70 font-mono">#<?= $t['id_turno'] ?></td>

                                            <td class="py-4 px-6">
                                                <div class="text-sm text-charcoal font-bold"><?= date('d/m/Y', strtotime($t['fecha'])) ?></div>
                                                <div class="text-xs text-slate mt-0.5 flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <?= substr($t['hora_inicio'], 0, 5) ?> hs
                                                </div>
                                            </td>

                                            <td class="py-4 px-6">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-lightblue to-slate text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-sm">
                                                        <?= substr(h($t['medico_nombre']), 0, 1) . substr(h($t['medico_apellido']), 0, 1) ?>
                                                    </div>
                                                    <div class="text-sm font-medium text-charcoal">
                                                        Dr/a. <?= h($t['medico_nombre']) . ' ' . h($t['medico_apellido']) ?>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="py-4 px-6 text-sm">
                                                <?php
                                                $estadoConfig = match ($t['estado']) {
                                                    'pendiente'  => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'dot' => 'bg-amber-500'],
                                                    'confirmado' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'dot' => 'bg-emerald-500'],
                                                    'cancelado'  => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200', 'dot' => 'bg-rose-500'],
                                                    'realizado'  => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'dot' => 'bg-blue-500'],
                                                    default      => ['bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'border' => 'border-gray-200', 'dot' => 'bg-gray-500']
                                                };
                                                ?>
                                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border <?= $estadoConfig['bg'] ?> <?= $estadoConfig['border'] ?> <?= $estadoConfig['text'] ?>">
                                                    <span class="w-1.5 h-1.5 rounded-full <?= $estadoConfig['dot'] ?>"></span>
                                                    <span class="text-[0.7rem] font-bold uppercase tracking-wider"><?= ucfirst($t['estado']) ?></span>
                                                </div>
                                            </td>

                                            <td class="py-4 px-6 text-right">
                                                <div class="flex items-center justify-end gap-2">

                                                    <?php if (isset($t['estado_pago']) && $t['estado_pago'] === 'pendiente' && $t['estado'] !== 'cancelado'): ?>
                                                        <form method="POST" action="../controllers/PacienteController.php" class="inline-block m-0">
                                                            <input type="hidden" name="accion" value="prepararPago">
                                                            <input type="hidden" name="id_turno" value="<?= $t['id_turno'] ?>">
                                                            <button type="submit" class="bg-charcoal border border-charcoal text-white hover:bg-slate hover:border-slate px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-sm flex items-center gap-1.5 cursor-pointer">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                                                </svg>
                                                                Pagar
                                                            </button>
                                                        </form>

                                                    <?php elseif (isset($t['estado_pago']) && $t['estado_pago'] === 'pagado'): ?>
                                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-[0.65rem] font-bold text-green-700 bg-green-50 border border-green-200 uppercase tracking-widest">
                                                            Abonado
                                                        </span>
                                                    <?php endif; ?>

                                                    <?php if ($t['estado'] === 'pendiente' || $t['estado'] === 'confirmado'): ?>
                                                        <form method="POST" action="../controllers/PacienteController.php" class="inline-block m-0" onsubmit="return confirm('¿Estás seguro que querés cancelar este turno? Esta acción no se puede deshacer.')">
                                                            <input type="hidden" name="accion" value="cancelarTurno">
                                                            <input type="hidden" name="id_turno" value="<?= $t['id_turno'] ?>">
                                                            <button type="submit" class="bg-white border border-gray-200 text-slate hover:border-red-200 hover:bg-red-50 hover:text-red-600 px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-sm flex items-center gap-1.5 group-hover:shadow">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                </svg>
                                                                Cancelar
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>

                                                    <?php if (!($t['estado'] === 'pendiente' || $t['estado'] === 'confirmado') && (!isset($t['estado_pago']) || $t['estado_pago'] === 'pendiente')): ?>
                                                        <span class="text-gray-300 font-bold block text-center mr-2">—</span>
                                                    <?php endif; ?>

                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($totalPaginas > 1): ?>
            <div class="mt-10 flex justify-center">
                <nav class="inline-flex items-center gap-1.5 bg-white border border-gray-200/80 rounded-xl p-1.5 shadow-sm">

                    <?php if ($paginaActual > 1): ?>
                        <a href="?pagina=<?= $paginaActual - 1 ?>&periodo=<?= $periodo ?>&especialidad=<?= $especialidadFiltro ?>"
                            class="flex items-center justify-center px-3 py-1.5 text-xs font-bold text-slate hover:bg-ghost rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Ant
                        </a>
                    <?php endif; ?>

                    <?php
                    $inicio = max(1, $paginaActual - 2);
                    $fin    = min($totalPaginas, $paginaActual + 2);
                    ?>

                    <?php if ($inicio > 1): ?>
                        <a href="?pagina=1&periodo=<?= $periodo ?>&especialidad=<?= $especialidadFiltro ?>" class="w-8 h-8 flex items-center justify-center text-xs font-bold text-slate hover:bg-ghost rounded-lg transition-colors">1</a>
                        <?php if ($inicio > 2): ?> <span class="px-1 text-slate/50 text-xs font-bold">...</span> <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $inicio; $i <= $fin; $i++): ?>
                        <?php if ($i == $paginaActual): ?>
                            <span class="w-8 h-8 flex items-center justify-center text-xs font-bold bg-charcoal text-white rounded-lg shadow-sm"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?pagina=<?= $i ?>&periodo=<?= $periodo ?>&especialidad=<?= $especialidadFiltro ?>" class="w-8 h-8 flex items-center justify-center text-xs font-bold text-slate hover:bg-ghost rounded-lg transition-colors"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($fin < $totalPaginas): ?>
                        <?php if ($fin < $totalPaginas - 1): ?> <span class="px-1 text-slate/50 text-xs font-bold">...</span> <?php endif; ?>
                        <a href="?pagina=<?= $totalPaginas ?>&periodo=<?= $periodo ?>&especialidad=<?= $especialidadFiltro ?>" class="w-8 h-8 flex items-center justify-center text-xs font-bold text-slate hover:bg-ghost rounded-lg transition-colors"><?= $totalPaginas ?></a>
                    <?php endif; ?>

                    <?php if ($paginaActual < $totalPaginas): ?>
                        <a href="?pagina=<?= $paginaActual + 1 ?>&periodo=<?= $periodo ?>&especialidad=<?= $especialidadFiltro ?>"
                            class="flex items-center justify-center px-3 py-1.5 text-xs font-bold text-slate hover:bg-ghost rounded-lg transition-colors">
                            Sig
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    <?php endif; ?>

                </nav>
            </div>
        <?php endif; ?>

    </main>

</body>

</html>