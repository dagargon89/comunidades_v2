<?php
require_once '../includes/config.php';
if (!isAuthenticated()) redirect('/auth/login.php');
$page_title = "Productos";
require_once '../includes/header.php';
$busqueda = trim($_GET['q'] ?? '');
$eje_id = intval($_GET['eje_id'] ?? 0);
$componente_id = intval($_GET['componente_id'] ?? 0);
try {
    $pdo = getDBConnection();
    $sql = "SELECT p.*, c.nombre as componente_nombre, e.nombre as eje_nombre, COUNT(a.id) as actividades_count FROM productos p LEFT JOIN componentes c ON p.componente_id = c.id LEFT JOIN ejes e ON c.eje_id = e.id LEFT JOIN actividades a ON p.id = a.producto_id ";
    $params = [];
    $where_conditions = [];

    if ($busqueda !== '') {
        $where_conditions[] = "(p.nombre LIKE ? OR p.descripcion LIKE ? OR p.tipo_producto LIKE ?)";
        $params[] = "%$busqueda%";
        $params[] = "%$busqueda%";
        $params[] = "%$busqueda%";
    }

    if ($eje_id > 0) {
        $where_conditions[] = "e.id = ?";
        $params[] = $eje_id;
    }

    if ($componente_id > 0) {
        $where_conditions[] = "c.id = ?";
        $params[] = $componente_id;
    }

    if (!empty($where_conditions)) {
        $sql .= "WHERE " . implode(' AND ', $where_conditions) . " ";
    }

    $sql .= "GROUP BY p.id ORDER BY e.nombre, c.nombre, p.nombre";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $productos = $stmt->fetchAll();

    // Obtener ejes y componentes para los filtros
    $stmt_ejes = $pdo->query("SELECT id, nombre FROM ejes ORDER BY nombre");
    $ejes = $stmt_ejes->fetchAll();

    $sql_componentes = "SELECT c.id, c.nombre, e.nombre as eje_nombre FROM componentes c LEFT JOIN ejes e ON c.eje_id = e.id ORDER BY e.nombre, c.nombre";
    $stmt_componentes = $pdo->query($sql_componentes);
    $componentes = $stmt_componentes->fetchAll();
} catch (PDOException $e) {
    $productos = [];
    $ejes = [];
    $componentes = [];
}
?>
<div class="flex flex-col items-center justify-center min-h-[70vh]">
    <div class="bg-white rounded-2xl shadow-2xl p-8 border border-cadet/40 w-full w-[90vw] mx-auto">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <h1 class="text-2xl font-bold text-primary flex items-center gap-2">
                <i class="fas fa-box text-accent"></i> Productos
            </h1>
            <div class="flex flex-col md:flex-row gap-2 w-full md:w-auto">
                <form method="get" class="flex flex-wrap gap-2">
                    <select name="eje_id" class="px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-darkpurple shadow-sm">
                        <option value="">Todos los ejes</option>
                        <?php foreach ($ejes as $eje): ?>
                            <option value="<?php echo $eje['id']; ?>" <?php echo $eje_id == $eje['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($eje['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="componente_id" class="px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-darkpurple shadow-sm">
                        <option value="">Todos los componentes</option>
                        <?php foreach ($componentes as $comp): ?>
                            <option value="<?php echo $comp['id']; ?>" <?php echo $componente_id == $comp['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($comp['eje_nombre'] . ' - ' . $comp['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" name="q" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Buscar..." class="w-full md:w-64 px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm">
                    <button type="submit" class="bg-secondary text-white px-4 py-2 rounded-lg font-medium hover:bg-primary transition-colors">Filtrar</button>
                </form>
                <a href="create.php" class="bg-accent text-darkpurple font-semibold px-4 py-2 rounded-lg shadow hover:bg-xanthous/80 transition-colors flex items-center gap-2"><i class="fas fa-plus"></i> Nuevo Producto</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-cadet/30">
                <thead class="bg-cadet/10">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">Eje</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">Componente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">Descripción</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">Actividades</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-cadet/20">
                    <?php if (empty($productos)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-8 text-cadet">No hay productos registrados.</td>
                        </tr>
                        <?php else: foreach ($productos as $p): ?>
                            <tr class="hover:bg-cadet/10">
                                <td class="px-6 py-4 whitespace-nowrap text-darkpurple font-medium"><?php echo htmlspecialchars($p['eje_nombre']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-darkpurple font-medium"><?php echo htmlspecialchars($p['componente_nombre']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-darkpurple font-medium"><?php echo htmlspecialchars($p['nombre']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-darkpurple"><?php echo htmlspecialchars($p['tipo_producto']); ?></td>
                                <td class="px-6 py-4 text-darkpurple">
                                    <?php echo htmlspecialchars(substr($p['descripcion'], 0, 80)) . (strlen($p['descripcion']) > 80 ? '...' : ''); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="bg-secondary text-white px-2 py-1 rounded-full text-xs font-semibold"><?php echo $p['actividades_count']; ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap flex gap-2">
                                    <a href="edit.php?id=<?php echo $p['id']; ?>" class="bg-secondary text-white px-3 py-1 rounded-lg text-xs font-semibold hover:bg-primary transition-colors"><i class="fas fa-edit"></i> Editar</a>
                                    <a href="delete.php?id=<?php echo $p['id']; ?>" class="bg-error text-white px-3 py-1 rounded-lg text-xs font-semibold hover:bg-bittersweet/80 transition-colors" onclick="return confirm('¿Eliminar este producto? Se eliminarán también todas sus actividades asociadas.');"><i class="fas fa-trash"></i> Eliminar</a>
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