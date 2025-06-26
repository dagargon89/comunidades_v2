<?php
$page_title = "Mi Perfil";
require_once '../includes/header.php';
if (!isAuthenticated()) redirect('/auth/login.php');

$pdo = getDBConnection();
$stmt = $pdo->prepare("
    SELECT u.*, o.nombre as organizacion_nombre 
    FROM usuarios u 
    LEFT JOIN organizaciones o ON u.organizacion_id = o.id 
    WHERE u.id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener roles del usuario
$stmt = $pdo->prepare("
    SELECT r.nombre 
    FROM roles r 
    JOIN usuario_roles ur ON r.id = ur.rol_id 
    WHERE ur.usuario_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$roles = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="flex flex-col items-center justify-center min-h-[70vh] py-8">
    <div class="bg-white rounded-2xl shadow-2xl p-8 border border-cadet/40 w-full max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-primary flex items-center gap-3">
                <i class="fas fa-user-circle text-accent text-4xl"></i>
                Mi Perfil
            </h1>
            <a href="update_perfil.php" class="bg-primary text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 shadow hover:bg-secondary focus:ring-2 focus:ring-accent focus:outline-none flex items-center gap-2">
                <i class="fas fa-edit"></i>
                Editar Perfil
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Columna izquierda: Foto y datos básicos -->
            <div class="lg:col-span-1">
                <div class="bg-gradient-to-br from-primary/10 to-accent/10 rounded-xl p-6 text-center">
                    <div class="relative inline-block mb-4">
                        <?php if ($usuario['foto_perfil']): ?>
                            <img src="<?php echo htmlspecialchars($usuario['foto_perfil']); ?>"
                                alt="Foto de perfil"
                                class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg">
                        <?php else: ?>
                            <div class="w-32 h-32 rounded-full bg-gradient-to-br from-primary to-accent flex items-center justify-center text-white text-4xl font-bold border-4 border-white shadow-lg">
                                <?php echo strtoupper(substr($usuario['nombre'], 0, 1) . substr($usuario['apellido_paterno'], 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <h2 class="text-xl font-bold text-darkpurple mb-2">
                        <?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido_paterno'] . ' ' . $usuario['apellido_materno']); ?>
                    </h2>

                    <?php if ($usuario['puesto']): ?>
                        <p class="text-cadet font-medium mb-3">
                            <i class="fas fa-briefcase text-accent mr-2"></i>
                            <?php echo htmlspecialchars($usuario['puesto']); ?>
                        </p>
                    <?php endif; ?>

                    <?php if (!empty($roles)): ?>
                        <div class="flex flex-wrap justify-center gap-2 mb-4">
                            <?php foreach ($roles as $rol): ?>
                                <span class="bg-accent/20 text-accent px-3 py-1 rounded-full text-sm font-medium">
                                    <?php echo htmlspecialchars($rol); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="text-sm text-cadet">
                        <p><i class="fas fa-calendar-alt text-accent mr-2"></i>
                            Miembro desde: <?php echo date('d/m/Y', strtotime($usuario['created_at'])); ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Columna derecha: Información detallada -->
            <div class="lg:col-span-2">
                <div class="space-y-6">
                    <!-- Información Personal -->
                    <div class="bg-white border border-cadet/30 rounded-xl p-6">
                        <h3 class="text-lg font-bold text-darkpurple mb-4 flex items-center gap-2">
                            <i class="fas fa-user text-accent"></i>
                            Información Personal
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-cadet mb-1">Nombre(s)</label>
                                <p class="text-darkpurple"><?php echo htmlspecialchars($usuario['nombre']); ?></p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-cadet mb-1">Apellido Paterno</label>
                                <p class="text-darkpurple"><?php echo htmlspecialchars($usuario['apellido_paterno']); ?></p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-cadet mb-1">Apellido Materno</label>
                                <p class="text-darkpurple"><?php echo htmlspecialchars($usuario['apellido_materno'] ?: 'No especificado'); ?></p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-cadet mb-1">Puesto</label>
                                <p class="text-darkpurple"><?php echo htmlspecialchars($usuario['puesto'] ?: 'No especificado'); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Información de Contacto -->
                    <div class="bg-white border border-cadet/30 rounded-xl p-6">
                        <h3 class="text-lg font-bold text-darkpurple mb-4 flex items-center gap-2">
                            <i class="fas fa-envelope text-accent"></i>
                            Información de Contacto
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-cadet mb-1">Correo Electrónico</label>
                                <p class="text-darkpurple flex items-center gap-2">
                                    <i class="fas fa-envelope text-accent"></i>
                                    <?php echo htmlspecialchars($usuario['email']); ?>
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-cadet mb-1">Teléfono</label>
                                <p class="text-darkpurple flex items-center gap-2">
                                    <i class="fas fa-phone text-accent"></i>
                                    <?php echo htmlspecialchars($usuario['telefono'] ?: 'No especificado'); ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Información de Organización -->
                    <div class="bg-white border border-cadet/30 rounded-xl p-6">
                        <h3 class="text-lg font-bold text-darkpurple mb-4 flex items-center gap-2">
                            <i class="fas fa-building text-accent"></i>
                            Organización
                        </h3>
                        <div>
                            <label class="block text-sm font-semibold text-cadet mb-1">Organización</label>
                            <p class="text-darkpurple flex items-center gap-2">
                                <i class="fas fa-building text-accent"></i>
                                <?php echo htmlspecialchars($usuario['organizacion_nombre'] ?: 'No asignada'); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Estado de la Cuenta -->
                    <div class="bg-white border border-cadet/30 rounded-xl p-6">
                        <h3 class="text-lg font-bold text-darkpurple mb-4 flex items-center gap-2">
                            <i class="fas fa-shield-alt text-accent"></i>
                            Estado de la Cuenta
                        </h3>
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo $usuario['activo'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                <i class="fas fa-circle text-xs mr-2"></i>
                                <?php echo $usuario['activo'] ? 'Activa' : 'Inactiva'; ?>
                            </span>
                            <span class="text-sm text-cadet">
                                Última actualización: <?php echo date('d/m/Y H:i', strtotime($usuario['updated_at'])); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-cadet/30">
            <div class="flex items-center justify-between">
                <a href="/index.php" class="text-sm text-cadet hover:text-primary flex items-center gap-1">
                    <i class="fas fa-arrow-left"></i>
                    Volver al Dashboard
                </a>
                <a href="update_perfil.php" class="bg-primary text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 shadow hover:bg-secondary focus:ring-2 focus:ring-accent focus:outline-none flex items-center gap-2">
                    <i class="fas fa-edit"></i>
                    Editar Perfil
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>