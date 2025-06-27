<?php
require_once 'includes/config.php';
if (!isAuthenticated() || !hasRole('capturista')) redirect('/auth/login.php');

$page_title = "Captura Especial - Actividades";
$user = getCurrentUser();

try {
    $pdo = getDBConnection();
    
    // Obtener actividades donde el usuario es responsable o capturista
    $sql = "SELECT a.*, 
            p.nombre as producto_nombre, 
            c.nombre as componente_nombre, 
            e.nombre as eje_nombre,
            ta.nombre as tipo_actividad_nombre,
            ea.nombre as estado_actividad_nombre,
            pol.nombre as poligono_nombre,
            COUNT(ab.beneficiario_id) as beneficiarios_count
            FROM actividades a 
            LEFT JOIN productos p ON a.producto_id = p.id 
            LEFT JOIN componentes c ON p.componente_id = c.id 
            LEFT JOIN ejes e ON c.eje_id = e.id 
            LEFT JOIN tipos_actividad ta ON a.tipo_actividad_id = ta.id 
            LEFT JOIN estados_actividad ea ON a.estado_actividad_id = ea.id 
            LEFT JOIN poligonos pol ON a.poligono_id = pol.id 
            LEFT JOIN actividad_beneficiario ab ON a.id = ab.actividad_id 
            WHERE a.responsable_id = ? OR a.responsable_registro_id = ?
            GROUP BY a.id 
            ORDER BY a.fecha_inicio DESC, a.nombre";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
    $actividades = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $actividades = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo $page_title; ?> - Comunidades</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Configuración personalizada de Tailwind -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        xanthous: {
                            DEFAULT: '#FFBA49',
                        },
                        seagreen: {
                            DEFAULT: '#20A39E',
                        },
                        bittersweet: {
                            DEFAULT: '#EF5B5B',
                        },
                        darkpurple: {
                            DEFAULT: '#23001E',
                        },
                        cadet: {
                            DEFAULT: '#A4A9AD',
                        },
                        primary: {
                            DEFAULT: '#23001E', // Dark purple
                        },
                        secondary: {
                            DEFAULT: '#20A39E', // Light sea green
                        },
                        accent: {
                            DEFAULT: '#FFBA49', // Xanthous
                        },
                        error: {
                            DEFAULT: '#EF5B5B', // Bittersweet
                        },
                        base: {
                            DEFAULT: '#A4A9AD', // Cadet gray
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-base min-h-screen">
    <!-- Header específico para capturistas -->
    <div class="bg-white shadow-sm border-b border-cadet/20 px-4 py-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <h1 class="text-xl font-bold text-primary flex items-center gap-2">
                    <i class="fas fa-keyboard text-accent"></i> Captura Especial
                </h1>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm text-cadet"><?php echo htmlspecialchars($user['nombre']); ?></span>
                
                <!-- Menú de usuario -->
                <div class="relative group">
                    <button class="flex items-center gap-2 text-primary font-medium hover:text-secondary focus:outline-none p-2 rounded-lg hover:bg-base transition-colors">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary to-accent flex items-center justify-center text-white text-sm font-bold">
                            <?php echo strtoupper(substr($user['nombre'], 0, 1) . substr($user['apellido_paterno'], 0, 1)); ?>
                        </div>
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg py-2 z-50 hidden group-hover:block group-focus-within:block border border-cadet/20">
                        <div class="px-4 py-2 border-b border-cadet/20">
                            <div class="text-sm font-medium text-darkpurple"><?php echo htmlspecialchars($user['nombre'] . ' ' . $user['apellido_paterno']); ?></div>
                            <div class="text-xs text-cadet"><?php echo htmlspecialchars($user['email']); ?></div>
                        </div>
                        <a href="/usuarios/perfil.php" class="block px-4 py-2 text-sm text-primary hover:bg-base rounded-md flex items-center gap-2">
                            <i class="fas fa-user"></i> Mi Perfil
                        </a>
                        <a href="/usuarios/update_perfil.php" class="block px-4 py-2 text-sm text-primary hover:bg-base rounded-md flex items-center gap-2">
                            <i class="fas fa-edit"></i> Editar Perfil
                        </a>
                        <hr class="my-1">
                        <a href="/auth/logout.php" class="block px-4 py-2 text-sm text-error hover:bg-base rounded-md flex items-center gap-2">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido principal - 90% del ancho -->
    <div class="w-[90%] mx-auto p-4">
        <?php if (empty($actividades)): ?>
            <!-- Estado vacío -->
            <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                <div class="text-6xl text-cadet mb-4">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <h2 class="text-xl font-semibold text-darkpurple mb-2">No hay actividades asignadas</h2>
                <p class="text-cadet">No tienes actividades asignadas para capturar beneficiarios.</p>
            </div>
        <?php else: ?>
            <!-- Lista de actividades -->
            <div class="space-y-4">
                <h2 class="text-lg font-semibold text-darkpurple mb-4">
                    Mis Actividades (<?php echo count($actividades); ?>)
                </h2>
                
                <?php foreach ($actividades as $actividad): ?>
                    <div class="bg-white rounded-xl shadow-lg p-4 border border-cadet/20">
                        <!-- Header de la actividad -->
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h3 class="font-semibold text-darkpurple text-lg mb-1">
                                    <?php echo htmlspecialchars($actividad['nombre']); ?>
                                </h3>
                                <p class="text-sm text-cadet mb-2">
                                    <?php echo htmlspecialchars($actividad['producto_nombre']); ?>
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    <span class="bg-secondary text-white px-2 py-1 rounded-full text-xs">
                                        <?php echo htmlspecialchars($actividad['estado_actividad_nombre']); ?>
                                    </span>
                                    <span class="bg-accent text-darkpurple px-2 py-1 rounded-full text-xs">
                                        <?php echo $actividad['beneficiarios_count']; ?> beneficiarios
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Información de la actividad -->
                        <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                            <div>
                                <span class="text-cadet">Tipo:</span>
                                <div class="font-medium"><?php echo htmlspecialchars($actividad['tipo_actividad_nombre']); ?></div>
                            </div>
                            <div>
                                <span class="text-cadet">Lugar:</span>
                                <div class="font-medium"><?php echo htmlspecialchars($actividad['lugar']); ?></div>
                            </div>
                            <div>
                                <span class="text-cadet">Inicio:</span>
                                <div class="font-medium">
                                    <?php echo $actividad['fecha_inicio'] ? date('d/m/Y H:i', strtotime($actividad['fecha_inicio'])) : 'No definida'; ?>
                                </div>
                            </div>
                            <div>
                                <span class="text-cadet">Fin:</span>
                                <div class="font-medium">
                                    <?php echo $actividad['fecha_fin'] ? date('d/m/Y H:i', strtotime($actividad['fecha_fin'])) : 'No definida'; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Botones de acción -->
                        <div class="flex gap-2">
                            <a href="captura_especial/actividad.php?id=<?php echo $actividad['id']; ?>" 
                               class="flex-1 bg-primary text-white py-3 px-4 rounded-lg font-semibold text-center flex items-center justify-center gap-2">
                                <i class="fas fa-users"></i>
                                Capturar Beneficiarios
                            </a>
                            <a href="captura_especial/ver_beneficiarios.php?id=<?php echo $actividad['id']; ?>" 
                               class="bg-secondary text-white py-3 px-4 rounded-lg font-semibold flex items-center justify-center">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
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
        
        /* Aprovechar más espacio en móviles */
        .w-\[90\%\] {
            width: 95%;
        }
    }
    </style>
</body>
</html> 