<?php
// --- 1. SÉCURITÉ : Démarrer la session et vérifier l'accès ---
session_start();

// Si l'administrateur n'est pas connecté, le rediriger vers la page de connexion
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

// Inclure la connexion à la base de données
require_once __DIR__ . '/../includes/db.php';

// --- 2. LOGIQUE : Récupérer la liste des planètes ---
try {
    $stmt = $pdo->query("SELECT * FROM planete ORDER BY nom ASC");
    $planetes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // En cas d'erreur, on peut afficher un message simple
    $error_message = "Erreur lors de la récupération des planètes.";
    $planetes = []; // Initialiser comme un tableau vide pour éviter une erreur plus bas
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Projet SETI</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <style>
        /* --- Style de base --- */
        body {
            background-color: #000;
            margin: 0;
            padding: 0;
            overflow-y: auto; /* Permet de défiler si le contenu est long */
            font-family: 'Titillium Web', sans-serif;
            display: flex;
            flex-direction: column; /* Aligne les éléments verticalement */
            align-items: center;
            min-height: 100vh;
            padding-top: 100px; /* Espace pour le header fixe */
            padding-bottom: 40px;
        }

        /* --- Style du Canvas --- */
        #space-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
        
        /* --- Header du back-office --- */
        header.backoffice-header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: rgba(10, 20, 30, 0.9);
            padding: 15px 40px;
            z-index: 10;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #00bcd4;
            color: white;
        }

        header.backoffice-header h1 {
            margin: 0;
            font-size: 1.5em;
        }

        header.backoffice-header .user-info a {
            color: #ff8a80; /* Rouge clair pour la déconnexion */
            text-decoration: none;
            margin-left: 20px;
        }
        
        /* --- Conteneur principal du back-office --- */
        .backoffice-container {
            position: relative;
            z-index: 1;
            color: white;
            background-color: rgba(10, 20, 30, 0.85);
            padding: 30px;
            border-radius: 8px;
            border: 1px solid #00bcd4;
            width: 100%;
            max-width: 900px;
            box-shadow: 0 0 20px rgba(0, 188, 212, 0.3);
        }

        .backoffice-container h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #fff;
            text-shadow: 0 0 5px #00bcd4;
        }
        
        /* --- Boutons et Liens d'action --- */
        .button-create {
            display: inline-block;
            background-color: #00bcd4;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 20px;
            transition: background-color 0.3s;
        }
        .button-create:hover {
            background-color: #0097a7;
        }

        /* --- Tableau de gestion --- */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #444;
            text-align: left;
        }

        th {
            background-color: rgba(0, 188, 212, 0.1);
            color: #00bcd4;
        }

        tr:hover {
            background-color: rgba(0, 188, 212, 0.05);
        }

        td a {
            color: #00bcd4;
            text-decoration: none;
            margin-right: 15px;
            font-weight: bold;
        }
        td a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: #ff8a80;
            background-color: rgba(255, 0, 0, 0.2);
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 15px;
        }
        .logout-button {
            position: absolute;
            padding: 10px 20px;
            font-size: 1em;
            cursor: pointer;
            color: white;
            border: none;
            border-radius: 5px;
            margin-top: 20px;
            right: 20px;
            top: 10px;
            font-size: larger;
            font-family: 'Titillium Web', sans-serif;
            text-decoration: none;
        }
    </style>
