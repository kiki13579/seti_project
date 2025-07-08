<?php
session_start();
// Sécurité : vérifier la connexion de l'admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../includes/db.php';

// Vérifier que l'ID de la planète est bien présent
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}
$id_planete = $_GET['id'];
$message_notification = '';

// --- LOGIQUE D'AJOUT D'UN NOUVEAU MESSAGE (quand le formulaire est soumis en POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_message'])) {
    $contenu = $_POST['contenu'] ?? '';
    if (!empty($contenu)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO message (contenu, planete_id) VALUES (?, ?)");
            $stmt->execute([$contenu, $id_planete]);
            $message_notification = "Message ajouté avec succès !";
        } catch (PDOException $e) {
            $message_notification = "Erreur lors de l'ajout du message.";
        }
    } else {
        $message_notification = "Le contenu du message ne peut pas être vide.";
    }
}

// --- LOGIQUE D'AFFICHAGE (toujours exécutée) ---
try {
    // Récupérer les infos de la planète
    $stmt_planete = $pdo->prepare("SELECT * FROM planete WHERE id = ?");
    $stmt_planete->execute([$id_planete]);
    $planete = $stmt_planete->fetch(PDO::FETCH_ASSOC);

    // Si la planète n'existe pas, rediriger
    if (!$planete) {
        header('Location: dashboard.php');
        exit;
    }

    // Récupérer les messages associés à la planète
    $stmt_messages = $pdo->prepare("SELECT * FROM message WHERE planete_id = ? ORDER BY id DESC");
    $stmt_messages->execute([$id_planete]);
    $messages = $stmt_messages->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gérer les messages de <?php echo htmlspecialchars($planete['nom']); ?></title>
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body class="admin-bg">
    <header><h1>Gérer les messages de "<?php echo htmlspecialchars($planete['nom']); ?>"</h1></header>
    <main class="backoffice-container">
        <a href="dashboard.php">← Retour au tableau de bord</a>
        
        <hr>

        <h3>Ajouter un nouveau message</h3>
        <form method="POST">
            <div class="form-group">
                <label for="contenu">Contenu du message :</label>
                <textarea id="contenu" name="contenu" rows="3" required></textarea>
            </div>
            <button type="submit" name="add_message">Ajouter le message</button>
        </form>
        <?php if ($message_notification): ?>
            <p><?php echo $message_notification; ?></p>
        <?php endif; ?>

        <hr>

        <h3>Messages existants</h3>
        <table>
            <thead><tr><th>ID</th><th>Contenu</th><th>Actions</th></tr></thead>
            <tbody>
                <?php if (empty($messages)): ?>
                    <tr><td colspan="3">Aucun message pour cette planète.</td></tr>
                <?php else: ?>
                    <?php foreach ($messages as $message): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($message['id']); ?></td>
                            <td><?php echo htmlspecialchars($message['contenu']); ?></td>
                            <td>
                                <a href="edit_message.php?id=<?php echo $message['id']; ?>">Modifier</a>
                                <a href="delete_message.php?id=<?php echo $message['id']; ?>&planet_id=<?php echo $planete['id']; ?>" onclick="return confirm('Êtes-vous sûr ?');">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>