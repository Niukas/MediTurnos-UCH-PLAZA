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

        <div class="bg-white rounded-2xl border border-gray-200/80 p-5 mb-8 shadow-sm">
            <form id="filter-form" method="GET" action="" class="m-0 flex flex-col gap-4">
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    <div class="relative w-full sm:flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate/50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="search" name="q" placeholder="Buscar por médico..." value="<?= h($_GET['q'] ?? '') ?>"
                            class="w-full pl-10 pr-4 py-2.5 bg-[#F8FAFC] border border-gray-200/80 rounded-xl text-sm font-medium text-charcoal placeholder-gray-400 focus:outline-none focus:border-slate shadow-sm transition-colors">
                    </div>

                    <div class="relative w-full sm:w-64">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate/50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <input type="text" id="calendario" name="fecha" value="<?= h($_GET['fecha'] ?? '') ?>" placeholder="Seleccionar un rango..." 
                            class="w-full pl-10 pr-4 py-2.5 bg-[#F8FAFC] border border-gray-200/80 rounded-xl text-sm font-medium text-charcoal placeholder-gray-400 focus:outline-none focus:border-slate shadow-sm transition-colors cursor-pointer">
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    <div class="relative w-full sm:w-auto flex-1">
                        <select name="especialidad" class="w-full pl-4 pr-8 py-2.5 bg-[#F8FAFC] border border-gray-200/80 rounded-xl text-sm font-semibold text-charcoal focus:outline-none focus:border-slate appearance-none cursor-pointer transition-colors shadow-sm">
                            <option value="">Todas las especialidades</option>
                            <?php foreach ($listadoEspecialidades as $e): ?>
                                <option value="<?= h($e['nombre']) ?>" <?= (($_GET['especialidad'] ?? '') == $e['nombre']) ? 'selected' : '' ?>><?= h($e['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate/50"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></div>
                    </div>

                    <div class="relative w-full sm:w-auto flex-1">
                        <select name="estado" class="w-full pl-4 pr-8 py-2.5 bg-[#F8FAFC] border border-gray-200/80 rounded-xl text-sm font-semibold text-charcoal focus:outline-none focus:border-slate appearance-none cursor-pointer transition-colors shadow-sm">
                            <option value="">Todos los estados</option>
                            <?php foreach (['pendiente', 'confirmado', 'realizado', 'cancelado'] as $estado): ?>
                                <option value="<?= $estado ?>" <?= (($_GET['estado'] ?? '') == $estado) ? 'selected' : '' ?>><?= ucfirst($estado) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate/50"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></div>
                    </div>
                </div>

                <div class="flex items-center gap-4 pt-4 border-t border-gray-100">
                    <div class="flex-1 flex items-center gap-3">
                        <span class="text-[0.7rem] font-bold text-slate uppercase tracking-widest hidden sm:block">Períodos:</span>
                        <div class="flex bg-[#F8FAFC] rounded-xl p-1 border border-gray-200/60 w-full sm:w-auto overflow-x-auto">
                            <?php
                            $periodos = ['dia' => 'Hoy', 'semana' => 'Semana', 'mes' => 'Mes', 'todos' => 'Todos'];
                            foreach ($periodos as $valor => $label):
                                $periodoActual = $_GET['periodo'] ?? 'todos';
                                $isDateFilterActive = !empty($_GET['fecha']);
                                $isActive = !$isDateFilterActive && ($periodoActual === $valor);
                                $queryParams = http_build_query(array_merge(array_filter($_GET, fn($k) => $k !== 'fecha', ARRAY_FILTER_USE_KEY), ['periodo' => $valor, 'pagina' => 1]));
                            ?>
                                <a href="?<?= $queryParams ?>" class="flex-1 sm:flex-none text-center px-4 py-1.5 text-xs font-semibold rounded-lg transition-all duration-200 <?= $isActive ? 'bg-charcoal text-white shadow-sm' : 'text-slate hover:text-charcoal hover:bg-white' ?>"><?= $label ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <button type="submit" class="w-full sm:w-auto bg-charcoal hover:bg-slate text-white font-bold py-2.5 px-6 rounded-xl text-sm transition-all duration-200 shadow-md hover:shadow-lg">Filtrar</button>
                    <?php if (!empty($_GET['q']) || !empty($_GET['estado']) || !empty($_GET['especialidad']) || !empty($_GET['fecha'])): ?>
                    <a href="Panel.php" class="w-full sm:w-auto text-center bg-white hover:bg-gray-100 text-slate font-bold py-2.5 px-6 rounded-xl text-sm transition-all duration-200 border border-gray-200 shadow-sm">Limpiar</a>
                    <?php endif; ?>
                </div>
            </form>
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
                                            <td class="py-4 px-6 text-xs text-slate/70 font-mono align-top pt-5">#<?= $t['id_turno'] ?></td>

                                            <td class="py-4 px-6 align-top pt-5">
                                                <div class="text-sm text-charcoal font-bold"><?= date('d/m/Y', strtotime($t['fecha'])) ?></div>
                                                <div class="text-xs text-slate mt-0.5 flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <?= substr($t['hora_inicio'], 0, 5) ?> hs
                                                </div>
                                            </td>

                                            <td class="py-4 px-6 align-top pt-5">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-lightblue to-slate text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-sm">
                                                        <?= substr(h($t['medico_nombre']), 0, 1) . substr(h($t['medico_apellido']), 0, 1) ?>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-medium text-charcoal">
                                                            Dr/a. <?= h($t['medico_nombre']) . ' ' . h($t['medico_apellido']) ?>
                                                        </div>
                                                        <?php if (!empty($t['observacion']) && $t['estado'] === 'cancelado'): ?>
                                                            <div class="text-[0.65rem] text-rose-600 mt-1 max-w-[200px] leading-relaxed italic">
                                                                "<?= h($t['observacion']) ?>"
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="py-4 px-6 text-sm align-top pt-5">
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

                                            <td class="py-4 px-6 text-right align-top pt-5">
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
                                                         <button type="button" 
                                                            data-id-turno="<?= $t['id_turno'] ?>" 
                                                            class="historial-btn bg-white border border-gray-200 text-slate hover:border-charcoal hover:text-charcoal hover:shadow-sm w-8 h-8 rounded-lg flex items-center justify-center transition-all" 
                                                            title="Ver historial del turno">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                                        </button>
                                                     <?php elseif ($t['estado'] === 'cancelado'): ?>
                                                         <a href="SacarTurno.php?id_especialidad=<?= h($t['id_especialidad']) ?>&matricula=<?= h($t['matricula']) ?>&reagendar=true"
                                                             class="bg-white border border-gray-200 text-charcoal hover:border-charcoal hover:bg-ghost px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-sm flex items-center gap-1.5">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                            </svg>
                                                            Reagendar
                                                        </a>

                                                    <?php else: ?>
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
                        <a href="?<?= http_build_query(array_merge($_GET, ['pagina' => $paginaActual - 1])) ?>"
                            class="flex items-center justify-center px-3 py-1.5 text-xs font-bold text-slate hover:bg-ghost rounded-lg transition-colors">
                            Ant
                        </a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['pagina' => $i])) ?>" 
                           class="w-8 h-8 flex items-center justify-center text-xs font-bold rounded-lg transition-colors <?= $i == $paginaActual ? 'bg-charcoal text-white shadow-sm' : 'text-slate hover:bg-ghost' ?>">
                           <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($paginaActual < $totalPaginas): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['pagina' => $paginaActual + 1])) ?>"
                            class="flex items-center justify-center px-3 py-1.5 text-xs font-bold text-slate hover:bg-ghost rounded-lg transition-colors">
                            Sig
                        </a>
                    <?php endif; ?>
                </nav>
            </div>
        <?php endif; ?>

    </main>
    
    <!-- ADDED SCRIPT and MODAL -->
    <!-- Modal de Historial -->
    <div id="historial-modal" class="fixed inset-0 bg-black/30 backdrop-blur-sm z-[150] hidden items-center justify-center animate-fadeIn">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-lg border border-gray-200/80 p-6 m-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-serif text-xl text-charcoal tracking-tight">Historial del Turno <span id="modal-id-turno" class="font-mono text-lg"></span></h3>
                <button id="close-modal-btn" class="text-slate hover:text-charcoal transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div id="modal-body" class="space-y-4 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar"></div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        flatpickr("#calendario", {
            mode: "range",
            dateFormat: "Y-m-d",
            locale: "es",
            onChange: function(selectedDates, dateStr, instance) {
                const url = new URL(window.location);
                if (url.searchParams.has('periodo')) {
                    url.searchParams.delete('periodo');
                }
            }
        });

        const modal = document.getElementById('historial-modal');
        const closeModalBtn = document.getElementById('close-modal-btn');
        const modalBody = document.getElementById('modal-body');
        const modalIdTurno = document.getElementById('modal-id-turno');
        const historialBtns = document.querySelectorAll('.historial-btn');

        historialBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const idTurno = e.currentTarget.dataset.idTurno;
                modalIdTurno.textContent = `#${idTurno}`;
                modalBody.innerHTML = '<p class="text-sm text-slate italic py-8 text-center">Cargando historial...</p>';
                modal.classList.remove('hidden');
                modal.classList.add('flex');

                fetch(`../api/historial_turno.php?id_turno=${idTurno}`)
                    .then(response => response.ok ? response.json() : Promise.reject('Error de red'))
                    .then(data => {
                        if (data.error) return Promise.reject(data.error);
                        renderHistorial(data);
                    })
                    .catch(error => {
                        modalBody.innerHTML = `<p class="text-sm text-red-600 italic py-8 text-center">${error}</p>`;
                    });
            });
        });

        function renderHistorial(historial) {
            if (historial.length === 0) {
                modalBody.innerHTML = '<p class="text-sm text-slate italic py-8 text-center">No hay historial de cambios para este turno.</p>';
                return;
            }

            let content = historial.map(item => {
                const fecha = new Date(item.fecha_cambio).toLocaleString('es-AR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                return `
                    <div class="flex gap-3 text-xs py-2 border-b border-gray-50 last:border-0">
                        <div class="w-8 h-8 flex-shrink-0 mt-1 rounded-full bg-ghost text-slate flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        </div>
                        <div>
                            <p class="font-bold text-charcoal">
                                Estado: <span class="font-mono uppercase">${item.estado_anterior || 'CREADO'}</span> → <span class="font-mono uppercase">${item.estado_nuevo}</span>
                            </p>
                            <p class="text-slate/80 mt-0.5">${fecha} hs</p>
                        </div>
                    </div>`;
            }).join('');
            modalBody.innerHTML = content;
        }
        
        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        closeModalBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });
    });
    </script>
</body>

</html>