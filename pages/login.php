<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$errors = [];
$email = trim($_POST['email'] ?? '');
$pass = $_POST['password'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Email invalide.";
  }

  if ($pass === '') {
    $errors[] = "Mot de passe requis.";
  }

  if (empty($errors)) {
    $pdo = db();

    $stmt = $pdo->prepare("SELECT id, nom, email, password_hash, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($pass, $user['password_hash'])) {
      $errors[] = "Email ou mot de passe incorrect.";
    } else {
      // on stocke le user en session (sans le hash)
      $_SESSION['user'] = [
        'id' => (int)$user['id'],
        'nom' => $user['nom'],
        'email' => $user['email'],
        'role' => $user['role'],
      ];

      header("Location: /ProjetPHP/pages/catalog.php");
      exit;
    }
  }
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="hero" style="padding:40px 0 10px;">
  <div class="container">
    <h1 style="font-size:44px;">Connexion</h1>
    <p>Connecte-toi à ton compte.</p>
  </div>
</section>

<section class="section" style="padding-top:0;">
  <div class="container">

    <?php if (!empty($errors)): ?>
      <div style="border:1px solid #f0c; padding:12px; border-radius:12px; margin-bottom:14px;">
        <strong>Erreurs :</strong>
        <ul>
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="POST" style="max-width:520px;">
      <div style="margin-bottom:12px;">
        <label>Email</label><br>
        <input class="input" type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
      </div>

      <div style="margin-bottom:16px;">
        <label>Mot de passe</label><br>
        <input class="input" type="password" name="password" required>
      </div>

      <button class="btn gold" type="submit">Se connecter</button>
      <a class="btn" href="/ProjetPHP/pages/register.php" style="margin-left:10px;">Créer un compte</a>
    </form>

  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
