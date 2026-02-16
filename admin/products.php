<?php
require_once __DIR__ . '/../includes/admin_guard.php';
require_once __DIR__ . '/../config/db.php';

$pdo = db();

$deleteId = isset($_GET['delete']) ? (int)$_GET['delete'] : 0;
if ($deleteId > 0) {
  $del = $pdo->prepare("DELETE FROM parfums WHERE id = ?");
  $del->execute([$deleteId]);
  header("Location: /ProjetPHP/admin/products.php");
  exit;
}

$parfums = $pdo->query("SELECT id, nom, prix, description FROM parfums ORDER BY id DESC")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<section class="hero" style="padding:40px 0 10px;">
  <div class="container">
    <h1 style="font-size:44px;">Produits (CRUD)</h1>
    <p>Ajouter, modifier, supprimer les parfums.</p>
  </div>
</section>

<section class="section" style="padding-top:0;">
  <div class="container">

    <p style="display:flex; gap:10px; flex-wrap:wrap;">
      <a class="btn gold" href="/ProjetPHP/admin/product_create.php">+ Ajouter un produit</a>
      <a class="btn" href="/ProjetPHP/admin/index.php">Retour admin</a>
    </p>

    <table class="table">
      <tr>
        <th>ID</th><th>Nom</th><th>Prix</th><th>Description</th><th>Actions</th>
      </tr>

      <?php foreach ($parfums as $p): ?>
        <tr>
          <td><?= (int)$p['id'] ?></td>
          <td><?= htmlspecialchars($p['nom']) ?></td>
          <td><?= number_format((float)$p['prix'], 2) ?> €</td>
          <td><?= htmlspecialchars(mb_strimwidth($p['description'], 0, 80, '...')) ?></td>
          <td style="display:flex; gap:10px; flex-wrap:wrap;">
            <a class="btn secondary" href="/ProjetPHP/admin/product_edit.php?id=<?= (int)$p['id'] ?>">Modifier</a>
            <a class="btn" href="/ProjetPHP/admin/products.php?delete=<?= (int)$p['id'] ?>"
               onclick="return confirm('Supprimer ce produit ?');">Supprimer</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>

  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
