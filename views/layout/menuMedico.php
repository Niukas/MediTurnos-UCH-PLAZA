<nav class="bg-white border-b border-lightblue/50 px-6 sm:px-10 h-16 flex items-center justify-between sticky top-0 z-[100]">
    <div class="flex items-center gap-2 font-serif text-charcoal text-xl">
        <a href="panelMedico.php" class="flex items-center gap-2 hover:opacity-80 transition-opacity text-charcoal decoration-none">
            <span class="text-slate text-2xl">✚</span> MediTurnos
        </a>
        <span class="font-sans text-sm font-medium text-slate ml-3 border-l border-gray-200 pl-3 hidden sm:inline-block">Médico | Hola, <?= $_SESSION['usuario_nombre'] ?></span>
    </div>
    <div class="flex items-center gap-4 sm:gap-6">
        <a href="panelMedico.php" class="text-[0.85rem] font-medium text-slate uppercase tracking-wider hover:text-charcoal transition-colors hidden md:inline-block">Mis turnos</a>
        <a href="buscarPacienteMedico.php" class="text-[0.85rem] font-medium text-slate uppercase tracking-wider hover:text-charcoal transition-colors hidden md:inline-block">Buscar paciente</a>
        <form method="POST" action="../controllers/AuthController.php" class="m-0">
            <input type="hidden" name="accion" value="logout">
            <button type="submit" class="bg-charcoal hover:bg-slate text-white px-4 sm:px-5 py-2 rounded-md font-sans text-[0.85rem] font-semibold tracking-wide transition-colors sm:ml-2">
                Cerrar sesión
            </button>
        </form>
    </div>
</nav>