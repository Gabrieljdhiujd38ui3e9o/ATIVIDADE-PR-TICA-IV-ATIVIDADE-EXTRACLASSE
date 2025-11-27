<?php
require_once __DIR__ . '/../config/db.php';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $senha2 = $_POST['senha2'] ?? '';

    if ($nome === '' || $email === '' || $senha === '') {
        $msg = 'Preencha todos os campos.';
    } elseif ($senha !== $senha2) {
        $msg = 'As senhas não conferem.';
    } else {
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)');
        try {
            $stmt->execute([$nome, $email, $hash]);
            header('Location: login.php');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) $msg = 'Email já cadastrado.';
            else $msg = 'Erro ao cadastrar usuário.';
        }
    }
}
?>

<h2>Registrar</h2>
<?php if($msg): ?><p style="color:red;"><?=htmlspecialchars($msg)?></p><?php endif; ?>

<form method="post" style="max-width:420px;">
    <label>Nome:</label>
    <input type="text" name="nome" required>

    <label>Email:</label>
    <input type="email" name="email" required>

    <label>Senha:</label>
    <input type="password" name="senha" required>

    <label>Confirmar Senha:</label>
    <input type="password" name="senha2" required>

    <button type="submit">Registrar</button>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
