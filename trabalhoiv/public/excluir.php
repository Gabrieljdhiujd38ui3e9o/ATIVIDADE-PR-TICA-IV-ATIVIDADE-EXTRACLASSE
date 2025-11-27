<?php
require_once __DIR__ . '/../config/db.php';
session_start();
if (empty($_SESSION['usuario_id'])) { header('Location: login.php'); exit; }
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('DELETE FROM tarefas WHERE id = ? AND usuario_id = ?');
$stmt->execute([$id, $_SESSION['usuario_id']]);
header('Location: index.php');
exit;
