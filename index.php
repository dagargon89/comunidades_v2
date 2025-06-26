<?php
$page_title = "Dashboard";
require_once 'includes/header.php';

// Verificar autenticación
if (!isAuthenticated()) {
    redirect('/auth/login.php');
}

$user = getCurrentUser();

// Obtener estadísticas básicas
try {
    $pdo = getDBConnection();

    // Contar actividades
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM actividades");
    $total_actividades = $stmt->fetch()['total'];

    // Contar beneficiarios
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM beneficiarios");
    $total_beneficiarios = $stmt->fetch()['total'];

    // Contar organizaciones
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM organizaciones");
    $total_organizaciones = $stmt->fetch()['total'];

    // Actividades recientes
    $stmt = $pdo->query("
        SELECT a.*, p.nombre as producto_nombre, u.nombre as responsable_nombre 
        FROM actividades a 
        LEFT JOIN productos p ON a.producto_id = p.id 
        LEFT JOIN usuarios u ON a.responsable_id = u.id 
        ORDER BY a.created_at DESC 
        LIMIT 5
    ");
    $actividades_recientes = $stmt->fetchAll();
} catch (PDOException $e) {
    $total_actividades = 0;
    $total_beneficiarios = 0;
    $total_organizaciones = 0;
    $actividades_recientes = [];
}
?>

<div class="space-y-8">
    <!-- Bienvenida -->
    <div class="bg-gradient-to-r from-primary to-secondary rounded-2xl p-6 text-white shadow-lg flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold">¡Bienvenido, <?php echo htmlspecialchars($user['nombre']); ?>!</h1>
            <p class="text-base mt-1 text-xanthous">
                <?php echo htmlspecialchars($user['organizacion_nombre'] ?? 'Sin organización asignada'); ?>
            </p>
        </div>
        <div class="text-right mt-4 md:mt-0">
            <p class="text-sm text-cadet">Último acceso</p>
            <p class="font-semibold text-white"><?php echo date('d/m/Y H:i'); ?></p>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl shadow-2xl p-6 border border-cadet/40 flex items-center">
            <div class="p-3 rounded-full bg-xanthous/30 text-xanthous">
                <i class="fas fa-calendar-alt text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-cadet">Total Actividades</p>
                <p class="text-2xl font-semibold text-darkpurple"><?php echo number_format($total_actividades); ?></p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl p-6 border border-cadet/40 flex items-center">
            <div class="p-3 rounded-full bg-seagreen/30 text-seagreen">
                <i class="fas fa-users text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-cadet">Total Beneficiarios</p>
                <p class="text-2xl font-semibold text-darkpurple"><?php echo number_format($total_beneficiarios); ?></p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl p-6 border border-cadet/40 flex items-center">
            <div class="p-3 rounded-full bg-primary/30 text-primary">
                <i class="fas fa-building text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-cadet">Organizaciones</p>
                <p class="text-2xl font-semibold text-darkpurple"><?php echo number_format($total_organizaciones); ?></p>
            </div>
        </div>
    </div>

    <!-- Acciones rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="/actividades/create.php" class="bg-white rounded-2xl shadow-2xl p-6 border border-cadet/40 hover:shadow-xl transition-shadow duration-200 flex flex-col items-center text-center group">
            <div class="w-12 h-12 bg-xanthous/30 rounded-full flex items-center justify-center mb-3">
                <i class="fas fa-plus text-xanthous text-xl"></i>
            </div>
            <h3 class="font-semibold text-darkpurple">Nueva Actividad</h3>
            <p class="text-sm text-cadet mt-1">Crear una nueva actividad</p>
        </a>

        <a href="/beneficiarios/create.php" class="bg-white rounded-2xl shadow-2xl p-6 border border-cadet/40 hover:shadow-xl transition-shadow duration-200 flex flex-col items-center text-center group">
            <div class="w-12 h-12 bg-seagreen/30 rounded-full flex items-center justify-center mb-3">
                <i class="fas fa-user-plus text-seagreen text-xl"></i>
            </div>
            <h3 class="font-semibold text-darkpurple">Nuevo Beneficiario</h3>
            <p class="text-sm text-cadet mt-1">Registrar un beneficiario</p>
        </a>

        <a href="/organizaciones/create.php" class="bg-white rounded-2xl shadow-2xl p-6 border border-cadet/40 hover:shadow-xl transition-shadow duration-200 flex flex-col items-center text-center group">
            <div class="w-12 h-12 bg-primary/30 rounded-full flex items-center justify-center mb-3">
                <i class="fas fa-building text-primary text-xl"></i>
            </div>
            <h3 class="font-semibold text-darkpurple">Nueva Organización</h3>
            <p class="text-sm text-cadet mt-1">Agregar organización</p>
        </a>

        <a href="/gantt.php" class="bg-white rounded-2xl shadow-2xl p-6 border border-cadet/40 hover:shadow-xl transition-shadow duration-200 flex flex-col items-center text-center group">
            <div class="w-12 h-12 bg-bittersweet/30 rounded-full flex items-center justify-center mb-3">
                <i class="fas fa-chart-bar text-bittersweet text-xl"></i>
            </div>
            <h3 class="font-semibold text-darkpurple">Vista Gantt</h3>
            <p class="text-sm text-cadet mt-1">Ver cronograma</p>
        </a>
    </div>

    <!-- Actividades recientes -->
    <div class="bg-white rounded-2xl shadow-2xl p-8 border border-cadet/40">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-darkpurple">Actividades Recientes</h2>
            <a href="/actividades/" class="text-sm text-secondary hover:text-primary font-semibold">
                Ver todas <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>

        <?php if (empty($actividades_recientes)): ?>
            <div class="text-center py-8">
                <i class="fas fa-calendar-times text-cadet text-4xl mb-3"></i>
                <p class="text-cadet">No hay actividades registradas</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-cadet/30">
                    <thead class="bg-cadet/10">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">
                                Actividad
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">
                                Producto
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">
                                Responsable
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">
                                Fecha
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-cadet/20">
                        <?php foreach ($actividades_recientes as $actividad): ?>
                            <tr class="hover:bg-cadet/10">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-darkpurple">
                                        <?php echo htmlspecialchars($actividad['nombre']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-darkpurple">
                                        <?php echo htmlspecialchars($actividad['producto_nombre'] ?? 'N/A'); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-darkpurple">
                                        <?php echo htmlspecialchars($actividad['responsable_nombre'] ?? 'N/A'); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-cadet">
                                    <?php echo date('d/m/Y', strtotime($actividad['created_at'])); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>