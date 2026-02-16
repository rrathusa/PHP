<?php

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

function isLoggedIn(): bool {
  return isset($_SESSION['user']);
}

function currentUser() {
  return $_SESSION['user'] ?? null;
}

function requireLogin(): void {
  if (!isLoggedIn()) {
    header("Location: /ProjetPHP/pages/login.php");
    exit;
  }
}

function logout(): void {
  $_SESSION = [];
  session_destroy();
}
