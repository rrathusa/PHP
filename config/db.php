<?php

function db(): PDO
{
    $host = "localhost";
    $dbname = "parfum_shop";
    $user = "root";
    $pass = ""; // sur XAMPP c'est souvent vide

    try {
        $pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
            $user,
            $pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        die("Erreur connexion DB : " . $e->getMessage());
    }
}
