<?php
session_start();
// Sécurise la page, seul un admin peut y accéder
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../includes/db.php';

$message = '';
// Gère la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    if (!empty($nom)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO planete (nom) VALUES (?)");
            $stmt->execute([ucfirst($nom)]);
            // Redirige vers le dashboard pour voir la nouvelle planète
            header('Location: dashboard.php');
            exit;
        } catch (PDOException $e) {
            $message = "Erreur lors de la création de la planète : " . $e->getMessage();
        }
    } else {
        $message = "Le nom de la planète ne peut pas être vide.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer une Planète</title>
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body class="admin-bg">
    <header><h1>Créer une nouvelle planète</h1></header>
    <main class="backoffice-container">
        <form method="POST">
            <div class="form-group">
                <label for="nom">Nom de la planète</label>
                <input type="text" id="nom" name="nom" required>
            </div>
            <button type="submit">Créer</button>
            <a href="dashboard.php">Annuler</a>
        </form>
        <?php if ($message): ?>
            <p class="error-message"><?php echo $message; ?></p>
        <?php endif; ?>
    </main>
</body>
</html>