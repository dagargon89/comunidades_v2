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
    
    $sexos = ["Femenino", "Masculino", "Otro"];
    $user = getCurrentUser();
    
} catch (PDOException $e) {
    setFlashMessage('error', 'Error al cargar la actividad.');
    redirect('/captura_especial.php');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Captura Masiva - Comunidades</title>

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
                <h1 class="text-lg font-bold text-primary">Captura Masiva</h1>
            </div>
            
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

    <!-- Formulario - 90% del ancho -->
    <div class="w-[90%] mx-auto p-4">
        <div class="bg-white rounded-xl shadow-lg p-4 mb-4">
            <h2 class="font-semibold text-darkpurple text-lg mb-2">
                <?php echo htmlspecialchars($actividad['nombre']); ?>
            </h2>
            <p class="text-sm text-cadet">Captura masiva de beneficiarios</p>
        </div>

        <form action="guardar_masiva.php" method="POST" class="space-y-4">
            <input type="hidden" name="actividad_id" value="<?php echo $actividad_id; ?>">
            
            <!-- Información de la captura -->
            <div class="bg-white rounded-xl shadow-lg p-4">
                <h3 class="font-semibold text-darkpurple mb-4 flex items-center gap-2">
                    <i class="fas fa-calendar-check text-accent"></i> Información de Asistencia
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label for="fecha_asistencia" class="block text-sm font-semibold text-darkpurple mb-2">Fecha de Asistencia *</label>
                        <input type="date" id="fecha_asistencia" name="fecha_asistencia" value="<?php echo date('Y-m-d'); ?>" required 
                               class="w-full px-4 py-3 border border-cadet/50 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    
                    <div>
                        <label for="observaciones_generales" class="block text-sm font-semibold text-darkpurple mb-2">Observaciones Generales</label>
                        <textarea id="observaciones_generales" name="observaciones_generales" rows="3" maxlength="255" 
                                  class="w-full px-4 py-3 border border-cadet/50 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-primary resize-none"
                                  placeholder="Observaciones que se aplicarán a todos los beneficiarios"></textarea>
                    </div>
                </div>
            </div>

            <!-- Lista de beneficiarios -->
            <div class="bg-white rounded-xl shadow-lg p-4">
                <h3 class="font-semibold text-darkpurple mb-4 flex items-center gap-2">
                    <i class="fas fa-users text-accent"></i> Beneficiarios
                </h3>
                
                <div id="beneficiarios-container" class="space-y-4">
                    <!-- Beneficiario 1 -->
                    <div class="beneficiario-item border border-cadet/20 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-medium text-darkpurple">Beneficiario 1</h4>
                            <button type="button" class="remove-beneficiario text-error hover:text-bittersweet" style="display: none;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-darkpurple mb-2">Nombre(s) *</label>
                                    <input type="text" name="beneficiarios[0][nombre]" maxlength="100" required 
                                           class="w-full px-4 py-3 border border-cadet/50 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-primary"
                                           placeholder="Nombre(s)">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-darkpurple mb-2">Apellido Paterno *</label>
                                    <input type="text" name="beneficiarios[0][apellido_paterno]" maxlength="100" required 
                                           class="w-full px-4 py-3 border border-cadet/50 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-primary"
                                           placeholder="Apellido paterno">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-darkpurple mb-2">Apellido Materno</label>
                                    <input type="text" name="beneficiarios[0][apellido_materno]" maxlength="100" 
                                           class="w-full px-4 py-3 border border-cadet/50 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-primary"
                                           placeholder="Apellido materno">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-darkpurple mb-2">CURP</label>
                                    <input type="text" name="beneficiarios[0][curp]" maxlength="18" 
                                           class="w-full px-4 py-3 border border-cadet/50 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-primary"
                                           placeholder="CURP">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-darkpurple mb-2">Teléfono</label>
                                    <input type="tel" name="beneficiarios[0][telefono]" maxlength="20" 
                                           class="w-full px-4 py-3 border border-cadet/50 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-primary"
                                           placeholder="Teléfono">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-darkpurple mb-2">Fecha de Nacimiento</label>
                                    <input type="date" name="beneficiarios[0][fecha_nacimiento]" 
                                           class="w-full px-4 py-3 border border-cadet/50 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-primary">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-darkpurple mb-2">Sexo</label>
                                    <select name="beneficiarios[0][sexo]" 
                                            class="w-full px-4 py-3 border border-cadet/50 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-primary">
                                        <option value="">Seleccionar</option>
                                        <?php foreach ($sexos as $sexo): ?>
                                            <option value="<?php echo $sexo; ?>"><?php echo $sexo; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-darkpurple mb-2">Escolaridad</label>
                                    <input type="text" name="beneficiarios[0][escolaridad]" maxlength="100" 
                                           class="w-full px-4 py-3 border border-cadet/50 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-primary"
                                           placeholder="Escolaridad">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-darkpurple mb-2">Ocupación</label>
                                    <input type="text" name="beneficiarios[0][ocupacion]" maxlength="100" 
                                           class="w-full px-4 py-3 border border-cadet/50 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-primary"
                                           placeholder="Ocupación">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-darkpurple mb-2">Observaciones</label>
                                <textarea name="beneficiarios[0][observaciones]" rows="2" maxlength="255" 
                                          class="w-full px-4 py-3 border border-cadet/50 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-primary resize-none"
                                          placeholder="Observaciones específicas para este beneficiario"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="button" id="agregar-beneficiario" 
                        class="w-full mt-4 bg-secondary text-white py-3 px-4 rounded-lg font-semibold flex items-center justify-center gap-2">
                    <i class="fas fa-plus"></i>
                    Agregar Otro Beneficiario
                </button>
            </div>

            <!-- Botones de acción -->
            <div class="flex gap-4 pt-4">
                <a href="actividad.php?id=<?php echo $actividad_id; ?>" 
                   class="flex-1 bg-cadet text-white py-4 px-6 rounded-xl font-semibold text-center">
                    Cancelar
                </a>
                <button type="submit" 
                        class="flex-1 bg-primary text-white py-4 px-6 rounded-xl font-semibold flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    Guardar Todos
                </button>
            </div>
        </form>
    </div>

    <script>
        let beneficiarioCount = 1;
        
        document.getElementById('agregar-beneficiario').addEventListener('click', function() {
            const container = document.getElementById('beneficiarios-container');
            const template = container.querySelector('.beneficiario-item').cloneNode(true);
            
            // Actualizar índices
            const inputs = template.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.name = input.name.replace('[0]', `[${beneficiarioCount}]`);
                input.value = '';
            });
            
            // Actualizar título
            template.querySelector('h4').textContent = `Beneficiario ${beneficiarioCount + 1}`;
            
            // Mostrar botón de eliminar
            template.querySelector('.remove-beneficiario').style.display = 'block';
            
            container.appendChild(template);
            beneficiarioCount++;
        });
        
        // Eliminar beneficiario
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-beneficiario')) {
                e.target.closest('.beneficiario-item').remove();
                actualizarNumeros();
            }
        });
        
        function actualizarNumeros() {
            const items = document.querySelectorAll('.beneficiario-item');
            items.forEach((item, index) => {
                item.querySelector('h4').textContent = `Beneficiario ${index + 1}`;
                const inputs = item.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    input.name = input.name.replace(/\[\d+\]/, `[${index}]`);
                });
            });
            beneficiarioCount = items.length;
        }
    </script>

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
        .space-y-4 > * + * {
            margin-top: 1rem;
        }
    }
    </style>
</body>
</html> 