<?php
require_once '../includes/config.php';
if (!isAuthenticated() || !hasRole('capturista')) redirect('/auth/login.php');

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
$fecha_asistencia = $_POST['fecha_asistencia'] ?? null;
$obs = trim($_POST['observaciones'] ?? '');

if ($actividad_id <= 0 || $nombre === '' || $ap_pat === '' || !$fecha_asistencia) {
    setFlashMessage('error', 'Datos obligatorios faltantes.');
    redirect('captura_individual.php?id=' . $actividad_id);
}

try {
    $pdo = getDBConnection();
    
    // Verificar que la actividad pertenece al usuario
    $stmt = $pdo->prepare("SELECT id FROM actividades WHERE id = ? AND (responsable_id = ? OR responsable_registro_id = ?)");
    $stmt->execute([$actividad_id, $_SESSION['user_id'], $_SESSION['user_id']]);
    if (!$stmt->fetch()) {
        setFlashMessage('error', 'No tienes permisos para esta actividad.');
        redirect('/captura_especial.php');
    }
    
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
        $stmt = $pdo->prepare("INSERT INTO beneficiarios (curp, nombre, apellido_paterno, apellido_materno, fecha_nacimiento, sexo, telefono, email, escolaridad, ocupacion, capturista_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
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
    } else {
        setFlashMessage('error', 'Este beneficiario ya está registrado para esta fecha.');
        redirect('captura_individual.php?id=' . $actividad_id);
    }
    
    $pdo->commit();
    setFlashMessage('success', 'Beneficiario registrado correctamente.');
    redirect('actividad.php?id=' . $actividad_id);
    
} catch (PDOException $e) {
    if (isset($pdo)) $pdo->rollBack();
    setFlashMessage('error', 'Error al guardar: ' . $e->getMessage());
    redirect('captura_individual.php?id=' . $actividad_id);
}
?> 