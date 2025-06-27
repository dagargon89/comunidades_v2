<?php
require_once '../includes/config.php';
if (!isAuthenticated()) redirect('/auth/login.php');
$page_title = "Ejes Estratégicos";
require_once '../includes/header.php';
$busqueda = trim($_GET['q'] ?? '');
try {
    $pdo = getDBConnection();
    $sql = "SELECT e.*, COUNT(c.id) as componentes_count FROM ejes e LEFT JOIN componentes c ON e.id = c.eje_id ";
    $params = [];
    if ($busqueda !== '') {
        $sql .= "WHERE e.nombre LIKE ? OR e.descripcion LIKE ? ";
        $params[] = "%$busqueda%";
        $params[] = "%$busqueda%";
    }
    $sql .= "GROUP BY e.id ORDER BY e.id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $ejes = $stmt->fetchAll();
} catch (PDOException $e) {
    $ejes = [];
}
?>
<div class="flex flex-col items-center justify-center min-h-[70vh]">
    <div class="bg-white rounded-2xl shadow-2xl p-8 border border-cadet/40 w-full max-w-4xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <h1 class="text-2xl font-bold text-primary flex items-center gap-2">
                <i class="fas fa-sitemap text-accent"></i> Ejes Estratégicos
            </h1>
            <form method="get" class="flex gap-2 w-full md:w-auto">
                <input type="text" name="q" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Buscar..." class="w-full md:w-64 px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm">
                <button type="submit" class="bg-secondary text-white px-4 py-2 rounded-lg font-medium hover:bg-primary transition-colors">Buscar</button>
            </form>
            <a href="create.php" class="bg-accent text-darkpurple font-semibold px-4 py-2 rounded-lg shadow hover:bg-xanthous/80 transition-colors flex items-center gap-2"><i class="fas fa-plus"></i> Nuevo Eje</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-cadet/30">
                <thead class="bg-cadet/10">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">Descripción</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">Componentes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-cadet/20">
                    <?php if (empty($ejes)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-8 text-cadet">No hay ejes registrados.</td>
                        </tr>
                        <?php else: foreach ($ejes as $e): ?>
                            <tr class="hover:bg-cadet/10">
                                <td class="px-6 py-4 whitespace-nowrap text-darkpurple font-medium"><?php echo htmlspecialchars($e['nombre']); ?></td>
                                <td class="px-6 py-4 text-darkpurple">
                                    <?php echo htmlspecialchars(substr($e['descripcion'], 0, 100)) . (strlen($e['descripcion']) > 100 ? '...' : ''); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="bg-secondary text-white px-2 py-1 rounded-full text-xs font-semibold"><?php echo $e['componentes_count']; ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap flex gap-2">
                                    <a href="edit.php?id=<?php echo $e['id']; ?>" class="bg-secondary text-white px-3 py-1 rounded-lg text-xs font-semibold hover:bg-primary transition-colors"><i class="fas fa-edit"></i> Editar</a>
                                    <a href="delete.php?id=<?php echo $e['id']; ?>" class="bg-error text-white px-3 py-1 rounded-lg text-xs font-semibold hover:bg-bittersweet/80 transition-colors" onclick="return confirm('¿Eliminar este eje? Se eliminarán también todos sus componentes y productos asociados.');"><i class="fas fa-trash"></i> Eliminar</a>
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