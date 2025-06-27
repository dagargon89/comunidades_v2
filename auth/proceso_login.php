<?php
require_once '../includes/config.php';

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/auth/login.php');
}

// Obtener y limpiar los datos del formulario
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$remember_me = isset($_POST['remember_me']);

// Validaciones básicas
if (empty($email) || empty($password)) {
    setFlashMessage('error', 'Por favor, completa todos los campos.');
    redirect('/auth/login.php');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    setFlashMessage('error', 'El formato del correo electrónico no es válido.');
    redirect('/auth/login.php');
}

try {
    $pdo = getDBConnection();

    // Buscar usuario por email
    $stmt = $pdo->prepare("
        SELECT u.*, o.nombre as organizacion_nombre 
        FROM usuarios u 
        LEFT JOIN organizaciones o ON u.organizacion_id = o.id 
        WHERE u.email = ? AND u.activo = 1
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Verificar si el usuario existe y la contraseña es correcta
    if (!$user || !password_verify($password, $user['password'])) {
        setFlashMessage('error', 'Credenciales incorrectas. Por favor, verifica tu correo y contraseña.');
        redirect('/auth/login.php');
    }

    // Verificar si el usuario está activo
    if (!$user['activo']) {
        setFlashMessage('error', 'Tu cuenta ha sido desactivada. Contacta al administrador.');
        redirect('/auth/login.php');
    }

    // Establecer la sesión
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_nombre'] = $user['nombre'];
    $_SESSION['user_apellido'] = $user['apellido_paterno'];
    $_SESSION['organizacion_id'] = $user['organizacion_id'];
    $_SESSION['organizacion_nombre'] = $user['organizacion_nombre'];

    // Si marcó "recordarme", establecer cookie
    if ($remember_me) {
        $token = bin2hex(random_bytes(32));
        setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/'); // 30 días

        // Aquí podrías guardar el token en la base de datos si quieres implementar
        // un sistema de "recordarme" más robusto
    }

    // Actualizar último login
    $stmt = $pdo->prepare("UPDATE usuarios SET updated_at = NOW() WHERE id = ?");
    $stmt->execute([$user['id']]);

    setFlashMessage('success', '¡Bienvenido, ' . htmlspecialchars($user['nombre']) . '!');

    // Redirección según rol
    if (hasRole('admin', $user['id'])) {
        redirect('/');
    } elseif (hasRole('financiadora', $user['id'])) {
        redirect('/financiadora_dashboard.php');
    } elseif (hasRole('coordinador', $user['id'])) {
        redirect('/coordinador_dashboard.php');
    } elseif (hasRole('usuario', $user['id'])) {
        redirect('/bienvenida_usuario.php');
    } elseif (hasRole('capturista', $user['id'])) {
        redirect('/captura_especial.php');
    } else {
        redirect('/'); // Por defecto
    }
} catch (PDOException $e) {
    error_log("Error en login: " . $e->getMessage());
    setFlashMessage('error', 'Error interno del servidor. Por favor, intenta nuevamente.');
    redirect('/auth/login.php');
}
