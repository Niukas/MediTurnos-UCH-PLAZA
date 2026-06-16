<?php
define('SECCION', 'buscarPaciente');
require_once '../controllers/MedicoController.php';
require_once 'layout/menuMedico.php';
require_once '../config/helpers.php';
$titulo = 'Buscar Paciente — MediTurnos';
?>
<!DOCTYPE html>
<html lang="es">

<?php require_once 'layout/head.php'; ?>

<body class="bg-[#F8FAFC] font-sans text-charcoal min-h-screen flex flex-col">

    <main class="flex-grow p-6 sm:p-10 max-w-5xl mx-auto w-full">

        <div class="mb-8">
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-white border border-gray-200 text-slate text-[0.65rem] font-bold uppercase tracking-widest mb-3 shadow-sm">
                <svg class="w-3.5 h-3.5 text-slate" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Búsqueda de Pacientes
            </div>
            <h1 class="font-serif text-3xl md:text-4xl text-charcoal tracking-tight mb-1">Buscar Paciente</h1>
            <p class="text-slate text-sm">Buscá por nombre, apellido o DNI para encontrar un paciente registrado.</p>
        </div>

        <?php
        $mensajes = [
            'error'   => ['bg' => 'bg-rose-500/10',    'border' => 'border-rose-500/20',    'text' => 'text-rose-700',    'texto' => 'Hubo un error al actualizar los datos.',         'icon' => 'M6 18L18 6M6 6l12 12'],
            'exitoso' => ['bg' => 'bg-emerald-500/10', 'border' => 'border-emerald-500/20', 'text' => 'text-emerald-700', 'texto' => 'Ficha médica actualizada con éxito.',            'icon' => 'M5 13l4 4L19 7'],
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

        <form method="GET" action="" class="mb-8">
            <div class="flex gap-3">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="busqueda" value="<?= h($_GET['busqueda'] ?? '') ?>"
                        placeholder="Nombre, apellido o DNI del paciente..."
                        class="w-full pl-11 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-sm text-charcoal placeholder-gray-400 focus:outline-none focus:border-slate focus:ring-2 focus:ring-slate/10 transition-all shadow-sm">
                </div>
                <button type="submit"
                    class="bg-charcoal hover:bg-slate text-white px-6 py-3 rounded-xl text-sm font-bold transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Buscar
                </button>
            </div>
        </form>

        <?php if (isset($_GET['busqueda'])): ?>

            <?php if (empty($resultados)): ?>
                <div class="bg-white rounded-2xl border border-gray-200/80 p-12 text-center shadow-sm flex flex-col items-center">
                    <div class="w-16 h-16 bg-ghost rounded-full flex items-center justify-center mb-4 text-slate">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-charcoal mb-1">Sin resultados</h3>
                    <p class="text-slate text-sm">No se encontraron pacientes con "<strong><?= h($_GET['busqueda']) ?></strong>".</p>
                </div>

            <?php else: ?>

                <?php if (!isset($_GET['id_paciente'])): ?>
                    <div class="bg-white rounded-2xl border border-gray-200/80 overflow-hidden shadow-sm mb-6">
                        <div class="bg-[#F8FAFC] px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h2 class="font-serif text-lg text-charcoal tracking-tight">Resultados</h2>
                            <span class="text-[0.7rem] text-slate font-bold uppercase tracking-wider">
                                <?= count($resultados) ?> paciente<?= count($resultados) > 1 ? 's' : '' ?> encontrado<?= count($resultados) > 1 ? 's' : '' ?>
                            </span>
                        </div>

                        <div class="divide-y divide-gray-50">
                            <?php foreach ($resultados as $p):
                                $isSelected = isset($_GET['id_paciente']) && $_GET['id_paciente'] == $p['id_paciente'];
                            ?>
                                <div class="px-6 py-4 <?= $isSelected ? 'bg-ghost/50' : 'hover:bg-ghost/30' ?> transition-colors">

                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-charcoal to-slate text-white flex items-center justify-center font-bold text-sm shadow-sm shrink-0">
                                                <?= substr(h($p['nombre']), 0, 1) . substr(h($p['apellido']), 0, 1) ?>
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-charcoal"><?= h($p['nombre']) . ' ' . h($p['apellido']) ?></div>
                                                <div class="flex items-center gap-3 mt-0.5 flex-wrap">
                                                    <span class="text-xs text-slate font-mono">DNI <?= h($p['dni']) ?></span>
                                                    <?php if (!empty($p['email'])): ?>
                                                        <span class="text-xs text-slate">· <?= h($p['email']) ?></span>
                                                    <?php endif; ?>
                                                    <?php if (!empty($p['telefono'])): ?>
                                                        <span class="text-xs text-slate">· <?= h($p['telefono']) ?></span>
                                                    <?php endif; ?>
                                                    <span class="text-[0.65rem] font-bold text-slate uppercase tracking-wider bg-ghost border border-gray-200 px-2 py-0.5 rounded-md">
                                                        Nac. <?= date('d/m/Y', strtotime($p['fecha_nac'])) ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2 shrink-0">
                                            <a href="?busqueda=<?= h($_GET['busqueda']) ?>&id_paciente=<?= $p['id_paciente'] ?>"
                                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all <?= $isSelected ? 'bg-charcoal text-white shadow-sm' : 'bg-white border border-gray-200 text-slate hover:border-charcoal hover:text-charcoal' ?>">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                <?= $isSelected ? 'Viendo turnos' : 'Ver turnos' ?>
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="mb-6">
                        <a href="?busqueda=<?= h($_GET['busqueda']) ?>" class="inline-flex items-center gap-2 text-sm font-bold text-slate hover:text-charcoal transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver a los resultados de "<?= h($_GET['busqueda']) ?>"
                        </a>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['id_paciente'])): ?>
                    <?php if (!empty($turnosPaciente)):
                        $turnosPorEspecialidad = [];
                        foreach ($turnosPaciente as $t) {
                            $turnosPorEspecialidad[$t['especialidad']][] = $t;
                        }
                    ?>
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
                                                                <form method="POST" action="../controllers/MedicoController.php" class="m-0 bg-white border border-gray-200 rounded-xl p-3 shadow-sm focus-within:border-lightblue focus-within:ring-2 focus-within:ring-lightblue/20 transition-all">
                                                                    <input type="hidden" name="accion" value="cambiarEstado">
                                                                    <input type="hidden" name="origen" value="buscar">
                                                                    <input type="hidden" name="id_turno" value="<?= $t['id_turno'] ?>">
                                                                    <input type="hidden" name="id_paciente" value="<?= h($_GET['id_paciente']) ?>">
                                                                    <input type="hidden" name="busqueda" value="<?= h($_GET['busqueda']) ?>">

                                                                    <textarea name="observacion" rows="2" placeholder="Escriba sus observaciones clínicas, síntomas o evolución del paciente..."
                                                                        class="auto-resize-textarea w-full text-sm text-charcoal placeholder-gray-400 border-none focus:ring-0 resize-none bg-transparent mb-3 p-1 outline-none"
                                                                        oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"><?= htmlspecialchars($t['observacion'] ?? '') ?></textarea>

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
                    <?php else: ?>
                        <div class="bg-white rounded-2xl border border-gray-200/80 p-8 text-center shadow-sm">
                            <p class="text-slate text-sm">Este paciente no tiene turnos registrados.</p>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

            <?php endif; ?>

        <?php else: ?>
            <div class="bg-white rounded-2xl border border-gray-200/80 p-12 text-center shadow-sm">
                <div class="w-16 h-16 bg-ghost rounded-full flex items-center justify-center mb-4 text-slate mx-auto">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-charcoal mb-1">Ingresá un término de búsqueda</h3>
                <p class="text-slate text-sm">Podés buscar por nombre, apellido o número de DNI.</p>
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