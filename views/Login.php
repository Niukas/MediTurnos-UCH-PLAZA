<?php $titulo = 'Login — MediTurnos'; ?>
<!DOCTYPE html>
<html lang="es">
<?php require 'layout/head.php'; ?>

<body class="bg-ghost min-h-screen flex items-center justify-center font-sans relative overflow-x-hidden px-4">

    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-[30%] -right-[20%] w-[70%] h-[80%] bg-lightblue/20 blur-[140px] rounded-full"></div>
        <div class="absolute -bottom-[20%] -left-[10%] w-[50%] h-[70%] bg-slate/20 blur-[120px] rounded-full"></div>
    </div>

    <div class="w-full max-w-md z-10 animate-fadeIn">

        <div class="text-center mb-8 group cursor-pointer">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-white shadow-md border border-lightblue/30 mb-3 group-hover:scale-105 transition-transform duration-300">
                <span class="text-charcoal text-2xl font-bold">✚</span>
            </div>
            <h1 class="font-serif text-3xl text-charcoal tracking-tight">MediTurnos</h1>
            <p class="text-slate text-xs font-semibold uppercase tracking-widest mt-1.5 opacity-80">Portal de Acceso General</p>
        </div>

        <div class="bg-white/80 backdrop-blur-lg rounded-2xl border border-white/60 shadow-[0_20px_50px_rgba(47,69,80,0.06)] p-8 md:p-10 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-lightblue via-slate to-charcoal"></div>

            <div class="mb-8">
                <h2 class="font-serif text-2xl text-charcoal tracking-tight">Bienvenido de nuevo</h2>
                <p class="text-slate text-sm mt-1">Ingresá tus credenciales para gestionar tus turnos.</p>
            </div>

            <?php
            $mensajes = [
                'errorLogin' => ['bg' => 'bg-red-500/10',   'border' => 'border-red-500/20',   'text' => 'text-red-700',   'texto' => 'Usuario o contraseña incorrectos.'],
                'error'      => ['bg' => 'bg-red-500/10',   'border' => 'border-red-500/20',   'text' => 'text-red-700',   'texto' => 'Hubo un error en el registro, intentá de nuevo.'],
                'registro'   => ['bg' => 'bg-green-500/10', 'border' => 'border-green-500/20', 'text' => 'text-green-700', 'texto' => 'Cuenta creada con éxito. Ya podés iniciar sesión.'],
                'password_actualizada'   => ['bg' => 'bg-green-500/10', 'border' => 'border-green-500/20', 'text' => 'text-green-700', 'texto' => 'Contraseña actualizada con éxito. Ya podés iniciar sesión.'],

            ];
                        foreach ($mensajes as $key => $msg):
                if (isset($_GET[$key]) || (isset($_GET['exito']) && $key === 'password_actualizada')): ?>
                    <div class="<?= $msg['bg'] ?> <?= $msg['border'] ?> border rounded-xl px-4 py-3 mb-6 text-xs font-medium <?= $msg['text'] ?> flex items-center gap-2.5 animate-fadeIn">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span><?= $msg['texto'] ?></span>
                    </div>
            <?php endif;
            endforeach; ?>

            <form method="POST" action="../controllers/AuthController.php" class="space-y-5">
                <input type="hidden" name="accion" value="login">

                <div>
                    <label class="block text-[0.7rem] font-bold text-slate uppercase tracking-widest mb-1.5">Correo Electrónico</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate/50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                            </svg>
                        </div>
                        <input type="email" name="email" placeholder="ejemplo@correo.com" required
                            class="w-full pl-10 pr-4 py-3 bg-ghost/50 border border-gray-200/80 rounded-xl text-sm text-charcoal placeholder-gray-400 focus:outline-none focus:border-slate focus:bg-white focus:shadow-sm transition-all duration-200">
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="block text-[0.7rem] font-bold text-slate uppercase tracking-widest">Contraseña</label>
                        <a href="Recuperar.php" class="text-[0.75rem] font-medium text-slate/70 hover:text-charcoal transition-colors underline cursor-pointer">¿La olvidaste?</a>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate/50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input type="password" name="password" placeholder="••••••••" required
                            class="w-full pl-10 pr-4 py-3 bg-ghost/50 border border-gray-200/80 rounded-xl text-sm text-charcoal placeholder-gray-400 focus:outline-none focus:border-slate focus:bg-white focus:shadow-sm transition-all duration-200">
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-charcoal hover:bg-slate text-white font-bold py-3 rounded-xl text-sm transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 mt-2 flex justify-center items-center gap-2">
                    <span>Ingresar al portal</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14"></path>
                    </svg>
                </button>
            </form>

            <div class="flex items-center gap-3 my-6">
                <div class="flex-1 h-px bg-gray-200/60"></div>
                <span class="text-[0.7rem] font-bold text-gray-400 uppercase tracking-wider">¿Nuevo en la plataforma?</span>
                <div class="flex-1 h-px bg-gray-200/60"></div>
            </div>

            <a href="Registro.php"
                class="block w-full text-center border-2 border-charcoal/10 text-charcoal hover:bg-charcoal hover:text-white font-bold py-3 rounded-xl text-sm transition-all duration-200 hover:border-charcoal">
                Crear una cuenta nueva
            </a>

        </div>
    </div>

</body>

</html>