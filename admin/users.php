<?php
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/nav.php'; // isLoggedIn/isAdmin

if (!isLoggedIn() || !isAdmin()) {
  header('Location: /ProjetPHP/pages/login.php');
  exit;
}

$pdo = db();

// supprimer un user (optionnel)
if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];

  // évite de supprimer l'admin id=1 si tu veux
  if ($id !== 1) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
  }
  header('Location: /ProjetPHP/admin/users.php');
  exit;
}

$users = $pdo->query("SELECT id, nom, email, role, created_at FROM users ORDER BY id DESC")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<section class="hero">
  <h1>Gestion des utilisateurs</h1>
  <p>Liste des comptes (admin / user). Tu peux supprimer un compte.</p>
</section>

<div class="card" style="padding:16px;">
  <table class="table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Email</th>
        <th>Rôle</th>
        <th>Créé le</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $u): ?>
      <tr>
        <td><?= (int)$u['id'] ?></td>
        <td><?= htmlspecialchars($u['nom']) ?></td>
        <td><?= htmlspecialchars($u['email']) ?></td>
        <td><span class="pill"><?= htmlspecialchars($u['role']) ?></span></td>
        <td><?= htmlspecialchars($u['created_at']) ?></td>
        <td>
          <?php if ((int)$u['id'] === 1): ?>
            <span class="muted">admin principal</span>
          <?php else: ?>
            <a class="btn btn-danger" href="/ProjetPHP/admin/users.php?delete=<?= (int)$u['id'] ?>"
               onclick="return confirm('Supprimer cet utilisateur ?');">
              Supprimer
            </a>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
