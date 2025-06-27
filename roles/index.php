<?php
require_once '../includes/config.php';
if (!isAuthenticated() || !hasRole('admin')) redirect('/index.php');
$page_title = "Roles";
require_once '../includes/header.php';

try {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT * FROM roles ORDER BY nombre");
    $roles = $stmt->fetchAll();
} catch (PDOException $e) {
    $roles = [];
}
?>
<div class="flex flex-col items-center justify-center min-h-[70vh]">
    <div class="bg-white rounded-2xl shadow-2xl p-8 border border-cadet/40 w-full max-w-2xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <h1 class="text-2xl font-bold text-primary flex items-center gap-2">
                <i class="fas fa-user-shield text-accent"></i> Roles
            </h1>
            <a href="create.php" class="bg-accent text-darkpurple font-semibold px-4 py-2 rounded-lg shadow hover:bg-xanthous/80 transition-colors flex items-center gap-2"><i class="fas fa-plus"></i> Nuevo Rol</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-cadet/30">
                <thead class="bg-cadet/10">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">Descripción</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-cadet/20">
                    <?php foreach ($roles as $rol): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($rol['nombre']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($rol['descripcion']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap flex gap-2">
                                <a href="edit.php?id=<?php echo $rol['id']; ?>" class="bg-primary text-white px-3 py-1 rounded hover:bg-secondary text-xs font-semibold flex items-center gap-1"><i class="fas fa-edit"></i> Editar</a>
                                <a href="delete.php?id=<?php echo $rol['id']; ?>" class="bg-error text-white px-3 py-1 rounded hover:bg-bittersweet text-xs font-semibold flex items-center gap-1" onclick="return confirm('¿Seguro que deseas eliminar este rol?');"><i class="fas fa-trash"></i> Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?> 