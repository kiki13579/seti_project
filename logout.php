<?php
session_start(); // Indispensable pour accéder à la session

// Détruire toutes les variables de session
$_SESSION = [];

// Détruire la session elle-même
session_destroy();

// Rediriger l'utilisateur vers la page de connexion
header('Location: login.php');
exit;