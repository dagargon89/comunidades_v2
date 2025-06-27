<?php
$page_title = "Editar Actividad";
require_once '../includes/header.php';
if (!isAuthenticated()) redirect('/auth/login.php');

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) redirect('index.php');

try {
    $pdo = getDBConnection();

    // Obtener la actividad a editar
    $stmt = $pdo->prepare("SELECT * FROM actividades WHERE id = ?");
    $stmt->execute([$id]);
    $actividad = $stmt->fetch();

    if (!$actividad) {
        setFlashMessage('error', 'Actividad no encontrada.');
        redirect('index.php');
    }

    // Obtener datos para los selectores
    $stmt_ejes = $pdo->query("SELECT id, nombre FROM ejes ORDER BY nombre");
    $ejes = $stmt_ejes->fetchAll();

    $sql_componentes = "SELECT c.id, c.nombre, e.nombre as eje_nombre FROM componentes c LEFT JOIN ejes e ON c.eje_id = e.id ORDER BY e.nombre, c.nombre";
    $stmt_componentes = $pdo->query($sql_componentes);
    $componentes = $stmt_componentes->fetchAll();

    $sql_productos = "SELECT p.id, p.nombre, c.nombre as componente_nombre, e.nombre as eje_nombre FROM productos p LEFT JOIN componentes c ON p.componente_id = c.id LEFT JOIN ejes e ON c.eje_id = e.id ORDER BY e.nombre, c.nombre, p.nombre";
    $stmt_productos = $pdo->query($sql_productos);
    $productos = $stmt_productos->fetchAll();

    $stmt_usuarios = $pdo->query("SELECT id, nombre, apellido_paterno, apellido_materno FROM usuarios WHERE activo = 1 ORDER BY nombre, apellido_paterno");
    $usuarios = $stmt_usuarios->fetchAll();

    $stmt_tipos = $pdo->query("SELECT id, nombre FROM tipos_actividad ORDER BY nombre");
    $tipos = $stmt_tipos->fetchAll();

    $stmt_estados = $pdo->query("SELECT id, nombre FROM estados_actividad ORDER BY nombre");
    $estados = $stmt_estados->fetchAll();

    $stmt_poligonos = $pdo->query("SELECT id, nombre FROM poligonos ORDER BY nombre");
    $poligonos = $stmt_poligonos->fetchAll();

    $stmt_tipos_poblacion = $pdo->query("SELECT id, nombre FROM tipos_poblacion ORDER BY nombre");
    $tipos_poblacion = $stmt_tipos_poblacion->fetchAll();

    // Obtener el componente y eje del producto seleccionado para el JavaScript
    $stmt_producto_info = $pdo->prepare("SELECT p.componente_id, c.eje_id FROM productos p LEFT JOIN componentes c ON p.componente_id = c.id WHERE p.id = ?");
    $stmt_producto_info->execute([$actividad['producto_id']]);
    $producto_info = $stmt_producto_info->fetch();

} catch (PDOException $e) {
    setFlashMessage('error', 'Error al cargar la actividad.');
    redirect('index.php');
}
?>