</head>
<body class="admin-bg">
    <canvas id="space-canvas"></canvas>
    <header>
        <h1>Back-Office SETI</h1>
        <p>Bienvenue, <?php echo htmlspecialchars($_SESSION['admin_username']); ?> !</p>
        <a href="../logout.php" class="logout-button">Se déconnecter</a>
    </header>

    <main class="backoffice-container">
        <h2>Gestion des Planètes</h2>
        
        <a href="create_planete.php" class="button-create">Créer une nouvelle planète</a>

        <?php if (isset($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($planetes)): ?>
                    <tr>
                        <td colspan="3">Aucune planète n'a été créée pour le moment.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($planetes as $planete): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($planete['id']); ?></td>
                            <td><?php echo htmlspecialchars($planete['nom']); ?></td>
                            <td>
                                <a href="update_planete.php?id=<?php echo $planete['id']; ?>">Modifier les messages</a>
                                <a href="delete_planete.php?id=<?php echo $planete['id']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette planète ?');">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
    <script>
        // Le script JS du canvas reste exactement le même que précédemment
        const canvas = document.getElementById('space-canvas');
        const ctx = canvas.getContext('2d');
        let planets = [], asteroids = [], particles = [], backgroundStars = [];
        const planetCount = 7, asteroidCount = 400, backgroundStarCount = 200;
        let sun = { x: 0, y: 0, radius: 25, color: '#ffcc00' };

        function init() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            sun.x = canvas.width / 2;
            sun.y = canvas.height / 2;
            planets = []; asteroids = []; particles = []; backgroundStars = [];
            const planetColors = ['#a9a9a9', '#deb887', '#4682b4', '#ff6347', '#daa520', '#add8e6', '#b0c4de'];
            for (let i = 0; i < planetCount; i++) {
                const orbitRadiusX = (i + 1) * (Math.min(canvas.width, canvas.height) / (planetCount + 2));
                planets.push({ radius: Math.random() * 8 + 4, orbitRadiusX, orbitRadiusY: orbitRadiusX * (Math.random() * 0.3 + 0.6), angle: Math.random() * Math.PI * 2, speed: (Math.random() * 0.005 + 0.001) / (i + 1), color: planetColors[i % planetColors.length], x: 0, y: 0 });
            }
            const beltStart = planets[3].orbitRadiusX + 20;
            const beltEnd = planets[4].orbitRadiusX - 20;
            for (let i = 0; i < asteroidCount; i++) {
                const orbitRadiusX = beltStart + Math.random() * (beltEnd - beltStart);
                asteroids.push({ radius: Math.random() * 1 + 0.5, orbitRadiusX, orbitRadiusY: orbitRadiusX * (Math.random() * 0.1 + 0.9), angle: Math.random() * Math.PI * 2, speed: (Math.random() * 0.005 + 0.001) * 0.5, x: 0, y: 0 });
            }
            for (let i = 0; i < backgroundStarCount; i++) {
                backgroundStars.push({ x: Math.random() * canvas.width, y: Math.random() * canvas.height, radius: Math.random() * 1.2, alpha: Math.random() * 0.8 + 0.2 });
            }
        }

        function animate() {
            ctx.fillStyle = 'rgba(0, 0, 0, 0.2)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            backgroundStars.forEach(star => { ctx.beginPath(); ctx.arc(star.x, star.y, star.radius, 0, Math.PI * 2); ctx.fillStyle = `rgba(255, 255, 255, ${star.alpha})`; ctx.fill(); });
            ctx.shadowBlur = 30; ctx.shadowColor = sun.color;
            ctx.beginPath(); ctx.arc(sun.x, sun.y, sun.radius, 0, Math.PI * 2); ctx.fillStyle = sun.color; ctx.fill();
            ctx.shadowBlur = 0;
            planets.forEach(p => { p.angle += p.speed; p.x = sun.x + p.orbitRadiusX * Math.cos(p.angle); p.y = sun.y + p.orbitRadiusY * Math.sin(p.angle); });
            asteroids.forEach(a => { a.angle += a.speed; a.x = sun.x + a.orbitRadiusX * Math.cos(a.angle); a.y = sun.y + a.orbitRadiusY * Math.sin(a.angle); });
            planets.forEach(p => { ctx.beginPath(); ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2); ctx.fillStyle = p.color; ctx.fill(); });
            asteroids.forEach(a => { ctx.beginPath(); ctx.arc(a.x, a.y, a.radius, 0, Math.PI * 2); ctx.fillStyle = 'rgba(160, 160, 160, 0.7)'; ctx.fill(); });
            requestAnimationFrame(animate);
        }
        window.addEventListener('resize', init);
        init();
        animate();
    </script>
</body>
</html>