<?php
session_start();
$old = $_SESSION['old_input'] ?? [];
unset($_SESSION['old_input']);
?>
<!DOCTYPE html>
<html lang="es">

<?php require 'layout/head.php'; ?>

<body class="bg-ghost min-h-screen flex items-center justify-center font-sans relative overflow-x-hidden py-12 px-4">

    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-[20%] -left-[10%] w-[60%] h-[70%] bg-lightblue/15 blur-[130px] rounded-full"></div>
        <div class="absolute -bottom-[20%] -right-[10%] w-[50%] h-[70%] bg-slate/15 blur-[120px] rounded-full"></div>
    </div>

    <div class="w-full max-w-lg z-10 animate-fadeIn">

        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-white shadow-md border border-lightblue/30 mb-3">
                <span class="text-charcoal text-xl font-bold">✚</span>
            </div>
            <h1 class="font-serif text-2xl text-charcoal tracking-tight">MediTurnos</h1>
            <p class="text-slate text-xs font-semibold uppercase tracking-widest mt-1 opacity-80">Registro de Nuevo Paciente</p>
        </div>

        <div class="bg-white/80 backdrop-blur-lg rounded-2xl border border-white/60 shadow-[0_20px_50px_rgba(47,69,80,0.06)] p-8 md:p-10 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-lightblue via-slate to-charcoal"></div>

            <div class="mb-6">
                <h2 class="font-serif text-2xl text-charcoal tracking-tight">Crear cuenta</h2>
                <p class="text-slate text-sm mt-1">Completá tus datos personales para acceder a la reserva de turnos.</p>
            </div>

            <?php
            $errores = [
                'campos_vacios'  => 'Completá todos los campos obligatorios.',
                'email_invalido' => 'El email no tiene un formato válido.',
                'password_corta' => 'La contraseña debe tener al menos 6 caracteres.',
                'dni_invalido'   => 'El DNI ingresado no es válido.',
                'dni_duplicado'  => 'Ya existe una cuenta registrada con ese DNI.',
                'desconocido'    => 'Ocurrió un error inesperado. Por favor, intentá de nuevo.',
            ];
            $error = $_GET['error'] ?? null;
            if ($error && isset($errores[$error])): ?>
                <div class="bg-red-500/10 border border-red-500/20 rounded-xl px-4 py-3 mb-6 text-xs font-medium text-red-700 flex items-center gap-2.5 animate-fadeIn">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span><?= $errores[$error] ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="../controllers/AuthController.php" class="space-y-4">
                <input type="hidden" name="accion" value="registrar">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[0.7rem] font-bold text-slate uppercase tracking-widest mb-1.5">Nombre</label>
                        <input type="text" name="nombre" placeholder="Juan" required
                            class="w-full px-4 py-2.5 bg-ghost/50 border border-gray-200/80 rounded-xl text-sm text-charcoal placeholder-gray-400 focus:outline-none focus:border-slate focus:bg-white transition-all duration-200" value="<?= htmlspecialchars($old['nombre'] ?? '') ?>">
                    </div>
                    <div>
                        <label class="block text-[0.7rem] font-bold text-slate uppercase tracking-widest mb-1.5">Apellido</label>
                        <input type="text" name="apellido" placeholder="Pérez" required
                            class="w-full px-4 py-2.5 bg-ghost/50 border border-gray-200/80 rounded-xl text-sm text-charcoal placeholder-gray-400 focus:outline-none focus:border-slate focus:bg-white transition-all duration-200" value="<?= htmlspecialchars($old['apellido'] ?? '') ?>">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[0.7rem] font-bold text-slate uppercase tracking-widest mb-1.5">DNI</label>
                        <input type="text" name="dni" placeholder="44444444" required
                            class="w-full px-4 py-2.5 bg-ghost/50 border border-gray-200/80 rounded-xl text-sm text-charcoal placeholder-gray-400 focus:outline-none focus:border-slate focus:bg-white transition-all duration-200" value="<?= htmlspecialchars($old['dni'] ?? '') ?>">
                    </div>
                    <div>
                        <label class="block text-[0.7rem] font-bold text-slate uppercase tracking-widest mb-1.5">Fecha de Nacimiento</label>
                        <input type="date" name="fecha_nac" required
                            class="w-full px-4 py-2.5 bg-ghost/50 border border-gray-200/80 rounded-xl text-sm text-slate focus:outline-none focus:border-slate focus:bg-white transition-all duration-200" value="<?= htmlspecialchars($old['fecha_nac'] ?? '') ?>">
                    </div>
                </div>

                <div>
                    <label class="block text-[0.7rem] font-bold text-slate uppercase tracking-widest mb-1.5">Teléfono de Contacto</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate/50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <input type="text" name="telefono" placeholder="2616859565" required
                            class="w-full pl-10 pr-4 py-2.5 bg-ghost/50 border border-gray-200/80 rounded-xl text-sm text-charcoal placeholder-gray-400 focus:outline-none focus:border-slate focus:bg-white transition-all duration-200" value="<?= htmlspecialchars($old['telefono'] ?? '') ?>">
                    </div>
                </div>

                <div>
                    <label class="block text-[0.7rem] font-bold text-slate uppercase tracking-widest mb-1.5">Correo Electrónico</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate/50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                            </svg>
                        </div>
                        <input type="email" name="email" placeholder="ejemplo@correo.com" required
                            class="w-full pl-10 pr-4 py-2.5 bg-ghost/50 border border-gray-200/80 rounded-xl text-sm text-charcoal placeholder-gray-400 focus:outline-none focus:border-slate focus:bg-white transition-all duration-200" value="<?= htmlspecialchars($old['email'] ?? '') ?>">
                    </div>
                </div>

                <div>
                    <label class="block text-[0.7rem] font-bold text-slate uppercase tracking-widest mb-1.5">Contraseña</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate/50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input type="password" name="password" placeholder="Mínimo 6 caracteres" required
                            class="w-full pl-10 pr-4 py-2.5 bg-ghost/50 border border-gray-200/80 rounded-xl text-sm text-charcoal placeholder-gray-400 focus:outline-none focus:border-slate focus:bg-white transition-all duration-200">
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-charcoal hover:bg-slate text-white font-bold py-3 rounded-xl text-sm transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 mt-4 flex justify-center items-center gap-2">
                    <span>Finalizar registro</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </form>

            <div class="flex items-center gap-3 my-5">
                <div class="flex-1 h-px bg-gray-200/60"></div>
                <span class="text-[0.65rem] font-bold text-gray-400 uppercase tracking-wider">¿Ya tenés un usuario?</span>
                <div class="flex-1 h-px bg-gray-200/60"></div>
            </div>

            <a href="Login.php"
                class="block w-full text-center border-2 border-charcoal/10 text-charcoal hover:bg-charcoal hover:text-white font-bold py-3 rounded-xl text-sm transition-all duration-200 hover:border-charcoal">
                Iniciar sesión
            </a>

        </div>
    </div>

</body>

</html>
