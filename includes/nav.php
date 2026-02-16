<?php
// includes/nav.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function cartCount(): int {
    if (empty($_SESSION['cart']) || !is_array($_SESSION['cart'])) return 0;
    return array_sum(array_map('intval', $_SESSION['cart']));
}

function isLoggedIn(): bool {
    return !empty($_SESSION['user']);
}

function isAdmin(): bool {
    return !empty($_SESSION['user']) && ($_SESSION['user']['role'] ?? '') === 'admin';
}
