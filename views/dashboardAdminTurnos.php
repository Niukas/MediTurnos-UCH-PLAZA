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
            <form method="GET" action="" class="m-0 flex flex-col sm:flex-row items-center gap-4">
                
                <!-- Search Input -->
                <div class="relative w-full sm:flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate/50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="search" name="q" placeholder="Buscar por paciente, médico..." value="<?= h($_GET['q'] ?? '') ?>"
                        class="w-full pl-10 pr-4 py-2 bg-[#F8FAFC] border border-gray-200/80 rounded-xl text-sm font-medium text-charcoal placeholder-gray-400 focus:outline-none focus:border-slate shadow-sm transition-colors">
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto">
                    <!-- Specialty Filter -->
                    <div class="relative w-full sm:w-48">
                        <select name="especialidad" class="w-full pl-4 pr-8 py-2 bg-[#F8FAFC] border border-gray-200/80 rounded-xl text-sm font-semibold text-charcoal focus:outline-none focus:border-slate appearance-none cursor-pointer transition-colors shadow-sm">
                            <option value="">Especialidad</option>
                            <?php foreach ($listadoEspecialidad as $e): ?>
                                <option value="<?= h($e['nombre']) ?>" <?= (($_GET['especialidad'] ?? '') == $e['nombre']) ? 'selected' : '' ?>>
                                    <?= h($e['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate/50"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></div>
                    </div>

                    <!-- Status Filter -->
                    <div class="relative w-full sm:w-48">
                        <select name="estado" class="w-full pl-4 pr-8 py-2 bg-[#F8FAFC] border border-gray-200/80 rounded-xl text-sm font-semibold text-charcoal focus:outline-none focus:border-slate appearance-none cursor-pointer transition-colors shadow-sm">
                            <option value="">Estado</option>
                            <?php foreach (['pendiente', 'confirmado', 'realizado', 'cancelado'] as $estado): ?>
                                <option value="<?= $estado ?>" <?= (($_GET['estado'] ?? '') == $estado) ? 'selected' : '' ?>><?= ucfirst($estado) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate/50"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></div>
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" class="w-full sm:w-auto bg-charcoal hover:bg-slate text-white font-bold py-2 px-6 rounded-xl text-sm transition-all duration-200 shadow-md hover:shadow-lg">
                        Filtrar
                    </button>
                    <?php
                    $filtrosAplicados = !empty($_GET['q']) || !empty($_GET['especialidad']) || !empty($_GET['estado']);
                    if ($filtrosAplicados):
                    ?>
                    <a href="dashboardAdminTurnos.php" class="w-full sm:w-auto text-center bg-white hover:bg-gray-100 text-slate font-bold py-2 px-6 rounded-xl text-sm transition-all duration-200 border border-gray-200 shadow-sm">
                        Limpiar
                    </a>
                    <?php endif; ?>
                </div>
            </form>
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
                                             <button type="button" 
                                                data-id-turno="<?= $t['id_turno'] ?>" 
                                                class="historial-btn bg-white border border-gray-200 text-slate hover:border-charcoal hover:text-charcoal hover:shadow-sm w-7 h-7 rounded-lg flex items-center justify-center transition-all" 
                                                title="Ver historial del turno">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
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

    <!-- Modal de Historial -->
    <div id="historial-modal" class="fixed inset-0 bg-black/30 backdrop-blur-sm z-[150] hidden items-center justify-center animate-fadeIn">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-lg border border-gray-200/80 p-6 m-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-serif text-xl text-charcoal tracking-tight">Historial del Turno <span id="modal-id-turno" class="font-mono text-lg"></span></h3>
                <button id="close-modal-btn" class="text-slate hover:text-charcoal transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div id="modal-body" class="space-y-4 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                <!-- Contenido dinámico del historial -->
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
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
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('La respuesta de la red no fue correcta.');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.error) {
                            throw new Error(data.error);
                        }
                        renderHistorial(data);
                    })
                    .catch(error => {
                        modalBody.innerHTML = `<p class="text-sm text-red-600 italic py-8 text-center">${error.message}</p>`;
                    });
            });
        });

        function renderHistorial(historial) {
            if (historial.length === 0) {
                modalBody.innerHTML = '<p class="text-sm text-slate italic py-8 text-center">No hay historial de cambios para este turno.</p>';
                return;
            }

            let content = '';
            historial.forEach(item => {
                const fecha = new Date(item.fecha_cambio).toLocaleString('es-AR', {
                    day: '2-digit', month: '2-digit', year: 'numeric',
                    hour: '2-digit', minute: '2-digit'
                });

                content += `
                    <div class="flex gap-3 text-xs">
                        <div class="w-8 h-8 flex-shrink-0 mt-1 rounded-full bg-ghost text-slate flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        </div>
                        <div>
                            <p class="font-bold text-charcoal">
                                Estado: <span class="font-mono uppercase">${item.estado_anterior || 'CREADO'}</span> → <span class="font-mono uppercase">${item.estado_nuevo}</span>
                            </p>
                            <p class="text-slate/80 mt-0.5">
                                ${fecha} hs
                            </p>
                        </div>
                    </div>`;
            });
            modalBody.innerHTML = content;
        }

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modalBody.innerHTML = '';
            modalIdTurno.textContent = '';
        }

        closeModalBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal();
            }
        });
    });
    </script>
</body>

</html>