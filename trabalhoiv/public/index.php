<?php
require_once __DIR__ . '/../config/db.php';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';

if (empty($_SESSION['usuario_id'])) { header('Location: login.php'); exit; }
$uid = $_SESSION['usuario_id'];

$search = trim($_GET['search'] ?? '');
$status = $_GET['status'] ?? 'all';
$where = ' WHERE usuario_id = :uid';
$params = ['uid' => $uid];
if ($status === 'pending') { $where .= ' AND concluida = 0'; }
if ($status === 'done')   { $where .= ' AND concluida = 1'; }
if ($search !== '') {
    $where .= ' AND (titulo LIKE :s OR descricao LIKE :s)';
    $params['s'] = "%$search%";
}

$stmt = $pdo->prepare('SELECT id, titulo, descricao, concluida, data_criacao FROM tarefas' . $where . ' ORDER BY data_criacao DESC');
$stmt->execute($params);
$tarefas = $stmt->fetchAll();

$cnt = $pdo->prepare('SELECT SUM(concluida = 0) AS pendentes, SUM(concluida = 1) AS concluidas 
                      FROM tarefas WHERE usuario_id = ?');
$cnt->execute([$uid]);
$counts = $cnt->fetch();
$pendentes = (int)($counts['pendentes'] ?? 0);
$concluidas = (int)($counts['concluidas'] ?? 0);
?>

<h2>Bem-vindo, <?=htmlspecialchars($_SESSION['usuario_nome'])?></h2>

<div style="display:flex;gap:20px;flex-wrap:wrap;">

  <div style="min-width:220px;padding:10px;background:#fff;border-radius:8px;">
    <h3>Resumo</h3>
    <p>Pendentes: <strong><?= $pendentes ?></strong></p>
    <p>Concluídas: <strong><?= $concluidas ?></strong></p>

    <!-- ===== GRÁFICO  ===== -->
    <div style="width:180px; margin-top:12px;">
      <canvas id="graficoTarefas" width="180" height="180"></canvas>
    </div>
    <!-- =========================== -->
  </div>

  <div style="flex:1;min-width:300px;background:#fff;padding:10px;border-radius:8px;">

    <form method="get" style="display:flex;gap:8px;align-items:center;">
      <input type="search" name="search" placeholder="Buscar" value="<?=htmlspecialchars($search)?>">
      <select name="status">
        <option value="all"     <?= $status=='all' ? 'selected':'' ?>>Todas</option>
        <option value="pending" <?= $status=='pending' ? 'selected':'' ?>>Pendentes</option>
        <option value="done"    <?= $status=='done' ? 'selected':'' ?>>Concluídas</option>
      </select>
      <button type="submit">Filtrar</button>
    </form>

    <a href="criar.php" style="display:inline-block;margin:10px 0;">+ Nova Tarefa</a>

    <ul style="list-style:none;padding:0;">
      <?php if (count($tarefas) === 0): ?>
        <li>Nenhuma tarefa encontrada.</li>
      <?php else: foreach ($tarefas as $t): ?>
        <li style="background:#fafafa;margin-bottom:8px;padding:8px;border-radius:6px;">
          <div style="display:flex;justify-content:space-between;align-items:center;">
            
            <div>
              <strong><?=htmlspecialchars($t['titulo'])?></strong>
              <div style="font-size:0.9em;color:#666;"><?=htmlspecialchars($t['descricao'])?></div>
            </div>

            <div>
              <?php if (!$t['concluida']): ?>
                <a href="concluir.php?id=<?=$t['id']?>">Concluir</a>
              <?php else: ?>
                <span style="color:green;">Concluída</span>
              <?php endif; ?>
              <a href="editar.php?id=<?=$t['id']?>">Editar</a>
              <a href="excluir.php?id=<?=$t['id']?>" onclick="return confirm('Remover?')">Excluir</a>
            </div>

          </div>
        </li>
      <?php endforeach; endif; ?>
    </ul>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
  const pendentes  = Number(<?= $pendentes ?>);
  const concluidas = Number(<?= $concluidas ?>);

  const canvas = document.getElementById('graficoTarefas');

  if (canvas) {
    const ctx = canvas.getContext('2d');

    if (window._graficoTarefas) {
      window._graficoTarefas.destroy();
    }

    window._graficoTarefas = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ['Pendentes', 'Concluídas'],
        datasets: [{
          data: [pendentes, concluidas],
         backgroundColor: ['red', 'green'],
          borderWidth: 0
        }]
      },
      options: {
        responsive: false,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: true,
            position: 'bottom',
            labels: { boxWidth: 12 }
          }
        }
      }
    });
  }
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>


