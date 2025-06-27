<?php
require_once '../includes/config.php';
if (!isAuthenticated()) redirect('/auth/login.php');
$page_title = "Actividades";
require_once '../includes/header.php';

$busqueda = trim($_GET['q'] ?? '');
$eje_id = intval($_GET['eje_id'] ?? 0);
$componente_id = intval($_GET['componente_id'] ?? 0);
$producto_id = intval($_GET['producto_id'] ?? 0);
$estado_id = intval($_GET['estado_id'] ?? 0);
$tipo_id = intval($_GET['tipo_id'] ?? 0);

try {
    $pdo = getDBConnection();

    // Query principal con joins
    $sql = "SELECT a.*, 
            p.nombre as producto_nombre, 
            c.nombre as componente_nombre, 
            e.nombre as eje_nombre,
            ta.nombre as tipo_actividad_nombre,
            ea.nombre as estado_actividad_nombre,
            pol.nombre as poligono_nombre,
            u.nombre as responsable_nombre,
            COUNT(ab.beneficiario_id) as beneficiarios_count
            FROM actividades a 
            LEFT JOIN productos p ON a.producto_id = p.id 
            LEFT JOIN componentes c ON p.componente_id = c.id 
            LEFT JOIN ejes e ON c.eje_id = e.id 
            LEFT JOIN tipos_actividad ta ON a.tipo_actividad_id = ta.id 
            LEFT JOIN estados_actividad ea ON a.estado_actividad_id = ea.id 
            LEFT JOIN poligonos pol ON a.poligono_id = pol.id 
            LEFT JOIN usuarios u ON a.responsable_id = u.id 
            LEFT JOIN actividad_beneficiario ab ON a.id = ab.actividad_id ";

    $params = [];
    $where_conditions = [];

    if ($busqueda !== '') {
        $where_conditions[] = "(a.nombre LIKE ? OR a.descripcion LIKE ? OR a.lugar LIKE ?)";
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

    if ($producto_id > 0) {
        $where_conditions[] = "p.id = ?";
        $params[] = $producto_id;
    }

    if ($estado_id > 0) {
        $where_conditions[] = "ea.id = ?";
        $params[] = $estado_id;
    }

    if ($tipo_id > 0) {
        $where_conditions[] = "ta.id = ?";
        $params[] = $tipo_id;
    }

    if (!empty($where_conditions)) {
        $sql .= "WHERE " . implode(' AND ', $where_conditions) . " ";
    }

    $sql .= "GROUP BY a.id ORDER BY a.fecha_inicio DESC, a.nombre";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $actividades = $stmt->fetchAll();

    // Obtener datos para filtros
    $stmt_ejes = $pdo->query("SELECT id, nombre FROM ejes ORDER BY nombre");
    $ejes = $stmt_ejes->fetchAll();

    $sql_componentes = "SELECT c.id, c.nombre, e.nombre as eje_nombre FROM componentes c LEFT JOIN ejes e ON c.eje_id = e.id ORDER BY e.nombre, c.nombre";
    $stmt_componentes = $pdo->query($sql_componentes);
    $componentes = $stmt_componentes->fetchAll();

    $sql_productos = "SELECT p.id, p.nombre, c.nombre as componente_nombre, e.nombre as eje_nombre FROM productos p LEFT JOIN componentes c ON p.componente_id = c.id LEFT JOIN ejes e ON c.eje_id = e.id ORDER BY e.nombre, c.nombre, p.nombre";
    $stmt_productos = $pdo->query($sql_productos);
    $productos = $stmt_productos->fetchAll();

    $stmt_estados = $pdo->query("SELECT id, nombre FROM estados_actividad ORDER BY nombre");
    $estados = $stmt_estados->fetchAll();

    $stmt_tipos = $pdo->query("SELECT id, nombre FROM tipos_actividad ORDER BY nombre");
    $tipos = $stmt_tipos->fetchAll();
} catch (PDOException $e) {
    $actividades = [];
    $ejes = [];
    $componentes = [];
    $productos = [];
    $estados = [];
    $tipos = [];
}
?>
<div class="flex flex-col items-center justify-center min-h-[70vh]">
    <div class="bg-white rounded-2xl shadow-2xl p-8 border border-cadet/40 w-full max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <h1 class="text-2xl font-bold text-primary flex items-center gap-2">
                <i class="fas fa-tasks text-accent"></i> Actividades
            </h1>
            <div class="flex flex-col md:flex-row gap-2 w-full md:w-auto">
                <form method="get" class="flex flex-wrap gap-2">
                    <select name="eje_id" class="px-3 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-darkpurple shadow-sm text-sm">
                        <option value="">Todos los ejes</option>
                        <?php foreach ($ejes as $eje): ?>
                            <option value="<?php echo $eje['id']; ?>" <?php echo $eje_id == $eje['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($eje['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="componente_id" class="px-3 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-darkpurple shadow-sm text-sm">
                        <option value="">Todos los componentes</option>
                        <?php foreach ($componentes as $comp): ?>
                            <option value="<?php echo $comp['id']; ?>" <?php echo $componente_id == $comp['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($comp['eje_nombre'] . ' - ' . $comp['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="producto_id" class="px-3 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-darkpurple shadow-sm text-sm">
                        <option value="">Todos los productos</option>
                        <?php foreach ($productos as $prod): ?>
                            <option value="<?php echo $prod['id']; ?>" <?php echo $producto_id == $prod['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($prod['eje_nombre'] . ' - ' . $prod['componente_nombre'] . ' - ' . $prod['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="estado_id" class="px-3 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-darkpurple shadow-sm text-sm">
                        <option value="">Todos los estados</option>
                        <?php foreach ($estados as $est): ?>
                            <option value="<?php echo $est['id']; ?>" <?php echo $estado_id == $est['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($est['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="tipo_id" class="px-3 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-darkpurple shadow-sm text-sm">
                        <option value="">Todos los tipos</option>
                        <?php foreach ($tipos as $tip): ?>
                            <option value="<?php echo $tip['id']; ?>" <?php echo $tipo_id == $tip['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($tip['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" name="q" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Buscar..." class="w-full md:w-48 px-3 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm text-sm">
                    <button type="submit" class="bg-secondary text-white px-4 py-2 rounded-lg font-medium hover:bg-primary transition-colors text-sm">Filtrar</button>
                </form>
                <a href="create.php" class="bg-accent text-darkpurple font-semibold px-4 py-2 rounded-lg shadow hover:bg-xanthous/80 transition-colors flex items-center gap-2"><i class="fas fa-plus"></i> Nueva Actividad</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-cadet/30">
                <thead class="bg-cadet/10">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">Actividad</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">Producto</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">Fechas</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">Estado</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">Responsable</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">Beneficiarios</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-cadet uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-cadet/20">
                    <?php if (empty($actividades)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-8 text-cadet">No hay actividades registradas.</td>
                        </tr>
                        <?php else: foreach ($actividades as $a): ?>
                            <tr class="hover:bg-cadet/10">
                                <td class="px-4 py-4">
                                    <div class="text-darkpurple font-medium"><?php echo htmlspecialchars($a['nombre']); ?></div>
                                    <div class="text-sm text-cadet"><?php echo htmlspecialchars($a['tipo_actividad_nombre']); ?></div>
                                    <div class="text-xs text-cadet"><?php echo htmlspecialchars($a['lugar']); ?></div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-darkpurple font-medium"><?php echo htmlspecialchars($a['producto_nombre']); ?></div>
                                    <div class="text-xs text-cadet"><?php echo htmlspecialchars($a['eje_nombre'] . ' - ' . $a['componente_nombre']); ?></div>
                                </td>
                                <td class="px-4 py-4 text-sm text-darkpurple">
                                    <div>Inicio: <?php echo $a['fecha_inicio'] ? date('d/m/Y H:i', strtotime($a['fecha_inicio'])) : 'No definida'; ?></div>
                                    <div>Fin: <?php echo $a['fecha_fin'] ? date('d/m/Y H:i', strtotime($a['fecha_fin'])) : 'No definida'; ?></div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="bg-secondary text-white px-2 py-1 rounded-full text-xs font-semibold"><?php echo htmlspecialchars($a['estado_actividad_nombre']); ?></span>
                                </td>
                                <td class="px-4 py-4 text-sm text-darkpurple"><?php echo htmlspecialchars($a['responsable_nombre']); ?></td>
                                <td class="px-4 py-4">
                                    <span class="bg-accent text-darkpurple px-2 py-1 rounded-full text-xs font-semibold"><?php echo $a['beneficiarios_count']; ?></span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap flex gap-2">
                                    <a href="edit.php?id=<?php echo $a['id']; ?>" class="bg-secondary text-white px-3 py-1 rounded-lg text-xs font-semibold hover:bg-primary transition-colors"><i class="fas fa-edit"></i> Editar</a>
                                    <a href="delete.php?id=<?php echo $a['id']; ?>" class="bg-error text-white px-3 py-1 rounded-lg text-xs font-semibold hover:bg-bittersweet/80 transition-colors" onclick="return confirm('¿Eliminar esta actividad? Se eliminarán también todos los beneficiarios asociados.');"><i class="fas fa-trash"></i> Eliminar</a>
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