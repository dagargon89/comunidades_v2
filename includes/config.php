<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'comunidades_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Configuración de la aplicación
define('APP_NAME', 'Comunidades v2');
define('APP_URL', 'http://localhost');

// Configuración de sesiones
session_start();

// Configuración de zona horaria
date_default_timezone_set('America/Mexico_City');

// Función para conectar a la base de datos
function getDBConnection()
{
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}

// Función para verificar si el usuario está autenticado
function isAuthenticated()
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Función para obtener información del usuario actual
function getCurrentUser()
{
    if (!isAuthenticated()) {
        return null;
    }

    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            SELECT u.*, o.nombre as organizacion_nombre 
            FROM usuarios u 
            LEFT JOIN organizaciones o ON u.organizacion_id = o.id 
            WHERE u.id = ? AND u.activo = 1
        ");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

// Función para verificar si el usuario tiene un rol específico
function hasRole($roleName)
{
    if (!isAuthenticated()) {
        return false;
    }

    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as count 
            FROM usuario_roles ur 
            JOIN roles r ON ur.rol_id = r.id 
            WHERE ur.usuario_id = ? AND r.nombre = ?
        ");
        $stmt->execute([$_SESSION['user_id'], $roleName]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    } catch (PDOException $e) {
        return false;
    }
}

// Función para redirigir
function redirect($url)
{
    header("Location: " . $url);
    exit();
}

// Función para mostrar mensajes de error/éxito
function setFlashMessage($type, $message)
{
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlashMessage()
{
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}
