<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user'])) {
  header("Location: /ProjetPHP/pages/login.php");
  exit;
}

$pdo = db();
$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
  header("Location: /ProjetPHP/pages/cart.php");
  exit;
}

$errors = [];
$address = trim($_POST['address'] ?? '');
$city = trim($_POST['city'] ?? '');
$postal = trim($_POST['postal'] ?? '');

function fetchCartItems(PDO $pdo, array $cart): array {
  $ids = array_keys($cart);
  $placeholders = implode(',', array_fill(0, count($ids), '?'));

  $sql = "
    SELECT p.id, p.nom, p.prix, COALESCE(s.quantity, 0) AS stock_qty
    FROM parfums p
    LEFT JOIN stock s ON s.parfum_id = p.id
    WHERE p.id IN ($placeholders)
  ";
  $stmt = $pdo->prepare($sql);
  $stmt->execute($ids);
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$items = fetchCartItems($pdo, $cart);
$total = 0;

foreach ($items as &$it) {
  $qty = (int)($cart[$it['id']] ?? 0);
  $it['qty'] = $qty;
  $it['subtotal'] = $qty * (float)$it['prix'];
  $total += $it['subtotal'];
}
unset($it);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if (mb_strlen($address) < 6) $errors[] = "Adresse invalide (min 6 caractères).";
  if (mb_strlen($city) < 2) $errors[] = "Ville invalide.";
  if (mb_strlen($postal) < 4) $errors[] = "Code postal invalide.";

  // Vérifier stock
  foreach ($items as $it) {
    if ($it['qty'] <= 0) $errors[] = "Quantité invalide pour {$it['nom']}.";
    if ($it['qty'] > (int)$it['stock_qty']) {
      $errors[] = "Stock insuffisant pour {$it['nom']} (stock: {$it['stock_qty']}).";
    }
  }

  if (empty($errors)) {
    $userId = (int)$_SESSION['user']['id'];

    try {
      $pdo->beginTransaction();

      // 1) créer la commande
      $stmt = $pdo->prepare("INSERT INTO orders (user_id, status, total) VALUES (?, 'paid', ?)");
      $stmt->execute([$userId, $total]);
      $orderId = (int)$pdo->lastInsertId();

      // 2) lignes de commande + décrément stock
      $insItem = $pdo->prepare("INSERT INTO order_items (order_id, parfum_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
      $decStock = $pdo->prepare("UPDATE stock SET quantity = quantity - ? WHERE parfum_id = ? AND quantity >= ?");

      foreach ($items as $it) {
        $qty = (int)$it['qty'];
        $price = (float)$it['prix'];

        $insItem->execute([$orderId, (int)$it['id'], $qty, $price]);

        // décrément stock (sécurisé)
        $decStock->execute([$qty, (int)$it['id'], $qty]);
        if ($decStock->rowCount() === 0) {
          throw new Exception("Stock insuffisant pendant la commande.");
        }
      }

      // 3) facture
      $inv = $pdo->prepare("
        INSERT INTO invoice (user_id, order_id, amount, billing_address, city, postal_code)
        VALUES (?, ?, ?, ?, ?, ?)
      ");
      $inv->execute([$userId, $orderId, $total, $address, $city, $postal]);

      $pdo->commit();

      // vider panier
      $_SESSION['cart'] = [];

      header("Location: /ProjetPHP/pages/order_success.php?id=" . $orderId);
      exit;

    } catch (Exception $e) {
      $pdo->rollBack();
      $errors[] = "Erreur commande : " . $e->getMessage();
    }
  }
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="hero" style="padding:40px 0 10px;">
  <div class="container">
    <h1 style="font-size:44px;">Commande</h1>
    <p>Adresse de facturation + validation stock.</p>
  </div>
</section>

<section class="section" style="padding-top:0;">
  <div class="container">

    <?php if (!empty($errors)): ?>
      <div style="border:1px solid #f0c; padding:12px; border-radius:12px; margin-bottom:14px;">
        <strong>Erreurs :</strong>
        <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
      </div>
    <?php endif; ?>

    <p><strong>Total :</strong> <?= number_format((float)$total, 2) ?> €</p>

    <table class="table">
      <tr>
        <th>Produit</th><th>Prix</th><th>Qté</th><th>Sous-total</th><th>Stock</th>
      </tr>
      <?php foreach ($items as $it): ?>
        <tr>
          <td><?= htmlspecialchars($it['nom']) ?></td>
          <td><?= number_format((float)$it['prix'], 2) ?> €</td>
          <td><?= (int)$it['qty'] ?></td>
          <td><?= number_format((float)$it['subtotal'], 2) ?> €</td>
          <td><?= (int)$it['stock_qty'] ?></td>
        </tr>
      <?php endforeach; ?>
    </table>

    <h3 style="margin-top:18px;">Adresse de facturation</h3>

    <form method="POST" style="max-width:650px;">
      <label>Adresse</label>
      <input class="input" type="text" name="address" value="<?= htmlspecialchars($address) ?>" required>

      <div style="height:12px;"></div>

      <label>Ville</label>
      <input class="input" type="text" name="city" value="<?= htmlspecialchars($city) ?>" required>

      <div style="height:12px;"></div>

      <label>Code postal</label>
      <input class="input" type="text" name="postal" value="<?= htmlspecialchars($postal) ?>" required>

      <div style="height:16px;"></div>

      <button class="btn gold" type="submit">Valider la commande</button>
      <a class="btn" href="/ProjetPHP/pages/cart.php" style="margin-left:10px;">Retour panier</a>
    </form>

  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
