<?php
session_start();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
  header("Location: /ProjetPHP/pages/catalog.php");
  exit;
}

if (!isset($_SESSION['cart'])) {
  $_SESSION['cart'] = [];
}

if (!isset($_SESSION['cart'][$id])) {
  $_SESSION['cart'][$id] = 1;
} else {
  $_SESSION['cart'][$id] += 1;
}

header("Location: /ProjetPHP/pages/catalog.php");
exit;
