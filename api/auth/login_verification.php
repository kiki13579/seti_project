<?php
session_start();
require_once __DIR__ . '/../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../login.php');
    exit;
}

$email = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    header('Location: ../../login.php?error=empty_fields');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password_hash'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['email'];
        header('Location: ../../backoffice/dashboard.php');
        exit;
    } else {
        header('Location: ../../login.php?error=invalid_credentials');
        exit;
    }
} catch (PDOException $e) {
    header('Location: ../../login.php?error=db_error');
    exit;
}
?>