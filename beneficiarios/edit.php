<?php
$page_title = "Editar Beneficiario";
require_once '../includes/header.php';
if (!isAuthenticated()) redirect('/auth/login.php');
$id = intval($_GET['id'] ?? 0);
$actividad_id = intval($_GET['actividad_id'] ?? 0);
if ($id <= 0) redirect('index.php?actividad_id=' . $actividad_id);
$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT b.*, ab.fecha_asistencia, ab.observaciones FROM beneficiarios b LEFT JOIN actividad_beneficiario ab ON ab.beneficiario_id = b.id AND ab.actividad_id = ? WHERE b.id = ?");
$stmt->execute([$actividad_id, $id]);
$benef = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$benef) redirect('index.php?actividad_id=' . $actividad_id);
$sexos = ["Femenino", "Masculino", "Otro"];
?>
<div class="flex flex-col items-center justify-center min-h-[70vh]">
    <div class="bg-white rounded-2xl shadow-2xl p-8 border border-cadet/40 w-full max-w-3xl mx-auto">
        <h1 class="text-2xl font-bold text-primary flex items-center gap-2 mb-6">
            <i class="fas fa-user-edit text-accent"></i> Editar Beneficiario
        </h1>
        <form action="update.php" method="POST" class="space-y-6">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="hidden" name="actividad_id" value="<?php echo $actividad_id; ?>">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="curp" class="block text-sm font-semibold text-darkpurple mb-1">CURP (opcional)</label>
                    <input type="text" id="curp" name="curp" maxlength="18" class="w-full px-4 py-2 border border-cadet/50 rounded-lg" value="<?php echo htmlspecialchars($benef['curp']); ?>">
                </div>
                <div>
                    <label for="nombre" class="block text-sm font-semibold text-darkpurple mb-1">Nombre(s) *</label>
                    <input type="text" id="nombre" name="nombre" maxlength="100" required class="w-full px-4 py-2 border border-cadet/50 rounded-lg" value="<?php echo htmlspecialchars($benef['nombre']); ?>">
                </div>
                <div>
                    <label for="apellido_paterno" class="block text-sm font-semibold text-darkpurple mb-1">Apellido Paterno *</label>
                    <input type="text" id="apellido_paterno" name="apellido_paterno" maxlength="100" required class="w-full px-4 py-2 border border-cadet/50 rounded-lg" value="<?php echo htmlspecialchars($benef['apellido_paterno']); ?>">
                </div>
                <div>
                    <label for="apellido_materno" class="block text-sm font-semibold text-darkpurple mb-1">Apellido Materno</label>
                    <input type="text" id="apellido_materno" name="apellido_materno" maxlength="100" class="w-full px-4 py-2 border border-cadet/50 rounded-lg" value="<?php echo htmlspecialchars($benef['apellido_materno']); ?>">
                </div>
                <div>
                    <label for="fecha_nacimiento" class="block text-sm font-semibold text-darkpurple mb-1">Fecha de Nacimiento</label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="w-full px-4 py-2 border border-cadet/50 rounded-lg" value="<?php echo htmlspecialchars($benef['fecha_nacimiento']); ?>">
                </div>
                <div>
                    <label for="sexo" class="block text-sm font-semibold text-darkpurple mb-1">Sexo</label>
                    <select id="sexo" name="sexo" class="w-full px-4 py-2 border border-cadet/50 rounded-lg">
                        <option value="">-</option>
                        <?php foreach ($sexos as $sx): ?>
                            <option value="<?php echo $sx; ?>" <?php if ($benef['sexo'] === $sx) echo 'selected'; ?>><?php echo $sx; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="telefono" class="block text-sm font-semibold text-darkpurple mb-1">Teléfono</label>
                    <input type="text" id="telefono" name="telefono" maxlength="20" class="w-full px-4 py-2 border border-cadet/50 rounded-lg" value="<?php echo htmlspecialchars($benef['telefono']); ?>">
                </div>
                <div>
                    <label for="email" class="block text-sm font-semibold text-darkpurple mb-1">Email</label>
                    <input type="email" id="email" name="email" maxlength="100" class="w-full px-4 py-2 border border-cadet/50 rounded-lg" value="<?php echo htmlspecialchars($benef['email']); ?>">
                </div>
                <div>
                    <label for="escolaridad" class="block text-sm font-semibold text-darkpurple mb-1">Escolaridad</label>
                    <input type="text" id="escolaridad" name="escolaridad" maxlength="100" class="w-full px-4 py-2 border border-cadet/50 rounded-lg" value="<?php echo htmlspecialchars($benef['escolaridad']); ?>">
                </div>
                <div>
                    <label for="ocupacion" class="block text-sm font-semibold text-darkpurple mb-1">Ocupación</label>
                    <input type="text" id="ocupacion" name="ocupacion" maxlength="100" class="w-full px-4 py-2 border border-cadet/50 rounded-lg" value="<?php echo htmlspecialchars($benef['ocupacion']); ?>">
                </div>
                <div>
                    <label for="colonia" class="block text-sm font-semibold text-darkpurple mb-1">Colonia</label>
                    <input type="text" id="colonia" name="colonia" maxlength="255" class="w-full px-4 py-2 border border-cadet/50 rounded-lg" value="<?php echo htmlspecialchars($benef['colonia']); ?>">
                </div>
                <div>
                    <label for="calle_numero" class="block text-sm font-semibold text-darkpurple mb-1">Calle y Número</label>
                    <input type="text" id="calle_numero" name="calle_numero" maxlength="255" class="w-full px-4 py-2 border border-cadet/50 rounded-lg" value="<?php echo htmlspecialchars($benef['calle_numero']); ?>">
                </div>
                <div>
                    <label for="codigo_postal" class="block text-sm font-semibold text-darkpurple mb-1">Código Postal</label>
                    <input type="text" id="codigo_postal" name="codigo_postal" maxlength="10" class="w-full px-4 py-2 border border-cadet/50 rounded-lg" value="<?php echo htmlspecialchars($benef['codigo_postal']); ?>">
                </div>
                <div>
                    <label for="municipio" class="block text-sm font-semibold text-darkpurple mb-1">Municipio</label>
                    <input type="text" id="municipio" name="municipio" maxlength="100" class="w-full px-4 py-2 border border-cadet/50 rounded-lg" value="<?php echo htmlspecialchars($benef['municipio']); ?>">
                </div>
                <div>
                    <label for="estado" class="block text-sm font-semibold text-darkpurple mb-1">Estado</label>
                    <input type="text" id="estado" name="estado" maxlength="100" class="w-full px-4 py-2 border border-cadet/50 rounded-lg" value="<?php echo htmlspecialchars($benef['estado']); ?>">
                </div>
                <div>
                    <label for="organizacion" class="block text-sm font-semibold text-darkpurple mb-1">Organización</label>
                    <input type="text" id="organizacion" name="organizacion" maxlength="255" class="w-full px-4 py-2 border border-cadet/50 rounded-lg" value="<?php echo htmlspecialchars($benef['organizacion']); ?>">
                </div>
                <div>
                    <label for="cargo" class="block text-sm font-semibold text-darkpurple mb-1">Cargo</label>
                    <input type="text" id="cargo" name="cargo" maxlength="100" class="w-full px-4 py-2 border border-cadet/50 rounded-lg" value="<?php echo htmlspecialchars($benef['cargo']); ?>">
                </div>
                <div>
                    <label for="fecha_asistencia" class="block text-sm font-semibold text-darkpurple mb-1">Fecha de Asistencia *</label>
                    <input type="date" id="fecha_asistencia" name="fecha_asistencia" value="<?php echo htmlspecialchars($benef['fecha_asistencia'] ?? date('Y-m-d')); ?>" required class="w-full px-4 py-2 border border-cadet/50 rounded-lg">
                </div>
                <div>
                    <label for="observaciones" class="block text-sm font-semibold text-darkpurple mb-1">Observaciones</label>
                    <input type="text" id="observaciones" name="observaciones" maxlength="255" class="w-full px-4 py-2 border border-cadet/50 rounded-lg" value="<?php echo htmlspecialchars($benef['observaciones']); ?>">
                </div>
            </div>
            <div class="flex items-center justify-between mt-4">
                <a href="index.php?actividad_id=<?php echo $actividad_id; ?>" class="text-sm text-cadet hover:text-primary flex items-center gap-1"><i class="fas fa-arrow-left"></i> Volver</a>
                <button type="submit" class="bg-primary text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 shadow hover:bg-secondary focus:ring-2 focus:ring-accent focus:outline-none flex items-center gap-2">
                    <i class="fas fa-save"></i> Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>