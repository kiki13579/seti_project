<?php
session_start();
// 1. Sécurité : seul un admin connecté peut accéder à cette page
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

// Inclure la connexion à la base de données
require_once __DIR__ . '/../includes/db.php';

// 2. Vérifier que l'ID de la planète est bien présent dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Rediriger si l'ID est manquant
    header('Location: dashboard.php');
    exit;
}

$id_planete = $_GET['id'];

try {
    // 3. Préparer et exécuter la requête de suppression
    // On cible la planète qui a l'ID reçu
    $stmt = $pdo->prepare("DELETE FROM planete WHERE id = ?");
    $stmt->execute([$id_planete]);

    // 4. Rediriger vers le tableau de bord une fois la suppression effectuée
    header('Location: dashboard.php');
    exit;

} catch (PDOException $e) {
    // En cas d'erreur, on pourrait afficher un message
    // Pour l'instant, une simple redirection suffit
    die("Erreur lors de la suppression de la planète : " . $e->getMessage());
}