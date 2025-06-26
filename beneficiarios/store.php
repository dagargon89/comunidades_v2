<?php
require_once '../includes/config.php';
if (!isAuthenticated()) redirect('/auth/login.php');

$actividad_id = intval($_POST['actividad_id'] ?? 0);
$curp = trim($_POST['curp'] ?? '');
$nombre = trim($_POST['nombre'] ?? '');
$ap_pat = trim($_POST['apellido_paterno'] ?? '');
$ap_mat = trim($_POST['apellido_materno'] ?? '');
$fecha_nac = $_POST['fecha_nacimiento'] ?? null;
$sexo = trim($_POST['sexo'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$email = trim($_POST['email'] ?? '');
$escolaridad = trim($_POST['escolaridad'] ?? '');
$ocupacion = trim($_POST['ocupacion'] ?? '');
$colonia = trim($_POST['colonia'] ?? '');
$calle = trim($_POST['calle_numero'] ?? '');
$codigo_postal = trim($_POST['codigo_postal'] ?? '');
$municipio = trim($_POST['municipio'] ?? '');
$estado = trim($_POST['estado'] ?? '');
$organizacion = trim($_POST['organizacion'] ?? '');
$cargo = trim($_POST['cargo'] ?? '');
$fecha_asistencia = $_POST['fecha_asistencia'] ?? null;
$obs = trim($_POST['observaciones'] ?? '');

if ($actividad_id <= 0 || $nombre === '' || $ap_pat === '' || !$fecha_asistencia) {
    setFlashMessage('error', 'Datos obligatorios faltantes.');
    redirect('create.php?actividad_id=' . $actividad_id);
}

try {
    $pdo = getDBConnection();
    $pdo->beginTransaction();
    // Buscar si ya existe el beneficiario (por CURP si se proporciona, si no por nombre completo)
    if ($curp !== '') {
        $stmt = $pdo->prepare("SELECT id FROM beneficiarios WHERE curp = ?");
        $stmt->execute([$curp]);
        $benef = $stmt->fetch();
    } else {
        $stmt = $pdo->prepare("SELECT id FROM beneficiarios WHERE nombre = ? AND apellido_paterno = ? AND apellido_materno = ?");
        $stmt->execute([$nombre, $ap_pat, $ap_mat]);
        $benef = $stmt->fetch();
    }
    if ($benef) {
        $benef_id = $benef['id'];
    } else {
        // Insertar beneficiario
        $stmt = $pdo->prepare("INSERT INTO beneficiarios (curp, nombre, apellido_paterno, apellido_materno, fecha_nacimiento, sexo, telefono, email, escolaridad, ocupacion, colonia, calle_numero, codigo_postal, municipio, estado, organizacion, cargo, capturista_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $curp ?: null,
            $nombre,
            $ap_pat,
            $ap_mat,
            $fecha_nac ?: null,
            $sexo,
            $telefono,
            $email,
            $escolaridad,
            $ocupacion,
            $colonia,
            $calle,
            $codigo_postal,
            $municipio,
            $estado,
            $organizacion,
            $cargo,
            $_SESSION['user_id']
        ]);
        $benef_id = $pdo->lastInsertId();
    }
    // Asociar beneficiario a la actividad (si no existe ya la relación para esa fecha)
    $stmt = $pdo->prepare("SELECT 1 FROM actividad_beneficiario WHERE actividad_id = ? AND beneficiario_id = ? AND fecha_asistencia = ?");
    $stmt->execute([$actividad_id, $benef_id, $fecha_asistencia]);
    if (!$stmt->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO actividad_beneficiario (actividad_id, beneficiario_id, fecha_asistencia, observaciones) VALUES (?, ?, ?, ?)");
        $stmt->execute([$actividad_id, $benef_id, $fecha_asistencia, $obs]);
    }
    $pdo->commit();
    setFlashMessage('success', 'Beneficiario registrado correctamente.');
    redirect('index.php?actividad_id=' . $actividad_id);
} catch (PDOException $e) {
    $pdo->rollBack();
    setFlashMessage('error', 'Error al guardar: ' . $e->getMessage());
    redirect('create.php?actividad_id=' . $actividad_id);
}
