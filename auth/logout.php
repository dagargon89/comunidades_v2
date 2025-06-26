<?php
require_once '../includes/config.php';

// Limpiar todas las variables de sesión
$_SESSION = array();

// Destruir la cookie de sesión si existe
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destruir la sesión
session_destroy();

// Limpiar cookie de "recordarme" si existe
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

// Redirigir al login con mensaje
setFlashMessage('success', 'Has cerrado sesión exitosamente.');
redirect('/auth/login.php');
