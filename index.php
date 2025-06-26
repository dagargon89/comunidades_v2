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

<div class="space-y-6">
    <!-- Bienvenida -->
    <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">¡Bienvenido, <?php echo htmlspecialchars($user['nombre']); ?>!</h1>
                <p class="text-primary-100 mt-1">
                    <?php echo htmlspecialchars($user['organizacion_nombre'] ?? 'Sin organización asignada'); ?>
                </p>
            </div>
            <div class="text-right">
                <p class="text-sm text-primary-100">Último acceso</p>
                <p class="font-semibold"><?php echo date('d/m/Y H:i'); ?></p>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-calendar-alt text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Actividades</p>
                    <p class="text-2xl font-semibold text-gray-900"><?php echo number_format($total_actividades); ?></p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Beneficiarios</p>
                    <p class="text-2xl font-semibold text-gray-900"><?php echo number_format($total_beneficiarios); ?></p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-building text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Organizaciones</p>
                    <p class="text-2xl font-semibold text-gray-900"><?php echo number_format($total_organizaciones); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="/actividades/create.php" class="card hover:shadow-lg transition-shadow duration-200">
            <div class="text-center">
                <div class="mx-auto w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center mb-3">
                    <i class="fas fa-plus text-primary-600 text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-900">Nueva Actividad</h3>
                <p class="text-sm text-gray-600 mt-1">Crear una nueva actividad</p>
            </div>
        </a>

        <a href="/beneficiarios/create.php" class="card hover:shadow-lg transition-shadow duration-200">
            <div class="text-center">
                <div class="mx-auto w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-3">
                    <i class="fas fa-user-plus text-green-600 text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-900">Nuevo Beneficiario</h3>
                <p class="text-sm text-gray-600 mt-1">Registrar un beneficiario</p>
            </div>
        </a>

        <a href="/organizaciones/create.php" class="card hover:shadow-lg transition-shadow duration-200">
            <div class="text-center">
                <div class="mx-auto w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mb-3">
                    <i class="fas fa-building text-purple-600 text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-900">Nueva Organización</h3>
                <p class="text-sm text-gray-600 mt-1">Agregar organización</p>
            </div>
        </a>

        <a href="/gantt.php" class="card hover:shadow-lg transition-shadow duration-200">
            <div class="text-center">
                <div class="mx-auto w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mb-3">
                    <i class="fas fa-chart-bar text-orange-600 text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-900">Vista Gantt</h3>
                <p class="text-sm text-gray-600 mt-1">Ver cronograma</p>
            </div>
        </a>
    </div>

    <!-- Actividades recientes -->
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Actividades Recientes</h2>
            <a href="/actividades/" class="text-sm text-primary-600 hover:text-primary-500">
                Ver todas <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>

        <?php if (empty($actividades_recientes)): ?>
            <div class="text-center py-8">
                <i class="fas fa-calendar-times text-gray-400 text-4xl mb-3"></i>
                <p class="text-gray-500">No hay actividades registradas</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actividad
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Producto
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Responsable
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($actividades_recientes as $actividad): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($actividad['nombre']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?php echo htmlspecialchars($actividad['producto_nombre'] ?? 'N/A'); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?php echo htmlspecialchars($actividad['responsable_nombre'] ?? 'N/A'); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
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