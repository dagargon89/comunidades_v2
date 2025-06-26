<?php
$page_title = "Nuevo Componente";
require_once '../includes/header.php';
if (!isAuthenticated()) redirect('/auth/login.php');
try {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT id, nombre FROM ejes ORDER BY nombre");
    $ejes = $stmt->fetchAll();
} catch (PDOException $e) {
    $ejes = [];
}
?>
<div class="flex flex-col items-center justify-center min-h-[70vh]">
    <div class="bg-white rounded-2xl shadow-2xl p-8 border border-cadet/40 w-full max-w-lg mx-auto">
        <h1 class="text-2xl font-bold text-primary flex items-center gap-2 mb-6">
            <i class="fas fa-cubes text-accent"></i> Nuevo Componente
        </h1>
        <form action="store.php" method="POST" class="space-y-6">
            <div>
                <label for="eje_id" class="block text-sm font-semibold text-darkpurple mb-1">Eje Estratégico *</label>
                <select id="eje_id" name="eje_id" required class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-darkpurple shadow-sm">
                    <option value="">Seleccionar eje</option>
                    <?php foreach ($ejes as $eje): ?>
                        <option value="<?php echo $eje['id']; ?>"><?php echo htmlspecialchars($eje['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="nombre" class="block text-sm font-semibold text-darkpurple mb-1">Nombre *</label>
                <input type="text" id="nombre" name="nombre" required maxlength="255" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm" placeholder="Nombre del componente">
            </div>
            <div>
                <label for="descripcion" class="block text-sm font-semibold text-darkpurple mb-1">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="4" maxlength="1000" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm resize-none" placeholder="Descripción del componente"></textarea>
            </div>
            <div class="flex items-center justify-between mt-4">
                <a href="index.php" class="text-sm text-cadet hover:text-primary flex items-center gap-1"><i class="fas fa-arrow-left"></i> Volver al listado</a>
                <button type="submit" class="bg-primary text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 shadow hover:bg-secondary focus:ring-2 focus:ring-accent focus:outline-none flex items-center gap-2">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>