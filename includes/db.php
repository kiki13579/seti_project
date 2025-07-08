<?php
// Connexion PDO à MySQL 
$host = 'localhost:3308';
$dbname = 'seti_project';
$user = 'root';
$pass = ''; // Mot de passe vide par défaut 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    // Options sécurisées
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
