<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user'])) {
  header("Location: /ProjetPHP/pages/login.php");
  exit;
}

if (($_SESSION['user']['role'] ?? 'user') !== 'admin') {
  http_response_code(403);
  die("Accès refusé (admin uniquement).");
}
