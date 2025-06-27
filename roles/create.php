<?php
require_once '../includes/config.php';
if (!isAuthenticated() || !hasRole('admin')) redirect('/index.php');
$page_title = "Nuevo Rol";
require_once '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    if ($nombre === '') {
        setFlashMessage('error', 'El nombre es obligatorio.');
        redirect('create.php');
    }
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("INSERT INTO roles (nombre, descripcion) VALUES (?, ?)");
        $stmt->execute([$nombre, $descripcion]);
        setFlashMessage('success', 'Rol creado correctamente.');
        redirect('index.php');
    } catch (PDOException $e) {
        setFlashMessage('error', 'Error al crear el rol: ' . $e->getMessage());
        redirect('create.php');
    }
}
?>
<div class="flex flex-col items-center justify-center min-h-[70vh] py-8">
    <div class="bg-white rounded-2xl shadow-2xl p-8 border border-cadet/40 w-full max-w-lg mx-auto">
        <h1 class="text-2xl font-bold text-primary flex items-center gap-2 mb-6">
            <i class="fas fa-user-shield text-accent"></i> Nuevo Rol
        </h1>
        <form action="create.php" method="POST" class="space-y-6">
            <div>
                <label class="block text-sm font-semibold text-cadet mb-1">Nombre *</label>
                <input type="text" name="nombre" class="w-full px-4 py-2 border border-cadet/50 rounded-lg" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-cadet mb-1">Descripción</label>
                <textarea name="descripcion" class="w-full px-4 py-2 border border-cadet/50 rounded-lg"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <a href="index.php" class="text-cadet hover:text-primary flex items-center gap-1"><i class="fas fa-arrow-left"></i> Cancelar</a>
                <button type="submit" class="bg-primary text-white font-semibold px-6 py-2 rounded-lg shadow hover:bg-secondary transition-colors flex items-center gap-2"><i class="fas fa-save"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?> 