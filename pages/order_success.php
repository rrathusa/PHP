<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user'])) {
  header("Location: /ProjetPHP/pages/login.php");
  exit;
}

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($orderId <= 0) {
  header("Location: /ProjetPHP/");
  exit;
}

$pdo = db();
$userId = (int)$_SESSION['user']['id'];

$order = $pdo->prepare("SELECT * FROM orders WHERE id=? AND user_id=?");
$order->execute([$orderId, $userId]);
$order = $order->fetch();

if (!$order) {
  die("Commande introuvable.");
}

$inv = $pdo->prepare("SELECT * FROM invoice WHERE order_id=? AND user_id=?");
$inv->execute([$orderId, $userId]);
$inv = $inv->fetch();

$items = $pdo->prepare("
  SELECT oi.quantity, oi.unit_price, p.nom
  FROM order_items oi
  JOIN parfums p ON p.id = oi.parfum_id
  WHERE oi.order_id = ?
");
$items->execute([$orderId]);
$items = $items->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../includes/header.php';
?>

<section class="hero" style="padding:40px 0 10px;">
  <div class="container">
    <h1 style="font-size:44px;">Commande validée ✅</h1>
    <p>Merci ! Ta commande a été enregistrée.</p>
  </div>
</section>

<section class="section" style="padding-top:0;">
  <div class="container">
    <p><strong>N° commande :</strong> <?= (int)$order['id'] ?></p>
    <p><strong>Total :</strong> <?= number_format((float)$order['total'], 2) ?> €</p>
    <p><strong>Date :</strong> <?= htmlspecialchars($order['created_at']) ?></p>

    <h3>Détails</h3>
    <table class="table">
      <tr><th>Produit</th><th>Prix</th><th>Qté</th></tr>
      <?php foreach ($items as $it): ?>
        <tr>
          <td><?= htmlspecialchars($it['nom']) ?></td>
          <td><?= number_format((float)$it['unit_price'], 2) ?> €</td>
          <td><?= (int)$it['quantity'] ?></td>
        </tr>
      <?php endforeach; ?>
    </table>

    <?php if ($inv): ?>
      <h3 style="margin-top:18px;">Facture</h3>
      <p>
        <strong>Adresse :</strong> <?= htmlspecialchars($inv['billing_address']) ?>,
        <?= htmlspecialchars($inv['city']) ?> <?= htmlspecialchars($inv['postal_code']) ?>
      </p>
      <p><strong>Montant :</strong> <?= number_format((float)$inv['amount'], 2) ?> €</p>
    <?php endif; ?>

    <p style="margin-top:14px;">
      <a class="btn gold" href="/ProjetPHP/pages/catalog.php">Retour au catalogue</a>
    </p>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
