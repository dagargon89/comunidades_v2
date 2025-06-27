<?php
require_once '../includes/header.php';
if (!isAuthenticated() || !hasRole('capturista')) redirect('/auth/login.php');

$actividad_id = intval($_GET['id'] ?? 0);
if ($actividad_id <= 0) redirect('/captura_especial.php');

try {
    $pdo = getDBConnection();
    
    // Obtener información de la actividad
    $sql = "SELECT a.*, 
            p.nombre as producto_nombre, 
            c.nombre as componente_nombre, 
            e.nombre as eje_nombre,
            ta.nombre as tipo_actividad_nombre,
            ea.nombre as estado_actividad_nombre,
            pol.nombre as poligono_nombre
            FROM actividades a 
            LEFT JOIN productos p ON a.producto_id = p.id 
            LEFT JOIN componentes c ON p.componente_id = c.id 
            LEFT JOIN ejes e ON c.eje_id = e.id 
            LEFT JOIN tipos_actividad ta ON a.tipo_actividad_id = ta.id 
            LEFT JOIN estados_actividad ea ON a.estado_actividad_id = ea.id 
            LEFT JOIN poligonos pol ON a.poligono_id = pol.id 
            WHERE a.id = ? AND (a.responsable_id = ? OR a.responsable_registro_id = ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$actividad_id, $_SESSION['user_id'], $_SESSION['user_id']]);
    $actividad = $stmt->fetch();
    
    if (!$actividad) {
        setFlashMessage('error', 'Actividad no encontrada o no tienes permisos.');
        redirect('/captura_especial.php');
    }
    
    // Obtener beneficiarios existentes
    $sql_benef = "SELECT b.*, ab.fecha_asistencia, ab.observaciones
                  FROM actividad_beneficiario ab
                  LEFT JOIN beneficiarios b ON ab.beneficiario_id = b.id
                  WHERE ab.actividad_id = ?
                  ORDER BY ab.fecha_asistencia DESC, b.nombre";
    $stmt_benef = $pdo->prepare($sql_benef);
    $stmt_benef->execute([$actividad_id]);
    $beneficiarios = $stmt_benef->fetchAll();
    
    $sexos = ["Femenino", "Masculino", "Otro"];
    
} catch (PDOException $e) {
    setFlashMessage('error', 'Error al cargar la actividad.');
    redirect('/captura_especial.php');
}
?>

<!-- Meta viewport para dispositivos móviles -->
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

<div class="min-h-screen bg-base">
    <!-- Header móvil -->
    <div class="bg-white shadow-sm border-b border-cadet/20 px-4 py-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="/captura_especial.php" class="text-primary">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <h1 class="text-lg font-bold text-primary">Captura de Beneficiarios</h1>
            </div>
            <span class="text-sm text-cadet"><?php echo count($beneficiarios); ?> registrados</span>
        </div>
    </div>

    <!-- Información de la actividad -->
    <div class="p-4">
        <div class="bg-white rounded-xl shadow-lg p-4 mb-4 border border-cadet/20">
            <h2 class="font-semibold text-darkpurple text-lg mb-2">
                <?php echo htmlspecialchars($actividad['nombre']); ?>
            </h2>
            <p class="text-sm text-cadet mb-3">
                <?php echo htmlspecialchars($actividad['producto_nombre']); ?>
            </p>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <span class="text-cadet">Tipo:</span>
                    <div class="font-medium"><?php echo htmlspecialchars($actividad['tipo_actividad_nombre']); ?></div>
                </div>
                <div>
                    <span class="text-cadet">Lugar:</span>
                    <div class="font-medium"><?php echo htmlspecialchars($actividad['lugar']); ?></div>
                </div>
                <div>
                    <span class="text-cadet">Estado:</span>
                    <div class="font-medium"><?php echo htmlspecialchars($actividad['estado_actividad_nombre']); ?></div>
                </div>
                <div>
                    <span class="text-cadet">Fecha:</span>
                    <div class="font-medium">
                        <?php echo $actividad['fecha_inicio'] ? date('d/m/Y H:i', strtotime($actividad['fecha_inicio'])) : 'No definida'; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Opciones de captura -->
        <div class="grid grid-cols-1 gap-4 mb-6">
            <a href="captura_individual.php?id=<?php echo $actividad_id; ?>" 
               class="bg-primary text-white p-4 rounded-xl shadow-lg flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="bg-white/20 rounded-lg p-2">
                        <i class="fas fa-user-plus text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg">Captura Individual</h3>
                        <p class="text-white/80 text-sm">Agregar beneficiarios uno por uno</p>
                    </div>
                </div>
                <i class="fas fa-chevron-right text-xl"></i>
            </a>
            
            <a href="captura_masiva.php?id=<?php echo $actividad_id; ?>" 
               class="bg-secondary text-white p-4 rounded-xl shadow-lg flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="bg-white/20 rounded-lg p-2">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg">Captura Masiva</h3>
                        <p class="text-white/80 text-sm">Agregar múltiples beneficiarios</p>
                    </div>
                </div>
                <i class="fas fa-chevron-right text-xl"></i>
            </a>
        </div>

        <!-- Lista de beneficiarios -->
        <div class="bg-white rounded-xl shadow-lg p-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-darkpurple">Beneficiarios Registrados</h3>
                <span class="bg-accent text-darkpurple px-3 py-1 rounded-full text-sm font-semibold">
                    <?php echo count($beneficiarios); ?>
                </span>
            </div>
            
            <?php if (empty($beneficiarios)): ?>
                <div class="text-center py-8">
                    <div class="text-4xl text-cadet mb-2">
                        <i class="fas fa-users"></i>
                    </div>
                    <p class="text-cadet">No hay beneficiarios registrados aún</p>
                </div>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($beneficiarios as $beneficiario): ?>
                        <div class="border border-cadet/20 rounded-lg p-3">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-medium text-darkpurple">
                                    <?php echo htmlspecialchars($beneficiario['nombre'] . ' ' . $beneficiario['apellido_paterno']); ?>
                                </h4>
                                <span class="text-xs text-cadet">
                                    <?php echo $beneficiario['fecha_asistencia'] ? date('d/m/Y', strtotime($beneficiario['fecha_asistencia'])) : ''; ?>
                                </span>
                            </div>
                            <div class="text-sm text-cadet">
                                <?php if ($beneficiario['curp']): ?>
                                    <div>CURP: <?php echo htmlspecialchars($beneficiario['curp']); ?></div>
                                <?php endif; ?>
                                <?php if ($beneficiario['telefono']): ?>
                                    <div>Tel: <?php echo htmlspecialchars($beneficiario['telefono']); ?></div>
                                <?php endif; ?>
                                <?php if ($beneficiario['observaciones']): ?>
                                    <div class="mt-1 text-xs bg-cadet/10 p-2 rounded">
                                        <?php echo htmlspecialchars($beneficiario['observaciones']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* Estilos específicos para móviles */
@media (max-width: 768px) {
    .min-h-screen {
        min-height: 100vh;
    }
    
    /* Prevenir zoom en inputs en iOS */
    input[type="text"], input[type="email"], input[type="tel"], input[type="date"], select, textarea {
        font-size: 16px;
    }
    
    /* Botones más grandes para touch */
    button, a[href] {
        min-height: 44px;
    }
}
</style>

<?php require_once '../includes/footer.php'; ?> 