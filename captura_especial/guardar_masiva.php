<?php
require_once '../includes/config.php';
if (!isAuthenticated() || !hasRole('capturista')) redirect('/auth/login.php');

$actividad_id = intval($_POST['actividad_id'] ?? 0);
$fecha_asistencia = $_POST['fecha_asistencia'] ?? null;
$nombres = $_POST['nombre'] ?? [];
$apellidos_pat = $_POST['apellido_paterno'] ?? [];
$apellidos_mat = $_POST['apellido_materno'] ?? [];
$telefonos = $_POST['telefono'] ?? [];
$sexos = $_POST['sexo'] ?? [];
$observaciones = $_POST['observaciones'] ?? [];

if ($actividad_id <= 0 || !$fecha_asistencia || empty($nombres)) {
    setFlashMessage('error', 'Datos obligatorios faltantes.');
    redirect('captura_masiva.php?id=' . $actividad_id);
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
    $guardados = 0;
    $errores = [];
    
    for ($i = 0; $i < count($nombres); $i++) {
        $nombre = trim($nombres[$i] ?? '');
        $ap_pat = trim($apellidos_pat[$i] ?? '');
        $ap_mat = trim($apellidos_mat[$i] ?? '');
        $telefono = trim($telefonos[$i] ?? '');
        $sexo = trim($sexos[$i] ?? '');
        $obs = trim($observaciones[$i] ?? '');
        
        if (empty($nombre) || empty($ap_pat)) {
            $errores[] = "Fila " . ($i + 1) . ": Nombre y apellido paterno son obligatorios";
            continue;
        }
        
        // Buscar si ya existe el beneficiario
        $stmt = $pdo->prepare("SELECT id FROM beneficiarios WHERE nombre = ? AND apellido_paterno = ? AND apellido_materno = ?");
        $stmt->execute([$nombre, $ap_pat, $ap_mat]);
        $benef = $stmt->fetch();
        
        if ($benef) {
            $benef_id = $benef['id'];
        } else {
            // Insertar beneficiario
            $stmt = $pdo->prepare("INSERT INTO beneficiarios (nombre, apellido_paterno, apellido_materno, telefono, sexo, capturista_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $nombre,
                $ap_pat,
                $ap_mat,
                $telefono,
                $sexo,
                $_SESSION['user_id']
            ]);
            $benef_id = $pdo->lastInsertId();
        }
        
        // Verificar si ya está asociado a la actividad para esa fecha
        $stmt = $pdo->prepare("SELECT 1 FROM actividad_beneficiario WHERE actividad_id = ? AND beneficiario_id = ? AND fecha_asistencia = ?");
        $stmt->execute([$actividad_id, $benef_id, $fecha_asistencia]);
        
        if (!$stmt->fetch()) {
            // Asociar beneficiario a la actividad
            $stmt = $pdo->prepare("INSERT INTO actividad_beneficiario (actividad_id, beneficiario_id, fecha_asistencia, observaciones) VALUES (?, ?, ?, ?)");
            $stmt->execute([$actividad_id, $benef_id, $fecha_asistencia, $obs]);
            $guardados++;
        } else {
            $errores[] = "Fila " . ($i + 1) . ": Ya registrado para esta fecha";
        }
    }
    
    $pdo->commit();
    
    if ($guardados > 0) {
        $mensaje = "Se registraron $guardados beneficiarios correctamente.";
        if (!empty($errores)) {
            $mensaje .= " Errores: " . implode(", ", $errores);
        }
        setFlashMessage('success', $mensaje);
    } else {
        setFlashMessage('error', 'No se pudo registrar ningún beneficiario. ' . implode(", ", $errores));
    }
    
    redirect('actividad.php?id=' . $actividad_id);
    
} catch (PDOException $e) {
    if (isset($pdo)) $pdo->rollBack();
    setFlashMessage('error', 'Error al guardar: ' . $e->getMessage());
    redirect('captura_masiva.php?id=' . $actividad_id);
}
?> 