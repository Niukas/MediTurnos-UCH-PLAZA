<nav class="bg-white border-b border-lightblue/50 px-6 sm:px-10 h-16 flex items-center justify-between sticky top-0 z-[100]">

    <div class="flex items-center gap-2 font-serif text-charcoal text-xl">
        <a href="panelMedico.php" class="flex items-center gap-2 hover:opacity-80 transition-opacity text-charcoal decoration-none">
            <span class="text-slate text-2xl">✚</span> MediTurnos
        </a>
        <span class="font-sans text-sm font-medium text-slate ml-3 border-l border-gray-200 pl-3 hidden sm:inline-block">
            Médico | Hola, <?= $_SESSION['usuario_nombre'] ?? '' ?>
        </span>
    </div>

    <div class="flex items-center gap-2 sm:gap-4">

        <a href="panelMedico.php"
            class="hidden md:inline-block text-[0.85rem] uppercase tracking-wider transition-colors px-3 py-2 rounded-lg <?= SECCION === 'misTurnos' ? 'bg-ghost text-charcoal font-bold' : 'font-medium text-slate hover:text-charcoal hover:bg-ghost' ?>">
            Mis Turnos
        </a>

        <a href="buscarPaciente.php"
            class="hidden md:inline-block text-[0.85rem] uppercase tracking-wider transition-colors px-3 py-2 rounded-lg <?= SECCION === 'buscarPaciente' ? 'bg-ghost text-charcoal font-bold' : 'font-medium text-slate hover:text-charcoal hover:bg-ghost' ?>">
            Buscar Paciente
        </a>

        <a href="configurarHorarios.php"
            class="hidden sm:inline-block text-[0.85rem] uppercase tracking-wider transition-colors px-3 py-2 rounded-lg <?= SECCION === 'configurarHorarios' ? 'bg-ghost text-charcoal font-bold' : 'font-medium text-slate hover:text-charcoal hover:bg-ghost' ?>">
            Mis Horarios
        </a>

        <form method="POST" action="../controllers/AuthController.php" class="m-0 sm:ml-2">
            <input type="hidden" name="accion" value="logout">
            <button type="submit" class="bg-charcoal hover:bg-slate text-white px-4 sm:px-5 py-2 rounded-md font-sans text-[0.85rem] font-semibold tracking-wide transition-colors flex items-center gap-2">
                <span class="hidden sm:inline">Cerrar sesión</span>
                <span class="sm:hidden">Salir</span>
            </button>
        </form>

    </div>
</nav>