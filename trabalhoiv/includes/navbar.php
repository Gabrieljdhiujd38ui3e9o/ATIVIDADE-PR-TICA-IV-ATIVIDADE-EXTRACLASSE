<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<nav class="menu">
    <a href="index.php">Início</a>
    <?php if(isset($_SESSION['usuario_id'])): ?>
        <a href="criar.php">Nova Tarefa</a>
        <a href="logout.php">Sair</a>
    <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php">Registrar</a>
    <?php endif; ?>
</nav>
<hr>
