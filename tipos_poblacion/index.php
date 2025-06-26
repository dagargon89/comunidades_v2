<?php
$page_title = "Tipos de Población";
require_once '../includes/header.php';
if (!isAuthenticated()) redirect('/auth/login.php');
$busqueda = trim($_GET['q'] ?? '');
try {
    $pdo = getDBConnection();
    $sql = "SELECT * FROM tipos_poblacion ";
    $params = [];
    if ($busqueda !== '') {
        $sql .= "WHERE nombre LIKE ? ";
        $params[] = "%$busqueda%";
    }
    $sql .= "ORDER BY id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $tipos = $stmt->fetchAll();
} catch (PDOException $e) {
    $tipos = [];
}
?>
<div class="flex flex-col items-center justify-center min-h-[70vh]">
    <div class="bg-white rounded-2xl shadow-2xl p-8 border border-cadet/40 w-full max-w-2xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <h1 class="text-2xl font-bold text-primary flex items-center gap-2">
                <i class="fas fa-users text-accent"></i> Tipos de Población
            </h1>
            <form method="get" class="flex gap-2 w-full md:w-auto">
                <input type="text" name="q" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Buscar..." class="w-full md:w-64 px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm">
                <button type="submit" class="bg-secondary text-white px-4 py-2 rounded-lg font-medium hover:bg-primary transition-colors">Buscar</button>
            </form>
            <a href="create.php" class="bg-accent text-darkpurple font-semibold px-4 py-2 rounded-lg shadow hover:bg-xanthous/80 transition-colors flex items-center gap-2"><i class="fas fa-plus"></i> Nuevo Tipo</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-cadet/30">
                <thead class="bg-cadet/10">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-cadet/20">
                    <?php if (empty($tipos)): ?>
                        <tr>
                            <td colspan="2" class="text-center py-8 text-cadet">No hay tipos registrados.</td>
                        </tr>
                        <?php else: foreach ($tipos as $t): ?>
                            <tr class="hover:bg-cadet/10">
                                <td class="px-6 py-4 whitespace-nowrap text-darkpurple font-medium"><?php echo htmlspecialchars($t['nombre']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap flex gap-2">
                                    <a href="edit.php?id=<?php echo $t['id']; ?>" class="bg-secondary text-white px-3 py-1 rounded-lg text-xs font-semibold hover:bg-primary transition-colors"><i class="fas fa-edit"></i> Editar</a>
                                    <a href="delete.php?id=<?php echo $t['id']; ?>" class="bg-error text-white px-3 py-1 rounded-lg text-xs font-semibold hover:bg-bittersweet/80 transition-colors" onclick="return confirm('¿Eliminar este tipo?');"><i class="fas fa-trash"></i> Eliminar</a>
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