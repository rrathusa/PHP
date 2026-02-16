<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$errors = [];
$success = null;

$nom = trim($_POST['nom'] ?? '');
$email = trim($_POST['email'] ?? '');
$pass1 = $_POST['password'] ?? '';
$pass2 = $_POST['password_confirm'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if ($nom === '' || mb_strlen($nom) < 2) {
    $errors[] = "Le nom doit faire au moins 2 caractères.";
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Email invalide.";
  }

  if (strlen($pass1) < 6) {
    $errors[] = "Le mot de passe doit faire au moins 6 caractères.";
  }

  if ($pass1 !== $pass2) {
    $errors[] = "Les mots de passe ne correspondent pas.";
  }

  if (empty($errors)) {
    $pdo = db();

    // éviter doublon email (exigé)
    $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);

    if ($check->fetch()) {
      $errors[] = "Cet email est déjà utilisé.";
    } else {
      $hash = password_hash($pass1, PASSWORD_DEFAULT); // exigé
      $ins = $pdo->prepare("INSERT INTO users (nom, email, password_hash, role) VALUES (?, ?, ?, 'user')");
      $ins->execute([$nom, $email, $hash]);

      $success = "Compte créé ! Tu peux te connecter.";
      $nom = $email = '';
    }
  }
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="hero" style="padding:40px 0 10px;">
  <div class="container">
    <h1 style="font-size:44px;">Inscription</h1>
    <p>Crée ton compte (email valide + mot de passe confirmé).</p>
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

    <?php if ($success): ?>
      <div style="border:1px solid #2d7; padding:12px; border-radius:12px; margin-bottom:14px;">
        <?= htmlspecialchars($success) ?>
      </div>
    <?php endif; ?>

    <form method="POST" style="max-width:520px;">
      <div style="margin-bottom:12px;">
        <label>Nom</label><br>
        <input class="input" type="text" name="nom" value="<?= htmlspecialchars($nom) ?>" required>
      </div>

      <div style="margin-bottom:12px;">
        <label>Email</label><br>
        <input class="input" type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
      </div>

      <div style="margin-bottom:12px;">
        <label>Mot de passe</label><br>
        <input class="input" type="password" name="password" required>
      </div>

      <div style="margin-bottom:16px;">
        <label>Confirmer le mot de passe</label><br>
        <input class="input" type="password" name="password_confirm" required>
      </div>

      <button class="btn gold" type="submit">Créer mon compte</button>
      <a class="btn" href="/ProjetPHP/pages/login.php" style="margin-left:10px;">J’ai déjà un compte</a>
    </form>

  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
