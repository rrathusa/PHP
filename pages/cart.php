<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$pdo = db();

// 1) Initialiser le panier correctement (évite ton erreur)
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
  $_SESSION['cart'] = [];
}

// 2) Actions panier
$action = $_GET['action'] ?? null;
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($action === 'clear') {
  $_SESSION['cart'] = [];
  header("Location: /ProjetPHP/pages/cart.php");
  exit;
}

if ($id > 0) {
  if ($action === 'add' || $action === 'inc') {
    if (!isset($_SESSION['cart'][$id])) {
      $_SESSION['cart'][$id] = 0;
    }
    $_SESSION['cart'][$id] += 1;
    header("Location: /ProjetPHP/pages/cart.php");
    exit;
  }

  if ($action === 'dec') {
    if (isset($_SESSION['cart'][$id])) {
      $_SESSION['cart'][$id] -= 1;
      if ($_SESSION['cart'][$id] <= 0) {
        unset($_SESSION['cart'][$id]);
      }
    }
    header("Location: /ProjetPHP/pages/cart.php");
    exit;
  }

  if ($action === 'remove') {
    unset($_SESSION['cart'][$id]);
    header("Location: /ProjetPHP/pages/cart.php");
    exit;
  }
}

// 3) Lire panier
$cart = $_SESSION['cart'];
$items = [];
$total = 0;

if (!empty($cart)) {
  $ids = array_keys($cart);
  $placeholders = implode(',', array_fill(0, count($ids), '?'));

  $stmt = $pdo->prepare("SELECT id, nom, prix FROM parfums WHERE id IN ($placeholders)");
  $stmt->execute($ids);
  $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

  foreach ($items as &$it) {
    $qty = (int)$cart[$it['id']];
    $it['qty'] = $qty;
    $it['subtotal'] = $qty * (float)$it['prix'];
    $total += $it['subtotal'];
  }
  unset($it);
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="hero" style="padding:40px 0 10px;">
  <div class="container">
    <h1 style="font-size:44px;">Mon panier</h1>
    <p>Ajoute plusieurs parfums, gère les quantités, supprime ce que tu veux.</p>
  </div>
</section>

<section class="section" style="padding-top:0;">
  <div class="container">

    <?php if (empty($cart)): ?>
      <p>Ton panier est vide.</p>
      <p>
        <a class="btn gold" href="/ProjetPHP/pages/catalog.php">Retour au catalogue</a>
      </p>
    <?php else: ?>

      <table class="table">
        <thead>
          <tr>
            <th>Produit</th>
            <th>Prix</th>
            <th>Quantité</th>
            <th>Sous-total</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody>
          <?php foreach ($items as $it): ?>
            <tr>
              <td><?= htmlspecialchars($it['nom']) ?></td>
              <td><?= number_format((float)$it['prix'], 2) ?> €</td>

              <td>
                <a class="btn secondary" href="/ProjetPHP/pages/cart.php?action=dec&id=<?= (int)$it['id'] ?>">-</a>
                <span style="display:inline-block; min-width:34px; text-align:center; font-weight:800;">
                  <?= (int)$it['qty'] ?>
                </span>
                <a class="btn secondary" href="/ProjetPHP/pages/cart.php?action=inc&id=<?= (int)$it['id'] ?>">+</a>
              </td>

              <td><?= number_format((float)$it['subtotal'], 2) ?> €</td>

              <td>
                <a class="btn" href="/ProjetPHP/pages/cart.php?action=remove&id=<?= (int)$it['id'] ?>">Supprimer</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>

        <tfoot>
          <tr>
            <td colspan="3">Total</td>
            <td colspan="2"><?= number_format((float)$total, 2) ?> €</td>
          </tr>
        </tfoot>
      </table>

      <p style="display:flex; gap:10px; margin-top:14px; flex-wrap:wrap;">
        <a class="btn secondary" href="/ProjetPHP/pages/catalog.php">Continuer les achats</a>
        <a class="btn gold" href="/ProjetPHP/pages/checkout.php">Commander</a>
        <a class="btn" href="/ProjetPHP/pages/cart.php?action=clear">Vider le panier</a>
      </p>

    <?php endif; ?>

  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
