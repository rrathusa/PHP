<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/nav.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Parfum Shop</title>
  <link rel="stylesheet" href="/ProjetPHP/assets/css/style.css">
</head>
<body>

<div class="topbar">
  Livraison offerte dès 150€ • -15% avec le code LOVE15
</div>

<header class="header">
  <div class="container header-inner">
    <a class="brand" href="/ProjetPHP/">PARFUM SHOP</a>

    <nav class="nav">
      <a href="/ProjetPHP/">Accueil</a>
      <a href="/ProjetPHP/pages/catalog.php">Parfums</a>

      <a href="/ProjetPHP/pages/cart.php">
        Panier <span class="badge"><?= cartCount() ?></span>
      </a>

      <?php if (isLoggedIn()): ?>
        <a href="/ProjetPHP/pages/my_orders.php">Mes commandes</a>
        <a href="/ProjetPHP/pages/logout.php">Déconnexion</a>
      <?php else: ?>
        <a href="/ProjetPHP/pages/login.php">Connexion</a>
        <a href="/ProjetPHP/pages/register.php">Inscription</a>
      <?php endif; ?>

      <?php if (isAdmin()): ?>
        <a href="/ProjetPHP/admin/index.php">Admin</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main class="container">
