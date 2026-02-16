<?php
session_start();
unset($_SESSION['cart']);
header("Location: /ProjetPHP/pages/cart.php");
exit;
