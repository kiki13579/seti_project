<?php
session_start();
// Sécurité : vérifier la connexion de l'admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../includes/db.php';

// Vérifier que l'ID du message ET l'ID de la planète sont présents
if (!isset($_GET['id']) || !isset($_GET['planet_id'])) {
    header('Location: dashboard.php');
    exit;
}

$id_message = $_GET['id'];
$id_planete = $_GET['planet_id'];

try {
    // Préparer et exécuter la requête de suppression
    $stmt = $pdo->prepare("DELETE FROM message WHERE id = ?");
    $stmt->execute([$id_message]);

    // Rediriger vers la page de gestion de la planète pour voir le résultat
    header('Location: update_planete.php?id=' . $id_planete);
    exit;

} catch (PDOException $e) {
    die("Erreur lors de la suppression du message : " . $e->getMessage());
}