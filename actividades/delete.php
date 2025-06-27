<?php
require_once '../includes/config.php';
if (!isAuthenticated()) redirect('/auth/login.php');

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) redirect('index.php');

try {
    $pdo = getDBConnection();

    // Verificar que la actividad existe
    $stmt = $pdo->prepare("SELECT id, nombre FROM actividades WHERE id = ?");
    $stmt->execute([$id]);
    $actividad = $stmt->fetch();

    if (!$actividad) {
        setFlashMessage('error', 'Actividad no encontrada.');
        redirect('index.php');
    }

    // Verificar si hay beneficiarios asociados
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM actividad_beneficiario WHERE actividad_id = ?");
    $stmt->execute([$id]);
    $beneficiarios_count = $stmt->fetch()['count'];

    // Si es una petición POST, proceder con la eliminación
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $pdo->beginTransaction();

        try {
            // Eliminar beneficiarios asociados (la tabla actividad_beneficiario tiene CASCADE)
            $stmt = $pdo->prepare("DELETE FROM actividad_beneficiario WHERE actividad_id = ?");
            $stmt->execute([$id]);

            // Eliminar la actividad
            $stmt = $pdo->prepare("DELETE FROM actividades WHERE id = ?");
            $stmt->execute([$id]);

            $pdo->commit();

            setFlashMessage('success', 'Actividad "' . htmlspecialchars($actividad['nombre']) . '" eliminada correctamente.');
            redirect('index.php');

        } catch (PDOException $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    // Si es GET, mostrar confirmación
    $page_title = "Eliminar Actividad";
    require_once '../includes/header.php';
    ?>

    <div class="flex flex-col items-center justify-center min-h-[70vh]">
        <div class="bg-white rounded-2xl shadow-2xl p-8 border border-cadet/40 w-full max-w-md mx-auto">
            <div class="text-center">
                <div class="text-6xl text-error mb-4">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h1 class="text-2xl font-bold text-darkpurple mb-4">Confirmar Eliminación</h1>
                <p class="text-cadet mb-6">
                    ¿Estás seguro de que quieres eliminar la actividad <strong>"<?php echo htmlspecialchars($actividad['nombre']); ?>"</strong>?
                </p>
                
                <?php if ($beneficiarios_count > 0): ?>
                    <div class="bg-error/10 border border-error/20 rounded-lg p-4 mb-6">
                        <div class="flex items-center gap-2 text-error mb-2">
                            <i class="fas fa-users"></i>
                            <span class="font-semibold">¡Atención!</span>
                        </div>
                        <p class="text-sm text-error">
                            Esta actividad tiene <strong><?php echo $beneficiarios_count; ?> beneficiario(s)</strong> registrado(s). 
                            Al eliminar la actividad, también se eliminarán todos los beneficiarios asociados.
                        </p>
                    </div>
                <?php endif; ?>

                <div class="flex gap-4">
                    <a href="index.php" class="flex-1 bg-cadet text-white py-3 px-6 rounded-lg font-semibold text-center">
                        Cancelar
                    </a>
                    <form method="POST" class="flex-1">
                        <button type="submit" class="w-full bg-error text-white py-3 px-6 rounded-lg font-semibold flex items-center justify-center gap-2">
                            <i class="fas fa-trash"></i>
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php require_once '../includes/footer.php'; ?>

<?php
} catch (PDOException $e) {
    setFlashMessage('error', 'Error al eliminar: ' . $e->getMessage());
    redirect('index.php');
}
?>
