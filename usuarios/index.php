<?php
require_once '../includes/config.php';
if (!isAuthenticated() || !hasRole('admin')) redirect('/index.php');

$busqueda = trim($_GET['q'] ?? '');
try {
    $pdo = getDBConnection();
    $sql = "SELECT u.*, o.nombre as organizacion_nombre FROM usuarios u LEFT JOIN organizaciones o ON u.organizacion_id = o.id ";
    $params = [];
    if ($busqueda !== '') {
        $sql .= "WHERE u.nombre LIKE ? OR u.email LIKE ? ";
        $params[] = "%$busqueda%";
        $params[] = "%$busqueda%";
    }
    $sql .= "ORDER BY u.nombre, u.apellido_paterno";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $usuarios = $stmt->fetchAll();
} catch (PDOException $e) {
    $usuarios = [];
}
require_once '../includes/header.php';
?>
<div class="flex flex-col items-center justify-center min-h-[70vh]">
    <div class="bg-white rounded-2xl shadow-2xl p-8 border border-cadet/40 w-full w-[90vw] mx-auto">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <h1 class="text-2xl font-bold text-primary flex items-center gap-2">
                <i class="fas fa-users text-accent"></i> Usuarios
            </h1>
            <form method="get" class="flex gap-2 w-full md:w-auto">
                <input type="text" name="q" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Buscar usuario..." class="w-full md:w-64 px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm">
                <button type="submit" class="bg-secondary text-white px-4 py-2 rounded-lg font-medium hover:bg-primary transition-colors">Buscar</button>
            </form>
            <a href="create.php" class="bg-accent text-darkpurple font-semibold px-4 py-2 rounded-lg shadow hover:bg-xanthous/80 transition-colors flex items-center gap-2"><i class="fas fa-plus"></i> Nuevo Usuario</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-cadet/30">
                <thead class="bg-cadet/10">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">Organización</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">Activo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-cadet/20">
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido_paterno'] . ' ' . $usuario['apellido_materno']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php echo htmlspecialchars($usuario['email']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php echo htmlspecialchars($usuario['organizacion_nombre'] ?: 'No asignada'); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo $usuario['activo'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <i class="fas fa-circle text-xs mr-2"></i>
                                    <?php echo $usuario['activo'] ? 'Sí' : 'No'; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap flex gap-2">
                                <a href="edit.php?id=<?php echo $usuario['id']; ?>" class="bg-primary text-white px-3 py-1 rounded hover:bg-secondary text-xs font-semibold flex items-center gap-1"><i class="fas fa-edit"></i> Editar</a>
                                <a href="delete.php?id=<?php echo $usuario['id']; ?>" class="bg-error text-white px-3 py-1 rounded hover:bg-bittersweet text-xs font-semibold flex items-center gap-1" onclick="return confirm('¿Seguro que deseas eliminar este usuario?');"><i class="fas fa-trash"></i> Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>