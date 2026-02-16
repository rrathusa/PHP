<?php
require_once __DIR__ . '/../includes/admin_guard.php';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="hero" style="padding:40px 0 10px;">
  <div class="container">
    <h1 style="font-size:44px;">Back-office</h1>
    <p>Gestion du site (admin).</p>
  </div>
</section>

<section class="section" style="padding-top:0;">
  <div class="container" style="display:flex; gap:12px; flex-wrap:wrap;">
    <a class="btn gold" href="/ProjetPHP/admin/products.php">Gérer les produits</a>
    <a class="btn" href="/ProjetPHP/admin/users.php">Gérer les utilisateurs</a>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
