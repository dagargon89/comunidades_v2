<?php
require_once '../includes/config.php';
if (!isAuthenticated()) redirect('/auth/login.php');
$page_title = "Beneficiarios por Actividad";
require_once '../includes/header.php';

$actividad_id = intval($_GET['actividad_id'] ?? 0);
$busqueda = trim($_GET['q'] ?? '');

try {
    $pdo = getDBConnection();
    // Obtener actividades para el selector
    $stmt_acts = $pdo->query("SELECT id, nombre FROM actividades ORDER BY fecha_inicio DESC, nombre");
    $actividades = $stmt_acts->fetchAll();

    $beneficiarios = [];
    if ($actividad_id > 0) {
        $sql = "SELECT b.*, ab.fecha_asistencia, ab.observaciones
                FROM actividad_beneficiario ab
                LEFT JOIN beneficiarios b ON ab.beneficiario_id = b.id
                WHERE ab.actividad_id = ? ";
        $params = [$actividad_id];
        if ($busqueda !== '') {
            $sql .= "AND (b.curp LIKE ? OR b.nombre LIKE ? OR b.apellido_paterno LIKE ?) ";
            $params[] = "%$busqueda%";
            $params[] = "%$busqueda%";
            $params[] = "%$busqueda%";
        }
        $sql .= "ORDER BY ab.fecha_asistencia DESC, b.nombre";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $beneficiarios = $stmt->fetchAll();
    }
    // Para el select de sexo
    $sexos = ["Femenino", "Masculino", "Otro"];
} catch (PDOException $e) {
    $actividades = [];
    $beneficiarios = [];
    $sexos = ["Femenino", "Masculino", "Otro"];
}
?>
<div class="flex flex-col items-center justify-center min-h-[70vh]">
    <div class="bg-white rounded-2xl shadow-2xl p-8 border border-cadet/40 w-full w-[90vw] mx-auto">
        <h1 class="flex gap-2 items-center mb-6 text-2xl font-bold text-primary">
            <i class="fas fa-user-friends text-accent"></i> Beneficiarios por Actividad
        </h1>
        <form method="get" class="flex flex-col gap-4 justify-between items-center mb-6 w-full md:flex-row">
            <div class="flex gap-2 w-full md:w-auto">
                <select name="actividad_id" class="px-4 py-2 bg-white rounded-lg border shadow-sm border-cadet/50 focus:outline-none focus:ring-2 focus:ring-primary text-darkpurple" onchange="this.form.submit()">
                    <option value="">Selecciona una actividad</option>
                    <?php foreach ($actividades as $act): ?>
                        <option value="<?php echo $act['id']; ?>" <?php echo $actividad_id == $act['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($act['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="q" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Buscar beneficiario..." class="px-4 py-2 w-full bg-white rounded-lg border shadow-sm md:w-64 border-cadet/50 focus:outline-none focus:ring-2 focus:ring-primary placeholder-cadet text-darkpurple">
                <button type="submit" class="px-4 py-2 font-medium text-white rounded-lg transition-colors bg-secondary hover:bg-primary">Filtrar</button>
            </div>
        </form>
        <?php if ($actividad_id > 0): ?>
            <div class="flex flex-wrap gap-4 mb-6">
                <button type="button" onclick="toggleRepeater(true)" class="flex gap-2 items-center px-6 py-2 font-semibold text-white bg-green-600 rounded-lg"><i class="fas fa-users"></i> Agregar de forma masiva</button>
                <a href="create.php?actividad_id=<?php echo $actividad_id; ?>" class="flex gap-2 items-center px-6 py-2 font-semibold text-white rounded-lg bg-primary"><i class="fas fa-user-plus"></i> Agregar de uno por uno</a>
            </div>
            <div id="repeaterCard" class="hidden mb-8">
                <div class="flex justify-between items-center mb-2">
                    <div class="flex gap-2 items-center text-lg font-semibold text-primary"><i class="fas fa-users"></i> Alta masiva de beneficiarios</div>
                    <button type="button" onclick="toggleRepeater(false)" class="px-3 py-1 text-lg font-bold rounded text-error hover:bg-error/10">&times;</button>
                </div>
                <form id="formRepeater" action="store.php" method="POST">
                    <input type="hidden" name="actividad_id" value="<?php echo $actividad_id; ?>">
                    <div class="overflow-x-auto">
                        <table class="mb-4 min-w-full divide-y divide-cadet/30">
                            <thead class="bg-cadet/10">
                                <tr>
                                    <th class="px-2 py-2 text-xs uppercase text-cadet">CURP</th>
                                    <th class="px-2 py-2 text-xs uppercase text-cadet">Nombre(s)</th>
                                    <th class="px-2 py-2 text-xs uppercase text-cadet">Apellido Paterno</th>
                                    <th class="px-2 py-2 text-xs uppercase text-cadet">Apellido Materno</th>
                                    <th class="px-2 py-2 text-xs uppercase text-cadet">Fecha Nacimiento</th>
                                    <th class="px-2 py-2 text-xs uppercase text-cadet">Sexo</th>
                                    <th class="px-2 py-2 text-xs uppercase text-cadet">Teléfono</th>
                                    <th class="px-2 py-2 text-xs uppercase text-cadet">Email</th>
                                    <th class="px-2 py-2 text-xs uppercase text-cadet">Escolaridad</th>
                                    <th class="px-2 py-2 text-xs uppercase text-cadet">Ocupación</th>
                                    <th class="px-2 py-2 text-xs uppercase text-cadet">Colonia</th>
                                    <th class="px-2 py-2 text-xs uppercase text-cadet">Calle y Número</th>
                                    <th class="px-2 py-2 text-xs uppercase text-cadet">Código Postal</th>
                                    <th class="px-2 py-2 text-xs uppercase text-cadet">Municipio</th>
                                    <th class="px-2 py-2 text-xs uppercase text-cadet">Estado</th>
                                    <th class="px-2 py-2 text-xs uppercase text-cadet">Organización</th>
                                    <th class="px-2 py-2 text-xs uppercase text-cadet">Cargo</th>
                                    <th class="px-2 py-2 text-xs uppercase text-cadet">Fecha Asistencia</th>
                                    <th class="px-2 py-2 text-xs uppercase text-cadet">Observaciones</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="repeaterBody">
                                <tr>
                                    <td><input type="text" name="curp[]" maxlength="18" class="px-2 py-1 w-full rounded-lg border border-cadet/50"></td>
                                    <td><input type="text" name="nombre[]" maxlength="100" class="px-2 py-1 w-full rounded-lg border border-cadet/50" required></td>
                                    <td><input type="text" name="apellido_paterno[]" maxlength="100" class="px-2 py-1 w-full rounded-lg border border-cadet/50" required></td>
                                    <td><input type="text" name="apellido_materno[]" maxlength="100" class="px-2 py-1 w-full rounded-lg border border-cadet/50"></td>
                                    <td><input type="date" name="fecha_nacimiento[]" class="px-2 py-1 w-full rounded-lg border border-cadet/50"></td>
                                    <td>
                                        <select name="sexo[]" class="px-2 py-1 w-full rounded-lg border border-cadet/50">
                                            <option value="">-</option>
                                            <?php foreach ($sexos as $sx): ?>
                                                <option value="<?php echo $sx; ?>"><?php echo $sx; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td><input type="text" name="telefono[]" maxlength="20" class="px-2 py-1 w-full rounded-lg border border-cadet/50"></td>
                                    <td><input type="email" name="email[]" maxlength="100" class="px-2 py-1 w-full rounded-lg border border-cadet/50"></td>
                                    <td><input type="text" name="escolaridad[]" maxlength="100" class="px-2 py-1 w-full rounded-lg border border-cadet/50"></td>
                                    <td><input type="text" name="ocupacion[]" maxlength="100" class="px-2 py-1 w-full rounded-lg border border-cadet/50"></td>
                                    <td><input type="text" name="colonia[]" maxlength="255" class="px-2 py-1 w-full rounded-lg border border-cadet/50"></td>
                                    <td><input type="text" name="calle_numero[]" maxlength="255" class="px-2 py-1 w-full rounded-lg border border-cadet/50"></td>
                                    <td><input type="text" name="codigo_postal[]" maxlength="10" class="px-2 py-1 w-full rounded-lg border border-cadet/50"></td>
                                    <td><input type="text" name="municipio[]" maxlength="100" class="px-2 py-1 w-full rounded-lg border border-cadet/50"></td>
                                    <td><input type="text" name="estado[]" maxlength="100" class="px-2 py-1 w-full rounded-lg border border-cadet/50"></td>
                                    <td><input type="text" name="organizacion[]" maxlength="255" class="px-2 py-1 w-full rounded-lg border border-cadet/50"></td>
                                    <td><input type="text" name="cargo[]" maxlength="100" class="px-2 py-1 w-full rounded-lg border border-cadet/50"></td>
                                    <td><input type="date" name="fecha_asistencia[]" value="<?php echo date('Y-m-d'); ?>" class="px-2 py-1 w-full rounded-lg border border-cadet/50" required></td>
                                    <td><input type="text" name="observaciones[]" maxlength="255" class="px-2 py-1 w-full rounded-lg border border-cadet/50"></td>
                                    <td><button type="button" onclick="removeRow(this)" class="px-3 py-1 font-bold text-white rounded-lg bg-error">X</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <button type="button" onclick="addRow()" class="flex gap-2 items-center px-6 py-2 mb-4 font-semibold text-white bg-green-600 rounded-lg"><i class="fas fa-plus"></i> Agregar Beneficiario</button>
                    <button type="submit" class="flex float-right gap-2 items-center px-6 py-2 font-semibold text-white rounded-lg bg-primary"><i class="fas fa-save"></i> Guardar Todo</button>
                </form>
                <div class="flex justify-end mt-2 w-full">
                    <!-- Botón Guardar Todo ahora está arriba de la división -->
                </div>
                <hr class="my-8 border-t-2 border-cadet/40">
                <script>
                    function toggleRepeater(show) {
                        document.getElementById('repeaterCard').classList.toggle('hidden', !show);
                    }

                    function addRow() {
                        const tbody = document.getElementById('repeaterBody');
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                        <td><input type="text" name="curp[]" maxlength="18" class="px-2 py-1 w-full rounded-lg border border-cadet/50"></td>
                        <td><input type="text" name="nombre[]" maxlength="100" class="px-2 py-1 w-full rounded-lg border border-cadet/50" required></td>
                        <td><input type="text" name="apellido_paterno[]" maxlength="100" class="px-2 py-1 w-full rounded-lg border border-cadet/50" required></td>
                        <td><input type="text" name="apellido_materno[]" maxlength="100" class="px-2 py-1 w-full rounded-lg border border-cadet/50"></td>
                        <td><input type="date" name="fecha_nacimiento[]" class="px-2 py-1 w-full rounded-lg border border-cadet/50"></td>
                        <td><select name="sexo[]" class="px-2 py-1 w-full rounded-lg border border-cadet/50"><option value="">-</option><?php foreach ($sexos as $sx): ?><option value=\"<?php echo $sx; ?>\"><?php echo $sx; ?></option><?php endforeach; ?></select></td>
                        <td><input type="text" name="telefono[]" maxlength="20" class="px-2 py-1 w-full rounded-lg border border-cadet/50"></td>
                        <td><input type="email" name="email[]" maxlength="100" class="px-2 py-1 w-full rounded-lg border border-cadet/50"></td>
                        <td><input type="text" name="escolaridad[]" maxlength="100" class="px-2 py-1 w-full rounded-lg border border-cadet/50"></td>
                        <td><input type="text" name="ocupacion[]" maxlength="100" class="px-2 py-1 w-full rounded-lg border border-cadet/50"></td>
                        <td><input type="text" name="colonia[]" maxlength="255" class="px-2 py-1 w-full rounded-lg border border-cadet/50"></td>
                        <td><input type="text" name="calle_numero[]" maxlength="255" class="px-2 py-1 w-full rounded-lg border border-cadet/50"></td>
                        <td><input type="text" name="codigo_postal[]" maxlength="10" class="px-2 py-1 w-full rounded-lg border border-cadet/50"></td>
                        <td><input type="text" name="municipio[]" maxlength="100" class="px-2 py-1 w-full rounded-lg border border-cadet/50"></td>
                        <td><input type="text" name="estado[]" maxlength="100" class="px-2 py-1 w-full rounded-lg border border-cadet/50"></td>
                        <td><input type="text" name="organizacion[]" maxlength="255" class="px-2 py-1 w-full rounded-lg border border-cadet/50"></td>
                        <td><input type="text" name="cargo[]" maxlength="100" class="px-2 py-1 w-full rounded-lg border border-cadet/50"></td>
                        <td><input type="date" name="fecha_asistencia[]" value="<?php echo date('Y-m-d'); ?>" class="px-2 py-1 w-full rounded-lg border border-cadet/50" required></td>
                        <td><input type="text" name="observaciones[]" maxlength="255" class="px-2 py-1 w-full rounded-lg border border-cadet/50"></td>
                        <td><button type="button" onclick="removeRow(this)" class="px-3 py-1 font-bold text-white rounded-lg bg-error">X</button></td>
                        `;
                        tbody.appendChild(tr);
                    }

                    function removeRow(btn) {
                        const tr = btn.closest('tr');
                        tr.remove();
                    }
                </script>
            </div>
            <div class="flex justify-between items-center mb-4">
                <div class="text-lg font-semibold text-primary">Beneficiarios registrados en esta actividad</div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-cadet/30">
                    <thead class="bg-cadet/10">
                        <tr>
                            <th class="px-4 py-2 text-xs font-medium tracking-wider text-left uppercase text-cadet">CURP</th>
                            <th class="px-4 py-2 text-xs font-medium tracking-wider text-left uppercase text-cadet">Nombre(s)</th>
                            <th class="px-4 py-2 text-xs font-medium tracking-wider text-left uppercase text-cadet">Apellido Paterno</th>
                            <th class="px-4 py-2 text-xs font-medium tracking-wider text-left uppercase text-cadet">Apellido Materno</th>
                            <th class="px-4 py-2 text-xs font-medium tracking-wider text-left uppercase text-cadet">Fecha Asistencia</th>
                            <th class="px-4 py-2 text-xs font-medium tracking-wider text-left uppercase text-cadet">Observaciones</th>
                            <th class="px-4 py-2 text-xs font-medium tracking-wider text-left uppercase text-cadet">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($beneficiarios as $b): ?>
                            <tr class="hover:bg-cadet/10">
                                <td class="px-4 py-2 text-sm text-darkpurple"><?php echo htmlspecialchars($b['curp']); ?></td>
                                <td class="px-4 py-2 text-sm text-darkpurple"><?php echo htmlspecialchars($b['nombre']); ?></td>
                                <td class="px-4 py-2 text-sm text-darkpurple"><?php echo htmlspecialchars($b['apellido_paterno']); ?></td>
                                <td class="px-4 py-2 text-sm text-darkpurple"><?php echo htmlspecialchars($b['apellido_materno']); ?></td>
                                <td class="px-4 py-2 text-sm text-darkpurple"><?php echo htmlspecialchars(date('d/m/Y', strtotime($b['fecha_asistencia']))); ?></td>
                                <td class="px-4 py-2 text-sm text-darkpurple"><?php echo htmlspecialchars($b['observaciones']); ?></td>
                                <td class="flex gap-2 px-4 py-2">
                                    <a href="edit.php?id=<?php echo $b['id']; ?>&actividad_id=<?php echo $actividad_id; ?>" class="px-3 py-1 text-xs font-semibold text-white rounded-lg transition-colors bg-secondary hover:bg-primary"><i class="fas fa-edit"></i> Editar</a>
                                    <a href="delete.php?id=<?php echo $b['id']; ?>&actividad_id=<?php echo $actividad_id; ?>" class="px-3 py-1 text-xs font-semibold text-white rounded-lg transition-colors bg-error hover:bg-bittersweet/80" onclick="return confirm('¿Eliminar este beneficiario de la actividad?');"><i class="fas fa-trash"></i> Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center text-cadet">Selecciona una actividad para ver o agregar beneficiarios.</div>
        <?php endif; ?>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>