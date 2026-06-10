<?php
session_start();
$logueado = isset($_SESSION['usuario_id']);
$rol = $_SESSION['usuario_rol'] ?? null;
$titulo = 'Inicio — MediTurnos';
?>

<!DOCTYPE html>
<html lang="es">

<?php require '../views/layout/head.php'; ?>

<body class="bg-ghost font-sans text-charcoal min-h-screen flex flex-col">

    <nav class="bg-white/90 backdrop-blur-md border-b border-lightblue/30 px-6 sm:px-10 h-20 flex items-center justify-between sticky top-0 z-[100] transition-all">
        <div class="flex items-center gap-2 font-serif text-charcoal text-2xl">
            <span class="text-lightblue">✚</span> MediTurnos
        </div>
        <div class="flex items-center gap-4 sm:gap-8">
            <a href="#especialidades" class="text-[0.8rem] font-bold text-slate uppercase tracking-widest hover:text-charcoal transition-colors hidden md:block">Especialidades</a>
            <a href="../views/Medicos.php" class="text-[0.8rem] font-bold text-slate uppercase tracking-widest hover:text-charcoal transition-colors hidden md:block">Staff Médico</a>

            <div class="flex items-center gap-4 ml-2">
                <?php if (!$logueado): ?>
                    <a href="../views/Registro.php" class="text-[0.85rem] font-bold text-slate hover:text-charcoal transition-colors hidden sm:block">Crear cuenta</a>
                    <a href="../views/Login.php" class="bg-charcoal hover:bg-slate text-white px-5 py-2.5 rounded-lg font-sans text-[0.85rem] font-bold tracking-wide transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        Ingresar
                    </a>
                <?php else: ?>
                    <?php
                    $dashboardUrl = match ($rol) {
                        'admin'  => '../views/dashboardAdmin.php',
                        'medico' => '../views/panelMedico.php',
                        default  => '../views/Panel.php'
                    };
                    ?>
                    <a href="<?= $dashboardUrl ?>" class="bg-charcoal hover:bg-slate text-white px-5 py-2.5 rounded-lg font-sans text-[0.85rem] font-bold tracking-wide transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Mi Panel
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="relative bg-charcoal overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
            <div class="absolute -top-[20%] -right-[10%] w-[50%] h-[70%] bg-lightblue/20 blur-[120px] rounded-full"></div>
            <div class="absolute -bottom-[20%] -left-[10%] w-[40%] h-[60%] bg-slate/40 blur-[100px] rounded-full"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-6 sm:px-10 py-20 lg:py-32 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">

            <div class="lg:col-span-7 z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 border border-white/20 text-lightblue text-xs font-bold uppercase tracking-widest mb-6">
                    <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-lightblue opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-lightblue"></span></span>
                    Plataforma de gestión médica
                </div>

                <h1 class="font-serif text-5xl sm:text-6xl lg:text-7xl text-white leading-[1.1] mb-6 text-balance">
                    Tu salud, a un<br><span class="text-transparent bg-clip-text bg-gradient-to-r from-lightblue to-white">clic de distancia.</span>
                </h1>
                <p class="text-white/70 text-lg sm:text-xl mb-10 leading-relaxed max-w-xl text-balance">
                    Conectamos pacientes con los mejores especialistas. Agendá, gestioná y controlá tus turnos médicos sin demoras ni llamadas.
                </p>

                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="<?= $logueado && $rol === 'paciente' ? '../views/SacarTurno.php' : '../views/Login.php' ?>"
                        class="bg-lightblue hover:bg-white text-charcoal px-8 py-4 rounded-xl font-bold transition-all shadow-[0_0_20px_rgba(184,219,217,0.3)] hover:shadow-[0_0_30px_rgba(255,255,255,0.5)] transform hover:-translate-y-1 text-center flex justify-center items-center gap-2 group">
                        Agendar un turno
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>

                    <?php if (!$logueado): ?>
                        <a href="../views/Registro.php" class="border border-white/20 bg-white/5 text-white hover:bg-white/10 px-8 py-4 rounded-xl font-bold transition-all backdrop-blur-sm text-center">
                            Soy nuevo paciente
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="lg:col-span-5 z-10 hidden md:block">
                <div class="bg-white/10 border border-white/10 rounded-2xl p-6 backdrop-blur-md shadow-2xl transform rotate-2 hover:rotate-0 transition-transform duration-500">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-white/10">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-lightblue to-slate flex items-center justify-center text-white font-serif font-bold">DR</div>
                            <div>
                                <div class="text-white font-bold text-sm">Dr. Roberto García</div>
                                <div class="text-lightblue text-xs">Cardiología</div>
                            </div>
                        </div>
                        <span class="bg-green-500/20 text-green-300 text-[0.65rem] font-bold uppercase tracking-wider px-2 py-1 rounded-md">Disponible</span>
                    </div>

                    <div class="space-y-3">
                        <div class="bg-white/5 border border-white/10 rounded-lg p-3 flex justify-between items-center cursor-pointer hover:bg-white/10 transition-colors">
                            <span class="text-white/80 text-sm">Hoy, 14:30 hs</span>
                            <span class="text-lightblue text-xs font-bold uppercase">Reservar</span>
                        </div>
                        <div class="bg-white/5 border border-white/10 rounded-lg p-3 flex justify-between items-center cursor-pointer hover:bg-white/10 transition-colors">
                            <span class="text-white/80 text-sm">Mañana, 09:00 hs</span>
                            <span class="text-lightblue text-xs font-bold uppercase">Reservar</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white border-b border-lightblue/30">
        <div class="max-w-7xl mx-auto px-6 sm:px-10 py-6 grid grid-cols-2 md:grid-cols-4 gap-6 divide-x divide-gray-100 text-center">
            <div class="px-4">
                <div class="text-2xl font-serif text-charcoal mb-1">+50</div>
                <div class="text-xs font-bold text-slate uppercase tracking-wider">Especialistas</div>
            </div>
            <div class="px-4">
                <div class="text-2xl font-serif text-charcoal mb-1">100%</div>
                <div class="text-xs font-bold text-slate uppercase tracking-wider">Gestión Online</div>
            </div>
            <div class="px-4">
                <div class="text-2xl font-serif text-charcoal mb-1">24/7</div>
                <div class="text-xs font-bold text-slate uppercase tracking-wider">Disponibilidad</div>
            </div>
            <div class="px-4">
                <div class="text-2xl font-serif text-charcoal mb-1">+10k</div>
                <div class="text-xs font-bold text-slate uppercase tracking-wider">Pacientes Felices</div>
            </div>
        </div>
    </div>

    <div id="especialidades" class="py-20 px-6 sm:px-10 max-w-7xl mx-auto flex-grow">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <h2 class="font-serif text-4xl text-charcoal mb-4">Especialidades Médicas</h2>
            <p class="text-slate text-base">Contamos con un equipo interdisciplinario preparado para brindarte la mejor atención. Encontrá tu especialista ideal.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">

            <a href="../views/Medicos.php?especialidad=cardiologia" class="bg-white border border-gray-200 rounded-2xl p-8 text-center cursor-pointer hover:border-lightblue hover:shadow-xl transition-all group block relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-lightblue transform -translate-x-full group-hover:translate-x-0 transition-transform duration-300"></div>
                <div class="w-14 h-14 mx-auto bg-ghost text-slate rounded-xl flex items-center justify-center mb-5 group-hover:bg-charcoal group-hover:text-white transition-colors duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <div class="font-bold text-charcoal text-base mb-1">Cardiología</div>
                <div class="text-slate text-xs font-medium uppercase tracking-wider">Ver médicos</div>
            </a>

            <a href="../views/Medicos.php?especialidad=clinica_general" class="bg-white border border-gray-200 rounded-2xl p-8 text-center cursor-pointer hover:border-lightblue hover:shadow-xl transition-all group block relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-lightblue transform -translate-x-full group-hover:translate-x-0 transition-transform duration-300"></div>
                <div class="w-14 h-14 mx-auto bg-ghost text-slate rounded-xl flex items-center justify-center mb-5 group-hover:bg-charcoal group-hover:text-white transition-colors duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="font-bold text-charcoal text-base mb-1">Clínica General</div>
                <div class="text-slate text-xs font-medium uppercase tracking-wider">Ver médicos</div>
            </a>

            <a href="../views/Medicos.php?especialidad=pediatria" class="bg-white border border-gray-200 rounded-2xl p-8 text-center cursor-pointer hover:border-lightblue hover:shadow-xl transition-all group block relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-lightblue transform -translate-x-full group-hover:translate-x-0 transition-transform duration-300"></div>
                <div class="w-14 h-14 mx-auto bg-ghost text-slate rounded-xl flex items-center justify-center mb-5 group-hover:bg-charcoal group-hover:text-white transition-colors duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="font-bold text-charcoal text-base mb-1">Pediatría</div>
                <div class="text-slate text-xs font-medium uppercase tracking-wider">Ver médicos</div>
            </a>

            <a href="../views/Medicos.php?especialidad=dermatologia" class="bg-white border border-gray-200 rounded-2xl p-8 text-center cursor-pointer hover:border-lightblue hover:shadow-xl transition-all group block relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-lightblue transform -translate-x-full group-hover:translate-x-0 transition-transform duration-300"></div>
                <div class="w-14 h-14 mx-auto bg-ghost text-slate rounded-xl flex items-center justify-center mb-5 group-hover:bg-charcoal group-hover:text-white transition-colors duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                    </svg>
                </div>
                <div class="font-bold text-charcoal text-base mb-1">Dermatología</div>
                <div class="text-slate text-xs font-medium uppercase tracking-wider">Ver médicos</div>
            </a>

            <a href="../views/Medicos.php?especialidad=traumatologia" class="bg-white border border-gray-200 rounded-2xl p-8 text-center cursor-pointer hover:border-lightblue hover:shadow-xl transition-all group block relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-lightblue transform -translate-x-full group-hover:translate-x-0 transition-transform duration-300"></div>
                <div class="w-14 h-14 mx-auto bg-ghost text-slate rounded-xl flex items-center justify-center mb-5 group-hover:bg-charcoal group-hover:text-white transition-colors duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <div class="font-bold text-charcoal text-base mb-1">Traumatología</div>
                <div class="text-slate text-xs font-medium uppercase tracking-wider">Ver médicos</div>
            </a>

        </div>

        <div class="mt-14 text-center">
            <a href="../views/Medicos.php" class="inline-flex items-center justify-center gap-2 bg-ghost border border-gray-200 text-charcoal hover:bg-gray-100 px-8 py-3.5 rounded-xl font-bold transition-colors shadow-sm">
                Ver Directorio Médico Completo
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>

    <footer class="bg-charcoal text-white pt-16 pb-8 border-t-[4px] border-lightblue mt-auto">
        <div class="max-w-7xl mx-auto px-6 sm:px-10 grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">
            <div class="md:col-span-2">
                <div class="flex items-center gap-2 font-serif text-2xl mb-4">
                    <span class="text-lightblue">✚</span> MediTurnos
                </div>
                <p class="text-white/60 text-sm leading-relaxed max-w-sm">
                    Revolucionando el acceso a la salud. Gestioná tus turnos de forma inteligente, rápida y segura desde cualquier dispositivo.
                </p>
            </div>
            <div>
                <h4 class="font-bold uppercase tracking-wider text-xs mb-4 text-lightblue">Enlaces Rápidos</h4>
                <ul class="space-y-2 text-sm text-white/70">
                    <li><a href="#especialidades" class="hover:text-white transition-colors">Especialidades</a></li>
                    <li><a href="../views/Medicos.php" class="hover:text-white transition-colors">Profesionales</a></li>
                    <li><a href="../views/Login.php" class="hover:text-white transition-colors">Portal del Paciente</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold uppercase tracking-wider text-xs mb-4 text-lightblue">Soporte</h4>
                <ul class="space-y-2 text-sm text-white/70">
                    <li><span class="block">📞 0800-123-MEDICO</span></li>
                    <li><span class="block">✉️ soporte@mediturnos.com.ar</span></li>
                    <li><span class="block mt-4 text-xs">Lunes a Viernes de 08:00 a 20:00hs</span></li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-6 sm:px-10 border-t border-white/10 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-white/50">
            <div>&copy; <?= date('Y') ?> MediTurnos. Todos los derechos reservados.</div>
            <div class="flex gap-4">
                <a href="#" class="hover:text-white transition-colors">Términos y Condiciones</a>
                <a href="#" class="hover:text-white transition-colors">Políticas de Privacidad</a>
            </div>
        </div>
    </footer>

</body>

</html>