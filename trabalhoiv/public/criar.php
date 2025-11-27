<?php
require_once __DIR__ . '/../config/db.php';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';

if (empty($_SESSION['usuario_id'])) { header('Location: login.php'); exit; }
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    if ($titulo === '') $msg = 'Título obrigatório.';
    else {
        $stmt = $pdo->prepare('INSERT INTO tarefas (usuario_id, titulo, descricao) VALUES (?, ?, ?)');
        $stmt->execute([$_SESSION['usuario_id'], $titulo, $descricao]);
        header('Location: index.php');
        exit;
    }
}
?>

<h2>Nova Tarefa</h2>
<?php if($msg): ?><p style="color:red;"><?=htmlspecialchars($msg)?></p><?php endif; ?>

<form method="post" style="max-width:600px;">
    <label>Título:</label>
    <input type="text" name="titulo" required>

    <label>Descrição:</label>
    <textarea name="descricao"></textarea>

    <button type="submit">Criar</button>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
