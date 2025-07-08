<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';

// 1. Vérifier que la requête est bien en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Méthode non autorisée
    echo json_encode(['error' => 'Méthode non autorisée. Seule la méthode POST est acceptée.']);
    exit;
}

// 2. Récupérer les données envoyées par le JavaScript
$data = json_decode(file_get_contents('php://input'), true);
$planetName = $data['planete'] ?? '';

if (empty($planetName)) {
    http_response_code(400); // Mauvaise requête
    echo json_encode(['error' => 'Nom de la planète manquant.']);
    exit;
}

try {
    // 3. Trouver l'ID de la planète à partir de son nom
    $stmt = $pdo->prepare("SELECT id FROM planete WHERE nom = ?");
    $stmt->execute([ucfirst($planetName)]); // Assure que la première lettre est en majuscule, ex: "Mars"
    $planet = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$planet) {
        http_response_code(404); // Non trouvé
        echo json_encode(['message' => 'Planète inconnue... signal perdu dans l\'espace.']);
        exit;
    }

    // 4. Sélectionner un message aléatoire pour cette planète
    $stmt = $pdo->prepare("SELECT contenu FROM message WHERE planete_id = ? ORDER BY RAND() LIMIT 1");
    $stmt->execute([$planet['id']]);
    $message = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($message) {
        echo json_encode(['message' => $message['contenu']]);
    } else {
        echo json_encode(['message' => '...silence radio. Aucun message reçu de ' . ucfirst($planetName) . '.']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur interstellaire... impossible de traiter le signal.']);
}