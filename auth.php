<?php
if (session_status() === PHP_SESSION_NONE) session_start();

const APP_BASE  = '/Lab04_Group03';
const LOGIN_URL = APP_BASE . '/public/assets/auth/login.php';
const VOL_DASH  = APP_BASE . '/volunteer/volunteer_dashboard.php';
const ADM_DASH  = APP_BASE . '/admin/admin_dashboard.php';

function current_user(){ return $_SESSION['user'] ?? null; }
function is_logged_in(): bool { return isset($_SESSION['user']); }

function login(array $u): void {
    $_SESSION['user'] = [
        'id' => $u['id'] ?? null,
        'name' => $u['name'] ?? ($u['username'] ?? ''),
        'email' => $u['email'] ?? '',
        'role' => strtolower($u['role'] ?? 'volunteer'),
    ];
}

function logout(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time()-42000, $p['path'],$p['domain'],$p['secure'],$p['httponly']);
    }
    session_destroy();
}


function require_login(): void {
    if (!is_logged_in()) {
        $next = urlencode($_SERVER['REQUEST_URI'] ?? VOL_DASH);
        header('Location: ' . LOGIN_URL . '?next=' . $next);
        exit;
    }
}

function require_role(string $role): void {
    require_login();
    $r = strtolower($_SESSION['user']['role'] ?? '');
    if ($r !== strtolower($role)) {
        header('Location: ' . ($r === 'admin' ? ADM_DASH : VOL_DASH));
        exit;
    }
}



