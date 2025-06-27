<?php
require_once '../includes/config.php';
if (!isAuthenticated() || !hasRole('capturista')) redirect('/auth/login.php');

$actividad_id = intval($_GET['id'] ?? 0);
if ($actividad_id <= 0) redirect('/captura_especial.php');

try {
    $pdo = getDBConnection();
    
    // Verificar que la actividad pertenece al usuario
    $sql = "SELECT id, nombre FROM actividades WHERE id = ? AND (responsable_id = ? OR responsable_registro_id = ?)";
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
    
    $user = getCurrentUser();
    
} catch (PDOException $e) {
    setFlashMessage('error', 'Error al cargar los beneficiarios.');
    redirect('/captura_especial.php');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Ver Beneficiarios - Comunidades</title>

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
                <a href="actividad.php?id=<?php echo $actividad_id; ?>" class="text-primary">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <h1 class="text-lg font-bold text-primary">Ver Beneficiarios</h1>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm text-cadet"><?php echo count($beneficiarios); ?> registrados</span>
                
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
        <!-- Información de la actividad -->
        <div class="bg-white rounded-xl shadow-lg p-4 mb-4 border border-cadet/20">
            <h2 class="font-semibold text-darkpurple text-lg mb-2">
                <?php echo htmlspecialchars($actividad['nombre']); ?>
            </h2>
            <p class="text-sm text-cadet">Lista de beneficiarios registrados</p>
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
                       class="inline-block mt-4 bg-primary text-white px-6 py-2 rounded-lg font-semibold">
                        <i class="fas fa-plus"></i> Agregar Beneficiario
                    </a>
                </div>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($beneficiarios as $index => $beneficiario): ?>
                        <div class="border border-cadet/20 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary to-accent flex items-center justify-center text-white font-bold">
                                        <?php echo $index + 1; ?>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-darkpurple">
                                            <?php echo htmlspecialchars($beneficiario['nombre'] . ' ' . $beneficiario['apellido_paterno']); ?>
                                        </h4>
                                        <?php if ($beneficiario['apellido_materno']): ?>
                                            <p class="text-sm text-cadet">
                                                <?php echo htmlspecialchars($beneficiario['apellido_materno']); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <span class="text-xs text-cadet bg-cadet/10 px-2 py-1 rounded">
                                    <?php echo $beneficiario['fecha_asistencia'] ? date('d/m/Y', strtotime($beneficiario['fecha_asistencia'])) : 'Sin fecha'; ?>
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <?php if ($beneficiario['curp']): ?>
                                    <div>
                                        <span class="text-cadet font-medium">CURP:</span>
                                        <div class="font-mono"><?php echo htmlspecialchars($beneficiario['curp']); ?></div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($beneficiario['telefono']): ?>
                                    <div>
                                        <span class="text-cadet font-medium">Teléfono:</span>
                                        <div><?php echo htmlspecialchars($beneficiario['telefono']); ?></div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($beneficiario['fecha_nacimiento']): ?>
                                    <div>
                                        <span class="text-cadet font-medium">Fecha Nacimiento:</span>
                                        <div><?php echo date('d/m/Y', strtotime($beneficiario['fecha_nacimiento'])); ?></div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($beneficiario['sexo']): ?>
                                    <div>
                                        <span class="text-cadet font-medium">Sexo:</span>
                                        <div><?php echo htmlspecialchars($beneficiario['sexo']); ?></div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($beneficiario['escolaridad']): ?>
                                    <div>
                                        <span class="text-cadet font-medium">Escolaridad:</span>
                                        <div><?php echo htmlspecialchars($beneficiario['escolaridad']); ?></div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($beneficiario['ocupacion']): ?>
                                    <div>
                                        <span class="text-cadet font-medium">Ocupación:</span>
                                        <div><?php echo htmlspecialchars($beneficiario['ocupacion']); ?></div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($beneficiario['observaciones']): ?>
                                <div class="mt-3 p-3 bg-cadet/10 rounded-lg">
                                    <span class="text-cadet font-medium text-sm">Observaciones:</span>
                                    <div class="text-sm mt-1"><?php echo htmlspecialchars($beneficiario['observaciones']); ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Botones de acción -->
                <div class="flex gap-4 mt-6">
                    <a href="captura_individual.php?id=<?php echo $actividad_id; ?>" 
                       class="flex-1 bg-primary text-white py-3 px-4 rounded-xl font-semibold text-center flex items-center justify-center gap-2">
                        <i class="fas fa-user-plus"></i>
                        Agregar Individual
                    </a>
                    <a href="captura_masiva.php?id=<?php echo $actividad_id; ?>" 
                       class="flex-1 bg-secondary text-white py-3 px-4 rounded-xl font-semibold text-center flex items-center justify-center gap-2">
                        <i class="fas fa-users"></i>
                        Agregar Masivo
                    </a>
                </div>
            <?php endif; ?>
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
        
        /* Aprovechar más espacio en móviles */
        .w-\[90\%\] {
            width: 95%;
        }
        
        /* Mejorar espaciado en móviles */
        .space-y-3 > * + * {
            margin-top: 0.75rem;
        }
        
        /* Ajustar grid en móviles */
        .grid-cols-2 {
            grid-template-columns: 1fr;
        }
    }
    </style>
</body>
</html> 