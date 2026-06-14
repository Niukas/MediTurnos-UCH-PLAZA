<?php
define('SECCION', 'usuarios');
require '../controllers/AdminController.php';
require '../config/helpers.php';
$titulo = 'Gestión de Usuarios — MediTurnos';
?>
<!DOCTYPE html>
<html lang="es">

<?php require 'layout/head.php'; ?>

<body class="bg-[#F8FAFC] font-sans text-charcoal min-h-screen flex flex-col">

    <?php require '../views/layout/menuAdmin.php'; ?>

    <main class="flex-grow p-6 sm:p-10 max-w-7xl mx-auto w-full animate-fadeIn">

        <div class="mb-8">
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-white border border-gray-200 text-slate text-[0.65rem] font-bold uppercase tracking-widest mb-3 shadow-sm">
                <svg class="w-3.5 h-3.5 text-slate" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                Control de Accesos y Seguridad
            </div>
            <h1 class="font-serif text-3xl md:text-4xl text-charcoal tracking-tight mb-1">Gestión de Usuarios</h1>
            <p class="text-slate text-sm">Audita las cuentas de la plataforma, actualizá datos filiales y modificá roles de seguridad.</p>
        </div>

        <?php
        $mensajes = [
            'rol_actualizado' => ['bg' => 'bg-emerald-500/10', 'border' => 'border-emerald-500/20', 'text' => 'text-emerald-700', 'texto' => 'Rango y privilegios de usuario actualizados correctamente.', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
            'editado'         => ['bg' => 'bg-blue-500/10',    'border' => 'border-blue-500/20',    'text' => 'text-blue-700',    'texto' => 'Ficha de usuario modificada correctamente.', 'icon' => 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z'],
            'eliminado'       => ['bg' => 'bg-amber-500/10',   'border' => 'border-amber-500/20',   'text' => 'text-amber-700',   'texto' => 'La cuenta de usuario fue revocada y eliminada del sistema.', 'icon' => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'],
            'error'           => ['bg' => 'bg-rose-500/10',    'border' => 'border-rose-500/20',    'text' => 'text-rose-700',    'texto' => 'Hubo un error interno al procesar la operación.', 'icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
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

        <form method="GET" action="" class="mb-6">
            <div class="flex gap-3">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="busqueda" value="<?= h($_GET['busqueda'] ?? '') ?>"
                        placeholder="Buscar usuario por nombre, apellido o email..."
                        class="w-full pl-11 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-sm text-charcoal placeholder-gray-400 focus:outline-none focus:border-slate focus:ring-2 focus:ring-slate/10 transition-all shadow-sm">
                </div>
                <button type="submit"
                    class="bg-charcoal hover:bg-slate text-white px-6 py-3 rounded-xl text-sm font-bold transition-all shadow-md hover:shadow-lg flex items-center gap-2 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Buscar
                </button>

                <?php if (!empty($_GET['busqueda'])): ?>
                    <a href="dashboardAdminUsuarios.php" class="bg-white border border-gray-200 hover:border-charcoal text-slate hover:text-charcoal px-6 py-3 rounded-xl text-sm font-bold transition-all shadow-sm flex items-center gap-2 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Limpiar
                    </a>
                <?php endif; ?>
            </div>
        </form>

        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm relative">

            <div class="bg-ghost/60 px-6 py-4 border-b border-gray-100 flex items-center justify-between rounded-t-2xl">
                <h2 class="font-serif text-lg text-charcoal tracking-tight">Cuentas Registradas</h2>
                <span class="bg-white border border-gray-200 text-charcoal text-[0.65rem] font-bold px-3 py-1 rounded-full shadow-sm uppercase tracking-wider">
                    Página <?= $paginaActual ?> / <?= $totalPaginas ?>
                </span>
            </div>

            <div class="w-full overflow-visible rounded-b-2xl">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="py-3.5 px-6 text-[0.65rem] font-bold text-slate uppercase tracking-widest bg-white w-20">ID Ref</th>
                            <th class="py-3.5 px-6 text-[0.65rem] font-bold text-slate uppercase tracking-widest bg-white">Titular de Cuenta</th>
                            <th class="py-3.5 px-6 text-[0.65rem] font-bold text-slate uppercase tracking-widest bg-white">Nivel Actual</th>
                            <th class="py-3.5 px-6 text-[0.65rem] font-bold text-slate uppercase tracking-widest bg-white">Modificar Rango</th>
                            <th class="py-3.5 px-6 text-[0.65rem] font-bold text-slate uppercase tracking-widest bg-white text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 bg-white rounded-b-2xl">
                        <?php foreach ($listadoUsuarios as $u): ?>
                            <tr class="hover:bg-ghost/30 transition-colors group">

                                <td class="py-4 px-6 text-xs text-slate/70 font-mono align-middle">
                                    #<?= str_pad($u['id_usuario'], 4, '0', STR_PAD_LEFT) ?>
                                </td>

                                <td class="py-4 px-6 align-middle">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-charcoal to-slate text-white flex items-center justify-center font-bold text-xs uppercase shadow-sm shrink-0">
                                            <?= substr(h($u['nombre']), 0, 1) . substr(h($u['apellido']), 0, 1) ?>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-charcoal whitespace-nowrap"><?= h($u['nombre']) . ' ' . h($u['apellido']) ?></div>
                                            <div class="text-xs text-slate mt-0.5"><?= h($u['email']) ?></div>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-4 px-6 align-middle">
                                    <?php
                                    $rolStyle = match (strtolower($u['rol'])) {
                                        'admin'    => 'bg-charcoal text-white border-charcoal',
                                        'medico'   => 'bg-lightblue text-charcoal border-lightblue/60',
                                        default    => 'bg-ghost text-slate border-gray-200'
                                    };
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[0.65rem] font-bold uppercase tracking-wider border <?= $rolStyle ?> whitespace-nowrap">
                                        <?= h($u['rol']) ?>
                                    </span>
                                </td>

                                <td class="py-4 px-6 align-middle whitespace-nowrap">
                                    <form method="POST" action="dashboardAdminUsuarios.php" class="m-0 flex items-center gap-2">
                                        <input type="hidden" name="accion" value="cambiarRol">
                                        <input type="hidden" name="id_Usuario" value="<?= $u['id_usuario'] ?>">

                                        <div class="relative shrink-0">
                                            <select name="id_rol" class="pl-3 pr-8 py-1.5 bg-ghost border border-gray-200 rounded-lg text-xs font-bold text-charcoal focus:outline-none focus:border-slate appearance-none cursor-pointer transition-colors shadow-sm">
                                                <?php foreach ($listadoRoles as $rol): ?>
                                                    <option value="<?= $rol['id_rol'] ?>" <?= $rol['id_rol'] == $u['id_rol'] ? 'selected' : '' ?>>
                                                        <?= ucfirst(h($rol['nombre'])) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate opacity-60">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </div>
                                        </div>

                                        <button type="submit" class="bg-white border border-gray-200 text-charcoal hover:bg-charcoal hover:text-white w-7 h-7 rounded-lg flex items-center justify-center transition-all shadow-sm shrink-0" title="Confirmar cambio de privilegios">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </td>

                                <td class="py-4 px-6 align-middle text-right relative whitespace-nowrap">

                                    <div class="flex gap-2 justify-end shrink-0">
                                        <button type="button" onclick="document.getElementById('editar-<?= $u['id_usuario'] ?>').style.display = document.getElementById('editar-<?= $u['id_usuario'] ?>').style.display === 'none' ? 'block' : 'none'"
                                            class="bg-white border border-gray-200 text-slate hover:border-charcoal hover:text-charcoal px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-sm shrink-0">
                                            Editar
                                        </button>

                                        <form method="POST" action="dashboardAdminUsuarios.php" class="m-0 shrink-0"
                                            onsubmit="return confirm('ALERTA CRÍTICA: ¿Seguro que deseas eliminar permanentemente esta cuenta de usuario? Se revocarán todos los accesos.')">
                                            <input type="hidden" name="accion" value="eliminarUsuario">
                                            <input type="hidden" name="id_usuario" value="<?= $u['id_usuario'] ?>">
                                            <button type="submit" class="bg-rose-50 border border-rose-200 text-rose-600 hover:bg-rose-100 hover:text-rose-800 px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-sm shrink-0">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>

                                    <div id="editar-<?= $u['id_usuario'] ?>" style="display:none" class="absolute right-6 top-14 w-72 bg-white rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.15)] border border-gray-200 z-[9999] p-5 text-left animate-fadeIn before:content-[''] before:absolute before:-top-2 before:right-14 before:w-4 before:h-4 before:bg-white before:border-l before:border-t before:border-gray-200 before:rotate-45">
                                        <h4 class="font-serif text-sm text-charcoal mb-3">Modificar Credenciales</h4>
                                        <form method="POST" action="dashboardAdminUsuarios.php" class="space-y-3 m-0">
                                            <input type="hidden" name="accion" value="editarUsuario">
                                            <input type="hidden" name="id_usuario" value="<?= $u['id_usuario'] ?>">
                                            <input type="hidden" name="busqueda" value="<?= h($_GET['busqueda'] ?? '') ?>">

                                            <div>
                                                <label class="block text-[0.6rem] font-bold text-slate uppercase mb-1">Nombre</label>
                                                <input type="text" name="nombre" value="<?= h($u['nombre']) ?>" required
                                                    class="w-full px-3 py-2 bg-ghost/50 border border-gray-200 rounded-lg text-xs text-charcoal focus:outline-none focus:border-slate transition-colors">
                                            </div>
                                            <div>
                                                <label class="block text-[0.6rem] font-bold text-slate uppercase mb-1">Apellido</label>
                                                <input type="text" name="apellido" value="<?= h($u['apellido']) ?>" required
                                                    class="w-full px-3 py-2 bg-ghost/50 border border-gray-200 rounded-lg text-xs text-charcoal focus:outline-none focus:border-slate transition-colors">
                                            </div>
                                            <div>
                                                <label class="block text-[0.6rem] font-bold text-slate uppercase mb-1">Correo Electrónico</label>
                                                <input type="email" name="email" value="<?= h($u['email']) ?>" required
                                                    class="w-full px-3 py-2 bg-ghost/50 border border-gray-200 rounded-lg text-xs text-charcoal focus:outline-none focus:border-slate transition-colors">
                                            </div>

                                            <button type="submit" class="w-full bg-charcoal hover:bg-slate text-white py-2 rounded-lg text-xs font-bold transition-all shadow-sm mt-1">
                                                Guardar Cambios
                                            </button>

                                            <button type="button" onclick="document.getElementById('editar-<?= $u['id_usuario'] ?>').style.display = 'none'" class="w-full text-xs font-semibold text-slate hover:text-charcoal mt-2 transition-colors text-center block">
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

        <?php if ($totalPaginas > 1): ?>
            <div class="mt-10 flex justify-center">
                <nav class="inline-flex items-center gap-1.5 bg-white border border-gray-200/80 rounded-xl p-1.5 shadow-sm">

                    <?php if ($paginaActual > 1): ?>
                        <a href="?pagina=<?= $paginaActual - 1 ?>" class="flex items-center justify-center px-3 py-1.5 text-xs font-bold text-slate hover:bg-ghost rounded-lg transition-colors">← Ant</a>
                    <?php endif; ?>

                    <?php
                    $inicio = max(1, $paginaActual - 2);
                    $fin    = min($totalPaginas, $paginaActual + 2);
                    if ($inicio > 1): ?>
                        <a href="?pagina=1" class="w-8 h-8 flex items-center justify-center text-xs font-bold text-slate hover:bg-ghost rounded-lg transition-colors">1</a>
                        <?php if ($inicio > 2): ?> <span class="px-1 text-slate/50 text-xs font-bold">...</span> <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $inicio; $i <= $fin; $i++): ?>
                        <?php if ($i == $paginaActual): ?>
                            <span class="w-8 h-8 flex items-center justify-center text-xs font-bold bg-charcoal text-white rounded-lg shadow-sm"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?pagina=<?= $i ?>" class="w-8 h-8 flex items-center justify-center text-xs font-bold text-slate hover:bg-ghost rounded-lg transition-colors"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($fin < $totalPaginas): ?>
                        <?php if ($fin < $totalPaginas - 1): ?> <span class="px-1 text-slate/50 text-xs font-bold">...</span> <?php endif; ?>
                        <a href="?pagina=<?= $totalPaginas ?>" class="w-8 h-8 flex items-center justify-center text-xs font-bold text-slate hover:bg-ghost rounded-lg transition-colors"><?= $totalPaginas ?></a>
                    <?php endif; ?>

                    <?php if ($paginaActual < $totalPaginas): ?>
                        <a href="?pagina=<?= $paginaActual + 1 ?>" class="flex items-center justify-center px-3 py-1.5 text-xs font-bold text-slate hover:bg-ghost rounded-lg transition-colors">Sig →</a>
                    <?php endif; ?>

                </nav>
            </div>
        <?php endif; ?>

    </main>

</body>

</html>