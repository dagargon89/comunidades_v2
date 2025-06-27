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
    
    // Obtener beneficiarios
    $sql_benef = "SELECT b.*, ab.fecha_asistencia, ab.observaciones
                  FROM actividad_beneficiario ab
                  LEFT JOIN beneficiarios b ON ab.beneficiario_id = b.id
                  WHERE ab.actividad_id = ?
                  ORDER BY ab.fecha_asistencia DESC, b.nombre";
    $stmt_benef = $pdo->prepare($sql_benef);
    $stmt_benef->execute([$actividad_id]);
    $beneficiarios = $stmt_benef->fetchAll();
    
} catch (PDOException $e) {
    setFlashMessage('error', 'Error al cargar los beneficiarios.');
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
                <a href="actividad.php?id=<?php echo $actividad_id; ?>" class="text-primary">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <h1 class="text-lg font-bold text-primary">Beneficiarios</h1>
            </div>
            <span class="text-sm text-cadet"><?php echo count($beneficiarios); ?></span>
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
                    <a href="captura_individual.php?id=<?php echo $actividad_id; ?>" 
                       class="mt-4 inline-block bg-primary text-white px-6 py-3 rounded-lg font-semibold">
                        <i class="fas fa-user-plus"></i> Agregar Beneficiario
                    </a>
                </div>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($beneficiarios as $beneficiario): ?>
                        <div class="border border-cadet/20 rounded-lg p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-darkpurple text-lg">
                                        <?php echo htmlspecialchars($beneficiario['nombre'] . ' ' . $beneficiario['apellido_paterno']); ?>
                                        <?php if ($beneficiario['apellido_materno']): ?>
                                            <?php echo htmlspecialchars($beneficiario['apellido_materno']); ?>
                                        <?php endif; ?>
                                    </h4>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-xs text-cadet">
                                            <?php echo $beneficiario['fecha_asistencia'] ? date('d/m/Y', strtotime($beneficiario['fecha_asistencia'])) : ''; ?>
                                        </span>
                                        <?php if ($beneficiario['sexo']): ?>
                                            <span class="bg-secondary text-white px-2 py-1 rounded-full text-xs">
                                                <?php echo htmlspecialchars($beneficiario['sexo']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 gap-2 text-sm">
                                <?php if ($beneficiario['curp']): ?>
                                    <div class="flex items-center gap-2">
                                        <span class="text-cadet w-16">CURP:</span>
                                        <span class="font-medium"><?php echo htmlspecialchars($beneficiario['curp']); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($beneficiario['telefono']): ?>
                                    <div class="flex items-center gap-2">
                                        <span class="text-cadet w-16">Tel:</span>
                                        <span class="font-medium"><?php echo htmlspecialchars($beneficiario['telefono']); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($beneficiario['email']): ?>
                                    <div class="flex items-center gap-2">
                                        <span class="text-cadet w-16">Email:</span>
                                        <span class="font-medium"><?php echo htmlspecialchars($beneficiario['email']); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($beneficiario['escolaridad']): ?>
                                    <div class="flex items-center gap-2">
                                        <span class="text-cadet w-16">Escolaridad:</span>
                                        <span class="font-medium"><?php echo htmlspecialchars($beneficiario['escolaridad']); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($beneficiario['ocupacion']): ?>
                                    <div class="flex items-center gap-2">
                                        <span class="text-cadet w-16">Ocupación:</span>
                                        <span class="font-medium"><?php echo htmlspecialchars($beneficiario['ocupacion']); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($beneficiario['observaciones']): ?>
                                    <div class="mt-2 p-3 bg-cadet/10 rounded-lg">
                                        <div class="text-xs text-cadet mb-1">Observaciones:</div>
                                        <div class="text-sm"><?php echo htmlspecialchars($beneficiario['observaciones']); ?></div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Botón para agregar más -->
                <div class="mt-6 text-center">
                    <a href="captura_individual.php?id=<?php echo $actividad_id; ?>" 
                       class="bg-primary text-white px-6 py-3 rounded-lg font-semibold inline-flex items-center gap-2">
                        <i class="fas fa-user-plus"></i> Agregar Otro Beneficiario
                    </a>
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
    
    /* Mejorar espaciado en móviles */
    .space-y-3 > * + * {
        margin-top: 0.75rem;
    }
}
</style>

<?php require_once '../includes/footer.php'; ?> 