<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/nav.php';

$pdo = db();

$q = trim($_GET['q'] ?? '');
$max = trim($_GET['max'] ?? '');

$sql = "SELECT id, nom, prix, description, image FROM parfums WHERE 1";
$params = [];

if ($q !== '') {
  $sql .= " AND nom LIKE ?";
  $params[] = "%$q%";
}
if ($max !== '' && is_numeric($max)) {
  $sql .= " AND prix <= ?";
  $params[] = (float)$max;
}

$sql .= " ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$parfums = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<section class="hero">
  <h1>Senteurs d’exception</h1>
  <p>Explore une sélection élégante • blanc, vert & doré.</p>
</section>

<div class="card filters">
  <form method="GET" style="display:flex; gap:12px; flex-wrap:wrap; width:100%;">
    <div class="input">
      <label>Recherche (nom)</label>
      <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Ex: Dior">
    </div>

    <div class="input" style="max-width:220px;">
      <label>Prix maximum</label>
      <input type="number" step="0.01" name="max" value="<?= htmlspecialchars($max) ?>" placeholder="Ex: 150">
    </div>

    <div style="display:flex; gap:10px; align-items:flex-end;">
      <button class="btn btn-green" type="submit">Appliquer</button>
      <a class="btn" href="/ProjetPHP/pages/catalog.php">Réinitialiser</a>
    </div>
  </form>
</div>

<p class="muted"><?= count($parfums) ?> produit(s)</p>

<div class="grid">
  <?php foreach ($parfums as $p): ?>
    <?php
      $img = trim($p['image'] ?? '');
      $imgPath = $img !== '' ? "/ProjetPHP/assets/images/parfums/" . rawurlencode($img) : "";
    ?>
    <div class="card product">
      <div class="img">
        <?php if ($imgPath !== ""): ?>
          <img src="<?= $imgPath ?>" alt="<?= htmlspecialchars($p['nom']) ?>">
        <?php else: ?>
          <span class="muted">Pas d’image</span>
        <?php endif; ?>
      </div>

      <div class="body">
        <h3><?= htmlspecialchars($p['nom']) ?></h3>
        <div class="price"><?= number_format((float)$p['prix'], 2) ?> €</div>
        <div class="desc"><?= htmlspecialchars($p['description']) ?></div>

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
          <a class="btn" href="/ProjetPHP/pages/product.php?id=<?= (int)$p['id'] ?>">Voir</a>
          <a class="btn btn-gold" href="/ProjetPHP/pages/add_to_cart.php?id=<?= (int)$p['id'] ?>">Ajouter</a>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
