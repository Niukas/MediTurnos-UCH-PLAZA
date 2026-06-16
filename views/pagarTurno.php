<?php
session_start();
require_once '../config/helpers.php';

define('SECCION', 'sacarTurno');
$titulo = "Abonar Turno — MediTurnos";

// Datos de la URL
$id_turno    = isset($_GET['id_turno']) ? (int)$_GET['id_turno'] : 0;
$monto       = isset($_GET['monto']) ? (float)$_GET['monto'] : false;

// Estos datos son opcionales (solo vienen al sacar un turno nuevo, no desde el Panel)
$cobertura   = isset($_GET['cobertura']) ? (float)$_GET['cobertura'] : 0;
$precio_base = isset($_GET['precio_base']) ? (float)$_GET['precio_base'] : $monto;

if (!$id_turno || $monto === false) {
    header('Location: Panel.php');
    exit;
}

require_once 'layout/head.php';
?>

<body class="bg-ghost min-h-screen flex flex-col font-sans text-charcoal">

    <?php require_once 'layout/menuPaciente.php'; ?>

    <main class="flex-grow p-6 sm:p-10 max-w-2xl mx-auto w-full">

        <div class="mb-8">
            <h1 class="text-3xl font-serif text-charcoal">Completar Pago</h1>
            <p class="text-slate mt-2">Estás a un paso de confirmar tu turno. Por favor, verificá el detalle y completá el pago.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-lightblue/50 overflow-hidden">
            <div class="p-6 sm:p-8">
                <h2 class="text-xl font-bold text-charcoal mb-6 border-b border-gray-100 pb-4">Detalle de facturación</h2>

                <div class="bg-ghost rounded-xl p-6 mb-8 border border-lightblue/30">

                    <?php if ($cobertura > 0 && $precio_base > 0): ?>
                        <div class="flex justify-between items-center mb-3 text-slate">
                            <span>Valor de la consulta</span>
                            <span class="font-medium">$<?= number_format($precio_base, 2, ',', '.') ?></span>
                        </div>

                        <div class="flex justify-between items-center mb-3 text-slate">
                            <span>Cobertura Obra Social (<?= htmlspecialchars($cobertura) ?>%)</span>
                            <span class="text-green-600 font-medium">-$<?= number_format($precio_base * ($cobertura / 100), 2, ',', '.') ?></span>
                        </div>
                        <div class="border-t border-lightblue/50 my-4"></div>
                    <?php endif; ?>

                    <div class="flex justify-between items-center font-black text-2xl text-charcoal">
                        <span>Total a Pagar</span>
                        <span>$<?= number_format($monto, 2, ',', '.') ?></span>
                    </div>
                </div>

                <form method="POST" action="../controllers/PacienteController.php">
                    <input type="hidden" name="accion" value="confirmarPago">
                    <input type="hidden" name="id_turno" value="<?= htmlspecialchars($id_turno) ?>">

                    <div class="mb-5">
                        <label class="block text-sm font-bold text-charcoal mb-2">Método de Pago</label>
                        <select name="metodo_pago" class="w-full bg-ghost border border-lightblue/50 text-charcoal text-sm rounded-xl focus:ring-charcoal focus:border-charcoal block p-3 outline-none">
                            <option value="tarjeta_credito">Tarjeta de Crédito</option>
                            <option value="tarjeta_debito">Tarjeta de Débito</option>
                            <option value="mercado_pago">Mercado Pago</option>
                        </select>
                    </div>

                    <div class="mb-8">
                        <label class="block text-sm font-bold text-charcoal mb-2">Número de Tarjeta (Simulación)</label>
                        <input type="text" placeholder="**** **** **** ****" required
                            class="w-full bg-ghost border border-lightblue/50 text-charcoal text-sm rounded-xl focus:ring-charcoal focus:border-charcoal block p-3 outline-none transition-colors">
                    </div>

                    <div class="flex gap-4">
                        <a href="Panel.php?mensaje=pago_pendiente" class="w-1/3 bg-ghost hover:bg-lightblue/30 text-slate font-bold py-3 px-4 border border-lightblue/50 rounded-xl transition-colors text-center text-sm flex items-center justify-center">
                            Pagar luego
                        </a>
                        <button type="submit" class="w-2/3 bg-charcoal hover:bg-slate text-white font-bold py-3 px-4 rounded-xl transition-colors shadow-sm text-sm">
                            Pagar ahora
                        </button>
                    </div>
            </div>
            </form>
        </div>
        </div>
    </main>

</body>

</html>