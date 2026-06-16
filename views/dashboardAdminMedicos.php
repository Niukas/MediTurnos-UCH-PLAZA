<?php
define('SECCION', 'medicos');
require '../controllers/AdminController.php';
require 'layout/menuAdmin.php';
require '../config/helpers.php';
$titulo = 'Gestión de Médicos — MediTurnos';
?>
<!DOCTYPE html>
<html lang="es">

<?php require 'layout/head.php'; ?>

<body class="bg-[#F8FAFC] font-sans text-charcoal min-h-screen flex flex-col">

    <main class="flex-grow p-6 sm:p-10 max-w-7xl mx-auto w-full">

        <div class="mb-8">
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-white border border-gray-200 text-slate text-[0.65rem] font-bold uppercase tracking-widest mb-3 shadow-sm">
                <svg class="w-3.5 h-3.5 text-slate" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Gestión de Recursos Humanos
            </div>
            <h1 class="font-serif text-3xl md:text-4xl text-charcoal tracking-tight mb-1">Directorio Médico</h1>
            <p class="text-slate text-sm">Administrá el cuerpo de profesionales, registrá nuevas altas y modificá información de contacto.</p>
        </div>

        <?php
        $mensajes = [
            'creado'    => ['bg' => 'bg-emerald-500/10', 'border' => 'border-emerald-500/20', 'text' => 'text-emerald-700', 'texto' => 'Médico incorporado al directorio correctamente.', 'icon' => 'M5 13l4 4L19 7'],
            'editado'   => ['bg' => 'bg-blue-500/10',    'border' => 'border-blue-500/20',    'text' => 'text-blue-700',    'texto' => 'Información del profesional actualizada con éxito.', 'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
            'eliminado' => ['bg' => 'bg-amber-500/10',   'border' => 'border-amber-500/20',   'text' => 'text-amber-700',   'texto' => 'El médico ha sido dado de baja del sistema.', 'icon' => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'],
            'error'     => ['bg' => 'bg-rose-500/10',    'border' => 'border-rose-500/20',    'text' => 'text-rose-700',    'texto' => 'Hubo un error al procesar la solicitud.', 'icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
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

        <div class="bg-white rounded-2xl border border-gray-200/80 p-6 sm:p-8 mb-8 shadow-[0_4px_20px_rgba(0,0,0,0.01)] relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-gradient-to-b from-charcoal to-slate"></div>

            <h2 class="font-serif text-xl text-charcoal tracking-tight mb-5">Registrar Nuevo Médico</h2>

            <form method="POST" action="../controllers/AdminController.php" class="m-0 space-y-4">
                <input type="hidden" name="accion" value="crearMedico">

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-[0.65rem] font-bold text-slate uppercase tracking-widest mb-1.5">Matrícula Nacional</label>
                        <input type="text" name="matricula" placeholder="Ej: MN12345" required
                            class="w-full px-4 py-2.5 bg-ghost/50 border border-gray-200/80 rounded-xl text-sm text-charcoal placeholder-gray-400 focus:outline-none focus:border-slate focus:bg-white transition-all duration-200">
                    </div>
                    <div>
                        <label class="block text-[0.65rem] font-bold text-slate uppercase tracking-widest mb-1.5">Nombre</label>
                        <input type="text" name="nombre" placeholder="Nombre del profesional" required
                            class="w-full px-4 py-2.5 bg-ghost/50 border border-gray-200/80 rounded-xl text-sm text-charcoal placeholder-gray-400 focus:outline-none focus:border-slate focus:bg-white transition-all duration-200">
                    </div>
                    <div>
                        <label class="block text-[0.65rem] font-bold text-slate uppercase tracking-widest mb-1.5">Apellido</label>
                        <input type="text" name="apellido" placeholder="Apellido del profesional" required
                            class="w-full px-4 py-2.5 bg-ghost/50 border border-gray-200/80 rounded-xl text-sm text-charcoal placeholder-gray-400 focus:outline-none focus:border-slate focus:bg-white transition-all duration-200">
                    </div>
                    <div>
                        <label class="block text-[0.65rem] font-bold text-slate uppercase tracking-widest mb-1.5">Teléfono (Opcional)</label>
                        <input type="text" name="telefono" placeholder="Ej: 261..."
                            class="w-full px-4 py-2.5 bg-ghost/50 border border-gray-200/80 rounded-xl text-sm text-charcoal placeholder-gray-400 focus:outline-none focus:border-slate focus:bg-white transition-all duration-200">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[0.65rem] font-bold text-slate uppercase tracking-widest mb-1.5">Correo Electrónico (Opcional)</label>
                        <input type="email" name="email" placeholder="correo@ejemplo.com"
                            class="w-full px-4 py-2.5 bg-ghost/50 border border-gray-200/80 rounded-xl text-sm text-charcoal placeholder-gray-400 focus:outline-none focus:border-slate focus:bg-white transition-all duration-200">
                    </div>
                    <div>
                        <label class="block text-[0.65rem] font-bold text-slate uppercase tracking-widest mb-1.5">Especialidad Principal</label>
                        <div class="relative">
                            <select name="id_especialidad" required
                                class="w-full px-4 py-2.5 bg-ghost/50 border border-gray-200/80 rounded-xl text-sm text-charcoal focus:outline-none focus:border-slate focus:bg-white transition-all duration-200 appearance-none cursor-pointer">
                                <option value="">Seleccioná una especialidad</option>
                                <?php foreach ($listadoEspecialidad as $e): ?>
                                    <option value="<?= h($e['id_especialidad']) ?>"><?= h($e['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="bg-charcoal hover:bg-slate text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Dar de Alta al Médico
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/80 overflow-hidden shadow-sm">

            <div class="bg-[#F8FAFC] px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="font-serif text-lg text-charcoal tracking-tight">Directorio Activo</h2>
                    <p class="text-[0.7rem] text-slate mt-0.5 font-medium uppercase tracking-wider"><?= count($listadoMedicos) ?> profesionales registrados</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 bg-[#F8FAFC]">
                            <th class="py-3.5 px-6 text-[0.65rem] font-bold text-slate uppercase tracking-widest">Identificación</th>
                            <th class="py-3.5 px-6 text-[0.65rem] font-bold text-slate uppercase tracking-widest">Contacto</th>
                            <th class="py-3.5 px-6 text-[0.65rem] font-bold text-slate uppercase tracking-widest w-1/4">Especialidades</th>
                            <th class="py-3.5 px-6 text-[0.65rem] font-bold text-slate uppercase tracking-widest">Horarios de Atención</th>
                            <th class="py-3.5 px-6 text-[0.65rem] font-bold text-slate uppercase tracking-widest text-right">Gestión</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 bg-white">
                        <?php foreach ($listadoMedicos as $m): ?>
                            <tr class="hover:bg-ghost/30 transition-colors group">

                                <td class="py-4 px-6 align-top">
                                    <div class="flex items-center gap-3 w-max">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-charcoal to-slate text-white flex items-center justify-center font-bold text-sm shadow-sm">
                                            <?= substr(h($m['nombre']), 0, 1) . substr(h($m['apellido']), 0, 1) ?>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-charcoal">Dr/a. <?= h($m['nombre']) . ' ' . h($m['apellido']) ?></div>
                                            <div class="text-[0.7rem] font-mono text-slate mt-0.5 bg-ghost px-1.5 py-0.5 rounded-md inline-block border border-gray-200">
                                                MN: <?= h($m['matricula']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-4 px-6 align-top text-xs text-slate">
                                    <div class="truncate w-36 mb-1" title="<?= h($m['email']) ?>">
                                        <?= h($m['email']) ?: '<span class="italic text-gray-400">Sin registro</span>' ?>
                                    </div>
                                    <div class="font-medium text-charcoal">
                                        <?= h($m['telefono']) ?: '<span class="italic text-gray-400">Sin registro</span>' ?>
                                    </div>
                                </td>

                                <td class="py-4 px-6 align-top">
                                    <div class="flex flex-wrap gap-1.5 max-w-[200px]">
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

                                <td class="py-4 px-6 align-top">
                                    <?php if (empty(trim($m['horarios']))): ?>
                                        <span class="italic text-gray-400 text-xs font-medium">Sin asignar</span>
                                    <?php else: ?>
                                        <div class="flex flex-wrap gap-1.5 max-w-[260px]">
                                            <?php
                                            $horariosList = explode('|', $m['horarios']);
                                            foreach ($horariosList as $h):
                                                $h = trim($h);
                                                if (!empty($h)):
                                                    // Limpieza de segundos (:00) de los bloques horarios
                                                    $h = preg_replace('/(?<=\d{2}:\d{2}):00/', '', $h);
                                            ?>
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-ghost border border-gray-200 text-[0.68rem] font-medium text-slate">
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

                                <td class="py-4 px-6 align-top text-right relative">
                                    <div class="flex justify-end gap-2">
                                        <button onclick="document.getElementById('editar-<?= $m['matricula'] ?>').style.display = document.getElementById('editar-<?= $m['matricula'] ?>').style.display === 'none' ? 'block' : 'none'"
                                            class="bg-white border border-gray-200 text-slate hover:border-charcoal hover:text-charcoal px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-sm">
                                            Editar
                                        </button>

                                        <form method="POST" action="dashboardAdminMedicos.php" class="m-0"
                                            onsubmit="return confirm('ATENCIÓN: ¿Seguro que querés dar de baja al Dr/a. <?= h($m['apellido']) ?>? Esta acción no se puede deshacer.')">
                                            <input type="hidden" name="accion" value="eliminarMedico">
                                            <input type="hidden" name="matricula" value="<?= h($m['matricula']) ?>">
                                            <button type="submit" class="bg-rose-50 border border-rose-200 text-rose-600 hover:bg-rose-100 hover:text-rose-800 px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-sm">
                                                Baja
                                            </button>
                                        </form>
                                    </div>

                                    <div id="editar-<?= $m['matricula'] ?>" style="display:none" class="absolute right-6 top-14 w-72 bg-white rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.1)] border border-gray-200 z-50 p-5 text-left animate-fadeIn before:content-[''] before:absolute before:-top-2 before:right-8 before:w-4 before:h-4 before:bg-white before:border-l before:border-t before:border-gray-200 before:rotate-45">
                                        <h4 class="font-serif text-sm text-charcoal mb-3">Editar Información</h4>
                                        <form method="POST" action="dashboardAdminMedicos.php" class="m-0 space-y-3">
                                            <input type="hidden" name="accion" value="editarMedico">
                                            <input type="hidden" name="matricula" value="<?= h($m['matricula']) ?>">

                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <label class="block text-[0.6rem] font-bold text-slate uppercase mb-1">Nombre</label>
                                                    <input type="text" name="nombre" value="<?= h($m['nombre']) ?>" required
                                                        class="w-full px-3 py-2 bg-ghost/50 border border-gray-200 rounded-lg text-xs text-charcoal focus:outline-none focus:border-slate transition-colors">
                                                </div>
                                                <div>
                                                    <label class="block text-[0.6rem] font-bold text-slate uppercase mb-1">Apellido</label>
                                                    <input type="text" name="apellido" value="<?= h($m['apellido']) ?>" required
                                                        class="w-full px-3 py-2 bg-ghost/50 border border-gray-200 rounded-lg text-xs text-charcoal focus:outline-none focus:border-slate transition-colors">
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block text-[0.6rem] font-bold text-slate uppercase mb-1">Teléfono</label>
                                                <input type="text" name="telefono" value="<?= h($m['telefono']) ?>"
                                                    class="w-full px-3 py-2 bg-ghost/50 border border-gray-200 rounded-lg text-xs text-charcoal focus:outline-none focus:border-slate transition-colors">
                                            </div>

                                            <div>
                                                <label class="block text-[0.6rem] font-bold text-slate uppercase mb-1">Email</label>
                                                <input type="email" name="email" value="<?= h($m['email']) ?>"
                                                    class="w-full px-3 py-2 bg-ghost/50 border border-gray-200 rounded-lg text-xs text-charcoal focus:outline-none focus:border-slate transition-colors">
                                            </div>

                                            <button type="submit" class="w-full bg-charcoal hover:bg-slate text-white py-2 rounded-lg text-xs font-bold transition-all shadow-sm mt-1">
                                                Guardar Cambios
                                            </button>

                                            <button type="button" onclick="document.getElementById('editar-<?= $m['matricula'] ?>').style.display = 'none'" class="w-full text-xs font-semibold text-slate hover:text-charcoal mt-2 transition-colors">
                                                Cancelar
                                            </button>
                                        </form>
                                    </div>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

</body>

</html>