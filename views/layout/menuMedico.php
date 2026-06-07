<nav>
    <span>MediTurnos — Médico | Hola, <?= $_SESSION['usuario_nombre'] ?></span>
    <a href="panelMedico.php">Mis turnos</a>
    <form method="POST" action="../controllers/AuthController.php">
        <input type="hidden" name="accion" value="logout">
        <button type="submit">Cerrar sesión</button>
    </form>
</nav>