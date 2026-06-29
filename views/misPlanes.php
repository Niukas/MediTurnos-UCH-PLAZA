<?php
define('SECCION', 'misPlanes');
require_once '../controllers/PacienteController.php';
require_once 'layout/menuPaciente.php';
require_once '../config/helpers.php';
$titulo = 'Mis Planes — MediTurnos';
?>
<!DOCTYPE html>
<html lang="es">
<?php require_once 'layout/head.php'; ?>
<body class="bg-ghost font-sans text-charcoal min-h-screen">
    <?php require_once 'layout/menuPaciente.php'; ?>
    <main class="p-10 max-w-2xl mx-auto">
        <h1 class="text-3xl font-serif mb-4">Mis Planes de Salud</h1>

        <?php
        $mensajes = [
            'exitoso' => ['bg' => 'bg-emerald-500/10', 'border' => 'border-emerald-500/20', 'text' => 'text-emerald-700', 'texto' => 'Plan asignado correctamente.', 'icon' => 'M5 13l4 4L19 7'],
            'error'   => ['bg' => 'bg-rose-500/10', 'border' => 'border-rose-500/20', 'text' => 'text-rose-700', 'texto' => 'Hubo un error al asignar el plan.', 'icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        ];
        $registro = $_GET['registro'] ?? null;
        if ($registro && isset($mensajes[$registro])): ?>
            <div class="<?= $mensajes[$registro]['bg'] ?> <?= $mensajes[$registro]['border'] ?> border rounded-xl px-4 py-3 mb-6 text-sm font-medium <?= $mensajes[$registro]['text'] ?> flex items-center gap-3 shadow-sm">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $mensajes[$registro]['icon'] ?>"></path>
                </svg>
                <span><?= $mensajes[$registro]['texto'] ?></span>
            </div>
        <?php endif; ?>

        <div class="bg-white p-6 rounded-xl border border-gray-200 mb-8">
            <h2 class="text-lg font-bold mb-4">Planes Asignados</h2>
            <?php if (empty($misPlanes)): ?>
                <p class="text-slate text-sm">No tenés planes registrados.</p>
            <?php else: ?>
                <ul class="space-y-2">
                    <?php foreach ($misPlanes as $p): ?>
                        <li class="p-3 bg-gray-50 rounded-lg text-sm">
                            <strong><?= h($p['obra_social']) ?></strong> - <?= h($p['nombre_plan']) ?> 
                            (Afiliado: <?= h($p['nro_afiliado']) ?>)
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($misPlanes)): ?>
        <div id="contenedor-boton-agregar" class="mb-8 text-center">
            <button id="boton-mostrar-form" class="bg-charcoal hover:bg-slate text-white font-bold py-3 px-6 rounded-xl text-sm transition-all shadow-md">
                Asignar Nuevo Plan
            </button>
        </div>
        <?php endif; ?>

        <div id="form-asignar-plan" class="bg-white p-6 rounded-2xl border border-gray-200 <?= !empty($misPlanes) ? 'hidden' : '' ?>">
            <h2 class="text-sm font-bold text-slate uppercase tracking-widest mb-5">Asignar Nuevo Plan</h2>
            <form method="POST" action="../controllers/PacienteController.php" class="space-y-4">
                <input type="hidden" name="accion" value="asignarPlan">
                
                <div>
                    <label class="block text-[0.65rem] font-bold text-slate uppercase tracking-widest mb-1.5">Obra Social</label>
                    <select id="select-os" required class="w-full px-4 py-3 bg-ghost border border-gray-200 rounded-xl text-sm text-charcoal focus:outline-none focus:border-slate appearance-none shadow-sm cursor-pointer">
                        <option value="">Seleccioná una obra social...</option>
                        <?php foreach ($listadoObraSociales as $os): ?>
                            <option value="<?= $os['id_obra_social'] ?>"><?= h($os['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-[0.65rem] font-bold text-slate uppercase tracking-widest mb-1.5">Plan</label>
                    <select name="id_plan" id="select-plan" required disabled class="w-full px-4 py-3 bg-ghost border border-gray-200 rounded-xl text-sm text-charcoal focus:outline-none focus:border-slate appearance-none shadow-sm cursor-pointer">
                        <option value="">Seleccioná primero una obra social...</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[0.65rem] font-bold text-slate uppercase tracking-widest mb-1.5">Número de Afiliado</label>
                    <input type="text" name="nro_afiliado" required placeholder="Ej: 123456789-01" 
                        class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm text-charcoal placeholder-gray-400 focus:outline-none focus:border-slate shadow-sm">
                </div>
                
                <button type="submit" class="w-full bg-charcoal hover:bg-slate text-white font-bold py-3.5 rounded-xl text-sm transition-all shadow-md active:scale-[0.99]">
                    Confirmar y Asignar Plan
                </button>
            </form>
        </div>
    </main>

    <script>
        const planes = <?= json_encode($listadoPlanesDisponibles) ?>;
        const selectOS = document.getElementById('select-os');
        const selectPlan = document.getElementById('select-plan');

        selectOS.addEventListener('change', function() {
            const osId = this.value;
            selectPlan.innerHTML = '<option value="">Seleccioná un plan...</option>';
            
            if (osId) {
                const planesFiltrados = planes.filter(p => p.id_obra_social == osId);
                planesFiltrados.forEach(p => {
                    const option = document.createElement('option');
                    option.value = p.id_plan;
                    option.text = p.nombre_plan + ' (Cobertura: ' + p.porcentaje_cobertura + '%)';
                    selectPlan.appendChild(option);
                });
                selectPlan.disabled = false;
            } else {
                selectPlan.disabled = true;
            }
        });

        const botonMostrar = document.getElementById('boton-mostrar-form');
        if (botonMostrar) {
            botonMostrar.addEventListener('click', function() {
                document.getElementById('form-asignar-plan').classList.remove('hidden');
                document.getElementById('contenedor-boton-agregar').classList.add('hidden');
            });
        }
    </script>
</body>
</html>
