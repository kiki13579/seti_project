<?php

// 1. Définir le type de contenu de la réponse comme étant du JSON
header('Content-Type: application/json');

// 2. Inclure le fichier de connexion à la base de données
// Le chemin est correct car 'api' et 'includes' sont au même niveau
require_once __DIR__ . '/../includes/db.php';

try {
    // 3. Préparer et exécuter la requête SQL pour sélectionner toutes les planètes
    $stmt = $pdo->query("SELECT * FROM planete ORDER BY id ASC");
    
    // 4. Récupérer tous les résultats sous forme de tableau associatif
    $planetes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 5. Encoder le tableau en JSON et l'afficher
    echo json_encode($planetes);

} catch (PDOException $e) {
    // En cas d'erreur de la base de données, renvoyer une réponse d'erreur
    // Définir le code de statut HTTP à 500 (Erreur interne du serveur)
    http_response_code(500);
    echo json_encode(['error' => 'Erreur de base de données : ' . $e->getMessage()]);
}