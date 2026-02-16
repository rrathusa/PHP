<?php
require_once __DIR__ . '/../config/db.php';

try {
  $pdo = db();
  echo "✅ Connexion DB OK";
} catch (Exception $e) {
  echo "❌ Connexion DB FAIL : " . $e->getMessage();
}
