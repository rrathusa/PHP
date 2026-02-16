<?php
require_once __DIR__ . '/../includes/admin_guard.php';
require_once __DIR__ . '/../config/db.php';

$pdo = db();
$errors = [];

$nom = trim($_POST['nom'] ?? '');
$prix = trim($_POST['prix'] ?? '');
$description = trim($_POST['description'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if ($nom === '' || mb_strlen($nom) < 2) $errors[] = "Nom invalide.";
  if ($prix === '' || !is_numeric($prix) || (float)$prix <= 0) $errors[] = "Prix invalide.";
  if ($description === '' || mb_strlen($description) < 10) $errors[] = "Description trop courte (min 10).";

  if (empty($errors)) {
    $stmt = $pdo->prepare("INSERT INTO parfums (nom, prix, description) VALUES (?, ?, ?)");
    $stmt->execute([$nom, (float)$prix, $description]);
    header("Location: /ProjetPHP/admin/products.php");
    exit;
  }
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="hero" style="padding:40px 0 10px;">
  <div class="container">
    <h1 style="font-size:44px;">Ajouter un produit</h1>
    <p>Création d’un parfum.</p>
  </div>
</section>

<section class="section" style="padding-top:0;">
  <div class="container" style="max-width:700px;">

    <?php if (!empty($errors)): ?>
      <div style="border:1px solid #f0c; padding:12px; border-radius:12px; margin-bottom:14px;">
        <strong>Erreurs :</strong>
        <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
      </div>
    <?php endif; ?>

    <form method="POST">
      <label>Nom</label>
      <input class="input" type="text" name="nom" value="<?= htmlspecialchars($nom) ?>" required>

      <div style="height:12px;"></div>

      <label>Prix</label>
      <input class="input" type="text" name="prix" value="<?= htmlspecialchars($prix) ?>" required>

      <div style="height:12px;"></div>

      <label>Description</label>
      <input class="input" type="text" name="description" value="<?= htmlspecialchars($description) ?>" required>

      <div style="height:16px;"></div>

      <button class="btn gold" type="submit">Créer</button>
      <a class="btn" href="/ProjetPHP/admin/products.php" style="margin-left:10px;">Annuler</a>
    </form>

  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
