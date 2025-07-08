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
    <style>
        /* --- Style de base --- */
        body {
            background-color: #000;
            margin: 0;
            padding: 0;
            overflow: hidden;
            font-family: 'Titillium Web', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
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
        
        /* --- Style pour le conteneur de connexion --- */
        .login-container {
            position: relative;
            z-index: 1;
            color: white;
            background-color: rgba(10, 20, 30, 0.85);
            padding: 40px;
            border-radius: 8px;
            border: 1px solid #00bcd4;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 0 20px rgba(0, 188, 212, 0.3);
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #fff;
            text-shadow: 0 0 5px #00bcd4;
        }
        
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #ccc;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            background-color: rgba(0, 0, 0, 0.3);
            border: 1px solid #444;
            border-radius: 5px;
            color: white;
            font-size: 1em;
        }

        .form-group input:focus {
            outline: none;
            border-color: #00bcd4;
            box-shadow: 0 0 8px rgba(0, 188, 212, 0.5);
        }

        .login-container button, .login-container a{
            width: 100%;
            padding: 12px;
            font-size: 1.1em;
            cursor: pointer;
            background-color: #00bcd4;
            color: white;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .login-container button:hover {
            background-color: #0097a7;
        }

        .error-message {
            color: #ff8a80;
            background-color: rgba(255, 0, 0, 0.2);
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 15px;
        }
        .login-container a {
            margin: 1rem;
        }
    </style>
</head>
<body class="admin-bg">

    <canvas id="space-canvas"></canvas>
    <div class="login-container">
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
    </div>
    
    <script>
        const canvas = document.getElementById('space-canvas');
        const ctx = canvas.getContext('2d');

        let planets = [];
        let asteroids = [];
        let particles = [];
        let backgroundStars = [];
        const planetCount = 7;
        const asteroidCount = 400;
        const backgroundStarCount = 200;

        let sun = { x: 0, y: 0, radius: 25, color: '#ffcc00' };

        function getDistance(x1, y1, x2, y2) {
            return Math.sqrt(Math.pow(x2 - x1, 2) + Math.pow(y2 - y1, 2));
        }

        function createExplosion(x, y, color) {
            for (let i = 0; i < 20; i++) {
                particles.push({ x, y, vx: (Math.random() - 0.5) * 2, vy: (Math.random() - 0.5) * 2, radius: Math.random() * 2, life: 50, color });
            }
        }

        function init() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            sun.x = canvas.width / 2;
            sun.y = canvas.height / 2;
            planets = []; asteroids = []; particles = []; backgroundStars = [];

            const planetColors = ['#a9a9a9', '#deb887', '#4682b4', '#ff6347', '#daa520', '#add8e6', '#b0c4de'];
            for (let i = 0; i < planetCount; i++) {
                const orbitRadiusX = (i + 1) * (Math.min(canvas.width, canvas.height) / (planetCount + 2));
                planets.push({
                    radius: Math.random() * 8 + 4,
                    orbitRadiusX,
                    orbitRadiusY: orbitRadiusX * (Math.random() * 0.3 + 0.6),
                    angle: Math.random() * Math.PI * 2,
                    speed: (Math.random() * 0.005 + 0.001) / (i + 1),
                    color: planetColors[i % planetColors.length],
                    x: 0, y: 0
                });
            }

            const beltStart = planets[3].orbitRadiusX + 20;
            const beltEnd = planets[4].orbitRadiusX - 20;
            for (let i = 0; i < asteroidCount; i++) {
                const orbitRadiusX = beltStart + Math.random() * (beltEnd - beltStart);
                asteroids.push({
                    radius: Math.random() * 1 + 0.5,
                    orbitRadiusX,
                    orbitRadiusY: orbitRadiusX * (Math.random() * 0.1 + 0.9),
                    angle: Math.random() * Math.PI * 2,
                    speed: (Math.random() * 0.005 + 0.001) * 0.5,
                    x: 0, y: 0
                });
            }

            for (let i = 0; i < backgroundStarCount; i++) {
                backgroundStars.push({ x: Math.random() * canvas.width, y: Math.random() * canvas.height, radius: Math.random() * 1.2, alpha: Math.random() * 0.8 + 0.2 });
            }
        }

        function animate() {
            ctx.fillStyle = 'rgba(0, 0, 0, 0.2)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            backgroundStars.forEach(star => {
                ctx.beginPath(); ctx.arc(star.x, star.y, star.radius, 0, Math.PI * 2); ctx.fillStyle = `rgba(255, 255, 255, ${star.alpha})`; ctx.fill();
            });
            
            ctx.shadowBlur = 30; ctx.shadowColor = sun.color;
            ctx.beginPath(); ctx.arc(sun.x, sun.y, sun.radius, 0, Math.PI * 2); ctx.fillStyle = sun.color; ctx.fill();
            ctx.shadowBlur = 0;

            planets.forEach(planet => {
                planet.angle += planet.speed;
                planet.x = sun.x + planet.orbitRadiusX * Math.cos(planet.angle);
                planet.y = sun.y + planet.orbitRadiusY * Math.sin(planet.angle);
            });

            for (let i = asteroids.length - 1; i >= 0; i--) {
                const a1 = asteroids[i];
                a1.angle += a1.speed;
                a1.x = sun.x + a1.orbitRadiusX * Math.cos(a1.angle);
                a1.y = sun.y + a1.orbitRadiusY * Math.sin(a1.angle);
                let asteroidDestroyed = false;

                for (const planet of planets) {
                    if (getDistance(a1.x, a1.y, planet.x, planet.y) < a1.radius + planet.radius) {
                        createExplosion(a1.x, a1.y, planet.color);
                        const pDev = (Math.random() - 0.5) * 2;
                        planet.orbitRadiusX += pDev; planet.orbitRadiusY += pDev;
                        asteroids.splice(i, 1); asteroidDestroyed = true; break;
                    }
                }
                if (asteroidDestroyed) continue;

                for (let j = i - 1; j >= 0; j--) {
                    const a2 = asteroids[j];
                    if (getDistance(a1.x, a1.y, a2.x, a2.y) < a1.radius + a2.radius) {
                        const dev = (Math.random() - 0.5) * 0.5;
                        a1.orbitRadiusX += dev; a1.orbitRadiusY += dev;
                        const dev2 = (Math.random() - 0.5) * 0.5;
                        a2.orbitRadiusX += dev2; a2.orbitRadiusY += dev2;
                    }
                }
            }

            asteroids.forEach(asteroid => {
                ctx.beginPath(); ctx.arc(asteroid.x, asteroid.y, asteroid.radius, 0, Math.PI * 2); ctx.fillStyle = 'rgba(160, 160, 160, 0.7)'; ctx.fill();
            });

            planets.forEach(planet => {
                ctx.beginPath(); ctx.arc(planet.x, planet.y, planet.radius, 0, Math.PI * 2); ctx.fillStyle = planet.color; ctx.fill();
            });

            for (let i = particles.length - 1; i >= 0; i--) {
                const p = particles[i]; p.x += p.vx; p.y += p.vy; p.life--;
                if (p.life <= 0) { particles.splice(i, 1); } else {
                    ctx.globalAlpha = p.life / 50;
                    ctx.beginPath(); ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2); ctx.fillStyle = p.color; ctx.fill();
                    ctx.globalAlpha = 1.0;
                }
            }
            
            requestAnimationFrame(animate);
        }

        window.addEventListener('resize', init);
        init();
        animate();
    </script>
</body>
</html>