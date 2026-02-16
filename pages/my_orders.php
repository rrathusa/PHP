<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user'])) {
  header("Location: /ProjetPHP/pages/login.php");
  exit;
}

$pdo = db();
$userId = (int)$_SESSION['user']['id'];

$orders = $pdo->prepare("SELECT id, total, status, created_at FROM orders WHERE user_id=? ORDER BY id DESC");
$orders->execute([$userId]);
$orders = $orders->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../includes/header.php';
?>

<section class="hero" style="padding:40px 0 10px;">
  <div class="container">
    <h1 style="font-size:44px;">Mes commandes</h1>
    <p>Historique de tes achats.</p>
  </div>
</section>

<section class="section" style="padding-top:0;">
  <div class="container">

    <?php if (empty($orders)): ?>
      <p>Aucune commande pour le moment.</p>
      <p><a class="btn gold" href="/ProjetPHP/pages/catalog.php">Découvrir les parfums</a></p>
    <?php else: ?>
      <table class="table">
        <tr><th>#</th><th>Total</th><th>Statut</th><th>Date</th><th>Détails</th></tr>
        <?php foreach ($orders as $o): ?>
          <tr>
            <td><?= (int)$o['id'] ?></td>
            <td><?= number_format((float)$o['total'], 2) ?> €</td>
            <td><?= htmlspecialchars($o['status']) ?></td>
            <td><?= htmlspecialchars($o['created_at']) ?></td>
            <td><a class="btn secondary" href="/ProjetPHP/pages/order_success.php?id=<?= (int)$o['id'] ?>">Voir</a></td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>

  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
