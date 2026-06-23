<?php
define('SECCION', 'stats');
require_once '../controllers/AdminController.php';
require_once 'layout/menuAdmin.php';
require_once '../config/helpers.php';
$titulo = 'Dashboard Administrativo — MediTurnos';
$stats = [];

foreach ($turnosPorEstado as $e) {
    $stats[strtolower($e['estado'])] = $e['total'];
}
$totalesConsolidados = array_sum($stats);
?>
<!DOCTYPE html>
<html lang="es">

<?php require_once 'layout/head.php'; ?>

<body class="bg-[#F8FAFC] font-sans text-charcoal min-h-screen flex flex-col">

    <main class="flex-grow p-6 sm:p-10 max-w-7xl mx-auto w-full">

        <div class="mb-8">
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-white border border-gray-200 text-slate text-[0.65rem] font-bold uppercase tracking-widest mb-3 shadow-sm">
                <svg class="w-3.5 h-3.5 text-slate" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2"></path>
                </svg>
                Panel de Control Global
            </div>
            <h1 class="font-serif text-3xl md:text-4xl text-charcoal tracking-tight mb-1">Métricas y Rendimiento</h1>
            <p class="text-slate text-sm">Monitoreá el volumen de pacientes, actividad del staff médico y distribución de coberturas.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

            <div class="bg-white border border-gray-200/80 rounded-2xl p-6 shadow-sm flex items-center justify-between group">
                <div>
                    <span class="text-[0.65rem] font-bold text-slate uppercase tracking-widest block mb-1">Pacientes Totales</span>
                    <span class="font-serif text-3xl text-charcoal tracking-tight block font-bold">
                        <?= $pacientesTotales ?? (isset($usuariosTotales) ? $usuariosTotales : 0) ?>
                    </span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-ghost flex items-center justify-center text-slate">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>

            <div class="bg-white border border-gray-200/80 rounded-2xl p-6 shadow-sm flex items-center justify-between group">
                <div>
                    <span class="text-[0.65rem] font-bold text-slate uppercase tracking-widest block mb-1">Cuerpo Médico</span>
                    <span class="font-serif text-3xl text-charcoal tracking-tight block font-bold">
                        <?= $medicosTotales ?? 0 ?>
                    </span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-ghost flex items-center justify-center text-slate">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
            </div>

            <div class="bg-white border border-gray-200/80 rounded-2xl p-6 shadow-sm flex items-center justify-between group">
                <div>
                    <span class="text-[0.65rem] font-bold text-slate uppercase tracking-widest block mb-1">Gestión de Turnos</span>
                    <span class="font-serif text-3xl text-charcoal tracking-tight block font-bold">
                        <?= $totalesConsolidados ?>
                    </span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-ghost flex items-center justify-center text-slate">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>

            <div class="bg-white border border-gray-200/80 rounded-2xl p-6 shadow-sm flex items-center justify-between group">
                <div>
                    <span class="text-[0.65rem] font-bold text-slate uppercase tracking-widest block mb-1">Citas Confirmadas</span>
                    <span class="font-serif text-3xl text-[#10b981] tracking-tight block font-bold">
                        <?= $stats['confirmado'] ?? 0 ?>
                    </span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="w-6 h-6 mx-auto mt-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>

        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">

            <div class="bg-white rounded-2xl border border-gray-200/80 p-6 shadow-sm lg:col-span-7 flex flex-col justify-between">
                <div>
                    <h3 class="font-serif text-xl text-charcoal tracking-tight mb-1">Volumen de turnos por estado</h3>
                    <p class="text-xs text-slate mb-5">Estado operacional de las reservas consolidadas.</p>
                </div>

                <div class="space-y-4">
                    <?php foreach ($turnosPorEstado as $e):
                        $estadoKey = strtolower($e['estado']);
                        $colorData = match ($estadoKey) {
                            'pendiente'  => ['bg' => 'bg-amber-500', 'light' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200'],
                            'confirmado' => ['bg' => 'bg-emerald-500', 'light' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200'],
                            'cancelado'  => ['bg' => 'bg-rose-500', 'light' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200'],
                            'realizado'  => ['bg' => 'bg-blue-500', 'light' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200'],
                            default      => ['bg' => 'bg-slate-500', 'light' => 'bg-gray-50', 'text' => 'text-gray-700', 'border' => 'border-gray-200']
                        };

                        $porcentaje = $totalesConsolidados > 0 ? ($e['total'] / $totalesConsolidados) * 100 : 0;
                    ?>
                        <div>
                            <div class="flex justify-between items-center mb-1.5 text-xs font-bold">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded border <?= $colorData['light'] ?> <?= $colorData['border'] ?> <?= $colorData['text'] ?> uppercase tracking-wider text-[0.65rem]">
                                    <?= ucfirst(h($e['estado'])) ?>
                                </span>
                                <span class="text-charcoal font-mono text-sm"><?= $e['total'] ?></span>
                            </div>
                            <div class="w-full h-2 bg-ghost rounded-full overflow-hidden">
                                <div class="h-full <?= $colorData['bg'] ?> rounded-full transition-all" style="width: <?= $porcentaje ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="lg:col-span-5 space-y-4 flex flex-col justify-between">

                <?php if (!empty($medicoConMasTurnos)): ?>
                    <div class="bg-white rounded-2xl border border-gray-200/80 p-5 shadow-sm flex items-center gap-4 flex-1">
                        <div class="w-12 h-12 rounded-xl bg-charcoal text-white flex items-center justify-center font-serif text-lg">🏆</div>
                        <div>
                            <span class="text-[0.62rem] font-bold text-slate uppercase tracking-widest block mb-0.5">Médico con Mayor Demanda</span>
                            <h4 class="text-base font-bold text-charcoal">Dr/a. <?= h($medicoConMasTurnos['nombre'] ?? '') . ' ' . h($medicoConMasTurnos['apellido'] ?? '') ?></h4>
                            <div class="text-xs text-slate mt-1">
                                Actividad: <strong class="text-charcoal font-mono"><?= $medicoConMasTurnos['total_turnos'] ?? 0 ?></strong> consultas asignadas.
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($especialidadDemandada)): ?>
                    <div class="bg-white rounded-2xl border border-gray-200/80 p-5 shadow-sm flex items-center gap-4 flex-1">
                        <div class="w-12 h-12 rounded-xl bg-lightblue/40 text-charcoal flex items-center justify-center text-lg">📈</div>
                        <div>
                            <span class="text-[0.62rem] font-bold text-slate uppercase tracking-widest block mb-0.5">Especialidad Líder</span>
                            <h4 class="text-base font-bold text-charcoal"><?= h($especialidadDemandada['nombre'] ?? '') ?></h4>
                            <div class="text-xs text-slate mt-1">
                                Reservas: <strong class="text-charcoal font-mono"><?= h($especialidadDemandada['total_turnos'] ?? 0) ?></strong> asignaciones activas.
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

        </div>

        <?php if (!empty($listadoObraSocialTurnos)): ?>
            <div class="bg-white rounded-2xl border border-gray-200/80 overflow-hidden shadow-sm">
                <div class="bg-ghost/60 px-6 py-4 border-b border-gray-100">
                    <h3 class="font-serif text-lg text-charcoal tracking-tight">Distribución por cobertura médica</h3>
                    <p class="text-xs text-slate mt-0.5">Mapeo del total de turnos procesados por prestadora u obra social.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white border-b border-gray-100">
                                <th class="py-3.5 px-6 text-[0.65rem] font-bold text-slate uppercase tracking-widest bg-white">Entidad Médica / Obra Social</th>
                                <th class="py-3.5 px-6 text-[0.65rem] font-bold text-slate uppercase tracking-widest bg-white text-right">Volumen de Turnos</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 bg-white">
                            <?php foreach ($listadoObraSocialTurnos as $os): ?>
                                <tr class="hover:bg-ghost/30 transition-colors group">
                                    <td class="py-4 px-6 text-sm font-bold text-charcoal flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 bg-slate rounded-full"></span>
                                        <?= h($os['obra_social']) ?>
                                    </td>
                                    <td class="py-4 px-6 text-sm font-mono font-bold text-charcoal text-right">
                                        <?= $os['total_turnos'] ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

    </main>
</body>

</html>