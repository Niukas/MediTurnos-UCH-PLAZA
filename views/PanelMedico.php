<?php
define('SECCION', 'misTurnos');
require '../controllers/MedicoController.php';
require 'layout/menuMedico.php';
require '../config/helpers.php';
$titulo = 'Mis turnos — MediTurnos';
?>
<!DOCTYPE html>
<html lang="es">

<?php require 'layout/head.php'; ?>

<body class="bg-[#F8FAFC] font-sans text-charcoal min-h-screen flex flex-col">

    <main class="flex-grow p-6 sm:p-10 max-w-7xl mx-auto w-full">

        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-white border border-gray-200 text-slate text-[0.65rem] font-bold uppercase tracking-widest mb-3 shadow-sm">
                    <svg class="w-3.5 h-3.5 text-lightblue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    Portal del Profesional
                </div>
                <h1 class="font-serif text-3xl md:text-4xl text-charcoal tracking-tight mb-1">Agenda Médica</h1>
                <p class="text-slate text-sm">Gestioná tus citas, actualizá diagnósticos y estados de atención.</p>
            </div>
        </div>

        <?php
        $mensajes = [
            'exitoso' => ['bg' => 'bg-emerald-500/10', 'border' => 'border-emerald-500/20', 'text' => 'text-emerald-700', 'texto' => 'Turno actualizado correctamente.', 'icon' => 'M5 13l4 4L19 7'],
            'error'   => ['bg' => 'bg-rose-500/10',   'border' => 'border-rose-500/20',   'text' => 'text-rose-700',   'texto' => 'Hubo un error al actualizar el turno.', 'icon' => 'M6 18L18 6M6 6l12 12'],
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

        <div class="bg-white rounded-2xl border border-gray-200/80 p-4 mb-8 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4 relative z-10">
            <div class="flex items-center gap-3 w-full md:w-auto">
                <span class="text-[0.7rem] font-bold text-slate uppercase tracking-widest hidden sm:block">Período de vista:</span>
                <div class="flex bg-[#F8FAFC] rounded-xl p-1 border border-gray-200/60 w-full sm:w-auto overflow-x-auto">
                    <?php
                    $periodos = ['dia' => 'Hoy', 'semana' => 'Semana', 'mes' => 'Mes', 'todos' => 'Histórico'];
                    foreach ($periodos as $valor => $label):
                        $isActive = ($periodo === $valor);
                    ?>
                        <a href="?periodo=<?= $valor ?>"
                            class="flex-1 sm:flex-none text-center px-4 py-1.5 text-xs font-semibold rounded-lg transition-all duration-200 <?= $isActive ? 'bg-charcoal text-white shadow-sm' : 'text-slate hover:text-charcoal hover:bg-white' ?>">
                            <?= $label ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="text-[0.8rem] text-slate font-medium px-2 md:px-0">
                <span class="bg-ghost px-2.5 py-1 rounded-md border border-gray-200 mr-1 text-charcoal font-bold"><?= $totalTurnos ?></span> citas agendadas
            </div>
        </div>

        <?php
        $turnosPorEspecialidad = [];
        foreach ($listadoTurnos as $t) {
            $turnosPorEspecialidad[$t['especialidad']][] = $t;
        }

        if (empty($turnosPorEspecialidad)): ?>
            <div class="bg-white rounded-2xl border border-gray-200/80 p-12 text-center shadow-sm flex flex-col items-center">
                <div class="w-16 h-16 bg-ghost rounded-full flex items-center justify-center mb-4 text-slate">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-charcoal mb-1">Agenda liberada</h3>
                <p class="text-slate text-sm">No tenés turnos programados para el período seleccionado.</p>
            </div>
        <?php else: ?>
            <div class="space-y-8">
                <?php foreach ($turnosPorEspecialidad as $nombreEspecialidad => $turnos): ?>
                    <div class="bg-white rounded-2xl border border-gray-200/80 overflow-hidden shadow-sm">

                        <div class="bg-[#F8FAFC] px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h2 class="font-serif text-xl text-charcoal flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-lightblue block"></span>
                                <?= h($nombreEspecialidad) ?>
                            </h2>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-gray-100">
                                        <th class="py-3.5 px-6 text-[0.65rem] font-bold text-slate uppercase tracking-widest bg-white">Info Cita</th>
                                        <th class="py-3.5 px-6 text-[0.65rem] font-bold text-slate uppercase tracking-widest bg-white">Paciente</th>
                                        <th class="py-3.5 px-6 text-[0.65rem] font-bold text-slate uppercase tracking-widest bg-white">Diagnóstico / Evolución</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php foreach ($turnos as $t): ?>
                                        <tr class="hover:bg-[#F8FAFC]/50 transition-colors">

                                            <td class="py-5 px-6 align-top w-48">
                                                <div class="text-sm font-bold text-charcoal mb-1"><?= date('d/m/Y', strtotime($t['fecha'])) ?></div>
                                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-ghost border border-gray-200 text-xs font-mono text-charcoal mb-2">
                                                    <svg class="w-3.5 h-3.5 text-slate" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <?= substr($t['hora_inicio'], 0, 5) ?> hs
                                                </div>
                                                <div>
                                                    <?php
                                                    $estadoConfig = match ($t['estado']) {
                                                        'pendiente'  => 'bg-amber-50 text-amber-700 border-amber-200',
                                                        'confirmado' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                        'cancelado'  => 'bg-rose-50 text-rose-700 border-rose-200',
                                                        'realizado'  => 'bg-blue-50 text-blue-700 border-blue-200',
                                                        default      => 'bg-gray-50 text-gray-700 border-gray-200'
                                                    };
                                                    ?>
                                                    <span class="inline-block px-2 py-0.5 text-[0.65rem] font-bold uppercase tracking-wider rounded border <?= $estadoConfig ?>">
                                                        <?= ucfirst($t['estado']) ?>
                                                    </span>
                                                </div>
                                            </td>

                                            <td class="py-5 px-6 align-top w-64">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-full bg-slate text-white flex items-center justify-center font-bold text-xs uppercase shadow-sm shrink-0">
                                                        <?= substr(h($t['paciente_nombre']), 0, 1) . substr(h($t['paciente_apellido']), 0, 1) ?>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-bold text-charcoal">
                                                            <?= h($t['paciente_nombre']) . ' ' . h($t['paciente_apellido']) ?>
                                                        </div>
                                                        <div class="text-xs text-slate mt-0.5">ID Ref: #<?= $t['id_turno'] ?></div>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="py-5 px-6 align-top">
                                                <?php if ($t['estado'] !== 'cancelado'): ?>
                                                    <form method="POST" action="panelMedico.php" class="m-0 bg-white border border-gray-200 rounded-xl p-3 shadow-sm focus-within:border-lightblue focus-within:ring-2 focus-within:ring-lightblue/20 transition-all">
                                                        <input type="hidden" name="accion" value="cambiarEstado">
                                                        <input type="hidden" name="id_turno" value="<?= $t['id_turno'] ?>">

                                                        <textarea name="observacion" rows="2" placeholder="Escriba sus observaciones clínicas, síntomas o evolución del paciente..."
                                                            class="auto-resize-textarea w-full text-sm text-charcoal placeholder-gray-400 border-none focus:ring-0 resize-none bg-transparent mb-3 p-1 outline-none"
                                                            oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"><?= htmlspecialchars($t['observacion']) ?></textarea>

                                                        <div class="flex items-center gap-3 pt-3 border-t border-gray-100">
                                                            <div class="relative flex-1 max-w-[200px]">
                                                                <select name="estado" class="w-full pl-3 pr-8 py-2 bg-ghost/50 border border-gray-200 rounded-lg text-xs font-semibold text-charcoal focus:outline-none focus:border-slate appearance-none cursor-pointer transition-colors">
                                                                    <?php foreach (['pendiente', 'confirmado', 'realizado'] as $e): ?>
                                                                        <option value="<?= $e ?>" <?= $t['estado'] === $e ? 'selected' : '' ?>>
                                                                            Marcar como <?= ucfirst($e) ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                                <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none text-slate">
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                                    </svg>
                                                                </div>
                                                            </div>

                                                            <button type="submit" class="bg-charcoal hover:bg-slate text-white px-4 py-2 rounded-lg text-xs font-bold transition-all shadow-sm hover:shadow-md ml-auto flex items-center gap-1.5">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                                </svg>
                                                                Guardar Ficha
                                                            </button>
                                                        </div>
                                                    </form>
                                                <?php else: ?>
                                                    <div class="bg-rose-50 border border-rose-100 rounded-xl p-4 text-sm text-rose-700 flex items-center gap-2">
                                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        Este turno fue cancelado y su ficha clínica ha sido bloqueada.
                                                    </div>
                                                <?php endif; ?>
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
                        <a href="?pagina=<?= $paginaActual - 1 ?>&periodo=<?= $periodo ?>" class="flex items-center justify-center px-3 py-1.5 text-xs font-bold text-slate hover:bg-ghost rounded-lg transition-colors">Ant</a>
                    <?php endif; ?>

                    <?php
                    $inicio = max(1, $paginaActual - 2);
                    $fin    = min($totalPaginas, $paginaActual + 2);
                    if ($inicio > 1): ?>
                        <a href="?pagina=1&periodo=<?= $periodo ?>" class="w-8 h-8 flex items-center justify-center text-xs font-bold text-slate hover:bg-ghost rounded-lg transition-colors">1</a>
                        <?php if ($inicio > 2): ?> <span class="px-1 text-slate/50 text-xs font-bold">...</span> <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $inicio; $i <= $fin; $i++): ?>
                        <?php if ($i == $paginaActual): ?>
                            <span class="w-8 h-8 flex items-center justify-center text-xs font-bold bg-charcoal text-white rounded-lg shadow-sm"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?pagina=<?= $i ?>&periodo=<?= $periodo ?>" class="w-8 h-8 flex items-center justify-center text-xs font-bold text-slate hover:bg-ghost rounded-lg transition-colors"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($fin < $totalPaginas): ?>
                        <?php if ($fin < $totalPaginas - 1): ?> <span class="px-1 text-slate/50 text-xs font-bold">...</span> <?php endif; ?>
                        <a href="?pagina=<?= $totalPaginas ?>&periodo=<?= $periodo ?>" class="w-8 h-8 flex items-center justify-center text-xs font-bold text-slate hover:bg-ghost rounded-lg transition-colors"><?= $totalPaginas ?></a>
                    <?php endif; ?>

                    <?php if ($paginaActual < $totalPaginas): ?>
                        <a href="?pagina=<?= $paginaActual + 1 ?>&periodo=<?= $periodo ?>" class="flex items-center justify-center px-3 py-1.5 text-xs font-bold text-slate hover:bg-ghost rounded-lg transition-colors">Sig</a>
                    <?php endif; ?>
                </nav>
            </div>
        <?php endif; ?>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tx = document.querySelectorAll('.auto-resize-textarea');
            for (let i = 0; i < tx.length; i++) {
                tx[i].setAttribute('style', 'height:' + (tx[i].scrollHeight) + 'px;overflow-y:hidden;');
                tx[i].addEventListener("input", OnInput, false);
            }

            function OnInput() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            }
        });
    </script>
</body>

</html>