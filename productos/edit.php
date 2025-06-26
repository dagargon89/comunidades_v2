<?php
$page_title = "Editar Producto";
require_once '../includes/header.php';
if (!isAuthenticated()) redirect('/auth/login.php');
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) redirect('index.php');
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT p.*, c.eje_id FROM productos p LEFT JOIN componentes c ON p.componente_id = c.id WHERE p.id = ?");
    $stmt->execute([$id]);
    $producto = $stmt->fetch();
    if (!$producto) redirect('index.php');

    $stmt_ejes = $pdo->query("SELECT id, nombre FROM ejes ORDER BY nombre");
    $ejes = $stmt_ejes->fetchAll();

    $sql_componentes = "SELECT c.id, c.nombre, e.nombre as eje_nombre FROM componentes c LEFT JOIN ejes e ON c.eje_id = e.id ORDER BY e.nombre, c.nombre";
    $stmt_componentes = $pdo->query($sql_componentes);
    $componentes = $stmt_componentes->fetchAll();
} catch (PDOException $e) {
    setFlashMessage('error', 'Error al cargar el producto.');
    redirect('index.php');
}
?>
<div class="flex flex-col items-center justify-center min-h-[70vh]">
    <div class="bg-white rounded-2xl shadow-2xl p-8 border border-cadet/40 w-full max-w-lg mx-auto">
        <h1 class="text-2xl font-bold text-primary flex items-center gap-2 mb-6">
            <i class="fas fa-box text-accent"></i> Editar Producto
        </h1>
        <form action="update.php" method="POST" class="space-y-6">
            <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
            <div>
                <label for="eje_id" class="block text-sm font-semibold text-darkpurple mb-1">Eje Estratégico *</label>
                <select id="eje_id" name="eje_id" required class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-darkpurple shadow-sm" onchange="filtrarComponentes()">
                    <option value="">Seleccionar eje</option>
                    <?php foreach ($ejes as $eje): ?>
                        <option value="<?php echo $eje['id']; ?>" <?php echo $producto['eje_id'] == $eje['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($eje['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="componente_id" class="block text-sm font-semibold text-darkpurple mb-1">Componente *</label>
                <select id="componente_id" name="componente_id" required class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-darkpurple shadow-sm">
                    <option value="">Seleccionar componente</option>
                    <?php foreach ($componentes as $comp): ?>
                        <option value="<?php echo $comp['id']; ?>" <?php echo $producto['componente_id'] == $comp['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($comp['eje_nombre'] . ' - ' . $comp['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="nombre" class="block text-sm font-semibold text-darkpurple mb-1">Nombre *</label>
                <input type="text" id="nombre" name="nombre" required maxlength="255" value="<?php echo htmlspecialchars($producto['nombre']); ?>" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm" placeholder="Nombre del producto">
            </div>
            <div>
                <label for="tipo_producto" class="block text-sm font-semibold text-darkpurple mb-1">Tipo de Producto</label>
                <input type="text" id="tipo_producto" name="tipo_producto" maxlength="100" value="<?php echo htmlspecialchars($producto['tipo_producto']); ?>" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm" placeholder="Tipo de producto">
            </div>
            <div>
                <label for="descripcion" class="block text-sm font-semibold text-darkpurple mb-1">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="4" maxlength="1000" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm resize-none" placeholder="Descripción del producto"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
            </div>
            <div class="flex items-center justify-between mt-4">
                <a href="index.php" class="text-sm text-cadet hover:text-primary flex items-center gap-1"><i class="fas fa-arrow-left"></i> Volver al listado</a>
                <button type="submit" class="bg-primary text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 shadow hover:bg-secondary focus:ring-2 focus:ring-accent focus:outline-none flex items-center gap-2">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const componentes = <?php echo json_encode($componentes); ?>;
    const ejeSelect = document.getElementById('eje_id');
    const componenteSelect = document.getElementById('componente_id');

    function filtrarComponentes() {
        const ejeId = parseInt(ejeSelect.value);
        componenteSelect.innerHTML = '<option value="">Seleccionar componente</option>';

        if (ejeId) {
            const componentesFiltrados = componentes.filter(comp => {
                const ejeComponente = componentes.find(c => c.id == comp.id)?.eje_nombre;
                return ejeComponente && ejeComponente.includes(ejeSelect.options[ejeSelect.selectedIndex].text);
            });

            componentesFiltrados.forEach(comp => {
                const option = document.createElement('option');
                option.value = comp.id;
                option.textContent = comp.nombre;
                componenteSelect.appendChild(option);
            });
        }
    }
</script>

<?php require_once '../includes/footer.php'; ?>