<?php
require_once '../includes/config.php';
if (!isAuthenticated()) redirect('/auth/login.php');
$page_title = "Componentes";
require_once '../includes/header.php';
$busqueda = trim($_GET['q'] ?? '');
$eje_id = intval($_GET['eje_id'] ?? 0);
try {
    $pdo = getDBConnection();
    $sql = "SELECT c.*, e.nombre as eje_nombre, COUNT(p.id) as productos_count FROM componentes c LEFT JOIN ejes e ON c.eje_id = e.id LEFT JOIN productos p ON c.id = p.componente_id ";
    $params = [];
    $where_conditions = [];

    if ($busqueda !== '') {
        $where_conditions[] = "(c.nombre LIKE ? OR c.descripcion LIKE ?)";
        $params[] = "%$busqueda%";
        $params[] = "%$busqueda%";
    }

    if ($eje_id > 0) {
        $where_conditions[] = "c.eje_id = ?";
        $params[] = $eje_id;
    }

    if (!empty($where_conditions)) {
        $sql .= "WHERE " . implode(' AND ', $where_conditions) . " ";
    }

    $sql .= "GROUP BY c.id ORDER BY e.nombre, c.nombre";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $componentes = $stmt->fetchAll();

    // Obtener ejes para el filtro
    $stmt_ejes = $pdo->query("SELECT id, nombre FROM ejes ORDER BY nombre");
    $ejes = $stmt_ejes->fetchAll();
} catch (PDOException $e) {
    $componentes = [];
    $ejes = [];
}
?>
<div class="flex flex-col items-center justify-center min-h-[70vh]">
    <div class="bg-white rounded-2xl shadow-2xl p-8 border border-cadet/40 w-full max-w-6xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <h1 class="text-2xl font-bold text-primary flex items-center gap-2">
                <i class="fas fa-cubes text-accent"></i> Componentes
            </h1>
            <div class="flex flex-col md:flex-row gap-2 w-full md:w-auto">
                <form method="get" class="flex gap-2">
                    <select name="eje_id" class="px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-darkpurple shadow-sm">
                        <option value="">Todos los ejes</option>
                        <?php foreach ($ejes as $eje): ?>
                            <option value="<?php echo $eje['id']; ?>" <?php echo $eje_id == $eje['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($eje['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" name="q" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Buscar..." class="w-full md:w-64 px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm">
                    <button type="submit" class="bg-secondary text-white px-4 py-2 rounded-lg font-medium hover:bg-primary transition-colors">Filtrar</button>
                </form>
                <a href="create.php" class="bg-accent text-darkpurple font-semibold px-4 py-2 rounded-lg shadow hover:bg-xanthous/80 transition-colors flex items-center gap-2"><i class="fas fa-plus"></i> Nuevo Componente</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-cadet/30">
                <thead class="bg-cadet/10">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">Eje</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">Descripción</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">Productos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-cadet/20">
                    <?php if (empty($componentes)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-8 text-cadet">No hay componentes registrados.</td>
                        </tr>
                        <?php else: foreach ($componentes as $c): ?>
                            <tr class="hover:bg-cadet/10">
                                <td class="px-6 py-4 whitespace-nowrap text-darkpurple font-medium"><?php echo htmlspecialchars($c['eje_nombre']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-darkpurple font-medium"><?php echo htmlspecialchars($c['nombre']); ?></td>
                                <td class="px-6 py-4 text-darkpurple">
                                    <?php echo htmlspecialchars(substr($c['descripcion'], 0, 100)) . (strlen($c['descripcion']) > 100 ? '...' : ''); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="bg-secondary text-white px-2 py-1 rounded-full text-xs font-semibold"><?php echo $c['productos_count']; ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap flex gap-2">
                                    <a href="edit.php?id=<?php echo $c['id']; ?>" class="bg-secondary text-white px-3 py-1 rounded-lg text-xs font-semibold hover:bg-primary transition-colors"><i class="fas fa-edit"></i> Editar</a>
                                    <a href="delete.php?id=<?php echo $c['id']; ?>" class="bg-error text-white px-3 py-1 rounded-lg text-xs font-semibold hover:bg-bittersweet/80 transition-colors" onclick="return confirm('¿Eliminar este componente? Se eliminarán también todos sus productos asociados.');"><i class="fas fa-trash"></i> Eliminar</a>
                                </td>
                            </tr>
                    <?php endforeach;
                    endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>