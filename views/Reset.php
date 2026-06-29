<?php
session_start();
// Redirigir si no se ha validado la identidad del usuario
if (!isset($_SESSION['usuario_id_para_reset'])) {
    header('Location: Login.php');
    exit;
}
$titulo = 'Restablecer Contraseña — MediTurnos';
?>
<!DOCTYPE html>
<html lang="es">
<?php require 'layout/head.php'; ?>

<body class="bg-ghost min-h-screen flex items-center justify-center font-sans relative overflow-x-hidden px-4">

    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-[30%] -right-[20%] w-[70%] h-[80%] bg-lightblue/20 blur-[140px] rounded-full"></div>
        <div class="absolute -bottom-[20%] -left-[10%] w-[50%] h-[70%] bg-slate/20 blur-[120px] rounded-full"></div>
    </div>

    <div class="w-full max-w-md z-10 animate-fadeIn">

        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-white shadow-md border border-lightblue/30 mb-3">
                <span class="text-charcoal text-2xl font-bold">✚</span>
            </div>
            <h1 class="font-serif text-3xl text-charcoal tracking-tight">MediTurnos</h1>
        </div>

        <div class="bg-white/80 backdrop-blur-lg rounded-2xl border border-white/60 shadow-[0_20px_50px_rgba(47,69,80,0.06)] p-8 md:p-10 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-lightblue via-slate to-charcoal"></div>

            <div class="mb-8">
                <h2 class="font-serif text-2xl text-charcoal tracking-tight">Crear Nueva Contraseña</h2>
                <p class="text-slate text-sm mt-1">Tu nueva contraseña debe ser diferente a la anterior.</p>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="bg-red-500/10 border border-red-500/20 rounded-xl px-4 py-3 mb-6 text-xs font-medium text-red-700 flex items-center gap-2.5 animate-fadeIn">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span>
                        <?php 
                        if ($_GET['error'] === 'password_corta') echo 'La contraseña debe tener al menos 6 caracteres.';
                        elseif ($_GET['error'] === 'no_coinciden') echo 'Las contraseñas no coinciden.';
                        else echo 'Hubo un error inesperado. Intentá de nuevo.';
                        ?>
                    </span>
                </div>
            <?php endif; ?>

            <form method="POST" action="../controllers/AuthController.php" class="space-y-5">
                <input type="hidden" name="accion" value="reset">

                <div>
                    <label class="block text-[0.7rem] font-bold text-slate uppercase tracking-widest mb-1.5">Nueva Contraseña</label>
                    <input type="password" name="password" placeholder="••••••••" required
                        class="w-full px-4 py-3 bg-ghost/50 border border-gray-200/80 rounded-xl text-sm text-charcoal placeholder-gray-400 focus:outline-none focus:border-slate focus:bg-white focus:shadow-sm transition-all duration-200">
                </div>
                
                <div>
                    <label class="block text-[0.7rem] font-bold text-slate uppercase tracking-widest mb-1.5">Confirmar Contraseña</label>
                    <input type="password" name="password_confirm" placeholder="••••••••" required
                        class="w-full px-4 py-3 bg-ghost/50 border border-gray-200/80 rounded-xl text-sm text-charcoal placeholder-gray-400 focus:outline-none focus:border-slate focus:bg-white focus:shadow-sm transition-all duration-200">
                </div>

                <button type="submit"
                    class="w-full bg-charcoal hover:bg-slate text-white font-bold py-3 rounded-xl text-sm transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 mt-2">
                    Guardar Cambios
                </button>
            </form>
        </div>
    </div>
</body>
</html>
