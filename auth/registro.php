<?php
$page_title = "Registro";
require_once '../includes/config.php';

// Si ya está autenticado, redirigir al dashboard
if (isAuthenticated()) {
    redirect('/');
}

// Obtener organizaciones para el select
try {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT id, nombre FROM organizaciones ORDER BY nombre");
    $organizaciones = $stmt->fetchAll();
} catch (PDOException $e) {
    $organizaciones = [];
}

require_once '../includes/header.php';
?>

<div class="flex flex-col items-center justify-center min-h-[80vh]">
    <div class="bg-white rounded-2xl shadow-2xl p-8 border border-cadet/40 w-full max-w-2xl mx-auto">
        <div class="flex flex-col items-center mb-6">
            <div class="w-16 h-16 rounded-full bg-accent flex items-center justify-center mb-2 shadow">
                <i class="fas fa-user-plus text-3xl text-primary"></i>
            </div>
            <h2 class="text-2xl font-bold text-primary mb-1">Crear nueva cuenta</h2>
            <p class="text-sm text-cadet">
                O <a href="login.php" class="text-secondary font-semibold hover:underline">inicia sesión si ya tienes una cuenta</a>
            </p>
        </div>
        <form action="store.php" method="POST" class="space-y-6">
            <!-- Información personal -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="nombre" class="block text-sm font-semibold text-darkpurple mb-1">Nombre *</label>
                    <input type="text" id="nombre" name="nombre" required class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-white placeholder-cadet text-darkpurple shadow-sm" value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>">
                </div>
                <div>
                    <label for="apellido_paterno" class="block text-sm font-semibold text-darkpurple mb-1">Apellido Paterno *</label>
                    <input type="text" id="apellido_paterno" name="apellido_paterno" required class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-white placeholder-cadet text-darkpurple shadow-sm" value="<?php echo htmlspecialchars($_POST['apellido_paterno'] ?? ''); ?>">
                </div>
                <div>
                    <label for="apellido_materno" class="block text-sm font-semibold text-darkpurple mb-1">Apellido Materno</label>
                    <input type="text" id="apellido_materno" name="apellido_materno" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-white placeholder-cadet text-darkpurple shadow-sm" value="<?php echo htmlspecialchars($_POST['apellido_materno'] ?? ''); ?>">
                </div>
            </div>
            <!-- Información de contacto -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block text-sm font-semibold text-darkpurple mb-1">Correo Electrónico *</label>
                    <input type="email" id="email" name="email" required class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-white placeholder-cadet text-darkpurple shadow-sm" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                <div>
                    <label for="telefono" class="block text-sm font-semibold text-darkpurple mb-1">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-white placeholder-cadet text-darkpurple shadow-sm" value="<?php echo htmlspecialchars($_POST['telefono'] ?? ''); ?>">
                </div>
            </div>
            <!-- Información profesional -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="puesto" class="block text-sm font-semibold text-darkpurple mb-1">Puesto</label>
                    <input type="text" id="puesto" name="puesto" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-white placeholder-cadet text-darkpurple shadow-sm" value="<?php echo htmlspecialchars($_POST['puesto'] ?? ''); ?>">
                </div>
                <div>
                    <label for="organizacion_id" class="block text-sm font-semibold text-darkpurple mb-1">Organización</label>
                    <select id="organizacion_id" name="organizacion_id" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-white text-darkpurple shadow-sm">
                        <option value="">Selecciona una organización</option>
                        <?php foreach ($organizaciones as $org): ?>
                            <option value="<?php echo $org['id']; ?>" <?php echo (isset($_POST['organizacion_id']) && $_POST['organizacion_id'] == $org['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($org['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <!-- Contraseñas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-sm font-semibold text-darkpurple mb-1">Contraseña *</label>
                    <input type="password" id="password" name="password" required class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-white placeholder-cadet text-darkpurple shadow-sm" minlength="8">
                    <p class="text-xs text-cadet mt-1">Mínimo 8 caracteres</p>
                </div>
                <div>
                    <label for="password_confirm" class="block text-sm font-semibold text-darkpurple mb-1">Confirmar Contraseña *</label>
                    <input type="password" id="password_confirm" name="password_confirm" required class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-white placeholder-cadet text-darkpurple shadow-sm">
                </div>
            </div>
            <!-- Términos y condiciones -->
            <div class="flex items-center">
                <input id="terms" name="terms" type="checkbox" required class="h-4 w-4 accent-primary rounded">
                <label for="terms" class="ml-2 block text-sm text-primary">
                    Acepto los <a href="#" class="text-secondary hover:underline">términos y condiciones</a>
                </label>
            </div>
            <!-- Botones -->
            <div class="flex items-center justify-between mt-2">
                <a href="login.php" class="text-sm text-cadet hover:text-primary flex items-center gap-1">
                    <i class="fas fa-arrow-left"></i> Volver al login
                </a>
                <button type="submit" class="bg-primary text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 shadow hover:bg-secondary focus:ring-2 focus:ring-accent focus:outline-none flex items-center gap-2">
                    <i class="fas fa-user-plus"></i> Crear Cuenta
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Validación de contraseñas
    document.getElementById('password_confirm').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const confirm = this.value;
        if (password !== confirm) {
            this.setCustomValidity('Las contraseñas no coinciden');
        } else {
            this.setCustomValidity('');
        }
    });
    document.getElementById('password').addEventListener('input', function() {
        const confirm = document.getElementById('password_confirm');
        if (confirm.value) {
            confirm.dispatchEvent(new Event('input'));
        }
    });
</script>

<?php require_once '../includes/footer.php'; ?>