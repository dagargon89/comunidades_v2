<?php
$page_title = "Nueva Actividad";
require_once '../includes/header.php';
if (!isAuthenticated()) redirect('/auth/login.php');
try {
    $pdo = getDBConnection();

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
} catch (PDOException $e) {
    $ejes = [];
    $componentes = [];
    $productos = [];
    $usuarios = [];
    $tipos = [];
    $estados = [];
    $poligonos = [];
    $tipos_poblacion = [];
}
?>
<div class="flex flex-col items-center justify-center min-h-[70vh]">
    <div class="bg-white rounded-2xl shadow-2xl p-8 border border-cadet/40 w-full max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold text-primary flex items-center gap-2 mb-6">
            <i class="fas fa-tasks text-accent"></i> Nueva Actividad
        </h1>
        <form action="store.php" method="POST" class="space-y-6">
            <!-- Información Básica -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nombre" class="block text-sm font-semibold text-darkpurple mb-1">Nombre de la Actividad *</label>
                    <input type="text" id="nombre" name="nombre" required maxlength="255" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm" placeholder="Nombre de la actividad">
                </div>
                <div>
                    <label for="producto_id" class="block text-sm font-semibold text-darkpurple mb-1">Producto *</label>
                    <select id="producto_id" name="producto_id" required class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-darkpurple shadow-sm">
                        <option value="">Seleccionar producto</option>
                        <?php foreach ($productos as $prod): ?>
                            <option value="<?php echo $prod['id']; ?>" data-eje="<?php echo htmlspecialchars($prod['eje_nombre']); ?>" data-componente="<?php echo htmlspecialchars($prod['componente_nombre']); ?>"><?php echo htmlspecialchars($prod['eje_nombre'] . ' - ' . $prod['componente_nombre'] . ' - ' . $prod['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label for="descripcion" class="block text-sm font-semibold text-darkpurple mb-1">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="3" maxlength="1000" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm resize-none" placeholder="Descripción de la actividad"></textarea>
            </div>

            <!-- Clasificación -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="tipo_actividad_id" class="block text-sm font-semibold text-darkpurple mb-1">Tipo de Actividad</label>
                    <select id="tipo_actividad_id" name="tipo_actividad_id" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-darkpurple shadow-sm">
                        <option value="">Seleccionar tipo</option>
                        <?php foreach ($tipos as $tipo): ?>
                            <option value="<?php echo $tipo['id']; ?>"><?php echo htmlspecialchars($tipo['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="tipo_poblacion_id" class="block text-sm font-semibold text-darkpurple mb-1">Tipo de Población</label>
                    <select id="tipo_poblacion_id" name="tipo_poblacion_id" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-darkpurple shadow-sm">
                        <option value="">Seleccionar tipo</option>
                        <?php foreach ($tipos_poblacion as $tp): ?>
                            <option value="<?php echo $tp['id']; ?>"><?php echo htmlspecialchars($tp['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="estado_actividad_id" class="block text-sm font-semibold text-darkpurple mb-1">Estado</label>
                    <select id="estado_actividad_id" name="estado_actividad_id" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-darkpurple shadow-sm">
                        <option value="">Seleccionar estado</option>
                        <?php foreach ($estados as $est): ?>
                            <option value="<?php echo $est['id']; ?>"><?php echo htmlspecialchars($est['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Fechas y Responsable -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="fecha_inicio" class="block text-sm font-semibold text-darkpurple mb-1">Fecha de Inicio</label>
                    <input type="datetime-local" id="fecha_inicio" name="fecha_inicio" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-darkpurple shadow-sm">
                </div>
                <div>
                    <label for="fecha_fin" class="block text-sm font-semibold text-darkpurple mb-1">Fecha de Fin</label>
                    <input type="datetime-local" id="fecha_fin" name="fecha_fin" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-darkpurple shadow-sm">
                </div>
                <div>
                    <label for="responsable_id" class="block text-sm font-semibold text-darkpurple mb-1">Responsable</label>
                    <select id="responsable_id" name="responsable_id" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-darkpurple shadow-sm">
                        <option value="">Seleccionar responsable</option>
                        <?php foreach ($usuarios as $user): ?>
                            <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['nombre'] . ' ' . $user['apellido_paterno'] . ' ' . $user['apellido_materno']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Ubicación -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="lugar" class="block text-sm font-semibold text-darkpurple mb-1">Lugar</label>
                    <input type="text" id="lugar" name="lugar" maxlength="255" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm" placeholder="Lugar de la actividad">
                </div>
                <div>
                    <label for="poligono_id" class="block text-sm font-semibold text-darkpurple mb-1">Polígono</label>
                    <select id="poligono_id" name="poligono_id" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-darkpurple shadow-sm">
                        <option value="">Seleccionar polígono</option>
                        <?php foreach ($poligonos as $pol): ?>
                            <option value="<?php echo $pol['id']; ?>"><?php echo htmlspecialchars($pol['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Información Adicional -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="modalidad" class="block text-sm font-semibold text-darkpurple mb-1">Modalidad</label>
                    <input type="text" id="modalidad" name="modalidad" maxlength="50" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm" placeholder="Presencial, Virtual, etc.">
                </div>
                <div>
                    <label for="cantidad_sesiones" class="block text-sm font-semibold text-darkpurple mb-1">Cantidad de Sesiones</label>
                    <input type="number" id="cantidad_sesiones" name="cantidad_sesiones" min="0" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm" placeholder="0">
                </div>
                <div>
                    <label for="grupo" class="block text-sm font-semibold text-darkpurple mb-1">Grupo</label>
                    <input type="text" id="grupo" name="grupo" maxlength="100" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm" placeholder="Nombre del grupo">
                </div>
            </div>

            <!-- Metas e Indicadores -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="meta" class="block text-sm font-semibold text-darkpurple mb-1">Meta</label>
                    <textarea id="meta" name="meta" rows="3" maxlength="500" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm resize-none" placeholder="Meta de la actividad"></textarea>
                </div>
                <div>
                    <label for="indicador" class="block text-sm font-semibold text-darkpurple mb-1">Indicador</label>
                    <textarea id="indicador" name="indicador" rows="3" maxlength="500" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm resize-none" placeholder="Indicador de la actividad"></textarea>
                </div>
            </div>

            <!-- Dirección Detallada -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="calle" class="block text-sm font-semibold text-darkpurple mb-1">Calle</label>
                    <input type="text" id="calle" name="calle" maxlength="255" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm" placeholder="Nombre de la calle">
                </div>
                <div>
                    <label for="numero_casa" class="block text-sm font-semibold text-darkpurple mb-1">Número</label>
                    <input type="text" id="numero_casa" name="numero_casa" maxlength="50" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm" placeholder="Número de casa">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="colonia" class="block text-sm font-semibold text-darkpurple mb-1">Colonia</label>
                    <input type="text" id="colonia" name="colonia" maxlength="255" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm" placeholder="Colonia">
                </div>
                <div>
                    <label for="entre_calles" class="block text-sm font-semibold text-darkpurple mb-1">Entre Calles</label>
                    <input type="text" id="entre_calles" name="entre_calles" maxlength="255" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm" placeholder="Entre calles">
                </div>
            </div>

            <!-- Coordenadas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="latitud" class="block text-sm font-semibold text-darkpurple mb-1">Latitud</label>
                    <input type="number" id="latitud" name="latitud" step="0.0000001" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm" placeholder="19.4326">
                </div>
                <div>
                    <label for="longitud" class="block text-sm font-semibold text-darkpurple mb-1">Longitud</label>
                    <input type="number" id="longitud" name="longitud" step="0.0000001" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm" placeholder="-99.1332">
                </div>
            </div>

            <div>
                <label for="google_maps" class="block text-sm font-semibold text-darkpurple mb-1">Enlace Google Maps</label>
                <input type="url" id="google_maps" name="google_maps" maxlength="500" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm" placeholder="https://maps.google.com/...">
            </div>

            <div>
                <label for="observaciones" class="block text-sm font-semibold text-darkpurple mb-1">Observaciones</label>
                <textarea id="observaciones" name="observaciones" rows="3" maxlength="1000" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm resize-none" placeholder="Observaciones adicionales"></textarea>
            </div>

            <div class="flex items-center justify-between mt-6">
                <a href="index.php" class="text-sm text-cadet hover:text-primary flex items-center gap-1"><i class="fas fa-arrow-left"></i> Volver al listado</a>
                <button type="submit" class="bg-primary text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 shadow hover:bg-secondary focus:ring-2 focus:ring-accent focus:outline-none flex items-center gap-2">
                    <i class="fas fa-save"></i> Guardar Actividad
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>