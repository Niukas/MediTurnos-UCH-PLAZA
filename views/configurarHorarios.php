<?php
define('SECCION', 'configurarHorarios');
require_once '../controllers/MedicoController.php';
require_once 'layout/menuMedico.php';
require_once '../config/helpers.php';
$titulo = 'Configurar Agenda — MediTurnos';
?>
<!DOCTYPE html>
<html lang="es">

<?php require_once 'layout/head.php'; ?>

<body class="bg-[#F8FAFC] font-sans text-charcoal min-h-screen flex flex-col">

    <main class="flex-grow p-6 sm:p-10 max-w-5xl mx-auto w-full animate-fadeIn">

        <div class="mb-8">
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-white border border-gray-200 text-slate text-[0.65rem] font-bold uppercase tracking-widest mb-3 shadow-sm">
                <svg class="w-3.5 h-3.5 text-slate" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Gestión de Agenda
            </div>
            <h1 class="font-serif text-3xl md:text-4xl text-charcoal tracking-tight mb-1">Configuración de Horarios</h1>
            <p class="text-slate text-sm">Definí los días, horas y consultorios en los que vas a atender a tus pacientes.</p>
        </div>

        <?php
        $mensajes = [
            'agregado'          => ['bg' => 'bg-emerald-500/10', 'border' => 'border-emerald-500/20', 'text' => 'text-emerald-700', 'texto' => 'Nuevo bloque horario agregado a tu agenda.', 'icon' => 'M5 13l4 4L19 7'],
            'eliminado'         => ['bg' => 'bg-amber-500/10',   'border' => 'border-amber-500/20',   'text' => 'text-amber-700',   'texto' => 'Bloque horario eliminado correctamente.', 'icon' => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'],
            'error'             => ['bg' => 'bg-rose-500/10',    'border' => 'border-rose-500/20',    'text' => 'text-rose-700',    'texto' => 'Hubo un error al guardar los cambios.', 'icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            'error_hora'        => ['bg' => 'bg-rose-500/10',    'border' => 'border-rose-500/20',    'text' => 'text-rose-700',    'texto' => 'La hora de salida debe ser posterior a la hora de ingreso.', 'icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            'error_superpuesto' => ['bg' => 'bg-amber-500/10',   'border' => 'border-amber-500/20',   'text' => 'text-amber-800',   'texto' => '¡Atención! La franja horaria que intentás cargar choca con un horario que ya tenés configurado ese día.', 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
        ];
        $registro = $_GET['registro'] ?? null;
        if ($registro && isset($mensajes[$registro])): ?>
            <div class="<?= $mensajes[$registro]['bg'] ?> <?= $mensajes[$registro]['border'] ?> border rounded-xl px-4 py-3 mb-6 text-sm font-medium <?= $mensajes[$registro]['text'] ?> flex items-center gap-3 shadow-sm">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $mensajes[$registro]['icon'] ?>"></path>
                </svg>
                <span><?= $mensajes[$registro]['texto'] ?></span>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl border border-gray-200/80 overflow-hidden shadow-sm mb-8">
            <div class="bg-ghost/60 px-6 py-4 border-b border-gray-100">
                <h2 class="font-serif text-lg text-charcoal tracking-tight">Agregar Nuevo Horario</h2>
            </div>

            <form method="POST" action="../controllers/MedicoController.php" class="p-6">
                <input type="hidden" name="accion" value="agregarHorario">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                    <div>
                        <label class="block text-[0.65rem] font-bold text-slate uppercase mb-1.5 tracking-wider">Especialidad</label>
                        <select name="id_especialidad" required class="w-full px-3 py-2.5 bg-ghost border border-gray-200 rounded-xl text-sm font-semibold text-charcoal focus:outline-none focus:border-slate appearance-none cursor-pointer">
                            <option value="">Seleccioná una especialidad</option>
                            <?php foreach ($misEspecialidades as $esp): ?>
                                <option value="<?= $esp['id_especialidad'] ?>"><?= h($esp['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[0.65rem] font-bold text-slate uppercase mb-1.5 tracking-wider">Día de Atención</label>
                        <select name="dia_semana" required class="w-full px-3 py-2.5 bg-ghost border border-gray-200 rounded-xl text-sm font-semibold text-charcoal focus:outline-none focus:border-slate appearance-none cursor-pointer">
                            <option value="">Seleccioná un día</option>
                            <option value="Lunes">Lunes</option>
                            <option value="Martes">Martes</option>
                            <option value="Miercoles">Miércoles</option>
                            <option value="Jueves">Jueves</option>
                            <option value="Viernes">Viernes</option>
                            <option value="Sabado">Sábado</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[0.65rem] font-bold text-slate uppercase mb-1.5 tracking-wider">Consultorio / Piso</label>
                        <select name="id_consultorio" required class="w-full px-3 py-2.5 bg-ghost border border-gray-200 rounded-xl text-sm font-semibold text-charcoal focus:outline-none focus:border-slate appearance-none cursor-pointer">
                            <option value="">Seleccioná un consultorio</option>
                            <?php foreach ($listadoConsultorios as $cons): ?>
                                <option value="<?= $cons['id_consultorio'] ?>">Consultorio <?= $cons['numero'] ?> (Piso <?= $cons['piso'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[0.65rem] font-bold text-slate uppercase mb-1.5 tracking-wider">Hora de Ingreso</label>
                        <input type="text" name="hora_inicio" required placeholder="00:00"
                            class="flatpickr-time w-full px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-charcoal focus:outline-none focus:border-slate shadow-sm">
                    </div>

                    <div>
                        <label class="block text-[0.65rem] font-bold text-slate uppercase mb-1.5 tracking-wider">Hora de Salida</label>
                        <input type="text" name="hora_fin" required placeholder="00:00"
                            class="flatpickr-time w-full px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-charcoal focus:outline-none focus:border-slate shadow-sm">
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-charcoal hover:bg-slate text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Agregar a la agenda
                        </button>
                    </div>

                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm relative mb-10">
            <div class="bg-ghost/60 px-6 py-4 border-b border-gray-100 flex items-center justify-between rounded-t-2xl">
                <h2 class="font-serif text-lg text-charcoal tracking-tight">Tu Agenda Base</h2>
                <span class="text-[0.7rem] text-slate font-bold uppercase tracking-wider">
                    <?= count($misHorarios) ?> bloque<?= count($misHorarios) != 1 ? 's' : '' ?> configurado<?= count($misHorarios) != 1 ? 's' : '' ?>
                </span>
            </div>

            <?php if (empty($misHorarios)): ?>
                <div class="p-12 text-center flex flex-col items-center">
                    <div class="w-16 h-16 bg-ghost rounded-full flex items-center justify-center mb-4 text-slate">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-charcoal mb-1">Aún no configuraste tus horarios</h3>
                    <p class="text-slate text-sm">Comenzá agregando los días que vas a estar disponible para que los pacientes puedan solicitar turnos.</p>
                </div>
            <?php else: ?>
                <div class="w-full overflow-visible rounded-b-2xl">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="py-3 px-6 text-[0.65rem] font-bold text-slate uppercase tracking-widest bg-white">Día</th>
                                <th class="py-3 px-6 text-[0.65rem] font-bold text-slate uppercase tracking-widest bg-white">Rango Horario</th>
                                <th class="py-3 px-6 text-[0.65rem] font-bold text-slate uppercase tracking-widest bg-white">Especialidad</th>
                                <th class="py-3 px-6 text-[0.65rem] font-bold text-slate uppercase tracking-widest bg-white">Consultorio</th>
                                <th class="py-3 px-6 text-[0.65rem] font-bold text-slate uppercase tracking-widest bg-white text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 bg-white rounded-b-2xl">
                            <?php foreach ($misHorarios as $h): ?>
                                <tr class="hover:bg-ghost/30 transition-colors">

                                    <td class="py-4 px-6 align-middle font-bold text-sm text-charcoal">
                                        <?= h($h['dia_semana']) ?>
                                    </td>

                                    <td class="py-4 px-6 align-middle">
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-ghost border border-gray-200 text-xs font-mono text-charcoal">
                                            <svg class="w-3.5 h-3.5 text-slate" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <?= substr($h['hora_inicio'], 0, 5) ?> a <?= substr($h['hora_fin'], 0, 5) ?> hs
                                        </div>
                                    </td>

                                    <td class="py-4 px-6 align-middle">
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-ghost border border-gray-200 text-[0.65rem] font-bold text-slate uppercase tracking-wider whitespace-nowrap">
                                            <span class="w-1.5 h-1.5 rounded-full bg-lightblue block"></span>
                                            <?= h($h['especialidad']) ?>
                                        </span>
                                    </td>

                                    <td class="py-4 px-6 align-middle text-sm text-slate">
                                        N° <?= h($h['consultorio_nro']) ?>
                                    </td>

                                    <td class="py-4 px-6 align-middle text-right shrink-0">
                                        <form method="POST" action="../controllers/MedicoController.php" class="m-0 flex justify-end"
                                            onsubmit="return confirm('¿Estás seguro de que deseas eliminar este horario? Los turnos ya asignados en esta franja no se verán afectados, pero los pacientes ya no podrán sacar turnos nuevos.')">
                                            <input type="hidden" name="accion" value="eliminarHorario">
                                            <input type="hidden" name="id_horario" value="<?= $h['id_horario'] ?>">
                                            <button type="submit" class="bg-white border border-gray-200 text-rose-500 hover:border-rose-300 hover:text-rose-700 px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-sm flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Quitar
                                            </button>
                                        </form>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof flatpickr !== 'undefined') {
                flatpickr(".flatpickr-time", {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true,
                    minuteIncrement: 15 // Saltos de a 15 minutos (opcional, muy util para turnos)
                });
            } else {
                console.error("Flatpickr no está cargado. Asegurate de que los CDN estén en layout/head.php");
            }
        });
    </script>

</body>

</html>