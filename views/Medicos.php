<?php
require '../controllers/MedicosPublicController.php';
require '../config/helpers.php';
$titulo = 'Médicos — MediTurnos';
?>
<!DOCTYPE html>
<html lang="es">

<?php require 'layout/head.php'; ?>

<body class="bg-[#F8FAFC] font-sans text-charcoal min-h-screen flex flex-col">

    <nav class="bg-white/90 backdrop-blur-md border-b border-lightblue/30 px-6 sm:px-10 h-20 flex items-center justify-between sticky top-0 z-[100] transition-all">
        <div class="flex items-center gap-2 font-serif text-charcoal text-2xl">
            <span class="text-lightblue">✚</span> MediTurnos
        </div>
        <div class="flex items-center gap-4 sm:gap-6">
            <a href="../public/index.php" class="text-[0.8rem] font-bold text-slate uppercase tracking-widest hover:text-charcoal transition-colors hidden sm:inline-block">
                ← Volver al inicio
            </a>
            <a href="Login.php" class="bg-charcoal hover:bg-slate text-white px-5 py-2.5 rounded-lg font-sans text-[0.85rem] font-bold tracking-wide transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                Iniciar sesión
            </a>
        </div>
    </nav>

    <main class="flex-grow p-6 sm:p-10 max-w-7xl mx-auto w-full animate-fadeIn">

        <div class="mb-10 text-center sm:text-left">
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-white border border-gray-200 text-slate text-[0.65rem] font-bold uppercase tracking-widest mb-3 shadow-sm">
                <svg class="w-3.5 h-3.5 text-lightblue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Directorio Público
            </div>
            <h1 class="font-serif text-3xl md:text-4xl text-charcoal tracking-tight mb-2">Nuestros Especialistas</h1>
            <p class="text-slate text-sm">Conocé a los profesionales que forman parte de nuestro equipo de salud.</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/80 p-5 mb-8 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <form method="GET" action="" class="w-full sm:w-auto m-0 flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <span class="text-[0.65rem] font-bold text-slate uppercase tracking-widest whitespace-nowrap">Filtrar por rama:</span>
                <div class="relative w-full sm:w-72">
                    <select name="especialidad" onchange="this.form.submit()"
                        class="w-full pl-4 pr-8 py-2.5 bg-[#F8FAFC] border border-gray-200/80 rounded-xl text-sm font-semibold text-charcoal focus:outline-none focus:border-slate appearance-none cursor-pointer transition-colors">
                        <option value="">Todas las especialidades</option>
                        <?php foreach ($listadoEspecialidad as $e):
                            $isSelected = (isset($_GET['especialidad']) && $_GET['especialidad'] == $e['id_especialidad']) ? 'selected' : '';
                        ?>
                            <option value="<?= h($e['id_especialidad']) ?>" <?= $isSelected ?>>
                                <?= h($e['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
            </form>

            <div class="text-[0.8rem] text-slate font-medium hidden sm:block bg-ghost px-4 py-2 rounded-xl border border-gray-100">
                Mostrando <strong class="text-charcoal"><?= count($listadoMedicos) ?></strong> profesionales
            </div>
        </div>

        <?php if (empty($listadoMedicos)): ?>
            <div class="bg-white rounded-2xl border border-gray-200/80 p-12 text-center shadow-sm flex flex-col items-center">
                <div class="w-16 h-16 bg-ghost rounded-full flex items-center justify-center mb-4 text-slate">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-charcoal mb-1">No se encontraron resultados</h3>
                <p class="text-slate text-sm">No hay médicos registrados para la especialidad seleccionada.</p>
                <a href="Medicos.php" class="mt-4 bg-white border border-gray-200 text-charcoal hover:bg-ghost px-5 py-2 rounded-lg text-xs font-bold transition-all shadow-sm">Ver todos los médicos</a>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-2xl border border-gray-200/80 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 bg-[#F8FAFC]">
                                <th class="py-4 px-6 text-[0.65rem] font-bold text-slate uppercase tracking-widest">Profesional</th>
                                <th class="py-4 px-6 text-[0.65rem] font-bold text-slate uppercase tracking-widest w-1/4">Especialidades</th>
                                <th class="py-4 px-6 text-[0.65rem] font-bold text-slate uppercase tracking-widest">Horarios de Atención</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 bg-white">
                            <?php foreach ($listadoMedicos as $m): ?>
                                <tr class="hover:bg-ghost/30 transition-colors">

                                    <td class="py-5 px-6 align-top sm:align-middle">
                                        <div class="flex items-center gap-4 w-max">
                                            <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-charcoal to-slate text-white flex items-center justify-center font-serif text-xl flex-shrink-0 shadow-sm">
                                                <?= substr(h($m['nombre']), 0, 1) . substr(h($m['apellido']), 0, 1) ?>
                                            </div>
                                            <div>
                                                <div class="text-[0.95rem] font-bold text-charcoal">Dr/a. <?= h($m['nombre']) . ' ' . h($m['apellido']) ?></div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="py-5 px-6 align-top sm:align-middle">
                                        <div class="flex flex-wrap gap-1.5">
                                            <?php
                                            $especialidadesArray = explode(',', $m['especialidades']);
                                            foreach ($especialidadesArray as $esp):
                                                $esp = trim($esp);
                                                if (!empty($esp)):
                                            ?>
                                                    <span class="bg-[#F8FAFC] border border-gray-200 text-charcoal text-[0.65rem] font-bold px-2 py-0.5 rounded uppercase tracking-wider">
                                                        <?= h($esp) ?>
                                                    </span>
                                            <?php
                                                endif;
                                            endforeach;
                                            ?>
                                        </div>
                                    </td>

                                    <td class="py-5 px-6 align-top sm:align-middle">
                                        <?php if (empty(trim($m['horarios']))): ?>
                                            <span class="italic text-gray-400 text-xs font-medium">Consultar disponibilidad en el sistema</span>
                                        <?php else: ?>
                                            <div class="flex flex-wrap gap-2 max-w-lg">
                                                <?php
                                                // Separamos por el pipe "|"
                                                $horariosList = explode('|', $m['horarios']);
                                                foreach ($horariosList as $h):
                                                    $h = trim($h);
                                                    if (!empty($h)):
                                                        // Limpiamos los segundos (ej: 14:00:00 -> 14:00) usando Regex
                                                        $h = preg_replace('/(?<=\d{2}:\d{2}):00/', '', $h);
                                                ?>
                                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-ghost border border-gray-200 text-[0.7rem] font-medium text-slate">
                                                            <svg class="w-3 h-3 text-lightblue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            <?= h($h) ?>
                                                        </span>
                                                <?php
                                                    endif;
                                                endforeach;
                                                ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-8 text-center sm:hidden">
                <a href="../public/index.php" class="text-[0.7rem] font-bold text-slate uppercase tracking-widest hover:text-charcoal transition-colors">
                    ← Volver al inicio
                </a>
            </div>
        <?php endif; ?>

    </main>

</body>

</html>