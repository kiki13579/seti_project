<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../includes/db.php';

// Vérifier que l'ID du message est présent
if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}
$id_message = $_GET['id'];

// --- GESTION DE LA SOUMISSION DU FORMULAIRE (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contenu = $_POST['contenu'] ?? '';
    $id_planete = $_POST['planete_id'] ?? ''; // Récupérer l'ID de la planète du champ caché

    if (!empty($contenu) && !empty($id_planete)) {
        try {
            $stmt = $pdo->prepare("UPDATE message SET contenu = ? WHERE id = ?");
            $stmt->execute([$contenu, $id_message]);
            // Rediriger vers la page de gestion de la planète correspondante
            header('Location: update_planete.php?id=' . $id_planete);
            exit;
        } catch (PDOException $e) {
            $error_message = "Erreur lors de la mise à jour du message.";
        }
    } else {
        $error_message = "Le contenu ne peut pas être vide.";
    }
}

// --- AFFICHAGE DU FORMULAIRE PRÉ-REMPLI (GET) ---
try {
    // Récupérer les données actuelles du message pour pré-remplir le formulaire
    $stmt = $pdo->prepare("SELECT * FROM message WHERE id = ?");
    $stmt->execute([$id_message]);
    $message = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$message) {
        // Si le message n'existe pas, retour au tableau de bord
        header('Location: dashboard.php');
        exit;
    }
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un message</title>
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body class="admin-bg">
    <header><h1>Modifier un message</h1></header>
    <main class="backoffice-container">
        <form method="POST">
            <div class="form-group">
                <label for="contenu">Contenu du message :</label>
                <textarea id="contenu" name="contenu" rows="4" required><?php echo htmlspecialchars($message['contenu']); ?></textarea>
            </div>
            
            <input type="hidden" name="planete_id" value="<?php echo htmlspecialchars($message['planete_id']); ?>">
            
            <button type="submit">Mettre à jour</button>
            <a href="update_planete.php?id=<?php echo htmlspecialchars($message['planete_id']); ?>">Annuler</a>
        </form>
        <?php if (isset($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
    </main>
</body>
</html>