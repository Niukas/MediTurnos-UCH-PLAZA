<nav>
    <span>MediTurnos | Hola, <?= $_SESSION['usuario_nombre'] ?></span>
    <a href="panel.php">Mis turnos</a>
    <a href="SacarTurno.php">Sacar turno</a>
    <form method="POST" action="../controllers/AuthController.php">
        <input type="hidden" name="accion" value="logout">
        <button type="submit">Cerrar sesión</button>
    </form>
</nav>