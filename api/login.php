<?php
require_once '../includes/config.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$email = $input['email'] ?? '';
$password = $input['password'] ?? '';

if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Faltan credenciales.']);
    exit;
}

try {
    $pdo = getDBConnection();
    // Buscar usuario y su(s) rol(es)
    $stmt = $pdo->prepare("SELECT u.id, u.nombre, u.email, u.password, r.nombre as rol_nombre FROM usuarios u JOIN usuario_roles ur ON u.id = ur.usuario_id JOIN roles r ON ur.rol_id = r.id WHERE u.email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($password, $usuario['password'])) {
        // Solo permitir si el usuario tiene el rol 'capturista'
        if (strtolower($usuario['rol_nombre']) !== 'capturista') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Acceso denegado para este rol.']);
            exit;
        }
        unset($usuario['password']);
        echo json_encode([
            'success' => true,
            'message' => 'Login correcto.',
            'usuario' => $usuario,
            'token' => 'TOKEN_DEMO_123' // Aquí deberías generar un JWT real
        ]);
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas.']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de servidor: ' . $e->getMessage()]);
}
