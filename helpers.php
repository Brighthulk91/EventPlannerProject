<?php
// helpers.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// redirect and exit
function redirect($url) {
    header("Location: $url");
    exit;
}

// simple db connect helper (not necessary if using config.php global $conn, but included for flexibility)
// function db_connect() { ... }

// check logged-in state
function is_logged_in() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
}

// require login
function require_login() {
    if (!is_logged_in()) {
        redirect('login.php');
    }
}

// require manager role
function require_manager() {
    require_login();
    if (!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'manager') {
        redirect('dashboard.php');
    }
}

// require guest role
function require_guest() {
    require_login();
    if (!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'guest') {
        redirect('dashboard.php');
    }
}

// escape HTML output
function e($s) {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}



?>
