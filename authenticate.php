<?php
require_once __DIR__ . '/../../../app/config/db.php';
session_start();
$err = '';
$next = $_GET['next'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $st = $pdo->prepare("SELECT user_id, email, password_hash, identity, is_active FROM users WHERE email = :email LIMIT 1");
    $st->execute([':email' => $email]);
    $user = $st->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        if ($user['identity'] === 'admin') {
            $_SESSION['user'] = $user;
            header("Location: ../../../admin/admin_dashboard.php");
            exit;
        } elseif ($user['identity'] === 'volunteer') {
            if ((int)$user['is_active'] === 1) {
                $_SESSION['user'] = $user;
                header("Location: ../../../volunteer/volunteer_dashboard.php");
                exit;
            } else {
                $err = "Your account is not yet approved by an administrator.";
            }
        } else {
            $err = "Invalid identity.";
        }
    } else {
        $err = "Invalid email or password.";
    }
}

if ($err) {
    $qs = $next ? ('&next=' . urlencode($next)) : '';
    header("Location: login.php?error=" . urlencode($err) . $qs);
    exit;
}
