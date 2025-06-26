<?php
require_once '../includes/config.php';
if (!isAuthenticated()) redirect('/auth/login.php');

// Obtener y validar datos del formulario
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
$estatus = 'Programada';
$meta = trim($_POST['meta'] ?? '');
$indicador = trim($_POST['indicador'] ?? '');
$poligono_id = intval($_POST['poligono_id'] ?? 0);
$grupo = trim($_POST['grupo'] ?? '');
$cantidad_sesiones = intval($_POST['cantidad_sesiones'] ?? 0);
$calle = trim($_POST['calle'] ?? '');
$numero_casa = trim($_POST['numero_casa'] ?? '');
$colonia = trim($_POST['colonia'] ?? '');
$entre_calles = trim($_POST['entre_calles'] ?? '');
$google_maps = trim($_POST['google_maps'] ?? '');
$latitud = $_POST['latitud'] !== '' ? floatval($_POST['latitud']) : null;
$longitud = $_POST['longitud'] !== '' ? floatval($_POST['longitud']) : null;
$observaciones = trim($_POST['observaciones'] ?? '');

// Validaciones básicas
if ($producto_id <= 0 || $nombre === '') {
    setFlashMessage('error', 'El producto y el nombre son obligatorios.');
    redirect('create.php');
}

// Validar fechas si se proporcionan
if ($fecha_inicio && $fecha_fin) {
    $fecha_inicio_obj = new DateTime($fecha_inicio);
    $fecha_fin_obj = new DateTime($fecha_fin);
    if ($fecha_inicio_obj > $fecha_fin_obj) {
        setFlashMessage('error', 'La fecha de inicio no puede ser posterior a la fecha de fin.');
        redirect('create.php');
    }
}

try {
    $pdo = getDBConnection();

    // Insertar la actividad
    $sql = "INSERT INTO actividades (
        producto_id, nombre, descripcion, tipo_actividad_id, tipo_poblacion_id, 
        estado_actividad_id, fecha_inicio, fecha_fin, lugar, responsable_id, 
        responsable_registro_id, modalidad, estatus, meta, indicador, poligono_id, 
        grupo, cantidad_sesiones, calle, numero_casa, colonia, entre_calles, 
        google_maps, latitud, longitud, observaciones
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

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
        $_SESSION['user_id'],
        $modalidad,
        $estatus,
        $meta,
        $indicador,
        $poligono_id ?: null,
        $grupo,
        $cantidad_sesiones,
        $calle,
        $numero_casa,
        $colonia,
        $entre_calles,
        $google_maps,
        $latitud,
        $longitud,
        $observaciones
    ]);

    setFlashMessage('success', 'Actividad creada correctamente.');
    redirect('index.php');
} catch (PDOException $e) {
    setFlashMessage('error', 'Error al guardar: ' . $e->getMessage());
    redirect('create.php');
}
