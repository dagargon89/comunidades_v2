<?php
require_once '../includes/config.php';
if (!isAuthenticated()) redirect('/auth/login.php');

// Obtener y validar datos del formulario
$id = intval($_POST['id'] ?? 0);
$producto_id = intval($_POST['producto_id'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$tipo_actividad_id = intval($_POST['tipo_actividad_id'] ?? 0);
$tipo_poblacion_id = intval($_POST['tipo_poblacion_id'] ?? 0);
$estado_actividad_id = intval($_POST['estado_actividad_id'] ?? 0);
$fecha_inicio = trim($_POST['fecha_inicio'] ?? '');
$fecha_fin = trim($_POST['fecha_fin'] ?? '');
$lugar = trim($_POST['lugar'] ?? '');
$responsable_id = intval($_POST['responsable_id'] ?? 0);
$modalidad = trim($_POST['modalidad'] ?? '');
$meta = trim($_POST['meta'] ?? '');
$indicador = trim($_POST['indicador'] ?? '');
$poligono_id = intval($_POST['poligono_id'] ?? 0);
$grupo = trim($_POST['grupo'] ?? '');
$cantidad_sesiones = intval($_POST['cantidad_sesiones'] ?? 0);
$observaciones = trim($_POST['observaciones'] ?? '');

// Validaciones básicas
if ($id <= 0 || $producto_id <= 0 || $nombre === '') {
    setFlashMessage('error', 'El ID, producto y nombre son obligatorios.');
    redirect('index.php');
}

// Validar fechas si se proporcionan
if ($fecha_inicio && $fecha_fin) {
    $fecha_inicio_obj = new DateTime($fecha_inicio);
    $fecha_fin_obj = new DateTime($fecha_fin);
    if ($fecha_inicio_obj > $fecha_fin_obj) {
        setFlashMessage('error', 'La fecha de inicio no puede ser posterior a la fecha de fin.');
        redirect('edit.php?id=' . $id);
    }
}

try {
    $pdo = getDBConnection();

    // Verificar que la actividad existe
    $stmt = $pdo->prepare("SELECT id FROM actividades WHERE id = ?");
    $stmt->execute([$id]);
    if (!$stmt->fetch()) {
        setFlashMessage('error', 'Actividad no encontrada.');
        redirect('index.php');
    }

    // Actualizar la actividad
    $sql = "UPDATE actividades SET 
            producto_id = ?, 
            nombre = ?, 
            descripcion = ?, 
            tipo_actividad_id = ?, 
            tipo_poblacion_id = ?, 
            estado_actividad_id = ?, 
            fecha_inicio = ?, 
            fecha_fin = ?, 
            lugar = ?, 
            responsable_id = ?, 
            modalidad = ?, 
            meta = ?, 
            indicador = ?, 
            poligono_id = ?, 
            grupo = ?, 
            cantidad_sesiones = ?, 
            observaciones = ?,
            updated_at = NOW()
            WHERE id = ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $producto_id,
        $nombre,
        $descripcion,
        $tipo_actividad_id ?: null,
        $tipo_poblacion_id ?: null,
        $estado_actividad_id ?: null,
        $fecha_inicio ?: null,
        $fecha_fin ?: null,
        $lugar,
        $responsable_id ?: null,
        $modalidad,
        $meta,
        $indicador,
        $poligono_id ?: null,
        $grupo,
        $cantidad_sesiones,
        $observaciones,
        $id
    ]);

    setFlashMessage('success', 'Actividad actualizada correctamente.');
    redirect('index.php');
} catch (PDOException $e) {
    setFlashMessage('error', 'Error al actualizar: ' . $e->getMessage());
    redirect('edit.php?id=' . $id);
}
?>
