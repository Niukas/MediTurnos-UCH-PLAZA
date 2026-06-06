<nav>
    <span>MediTurnos — Admin | Hola, <?= $_SESSION['usuario_nombre'] ?></span>
    <a href="dashboardAdmin.php">Stats</a>
    <a href="dashboardAdminMedicos.php">Médicos</a>
    <a href="dashboardAdminTurnos.php">Turnos</a>
    <form method="POST" action="../controllers/AuthController.php">
        <input type="hidden" name="accion" value="logout">
        <button type="submit">Cerrar sesión</button>
    </form>
</nav>