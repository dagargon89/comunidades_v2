<?php
$page_title = "Editar Perfil";
require_once '../includes/header.php';
if (!isAuthenticated()) redirect('/auth/login.php');

// Procesamiento del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido_paterno = trim($_POST['apellido_paterno'] ?? '');
    $apellido_materno = trim($_POST['apellido_materno'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $puesto = trim($_POST['puesto'] ?? '');
    $organizacion_id = intval($_POST['organizacion_id'] ?? 0);
    $password_actual = $_POST['password_actual'] ?? '';
    $password_nueva = $_POST['password_nueva'] ?? '';
    $password_confirmar = $_POST['password_confirmar'] ?? '';

    // Validaciones básicas
    if (empty($nombre) || empty($apellido_paterno) || empty($email)) {
        setFlashMessage('error', 'Los campos nombre, apellido paterno y email son obligatorios.');
        redirect('update_perfil.php');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setFlashMessage('error', 'El formato del email no es válido.');
        redirect('update_perfil.php');
    }

    // Validar cambio de contraseña si se proporciona
    if (!empty($password_nueva)) {
        if (empty($password_actual)) {
            setFlashMessage('error', 'Debe proporcionar la contraseña actual para cambiarla.');
            redirect('update_perfil.php');
        }
        if ($password_nueva !== $password_confirmar) {
            setFlashMessage('error', 'Las contraseñas nuevas no coinciden.');
            redirect('update_perfil.php');
        }
        if (strlen($password_nueva) < 6) {
            setFlashMessage('error', 'La nueva contraseña debe tener al menos 6 caracteres.');
            redirect('update_perfil.php');
        }
    }

    try {
        $pdo = getDBConnection();
        $pdo->beginTransaction();

        // Verificar si el email ya existe en otro usuario
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
        $stmt->execute([$email, $_SESSION['user_id']]);
        if ($stmt->fetch()) {
            setFlashMessage('error', 'El email ya está registrado por otro usuario.');
            redirect('update_perfil.php');
        }

        // Verificar contraseña actual si se va a cambiar
        if (!empty($password_nueva)) {
            $stmt = $pdo->prepare("SELECT password FROM usuarios WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $usuario_actual = $stmt->fetch();

            if (!password_verify($password_actual, $usuario_actual['password'])) {
                setFlashMessage('error', 'La contraseña actual es incorrecta.');
                redirect('update_perfil.php');
            }
        }

        // Procesar subida de foto
        $foto_perfil = null;
        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
            $archivo = $_FILES['foto_perfil'];
            $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
            $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif'];

            if (!in_array($extension, $extensiones_permitidas)) {
                setFlashMessage('error', 'Solo se permiten archivos JPG, PNG y GIF.');
                redirect('update_perfil.php');
            }

            if ($archivo['size'] > 2 * 1024 * 1024) { // 2MB
                setFlashMessage('error', 'El archivo es demasiado grande. Máximo 2MB.');
                redirect('update_perfil.php');
            }

            // Crear directorio si no existe
            $upload_dir = '../assets/img/perfiles/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            // Generar nombre único
            $nombre_archivo = 'perfil_' . $_SESSION['user_id'] . '_' . time() . '.' . $extension;
            $ruta_completa = $upload_dir . $nombre_archivo;

            if (move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
                $foto_perfil = 'assets/img/perfiles/' . $nombre_archivo;
            }
        }

        // Actualizar usuario
        $sql = "UPDATE usuarios SET nombre = ?, apellido_paterno = ?, apellido_materno = ?, email = ?, telefono = ?, puesto = ?, organizacion_id = ?";
        $params = [$nombre, $apellido_paterno, $apellido_materno, $email, $telefono, $puesto, $organizacion_id ?: null];

        if ($foto_perfil) {
            $sql .= ", foto_perfil = ?";
            $params[] = $foto_perfil;
        }

        if (!empty($password_nueva)) {
            $sql .= ", password = ?";
            $params[] = password_hash($password_nueva, PASSWORD_DEFAULT);
        }

        $sql .= " WHERE id = ?";
        $params[] = $_SESSION['user_id'];

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $pdo->commit();

        // Actualizar datos de sesión
        $_SESSION['user_name'] = $nombre . ' ' . $apellido_paterno;
        $_SESSION['user_email'] = $email;

        setFlashMessage('success', 'Perfil actualizado correctamente.');
        redirect('perfil.php');
    } catch (PDOException $e) {
        $pdo->rollBack();
        setFlashMessage('error', 'Error al actualizar el perfil: ' . $e->getMessage());
        redirect('update_perfil.php');
    }
}

$pdo = getDBConnection();
$stmt = $pdo->prepare("
    SELECT u.*, o.nombre as organizacion_nombre 
    FROM usuarios u 
    LEFT JOIN organizaciones o ON u.organizacion_id = o.id 
    WHERE u.id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener lista de organizaciones para el select
$stmt = $pdo->prepare("SELECT id, nombre FROM organizaciones ORDER BY nombre");
$stmt->execute();
$organizaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="flex flex-col items-center justify-center min-h-[70vh] py-8">
    <div class="bg-white rounded-2xl shadow-2xl p-8 border border-cadet/40 w-full max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-primary flex items-center gap-3">
                <i class="fas fa-user-edit text-accent text-4xl"></i>
                Editar Perfil
            </h1>
            <a href="perfil.php" class="text-sm text-cadet hover:text-primary flex items-center gap-1">
                <i class="fas fa-arrow-left"></i>
                Volver al Perfil
            </a>
        </div>

        <form action="update_perfil.php" method="POST" enctype="multipart/form-data" class="space-y-8">
            <!-- Información Personal -->
            <div class="bg-gradient-to-br from-primary/5 to-accent/5 rounded-xl p-6">
                <h3 class="text-xl font-bold text-darkpurple mb-6 flex items-center gap-2">
                    <i class="fas fa-user text-accent"></i>
                    Información Personal
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nombre" class="block text-sm font-semibold text-darkpurple mb-2">Nombre(s) *</label>
                        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required
                            class="w-full px-4 py-3 border border-cadet/50 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent">
                    </div>
                    <div>
                        <label for="apellido_paterno" class="block text-sm font-semibold text-darkpurple mb-2">Apellido Paterno *</label>
                        <input type="text" id="apellido_paterno" name="apellido_paterno" value="<?php echo htmlspecialchars($usuario['apellido_paterno']); ?>" required
                            class="w-full px-4 py-3 border border-cadet/50 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent">
                    </div>
                    <div>
                        <label for="apellido_materno" class="block text-sm font-semibold text-darkpurple mb-2">Apellido Materno</label>
                        <input type="text" id="apellido_materno" name="apellido_materno" value="<?php echo htmlspecialchars($usuario['apellido_materno']); ?>"
                            class="w-full px-4 py-3 border border-cadet/50 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent">
                    </div>
                    <div>
                        <label for="puesto" class="block text-sm font-semibold text-darkpurple mb-2">Puesto</label>
                        <input type="text" id="puesto" name="puesto" value="<?php echo htmlspecialchars($usuario['puesto']); ?>"
                            class="w-full px-4 py-3 border border-cadet/50 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent">
                    </div>
                </div>
            </div>

            <!-- Información de Contacto -->
            <div class="bg-gradient-to-br from-primary/5 to-accent/5 rounded-xl p-6">
                <h3 class="text-xl font-bold text-darkpurple mb-6 flex items-center gap-2">
                    <i class="fas fa-envelope text-accent"></i>
                    Información de Contacto
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="email" class="block text-sm font-semibold text-darkpurple mb-2">Correo Electrónico *</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required
                            class="w-full px-4 py-3 border border-cadet/50 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent">
                    </div>
                    <div>
                        <label for="telefono" class="block text-sm font-semibold text-darkpurple mb-2">Teléfono</label>
                        <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($usuario['telefono']); ?>"
                            class="w-full px-4 py-3 border border-cadet/50 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent">
                    </div>
                </div>
            </div>

            <!-- Organización -->
            <div class="bg-gradient-to-br from-primary/5 to-accent/5 rounded-xl p-6">
                <h3 class="text-xl font-bold text-darkpurple mb-6 flex items-center gap-2">
                    <i class="fas fa-building text-accent"></i>
                    Organización
                </h3>
                <div>
                    <label for="organizacion_id" class="block text-sm font-semibold text-darkpurple mb-2">Organización</label>
                    <select id="organizacion_id" name="organizacion_id"
                        class="w-full px-4 py-3 border border-cadet/50 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent">
                        <option value="">Seleccionar organización</option>
                        <?php foreach ($organizaciones as $org): ?>
                            <option value="<?php echo $org['id']; ?>" <?php if ($usuario['organizacion_id'] == $org['id']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($org['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Foto de Perfil -->
            <div class="bg-gradient-to-br from-primary/5 to-accent/5 rounded-xl p-6">
                <h3 class="text-xl font-bold text-darkpurple mb-6 flex items-center gap-2">
                    <i class="fas fa-camera text-accent"></i>
                    Foto de Perfil
                </h3>
                <div class="flex items-center gap-6">
                    <div class="flex-shrink-0">
                        <?php if ($usuario['foto_perfil']): ?>
                            <img src="<?php echo htmlspecialchars($usuario['foto_perfil']); ?>"
                                alt="Foto actual"
                                class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-lg">
                        <?php else: ?>
                            <div class="w-24 h-24 rounded-full bg-gradient-to-br from-primary to-accent flex items-center justify-center text-white text-2xl font-bold border-4 border-white shadow-lg">
                                <?php echo strtoupper(substr($usuario['nombre'], 0, 1) . substr($usuario['apellido_paterno'], 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1">
                        <label for="foto_perfil" class="block text-sm font-semibold text-darkpurple mb-2">Nueva Foto (opcional)</label>
                        <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*"
                            class="w-full px-4 py-3 border border-cadet/50 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-secondary">
                        <p class="text-sm text-cadet mt-2">Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 2MB</p>
                    </div>
                </div>
            </div>

            <!-- Cambio de Contraseña -->
            <div class="bg-gradient-to-br from-primary/5 to-accent/5 rounded-xl p-6">
                <h3 class="text-xl font-bold text-darkpurple mb-6 flex items-center gap-2">
                    <i class="fas fa-lock text-accent"></i>
                    Cambio de Contraseña (opcional)
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="password_actual" class="block text-sm font-semibold text-darkpurple mb-2">Contraseña Actual</label>
                        <input type="password" id="password_actual" name="password_actual"
                            class="w-full px-4 py-3 border border-cadet/50 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent">
                    </div>
                    <div>
                        <label for="password_nueva" class="block text-sm font-semibold text-darkpurple mb-2">Nueva Contraseña</label>
                        <input type="password" id="password_nueva" name="password_nueva"
                            class="w-full px-4 py-3 border border-cadet/50 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent">
                    </div>
                    <div class="md:col-span-2">
                        <label for="password_confirmar" class="block text-sm font-semibold text-darkpurple mb-2">Confirmar Nueva Contraseña</label>
                        <input type="password" id="password_confirmar" name="password_confirmar"
                            class="w-full px-4 py-3 border border-cadet/50 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent">
                    </div>
                </div>
                <p class="text-sm text-cadet mt-3">Deja estos campos vacíos si no deseas cambiar la contraseña</p>
            </div>

            <!-- Botones de Acción -->
            <div class="flex items-center justify-between pt-6 border-t border-cadet/30">
                <a href="perfil.php" class="text-sm text-cadet hover:text-primary flex items-center gap-1">
                    <i class="fas fa-times"></i>
                    Cancelar
                </a>
                <button type="submit" class="bg-primary text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 shadow hover:bg-secondary focus:ring-2 focus:ring-accent focus:outline-none flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Validación de contraseñas
    document.getElementById('password_nueva').addEventListener('input', function() {
        const passwordNueva = this.value;
        const passwordConfirmar = document.getElementById('password_confirmar').value;

        if (passwordConfirmar && passwordNueva !== passwordConfirmar) {
            document.getElementById('password_confirmar').setCustomValidity('Las contraseñas no coinciden');
        } else {
            document.getElementById('password_confirmar').setCustomValidity('');
        }
    });

    document.getElementById('password_confirmar').addEventListener('input', function() {
        const passwordNueva = document.getElementById('password_nueva').value;
        const passwordConfirmar = this.value;

        if (passwordNueva !== passwordConfirmar) {
            this.setCustomValidity('Las contraseñas no coinciden');
        } else {
            this.setCustomValidity('');
        }
    });
</script>

<?php require_once '../includes/footer.php'; ?>