<?php
require_once '../includes/config.php';
if (!isAuthenticated()) redirect('/auth/login.php');

$id = intval($_POST['id'] ?? 0);
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

if ($id <= 0 || $actividad_id <= 0 || $nombre === '' || $ap_pat === '' || !$fecha_asistencia) {
    setFlashMessage('error', 'Datos obligatorios faltantes.');
    redirect('edit.php?id=' . $id . '&actividad_id=' . $actividad_id);
}

try {
    $pdo = getDBConnection();
    $pdo->beginTransaction();
    // Actualizar beneficiario
    $stmt = $pdo->prepare("UPDATE beneficiarios SET curp=?, nombre=?, apellido_paterno=?, apellido_materno=?, fecha_nacimiento=?, sexo=?, telefono=?, email=?, escolaridad=?, ocupacion=?, colonia=?, calle_numero=?, codigo_postal=?, municipio=?, estado=?, organizacion=?, cargo=? WHERE id=?");
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
        $id
    ]);
    // Actualizar asociación a la actividad
    $stmt = $pdo->prepare("SELECT id FROM actividad_beneficiario WHERE actividad_id = ? AND beneficiario_id = ?");
    $stmt->execute([$actividad_id, $id]);
    $rel = $stmt->fetch();
    if ($rel) {
        $stmt = $pdo->prepare("UPDATE actividad_beneficiario SET fecha_asistencia=?, observaciones=? WHERE actividad_id=? AND beneficiario_id=?");
        $stmt->execute([$fecha_asistencia, $obs, $actividad_id, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO actividad_beneficiario (actividad_id, beneficiario_id, fecha_asistencia, observaciones) VALUES (?, ?, ?, ?)");
        $stmt->execute([$actividad_id, $id, $fecha_asistencia, $obs]);
    }
    $pdo->commit();
    setFlashMessage('success', 'Beneficiario actualizado correctamente.');
    redirect('index.php?actividad_id=' . $actividad_id);
} catch (PDOException $e) {
    $pdo->rollBack();
    setFlashMessage('error', 'Error al actualizar: ' . $e->getMessage());
    redirect('edit.php?id=' . $id . '&actividad_id=' . $actividad_id);
}
