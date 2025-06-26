<?php
require_once '../includes/config.php';

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/auth/registro.php');
}

// Obtener y limpiar los datos del formulario
$nombre = trim($_POST['nombre'] ?? '');
$apellido_paterno = trim($_POST['apellido_paterno'] ?? '');
$apellido_materno = trim($_POST['apellido_materno'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$puesto = trim($_POST['puesto'] ?? '');
$organizacion_id = !empty($_POST['organizacion_id']) ? (int)$_POST['organizacion_id'] : null;
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';
$terms = isset($_POST['terms']);

// Validaciones
$errors = [];

// Validar campos requeridos
if (empty($nombre)) $errors[] = 'El nombre es requerido';
if (empty($apellido_paterno)) $errors[] = 'El apellido paterno es requerido';
if (empty($email)) $errors[] = 'El correo electrónico es requerido';
if (empty($password)) $errors[] = 'La contraseña es requerida';

// Validar formato de email
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'El formato del correo electrónico no es válido';
}

// Validar contraseña
if (strlen($password) < 8) {
    $errors[] = 'La contraseña debe tener al menos 8 caracteres';
}

if ($password !== $password_confirm) {
    $errors[] = 'Las contraseñas no coinciden';
}

// Validar términos y condiciones
if (!$terms) {
    $errors[] = 'Debes aceptar los términos y condiciones';
}

// Si hay errores, redirigir con los datos
if (!empty($errors)) {
    setFlashMessage('error', implode(', ', $errors));
    redirect('/auth/registro.php');
}

try {
    $pdo = getDBConnection();

    // Verificar si el email ya existe
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        setFlashMessage('error', 'El correo electrónico ya está registrado');
        redirect('/auth/registro.php');
    }

    // Verificar organización si se proporcionó
    if ($organizacion_id) {
        $stmt = $pdo->prepare("SELECT id FROM organizaciones WHERE id = ?");
        $stmt->execute([$organizacion_id]);
        if (!$stmt->fetch()) {
            setFlashMessage('error', 'La organización seleccionada no existe');
            redirect('/auth/registro.php');
        }
    }

    // Hash de la contraseña
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insertar nuevo usuario
    $stmt = $pdo->prepare("
        INSERT INTO usuarios (
            nombre, apellido_paterno, apellido_materno, email, password, 
            telefono, puesto, organizacion_id, activo, created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())
    ");

    $stmt->execute([
        $nombre,
        $apellido_paterno,
        $apellido_materno,
        $email,
        $password_hash,
        $telefono,
        $puesto,
        $organizacion_id
    ]);

    $user_id = $pdo->lastInsertId();

    // Asignar rol por defecto (asumiendo que existe un rol "usuario")
    // Primero verificamos si existe el rol "usuario"
    $stmt = $pdo->prepare("SELECT id FROM roles WHERE nombre = 'usuario'");
    $stmt->execute();
    $role = $stmt->fetch();

    if ($role) {
        $stmt = $pdo->prepare("INSERT INTO usuario_roles (usuario_id, rol_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $role['id']]);
    }

    setFlashMessage('success', '¡Cuenta creada exitosamente! Ya puedes iniciar sesión.');
    redirect('/auth/login.php');
} catch (PDOException $e) {
    error_log("Error en registro: " . $e->getMessage());
    setFlashMessage('error', 'Error interno del servidor. Por favor, intenta nuevamente.');
    redirect('/auth/registro.php');
}
