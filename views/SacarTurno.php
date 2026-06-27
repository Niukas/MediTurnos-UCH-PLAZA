<?php
define('SECCION', 'sacarTurno');
require_once '../controllers/PacienteController.php';
require_once '../config/helpers.php';
$titulo = 'Agendar Cita — MediTurnos';

// Determinar qué pestaña mostrar por defecto
$tabInicial = (isset($_GET['matricula']) && empty($_GET['id_especialidad'])) ? 'medico' : 'especialidad';
?>
<!DOCTYPE html>
<html lang="es">

<?php require_once 'layout/head.php'; ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

<body class="bg-ghost font-sans text-charcoal min-h-screen flex flex-col">

    <?php require_once 'layout/menuPaciente.php'; ?>

    <main class="flex-grow p-4 sm:p-10 flex flex-col items-center justify-center animate-fadeIn">

        <div class="w-full max-w-2xl bg-white rounded-2xl border border-gray-200 p-6 sm:p-8 shadow-[0_4px_25px_rgba(0,0,0,0.02)]">

            <div class="text-center border-b border-gray-100 pb-5 mb-6 relative">
                <?php if (isset($_GET['reagendar']) && $_GET['reagendar'] == 'true'): ?>
                    <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-charcoal text-white text-[0.65rem] font-bold uppercase tracking-widest px-3 py-1 rounded-full shadow-md animate-pulse">
                        Reagendando cita
                    </span>
                <?php endif; ?>
                <h1 class="font-serif text-2xl sm:text-3xl text-charcoal tracking-tight mb-1">Agendar un Turno</h1>
                <p class="text-slate text-xs sm:text-sm">Completá los pasos secuenciales para confirmar tu cita médica.</p>
            </div>

            <?php
            $mensajes = [
                'exitoso' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'text' => 'text-emerald-700', 'texto' => 'Turno registrado correctamente.'],
                'error'   => ['bg' => 'bg-rose-50',   'border' => 'border-rose-200',   'text' => 'text-rose-700',   'texto' => 'Hubo un error al registrar el turno.'],
            ];
            $registro = $_GET['registro'] ?? null;
            if ($registro && isset($mensajes[$registro])): ?>
                <div class="<?= $mensajes[$registro]['bg'] ?> <?= $mensajes[$registro]['border'] ?> border rounded-xl px-4 py-3 mb-6 text-sm font-medium <?= $mensajes[$registro]['text'] ?> flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span><?= $mensajes[$registro]['texto'] ?></span>
                </div>
            <?php endif; ?>

            <div class="mb-6 relative z-50">
                <label class="block text-[0.65rem] font-bold text-slate uppercase tracking-widest mb-3">1. Criterio de Búsqueda</label>

                <div class="flex p-1 bg-[#F8FAFC] border border-gray-200 rounded-xl mb-4 w-full sm:w-max mx-auto sm:mx-0">
                    <button type="button" id="tab-especialidad" class="flex-1 px-5 py-2 text-xs font-bold rounded-lg transition-all <?= $tabInicial === 'especialidad' ? 'bg-white text-charcoal shadow-sm border border-gray-200' : 'text-slate hover:text-charcoal' ?>" onclick="cambiarTab('especialidad')">
                        Buscar Especialidad
                    </button>
                    <button type="button" id="tab-medico" class="flex-1 px-5 py-2 text-xs font-bold rounded-lg transition-all <?= $tabInicial === 'medico' ? 'bg-white text-charcoal shadow-sm border border-gray-200' : 'text-slate hover:text-charcoal' ?>" onclick="cambiarTab('medico')">
                        Buscar Profesional
                    </button>
                </div>

                <div id="content-especialidad" class="<?= $tabInicial === 'especialidad' ? 'block animate-fadeIn' : 'hidden' ?>">
                    <form method="GET" action="" id="form-especialidad" class="m-0">
                        <?php if (isset($_GET['reagendar'])): ?>
                            <input type="hidden" name="reagendar" value="true">
                        <?php endif; ?>
                        <select name="id_especialidad" class="tom-select-search">
                            <option value="">Escriba para buscar una especialidad...</option>
                            <?php foreach ($listadoEspecialidades as $e): ?>
                                <option value="<?= $e['id_especialidad'] ?>" <?= (isset($_GET['id_especialidad']) && $_GET['id_especialidad'] == $e['id_especialidad']) ? 'selected' : '' ?>>
                                    <?= h($e['nombre']) ?> (<?= $e['duracion_turno_min'] ?> min)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>

                <div id="content-medico" class="<?= $tabInicial === 'medico' ? 'block animate-fadeIn' : 'hidden' ?>">
                    <form method="GET" action="" id="form-medico" class="m-0">
                        <?php if (isset($_GET['reagendar'])): ?>
                            <input type="hidden" name="reagendar" value="true">
                        <?php endif; ?>
                        <select name="matricula" class="tom-select-search">
                            <option value="">Escriba el apellido del profesional...</option>
                            <?php foreach ($todosLosMedicos as $tm): ?>
                                <option value="<?= h($tm['matricula']) ?>" <?= (isset($_GET['matricula']) && $_GET['matricula'] == $tm['matricula'] && !isset($_GET['id_especialidad'])) ? 'selected' : '' ?>>
                                    Dr/a. <?= h($tm['nombre']) ?> <?= h($tm['apellido']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
            </div>

            <?php if (isset($_GET['matricula']) && !isset($_GET['id_especialidad']) && !empty($especialidadesDelMedico)): ?>
                <div class="mb-6 p-4 bg-ghost/30 rounded-xl border border-gray-100 animate-fadeIn relative z-40">
                    <label class="block text-[0.65rem] font-bold text-slate uppercase tracking-widest mb-3">2. Seleccione la rama médica a atender</label>
                    <form method="GET" action="" class="m-0 space-y-2.5">
                        <input type="hidden" name="matricula" value="<?= h($_GET['matricula']) ?>">
                        <?php if (isset($_GET['reagendar'])): ?>
                            <input type="hidden" name="reagendar" value="true">
                        <?php endif; ?>

                        <?php foreach ($especialidadesDelMedico as $espM): ?>
                            <div class="flex items-center justify-between p-3.5 bg-white border border-gray-200 hover:border-slate rounded-xl transition-all shadow-sm">
                                <div class="text-sm font-bold text-charcoal"><?= h($espM['nombre']) ?></div>
                                <button type="submit" name="id_especialidad" value="<?= h($espM['id_especialidad']) ?>"
                                    class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all bg-charcoal text-white hover:bg-slate">
                                    Confirmar Especialidad
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </form>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['id_especialidad']) && !empty($listadoMedicos)): ?>
                <div class="mb-5 p-4 bg-ghost/30 rounded-xl border border-gray-100 animate-fadeIn relative z-40">
                    <label class="block text-[0.65rem] font-bold text-slate uppercase tracking-widest mb-3">2. Profesional Asignado</label>
                    <form method="GET" action="" class="m-0 space-y-2.5">
                        <input type="hidden" name="id_especialidad" value="<?= h($_GET['id_especialidad']) ?>">
                        <?php if (isset($_GET['reagendar'])): ?>
                            <input type="hidden" name="reagendar" value="true">
                        <?php endif; ?>

                        <?php foreach ($listadoMedicos as $m):
                            $isSelected = isset($_GET['matricula']) && $_GET['matricula'] == $m['matricula'];
                        ?>
                            <div class="flex items-center justify-between p-3.5 bg-white border rounded-xl transition-all <?= $isSelected ? 'border-charcoal ring-1 ring-charcoal/10' : 'border-gray-200 hover:border-slate' ?>">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate text-white flex items-center justify-center font-serif text-sm shadow-sm">
                                        <?= substr(h($m['nombre']), 0, 1) . substr(h($m['apellido']), 0, 1) ?>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-charcoal">Dr/a. <?= h($m['nombre']) . ' ' . h($m['apellido']) ?></div>
                                        <div class="text-[0.7rem] text-slate font-mono">Mat. <?= h($m['matricula']) ?></div>
                                    </div>
                                </div>
                                <button type="submit" name="matricula" value="<?= h($m['matricula']) ?>"
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all <?= $isSelected ? 'bg-charcoal text-white shadow-sm cursor-default' : 'bg-white border border-gray-300 text-charcoal hover:bg-gray-50' ?>">
                                    <?= $isSelected ? 'Seleccionado' : 'Elegir' ?>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </form>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['matricula']) && isset($_GET['id_especialidad'])): ?>
                <div class="mb-6 p-4 bg-ghost/30 rounded-xl border border-gray-100 animate-fadeIn space-y-4">

                    <div>
                        <label class="block text-[0.65rem] font-bold text-slate uppercase tracking-widest mb-2">3. Fecha del Turno</label>
                        <form method="GET" action="" id="form-fecha-calendario" class="m-0">
                            <input type="hidden" name="id_especialidad" value="<?= h($_GET['id_especialidad']) ?>">
                            <input type="hidden" name="matricula" value="<?= h($_GET['matricula']) ?>">
                            <div class="relative">
                                <input type="text" id="calendario-input" name="fecha" value="<?= h($_GET['fecha'] ?? '') ?>" placeholder="Seleccione una fecha disponible en el calendario..." readonly
                                    class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm text-charcoal font-medium focus:outline-none focus:border-slate cursor-pointer transition-all shadow-sm">
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                        </form>
                    </div>

                    <?php if (isset($_GET['fecha']) && !empty($listadoHorarios)): ?>
                        <div class="border-t border-gray-200/60 pt-4 animate-fadeIn">
                            <label class="block text-[0.65rem] font-bold text-slate uppercase tracking-widest mb-3">4. Horarios Libres Disponibles</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <?php foreach ($listadoHorarios as $h):
                                    $isSlotSelected = isset($_GET['hora_inicio']) && $_GET['hora_inicio'] == $h['hora_inicio'];
                                ?>
                                    <form method="GET" action="" class="m-0">
                                        <input type="hidden" name="id_especialidad" value="<?= h($_GET['id_especialidad']) ?>">
                                        <input type="hidden" name="matricula" value="<?= h($_GET['matricula']) ?>">
                                        <input type="hidden" name="fecha" value="<?= h($_GET['fecha']) ?>">
                                        <input type="hidden" name="hora_inicio" value="<?= h($h['hora_inicio']) ?>">
                                        <input type="hidden" name="id_consultorio" value="<?= h($h['id_consultorio']) ?>">

                                        <button type="submit" class="w-full text-left flex items-center justify-between p-3 bg-white border rounded-xl transition-all <?= $isSlotSelected ? 'border-charcoal ring-1 ring-charcoal/10 bg-ghost/20 shadow-sm' : 'border-gray-200 hover:border-slate' ?>">
                                            <div>
                                                <div class="text-sm font-bold text-charcoal"><?= substr($h['hora_inicio'], 0, 5) ?> hs</div>
                                                <div class="text-[0.65rem] text-slate font-medium">Cons. <?= $h['consultorio_nro'] ?> · Piso <?= $h['piso'] ?></div>
                                            </div>
                                            <div class="w-5 h-5 rounded-full border flex items-center justify-center transition-colors <?= $isSlotSelected ? 'border-charcoal bg-charcoal text-white' : 'border-gray-300' ?>">
                                                <?php if ($isSlotSelected): ?>
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                <?php endif; ?>
                                            </div>
                                        </button>
                                    </form>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php elseif (isset($_GET['fecha'])): ?>
                        <div class="p-4 bg-gray-50 border border-gray-200 text-slate text-xs font-medium rounded-xl text-center italic">
                            El profesional no registra disponibilidad para esta fecha.
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['hora_inicio'])): ?>
                <div class="mt-4 p-5 border-2 border-charcoal/10 bg-ghost/20 rounded-xl animate-fadeIn">
                    <div class="flex items-center justify-between border-b border-gray-200 pb-3 mb-4 text-xs font-bold text-slate uppercase tracking-wider">
                        <span>Resumen Final</span>
                        <span class="text-charcoal font-mono bg-white px-2 py-1 rounded border border-gray-200 shadow-sm">
                            <?= date('d/m/Y', strtotime($_GET['fecha'])) ?> — <?= substr($_GET['hora_inicio'], 0, 5) ?> hs
                        </span>
                    </div>

                    <form method="POST" action="../controllers/PacienteController.php" class="space-y-4 m-0">
                        <input type="hidden" name="accion" value="crearTurno">
                        <input type="hidden" name="fecha" value="<?= h($_GET['fecha']) ?>">
                        <input type="hidden" name="hora_inicio" value="<?= h($_GET['hora_inicio']) ?>">
                        <input type="hidden" name="matricula" value="<?= h($_GET['matricula']) ?>">
                        <input type="hidden" name="id_especialidad" value="<?= h($_GET['id_especialidad']) ?>">
                        <input type="hidden" name="id_consultorio" value="<?= h($_GET['id_consultorio']) ?>">

                        <div>
                            <label class="block text-[0.65rem] font-bold text-slate uppercase tracking-widest mb-1.5">Cobertura Médica / Plan</label>
                            <div class="relative">
                                <select name="id_plan" required onchange="this.form.nro_afiliado.value = this.options[this.selectedIndex].dataset.afiliado"
                                    class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm text-charcoal font-medium focus:outline-none focus:border-slate cursor-pointer appearance-none shadow-sm">

                                    <option value="0" data-afiliado="PARTICULAR">Atención Particular (Abonar sin obra social)</option>

                                    <?php if (!empty($listadoPlanes)): ?>
                                        <optgroup label="Mis Planes Registrados">
                                            <?php foreach ($listadoPlanes as $pl): ?>
                                                <option value="<?= $pl['id_plan'] ?>" data-afiliado="<?= h($pl['nro_afiliado']) ?>">
                                                    <?= h($pl['obra_social']) ?> — <?= h($pl['nombre_plan']) ?> (Cobertura: <?= $pl['porcentaje_cobertura'] ?>%)
                                                </option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endif; ?>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="nro_afiliado" value="PARTICULAR">

                        <div>
                            <label class="block text-[0.65rem] font-bold text-slate uppercase tracking-widest mb-1.5">Síntomas u Observaciones (Opcional)</label>
                            <textarea name="observacion" rows="2" placeholder="Breve descripción del motivo del turno..."
                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm text-charcoal placeholder-gray-400 focus:outline-none focus:border-slate resize-none shadow-sm"></textarea>
                        </div>

                        <button type="submit" class="w-full bg-charcoal hover:bg-slate text-white font-bold py-3.5 rounded-xl text-sm transition-all shadow-md active:scale-[0.99]">
                            Confirmar y Agendar Turno Médico
                        </button>
                    </form>
                </div>
            <?php endif; ?>

        </div>
    </main>

    <script>
        // Lógica del Switch de Pestañas
        function cambiarTab(tab) {
            const btnEsp = document.getElementById('tab-especialidad');
            const btnMed = document.getElementById('tab-medico');
            const contEsp = document.getElementById('content-especialidad');
            const contMed = document.getElementById('content-medico');

            if (tab === 'especialidad') {
                btnEsp.className = "flex-1 px-5 py-2 text-xs font-bold rounded-lg transition-all bg-white text-charcoal shadow-sm border border-gray-200";
                btnMed.className = "flex-1 px-5 py-2 text-xs font-bold rounded-lg transition-all text-slate hover:text-charcoal";
                contEsp.classList.remove('hidden');
                contEsp.classList.add('block');
                contMed.classList.remove('block');
                contMed.classList.add('hidden');
            } else {
                btnMed.className = "flex-1 px-5 py-2 text-xs font-bold rounded-lg transition-all bg-white text-charcoal shadow-sm border border-gray-200";
                btnEsp.className = "flex-1 px-5 py-2 text-xs font-bold rounded-lg transition-all text-slate hover:text-charcoal";
                contMed.classList.remove('hidden');
                contMed.classList.add('block');
                contEsp.classList.remove('block');
                contEsp.classList.add('hidden');
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            // Inicializar Buscadores (TomSelect) solo para el Paso 1
            document.querySelectorAll('.tom-select-search').forEach(function(el) {
                new TomSelect(el, {
                    create: false,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    },
                    placeholder: el.getAttribute('placeholder'),
                    onChange: function(value) {
                        if (value) {
                            el.closest('form').submit();
                        }
                    }
                });
            });

            // Inicializar Calendario
            if (document.getElementById('calendario-input')) {
                flatpickr("#calendario-input", {
                    locale: "es",
                    minDate: "today",
                    dateFormat: "Y-m-d",
                    defaultDate: "<?= $_GET['fecha'] ?? '' ?>",
                    disable: [
                        function(date) {
                            return (date.getDay() === 0 || date.getDay() === 6);
                        }
                    ],
                    onChange: function(selectedDates, dateStr, instance) {
                        if (dateStr) {
                            document.getElementById('form-fecha-calendario').submit();
                        }
                    }
                });
            }
        });
    </script>

</body>

</html>