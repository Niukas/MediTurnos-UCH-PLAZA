<nav class="bg-white border-b border-lightblue/50 px-6 sm:px-10 h-16 flex items-center justify-between sticky top-0 z-[100]">
    <div class="flex items-center gap-2 font-serif text-charcoal text-xl">
        <a href="panelMedico.php" class="flex items-center gap-2 hover:opacity-80 transition-opacity text-charcoal decoration-none">
            <span class="text-slate text-2xl">✚</span> MediTurnos
        </a>
        <span class="font-sans text-sm font-medium text-slate ml-3 border-l border-gray-200 pl-3 hidden sm:inline-block">
            Médico | Hola, <?= $_SESSION['usuario_nombre'] ?? '' ?>
        </span>
    </div>

    <div class="flex items-center gap-2 sm:gap-3">

        <a href="panelMedico.php"
            class="hidden md:flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-bold transition-all <?= SECCION === 'misTurnos' ? 'bg-charcoal text-white shadow-sm' : 'text-slate hover:bg-ghost hover:text-charcoal' ?>">
            <svg class="w-4 h-4 shrink-0" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span>Mis Turnos</span>
        </a>

        <a href="buscarPacienteMedico.php"
            class="hidden md:flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-bold transition-all <?= SECCION === 'buscarPaciente' ? 'bg-charcoal text-white shadow-sm' : 'text-slate hover:bg-ghost hover:text-charcoal' ?>">
            <svg class="w-4 h-4 shrink-0" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <span>Buscar Paciente</span>
        </a>

        <a href="configurarHorarios.php"
            class="hidden sm:flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-bold transition-all <?= SECCION === 'configurarHorarios' ? 'bg-charcoal text-white shadow-sm' : 'text-slate hover:bg-ghost hover:text-charcoal' ?>">
            <svg class="w-4 h-4 shrink-0" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>Mis Horarios</span>
        </a>

        <form method="POST" action="../controllers/AuthController.php" class="m-0 sm:ml-2">
            <input type="hidden" name="accion" value="logout">
            <button type="submit" class="bg-charcoal hover:bg-slate text-white px-4 py-2 rounded-xl font-sans text-sm font-bold transition-all shadow-sm flex items-center gap-2">
                <span class="hidden sm:inline">Cerrar sesión</span>
                <span class="sm:hidden">Salir</span>
            </button>
        </form>

    </div>
</nav>