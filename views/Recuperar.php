<?php $titulo = 'Recuperar Contraseña — MediTurnos'; ?>
<!DOCTYPE html>
<html lang="es">
<?php require 'layout/head.php'; ?>

<body class="bg-ghost min-h-screen flex items-center justify-center font-sans relative overflow-x-hidden px-4">

    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-[30%] -right-[20%] w-[70%] h-[80%] bg-lightblue/20 blur-[140px] rounded-full"></div>
        <div class="absolute -bottom-[20%] -left-[10%] w-[50%] h-[70%] bg-slate/20 blur-[120px] rounded-full"></div>
    </div>

    <div class="w-full max-w-md z-10 animate-fadeIn">

        <a href="Login.php" class="block text-center mb-8 group cursor-pointer">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-white shadow-md border border-lightblue/30 mb-3 group-hover:scale-105 transition-transform duration-300">
                <span class="text-charcoal text-2xl font-bold">✚</span>
            </div>
            <h1 class="font-serif text-3xl text-charcoal tracking-tight">MediTurnos</h1>
            <p class="text-slate text-xs font-semibold uppercase tracking-widest mt-1.5 opacity-80">Portal de Acceso General</p>
        </a>

        <div class="bg-white/80 backdrop-blur-lg rounded-2xl border border-white/60 shadow-[0_20px_50px_rgba(47,69,80,0.06)] p-8 md:p-10 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-lightblue via-slate to-charcoal"></div>

            <div class="mb-8">
                <h2 class="font-serif text-2xl text-charcoal tracking-tight">Recuperar Contraseña</h2>
                <p class="text-slate text-sm mt-1">Ingresá tu email y DNI para validar tu identidad.</p>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="bg-red-500/10 border border-red-500/20 rounded-xl px-4 py-3 mb-6 text-xs font-medium text-red-700 flex items-center gap-2.5 animate-fadeIn">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span>Los datos ingresados no coinciden con ninguna cuenta.</span>
                </div>
            <?php endif; ?>

            <form method="POST" action="../controllers/AuthController.php" class="space-y-5">
                <input type="hidden" name="accion" value="recuperar">

                <div>
                    <label class="block text-[0.7rem] font-bold text-slate uppercase tracking-widest mb-1.5">Correo Electrónico</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate/50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                        </div>
                        <input type="email" name="email" placeholder="ejemplo@correo.com" required
                            class="w-full pl-10 pr-4 py-3 bg-ghost/50 border border-gray-200/80 rounded-xl text-sm text-charcoal placeholder-gray-400 focus:outline-none focus:border-slate focus:bg-white focus:shadow-sm transition-all duration-200">
                    </div>
                </div>
                
                <div>
                    <label class="block text-[0.7rem] font-bold text-slate uppercase tracking-widest mb-1.5">DNI</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate/50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 012-2h4a2 2 0 012 2v1m-4 0h4"></path></svg>
                        </div>
                        <input type="text" name="dni" placeholder="Sin puntos ni espacios" required
                            class="w-full pl-10 pr-4 py-3 bg-ghost/50 border border-gray-200/80 rounded-xl text-sm text-charcoal placeholder-gray-400 focus:outline-none focus:border-slate focus:bg-white focus:shadow-sm transition-all duration-200">
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-charcoal hover:bg-slate text-white font-bold py-3 rounded-xl text-sm transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 mt-2 flex justify-center items-center gap-2">
                    <span>Verificar Identidad</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </button>
            </form>
        </div>
    </div>
</body>
</html>
