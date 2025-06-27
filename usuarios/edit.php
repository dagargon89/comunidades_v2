<?php
require_once '../includes/config.php';
if (!isAuthenticated() || !hasRole('admin')) redirect('/index.php');

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) redirect('index.php');
$pdo = getDBConnection();
// Obtener usuario
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch();
if (!$usuario) redirect('index.php');
// Obtener roles y roles actuales
$roles = $pdo->query("SELECT * FROM roles ORDER BY nombre")->fetchAll();
$stmt = $pdo->prepare("SELECT rol_id FROM usuario_roles WHERE usuario_id = ?");
$stmt->execute([$id]);
$roles_usuario = $stmt->fetchAll(PDO::FETCH_COLUMN);
// Organizaciones
$organizaciones = $pdo->query("SELECT * FROM organizaciones ORDER BY nombre")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido_paterno = trim($_POST['apellido_paterno'] ?? '');
    $apellido_materno = trim($_POST['apellido_materno'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $puesto = trim($_POST['puesto'] ?? '');
    $organizacion_id = intval($_POST['organizacion_id'] ?? 0);
    $activo = isset($_POST['activo']) ? 1 : 0;
    $roles_seleccionados = $_POST['roles'] ?? [];

    if (empty($nombre) || empty($apellido_paterno) || empty($email)) {
        setFlashMessage('error', 'Nombre, apellido paterno y email son obligatorios.');
        redirect('edit.php?id=' . $id);
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setFlashMessage('error', 'El formato del email no es válido.');
        redirect('edit.php?id=' . $id);
    }
    // Verificar email único
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
    $stmt->execute([$email, $id]);
    if ($stmt->fetch()) {
        setFlashMessage('error', 'El email ya está registrado por otro usuario.');
        redirect('edit.php?id=' . $id);
    }
    // Actualizar usuario
    $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, apellido_paterno = ?, apellido_materno = ?, email = ?, telefono = ?, puesto = ?, organizacion_id = ?, activo = ? WHERE id = ?");
    $stmt->execute([$nombre, $apellido_paterno, $apellido_materno, $email, $telefono, $puesto, $organizacion_id ?: null, $activo, $id]);
    // Actualizar roles
    $pdo->prepare("DELETE FROM usuario_roles WHERE usuario_id = ?")->execute([$id]);
    foreach ($roles_seleccionados as $rol_id) {
        $stmt = $pdo->prepare("INSERT INTO usuario_roles (usuario_id, rol_id) VALUES (?, ?)");
        $stmt->execute([$id, $rol_id]);
    }
    setFlashMessage('success', 'Usuario actualizado correctamente.');
    redirect('index.php');
}
require_once '../includes/header.php';
?>
<div class="flex flex-col items-center justify-center min-h-[70vh] py-8">
    <div class="bg-white rounded-2xl shadow-2xl p-8 border border-cadet/40 w-full max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold text-primary flex items-center gap-2 mb-6">
            <i class="fas fa-user-edit text-accent"></i> Editar Usuario
        </h1>
        <form action="edit.php?id=<?php echo $id; ?>" method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-cadet mb-1">Nombre(s) *</label>
                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" class="w-full px-4 py-2 border border-cadet/50 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-cadet mb-1">Apellido Paterno *</label>
                    <input type="text" name="apellido_paterno" value="<?php echo htmlspecialchars($usuario['apellido_paterno']); ?>" class="w-full px-4 py-2 border border-cadet/50 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-cadet mb-1">Apellido Materno</label>
                    <input type="text" name="apellido_materno" value="<?php echo htmlspecialchars($usuario['apellido_materno']); ?>" class="w-full px-4 py-2 border border-cadet/50 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-cadet mb-1">Correo Electrónico *</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" class="w-full px-4 py-2 border border-cadet/50 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-cadet mb-1">Teléfono</label>
                    <input type="text" name="telefono" value="<?php echo htmlspecialchars($usuario['telefono']); ?>" class="w-full px-4 py-2 border border-cadet/50 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-cadet mb-1">Puesto</label>
                    <input type="text" name="puesto" value="<?php echo htmlspecialchars($usuario['puesto']); ?>" class="w-full px-4 py-2 border border-cadet/50 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-cadet mb-1">Organización</label>
                    <select name="organizacion_id" class="w-full px-4 py-2 border border-cadet/50 rounded-lg">
                        <option value="">Seleccionar organización</option>
                        <?php foreach ($organizaciones as $org): ?>
                            <option value="<?php echo $org['id']; ?>" <?php if ($usuario['organizacion_id'] == $org['id']) echo 'selected'; ?>><?php echo htmlspecialchars($org['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-cadet mb-1">Activo</label>
                    <input type="checkbox" name="activo" value="1" <?php if ($usuario['activo']) echo 'checked'; ?>>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-cadet mb-1">Roles *</label>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($roles as $rol): ?>
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="roles[]" value="<?php echo $rol['id']; ?>" class="accent-primary" <?php if (in_array($rol['id'], $roles_usuario)) echo 'checked'; ?>>
                            <?php echo htmlspecialchars($rol['nombre']); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <a href="index.php" class="text-cadet hover:text-primary flex items-center gap-1"><i class="fas fa-arrow-left"></i> Cancelar</a>
                <button type="submit" class="bg-primary text-white font-semibold px-6 py-2 rounded-lg shadow hover:bg-secondary transition-colors flex items-center gap-2"><i class="fas fa-save"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?> 