<?php
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

$db = db();

// --- Filtres ---
$q = trim($_GET['q'] ?? '');
$max = trim($_GET['max'] ?? '');

$sql = "SELECT id, nom, prix, description FROM parfums WHERE 1";
$params = [];

if ($q !== '') {
  $sql .= " AND nom LIKE :q";
  $params[':q'] = "%$q%";
}

if ($max !== '' && is_numeric($max)) {
  $sql .= " AND prix <= :max";
  $params[':max'] = (float)$max;
}

$sql .= " ORDER BY id DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$parfums = $stmt->fetchAll(PDO::FETCH_ASSOC);

$count = count($parfums);
?>

<section class="hero">
  <div class="container">
    <h1>Senteurs d’exception</h1>
    <p>Explore notre sélection minutieusement choisie, et découvre le parfum qui te correspond. Blanc & doré, style premium.</p>
  </div>
</section>

<section class="section">
  <div class="container">

    <form class="filters" method="get">
      <div>
        <label>Recherche (nom)</label>
        <input type="text" name="q" placeholder="Ex: Dior" value="<?= htmlspecialchars($q) ?>">
      </div>

      <div>
        <label>Prix maximum</label>
        <input type="text" name="max" placeholder="Ex: 150" value="<?= htmlspecialchars($max) ?>">
      </div>

      <div style="display:flex; gap:10px;">
        <button class="btn gold" type="submit">Appliquer</button>
        <a class="btn secondary" href="catalog.php">Réinitialiser</a>
      </div>

      <div style="margin-left:auto; color:#6b7280; font-size:14px;">
        <strong><?= $count ?></strong> produit(s)
      </div>
    </form>

    <div class="grid">
      <?php foreach ($parfums as $p): ?>
        <article class="card">
          <div class="img">
            <div class="bottle" aria-hidden="true"></div>
          </div>

          <div class="body">
            <h3><?= htmlspecialchars($p['nom']) ?></h3>
            <div class="price"><?= number_format((float)$p['prix'], 2) ?> €</div>
            <p class="desc"><?= htmlspecialchars($p['description']) ?></p>

            <div class="actions">
              <a class="btn secondary" href="product.php?id=<?= (int)$p['id'] ?>">Voir</a>

              <a class="btn" href="cart.php?action=add&id=<?= (int)$p['id'] ?>">
                Ajouter
              </a>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>

  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
