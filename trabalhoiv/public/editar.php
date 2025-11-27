<?php
require_once __DIR__ . '/../config/db.php';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';

if (empty($_SESSION['usuario_id'])) { header('Location: login.php'); exit; }
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM tarefas WHERE id = ? AND usuario_id = ?');
$stmt->execute([$id, $_SESSION['usuario_id']]);
$tarefa = $stmt->fetch();
if (!$tarefa) { header('Location: index.php'); exit; }

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    if ($titulo === '') $msg = 'Título obrigatório.';
    else {
        $u = $pdo->prepare('UPDATE tarefas SET titulo = ?, descricao = ? WHERE id = ? AND usuario_id = ?');
        $u->execute([$titulo, $descricao, $id, $_SESSION['usuario_id']]);
        header('Location: index.php');
        exit;
    }
}
?>

<h2>Editar Tarefa</h2>
<?php if($msg): ?><p style="color:red;"><?=htmlspecialchars($msg)?></p><?php endif; ?>

<form method="post" style="max-width:600px;">
    <label>Título:</label>
    <input type="text" name="titulo" value="<?=htmlspecialchars($tarefa['titulo'])?>" required>

    <label>Descrição:</label>
    <textarea name="descricao"><?=htmlspecialchars($tarefa['descricao'])?></textarea>

    <button type="submit">Salvar</button>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
