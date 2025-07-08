<?php
// Inclure la connexion PDO
require_once __DIR__ . '/../includes/db.php';

// === CONFIGURATION TEMPORAIRE ===
// ⚠️ A SUPPRIMER APRÈS UTILISATION !
// Tu peux modifier ici les infos d’admin à créer :
$email = 'admin@seti.local';
$motDePasse = 'supersecret'; // À changer ! (sera hashé)

// === Vérifier si l'admin existe déjà ===
$stmt = $pdo->prepare("SELECT id FROM admin WHERE email = ?");
$stmt->execute([$email]);

if ($stmt->fetch()) {
    echo "❌ Un administrateur avec cet email existe déjà.";
    exit;
}

// === Hasher le mot de passe ===
$hash = password_hash($motDePasse, PASSWORD_DEFAULT);

// === Insertion en BDD ===
$stmt = $pdo->prepare("INSERT INTO admin (email, password_hash) VALUES (?, ?)");

if ($stmt->execute([$email, $hash])) {
    echo "✅ Administrateur enregistré avec succès !";
} else {
    echo "❌ Une erreur est survenue lors de l'enregistrement.";
}