<div class="flex flex-col items-center justify-center min-h-[70vh]">
    <div class="bg-white rounded-2xl shadow-2xl p-8 border border-cadet/40 w-full max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold text-primary flex items-center gap-2 mb-6">
            <i class="fas fa-edit text-accent"></i> Editar Actividad
        </h1>
        <form action="update.php" method="POST" class="space-y-6">
            <input type="hidden" name="id" value="<?php echo $actividad['id']; ?>">
            
            <!-- Información básica -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="producto_id" class="block text-sm font-semibold text-darkpurple mb-1">Producto *</label>
                    <select id="producto_id" name="producto_id" required class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-darkpurple shadow-sm" onchange="filtrarComponentes()">
                        <option value="">Seleccionar producto</option>
                        <?php foreach ($productos as $producto): ?>
                            <option value="<?php echo $producto['id']; ?>" <?php echo $actividad['producto_id'] == $producto['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($producto['eje_nombre'] . ' - ' . $producto['componente_nombre'] . ' - ' . $producto['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="nombre" class="block text-sm font-semibold text-darkpurple mb-1">Nombre *</label>
                    <input type="text" id="nombre" name="nombre" required maxlength="255" value="<?php echo htmlspecialchars($actividad['nombre']); ?>" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm" placeholder="Nombre de la actividad">
                </div>
            </div>

            <div>
                <label for="descripcion" class="block text-sm font-semibold text-darkpurple mb-1">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="3" maxlength="1000" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm resize-none" placeholder="Descripción de la actividad"><?php echo htmlspecialchars($actividad['descripcion']); ?></textarea>
            </div>

            <!-- Clasificación -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="tipo_actividad_id" class="block text-sm font-semibold text-darkpurple mb-1">Tipo de Actividad</label>
                    <select id="tipo_actividad_id" name="tipo_actividad_id" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-darkpurple shadow-sm">
                        <option value="">Seleccionar tipo</option>
                        <?php foreach ($tipos as $tipo): ?>
                            <option value="<?php echo $tipo['id']; ?>" <?php echo $actividad['tipo_actividad_id'] == $tipo['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($tipo['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="tipo_poblacion_id" class="block text-sm font-semibold text-darkpurple mb-1">Tipo de Población</label>
                    <select id="tipo_poblacion_id" name="tipo_poblacion_id" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-darkpurple shadow-sm">
                        <option value="">Seleccionar tipo</option>
                        <?php foreach ($tipos_poblacion as $tp): ?>
                            <option value="<?php echo $tp['id']; ?>" <?php echo $actividad['tipo_poblacion_id'] == $tp['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($tp['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="estado_actividad_id" class="block text-sm font-semibold text-darkpurple mb-1">Estado</label>
                    <select id="estado_actividad_id" name="estado_actividad_id" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-darkpurple shadow-sm">
                        <option value="">Seleccionar estado</option>
                        <?php foreach ($estados as $est): ?>
                            <option value="<?php echo $est['id']; ?>" <?php echo $actividad['estado_actividad_id'] == $est['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($est['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Fechas y Responsable -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="fecha_inicio" class="block text-sm font-semibold text-darkpurple mb-1">Fecha de Inicio</label>
                    <input type="datetime-local" id="fecha_inicio" name="fecha_inicio" value="<?php echo $actividad['fecha_inicio'] ? date('Y-m-d\TH:i', strtotime($actividad['fecha_inicio'])) : ''; ?>" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-darkpurple shadow-sm">
                </div>
                <div>
                    <label for="fecha_fin" class="block text-sm font-semibold text-darkpurple mb-1">Fecha de Fin</label>
                    <input type="datetime-local" id="fecha_fin" name="fecha_fin" value="<?php echo $actividad['fecha_fin'] ? date('Y-m-d\TH:i', strtotime($actividad['fecha_fin'])) : ''; ?>" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-darkpurple shadow-sm">
                </div>
                <div>
                    <label for="responsable_id" class="block text-sm font-semibold text-darkpurple mb-1">Responsable</label>
                    <select id="responsable_id" name="responsable_id" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-darkpurple shadow-sm">
                        <option value="">Seleccionar responsable</option>
                        <?php foreach ($usuarios as $user): ?>
                            <option value="<?php echo $user['id']; ?>" <?php echo $actividad['responsable_id'] == $user['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($user['nombre'] . ' ' . $user['apellido_paterno'] . ' ' . $user['apellido_materno']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Ubicación -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="lugar" class="block text-sm font-semibold text-darkpurple mb-1">Lugar</label>
                    <input type="text" id="lugar" name="lugar" maxlength="255" value="<?php echo htmlspecialchars($actividad['lugar']); ?>" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm" placeholder="Lugar de la actividad">
                </div>
                <div>
                    <label for="poligono_id" class="block text-sm font-semibold text-darkpurple mb-1">Polígono</label>
                    <select id="poligono_id" name="poligono_id" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-darkpurple shadow-sm">
                        <option value="">Seleccionar polígono</option>
                        <?php foreach ($poligonos as $pol): ?>
                            <option value="<?php echo $pol['id']; ?>" <?php echo $actividad['poligono_id'] == $pol['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($pol['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Información Adicional -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="modalidad" class="block text-sm font-semibold text-darkpurple mb-1">Modalidad</label>
                    <input type="text" id="modalidad" name="modalidad" maxlength="50" value="<?php echo htmlspecialchars($actividad['modalidad']); ?>" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm" placeholder="Presencial, Virtual, etc.">
                </div>
                <div>
                    <label for="cantidad_sesiones" class="block text-sm font-semibold text-darkpurple mb-1">Cantidad de Sesiones</label>
                    <input type="number" id="cantidad_sesiones" name="cantidad_sesiones" min="0" value="<?php echo $actividad['cantidad_sesiones']; ?>" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm" placeholder="0">
                </div>
                <div>
                    <label for="grupo" class="block text-sm font-semibold text-darkpurple mb-1">Grupo</label>
                    <input type="text" id="grupo" name="grupo" maxlength="100" value="<?php echo htmlspecialchars($actividad['grupo']); ?>" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm" placeholder="Nombre del grupo">
                </div>
            </div>

            <!-- Metas e Indicadores -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="meta" class="block text-sm font-semibold text-darkpurple mb-1">Meta</label>
                    <textarea id="meta" name="meta" rows="3" maxlength="500" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm resize-none" placeholder="Meta de la actividad"><?php echo htmlspecialchars($actividad['meta']); ?></textarea>
                </div>
                <div>
                    <label for="indicador" class="block text-sm font-semibold text-darkpurple mb-1">Indicador</label>
                    <textarea id="indicador" name="indicador" rows="3" maxlength="500" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm resize-none" placeholder="Indicador de la actividad"><?php echo htmlspecialchars($actividad['indicador']); ?></textarea>
                </div>
            </div>

            <!-- Observaciones -->
            <div>
                <label for="observaciones" class="block text-sm font-semibold text-darkpurple mb-1">Observaciones</label>
                <textarea id="observaciones" name="observaciones" rows="4" maxlength="1000" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm resize-none" placeholder="Observaciones adicionales"><?php echo htmlspecialchars($actividad['observaciones']); ?></textarea>
            </div>

            <div class="flex items-center justify-between mt-6">
                <a href="index.php" class="text-sm text-cadet hover:text-primary flex items-center gap-1"><i class="fas fa-arrow-left"></i> Volver al listado</a>
                <button type="submit" class="bg-primary text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 shadow hover:bg-secondary focus:ring-2 focus:ring-accent focus:outline-none flex items-center gap-2">
                    <i class="fas fa-save"></i> Actualizar
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
            const componentesFiltrados = componentes.filter(c => c.eje_id == ejeId);
            componentesFiltrados.forEach(componente => {
                const option = document.createElement('option');
                option.value = componente.id;
                option.textContent = componente.nombre;
                componenteSelect.appendChild(option);
            });
        }
    }
</script>

<?php require_once '../includes/footer.php'; ?>
