<?php
$page_title = "Nuevo Beneficiario";
require_once '../includes/header.php';
if (!isAuthenticated()) redirect('/auth/login.php');
$actividad_id = intval($_GET['actividad_id'] ?? 0);
$sexos = ["Femenino", "Masculino", "Otro"];
?>
<div class="flex flex-col items-center justify-center min-h-[70vh]">
    <div class="bg-white rounded-2xl shadow-2xl p-8 border border-cadet/40 w-full w-[90vw] mx-auto">
        <h1 class="text-2xl font-bold text-primary flex items-center gap-2 mb-6">
            <i class="fas fa-user-plus text-accent"></i> Nuevo Beneficiario
        </h1>
        <form action="store.php" method="POST" class="space-y-6">
            <input type="hidden" name="actividad_id" value="<?php echo $actividad_id; ?>">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="curp" class="block text-sm font-semibold text-darkpurple mb-1">CURP (opcional)</label>
                    <input type="text" id="curp" name="curp" maxlength="18" class="w-full px-4 py-2 border border-cadet/50 rounded-lg" placeholder="CURP">
                </div>
                <div>
                    <label for="nombre" class="block text-sm font-semibold text-darkpurple mb-1">Nombre(s) *</label>
                    <input type="text" id="nombre" name="nombre" maxlength="100" required class="w-full px-4 py-2 border border-cadet/50 rounded-lg" placeholder="Nombre(s)">
                </div>
                <div>
                    <label for="apellido_paterno" class="block text-sm font-semibold text-darkpurple mb-1">Apellido Paterno *</label>
                    <input type="text" id="apellido_paterno" name="apellido_paterno" maxlength="100" required class="w-full px-4 py-2 border border-cadet/50 rounded-lg" placeholder="Apellido paterno">
                </div>
                <div>
                    <label for="apellido_materno" class="block text-sm font-semibold text-darkpurple mb-1">Apellido Materno</label>
                    <input type="text" id="apellido_materno" name="apellido_materno" maxlength="100" class="w-full px-4 py-2 border border-cadet/50 rounded-lg" placeholder="Apellido materno">
                </div>
                <div>
                    <label for="fecha_nacimiento" class="block text-sm font-semibold text-darkpurple mb-1">Fecha de Nacimiento</label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="w-full px-4 py-2 border border-cadet/50 rounded-lg">
                </div>
                <div>
                    <label for="sexo" class="block text-sm font-semibold text-darkpurple mb-1">Sexo</label>
                    <select id="sexo" name="sexo" class="w-full px-4 py-2 border border-cadet/50 rounded-lg">
                        <option value="">-</option>
                        <?php foreach ($sexos as $sx): ?>
                            <option value="<?php echo $sx; ?>"><?php echo $sx; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="telefono" class="block text-sm font-semibold text-darkpurple mb-1">Teléfono</label>
                    <input type="text" id="telefono" name="telefono" maxlength="20" class="w-full px-4 py-2 border border-cadet/50 rounded-lg" placeholder="Teléfono">
                </div>
                <div>
                    <label for="email" class="block text-sm font-semibold text-darkpurple mb-1">Email</label>
                    <input type="email" id="email" name="email" maxlength="100" class="w-full px-4 py-2 border border-cadet/50 rounded-lg" placeholder="Correo electrónico">
                </div>
                <div>
                    <label for="escolaridad" class="block text-sm font-semibold text-darkpurple mb-1">Escolaridad</label>
                    <input type="text" id="escolaridad" name="escolaridad" maxlength="100" class="w-full px-4 py-2 border border-cadet/50 rounded-lg" placeholder="Escolaridad">
                </div>
                <div>
                    <label for="ocupacion" class="block text-sm font-semibold text-darkpurple mb-1">Ocupación</label>
                    <input type="text" id="ocupacion" name="ocupacion" maxlength="100" class="w-full px-4 py-2 border border-cadet/50 rounded-lg" placeholder="Ocupación">
                </div>
                <div>
                    <label for="colonia" class="block text-sm font-semibold text-darkpurple mb-1">Colonia</label>
                    <input type="text" id="colonia" name="colonia" maxlength="255" class="w-full px-4 py-2 border border-cadet/50 rounded-lg" placeholder="Colonia">
                </div>
                <div>
                    <label for="calle_numero" class="block text-sm font-semibold text-darkpurple mb-1">Calle y Número</label>
                    <input type="text" id="calle_numero" name="calle_numero" maxlength="255" class="w-full px-4 py-2 border border-cadet/50 rounded-lg" placeholder="Calle y número">
                </div>
                <div>
                    <label for="codigo_postal" class="block text-sm font-semibold text-darkpurple mb-1">Código Postal</label>
                    <input type="text" id="codigo_postal" name="codigo_postal" maxlength="10" class="w-full px-4 py-2 border border-cadet/50 rounded-lg" placeholder="Código postal">
                </div>
                <div>
                    <label for="municipio" class="block text-sm font-semibold text-darkpurple mb-1">Municipio</label>
                    <input type="text" id="municipio" name="municipio" maxlength="100" class="w-full px-4 py-2 border border-cadet/50 rounded-lg" placeholder="Municipio">
                </div>
                <div>
                    <label for="estado" class="block text-sm font-semibold text-darkpurple mb-1">Estado</label>
                    <input type="text" id="estado" name="estado" maxlength="100" class="w-full px-4 py-2 border border-cadet/50 rounded-lg" placeholder="Estado">
                </div>
                <div>
                    <label for="organizacion" class="block text-sm font-semibold text-darkpurple mb-1">Organización</label>
                    <input type="text" id="organizacion" name="organizacion" maxlength="255" class="w-full px-4 py-2 border border-cadet/50 rounded-lg" placeholder="Organización">
                </div>
                <div>
                    <label for="cargo" class="block text-sm font-semibold text-darkpurple mb-1">Cargo</label>
                    <input type="text" id="cargo" name="cargo" maxlength="100" class="w-full px-4 py-2 border border-cadet/50 rounded-lg" placeholder="Cargo">
                </div>
                <div>
                    <label for="fecha_asistencia" class="block text-sm font-semibold text-darkpurple mb-1">Fecha de Asistencia *</label>
                    <input type="date" id="fecha_asistencia" name="fecha_asistencia" value="<?php echo date('Y-m-d'); ?>" required class="w-full px-4 py-2 border border-cadet/50 rounded-lg">
                </div>
                <div>
                    <label for="observaciones" class="block text-sm font-semibold text-darkpurple mb-1">Observaciones</label>
                    <input type="text" id="observaciones" name="observaciones" maxlength="255" class="w-full px-4 py-2 border border-cadet/50 rounded-lg" placeholder="Observaciones">
                </div>
            </div>
            <div class="flex items-center justify-between mt-4">
                <a href="index.php?actividad_id=<?php echo $actividad_id; ?>" class="text-sm text-cadet hover:text-primary flex items-center gap-1"><i class="fas fa-arrow-left"></i> Volver</a>
                <button type="submit" class="bg-primary text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 shadow hover:bg-secondary focus:ring-2 focus:ring-accent focus:outline-none flex items-center gap-2">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>