<?php
require_once '../includes/header.php';
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
                <a href="actividad.php?id=<?php echo $actividad_id; ?>" class="text-primary">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <h1 class="text-lg font-bold text-primary">Captura Individual</h1>
            </div>
        </div>
    </div>

    <!-- Formulario -->
    <div class="p-4">
        <div class="bg-white rounded-xl shadow-lg p-4 mb-4">
            <h2 class="font-semibold text-darkpurple text-lg mb-2">
                <?php echo htmlspecialchars($actividad['nombre']); ?>
            </h2>
            <p class="text-sm text-cadet">Nuevo beneficiario</p>
        </div>

        <form action="guardar_individual.php" method="POST" class="space-y-4">
            <input type="hidden" name="actividad_id" value="<?php echo $actividad_id; ?>">
            
            <!-- Información básica -->
            <div class="bg-white rounded-xl shadow-lg p-4">
                <h3 class="font-semibold text-darkpurple mb-4 flex items-center gap-2">
                    <i class="fas fa-user text-accent"></i> Información Personal
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label for="curp" class="block text-sm font-semibold text-darkpurple mb-2">CURP (opcional)</label>
                        <input type="text" id="curp" name="curp" maxlength="18" 
                               class="w-full px-4 py-3 border border-cadet/50 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-primary"
                               placeholder="CURP">
                    </div>
                    
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="nombre" class="block text-sm font-semibold text-darkpurple mb-2">Nombre(s) *</label>
                            <input type="text" id="nombre" name="nombre" maxlength="100" required 
                                   class="w-full px-4 py-3 border border-cadet/50 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-primary"
                                   placeholder="Nombre(s)">
                        </div>
                        
                        <div>
                            <label for="apellido_paterno" class="block text-sm font-semibold text-darkpurple mb-2">Apellido Paterno *</label>
                            <input type="text" id="apellido_paterno" name="apellido_paterno" maxlength="100" required 
                                   class="w-full px-4 py-3 border border-cadet/50 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-primary"
                                   placeholder="Apellido paterno">
                        </div>
                        
                        <div>
                            <label for="apellido_materno" class="block text-sm font-semibold text-darkpurple mb-2">Apellido Materno</label>
                            <input type="text" id="apellido_materno" name="apellido_materno" maxlength="100" 
                                   class="w-full px-4 py-3 border border-cadet/50 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-primary"
                                   placeholder="Apellido materno">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="fecha_nacimiento" class="block text-sm font-semibold text-darkpurple mb-2">Fecha de Nacimiento</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" 
                                   class="w-full px-4 py-3 border border-cadet/50 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        
                        <div>
                            <label for="sexo" class="block text-sm font-semibold text-darkpurple mb-2">Sexo</label>
                            <select id="sexo" name="sexo" 
                                    class="w-full px-4 py-3 border border-cadet/50 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-primary">
                                <option value="">Seleccionar</option>
                                <?php foreach ($sexos as $sexo): ?>
                                    <option value="<?php echo $sexo; ?>"><?php echo $sexo; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información de contacto -->
            <div class="bg-white rounded-xl shadow-lg p-4">
                <h3 class="font-semibold text-darkpurple mb-4 flex items-center gap-2">
                    <i class="fas fa-phone text-accent"></i> Información de Contacto
                </h3>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="telefono" class="block text-sm font-semibold text-darkpurple mb-2">Teléfono</label>
                            <input type="tel" id="telefono" name="telefono" maxlength="20" 
                                   class="w-full px-4 py-3 border border-cadet/50 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-primary"
                                   placeholder="Teléfono">
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-semibold text-darkpurple mb-2">Correo Electrónico</label>
                            <input type="email" id="email" name="email" maxlength="100" 
                                   class="w-full px-4 py-3 border border-cadet/50 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-primary"
                                   placeholder="correo@ejemplo.com">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="bg-white rounded-xl shadow-lg p-4">
                <h3 class="font-semibold text-darkpurple mb-4 flex items-center gap-2">
                    <i class="fas fa-info-circle text-accent"></i> Información Adicional
                </h3>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="escolaridad" class="block text-sm font-semibold text-darkpurple mb-2">Escolaridad</label>
                            <input type="text" id="escolaridad" name="escolaridad" maxlength="100" 
                                   class="w-full px-4 py-3 border border-cadet/50 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-primary"
                                   placeholder="Escolaridad">
                        </div>
                        
                        <div>
                            <label for="ocupacion" class="block text-sm font-semibold text-darkpurple mb-2">Ocupación</label>
                            <input type="text" id="ocupacion" name="ocupacion" maxlength="100" 
                                   class="w-full px-4 py-3 border border-cadet/50 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-primary"
                                   placeholder="Ocupación">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información de asistencia -->
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
                        <label for="observaciones" class="block text-sm font-semibold text-darkpurple mb-2">Observaciones</label>
                        <textarea id="observaciones" name="observaciones" rows="3" maxlength="255" 
                                  class="w-full px-4 py-3 border border-cadet/50 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-primary resize-none"
                                  placeholder="Observaciones adicionales"></textarea>
                    </div>
                </div>
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
                    Guardar
                </button>
            </div>
        </form>
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
    .space-y-4 > * + * {
        margin-top: 1rem;
    }
}
</style>

<?php require_once '../includes/footer.php'; ?> 