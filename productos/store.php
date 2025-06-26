<?php
require_once '../includes/config.php';
if (!isAuthenticated()) redirect('/auth/login.php');
$componente_id = intval($_POST['componente_id'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');
$tipo_producto = trim($_POST['tipo_producto'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
if ($componente_id <= 0 || $nombre === '') {
    setFlashMessage('error', 'El componente y el nombre son obligatorios.');
    redirect('create.php');
}
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("INSERT INTO productos (componente_id, nombre, tipo_producto, descripcion) VALUES (?, ?, ?, ?)");
    $stmt->execute([$componente_id, $nombre, $tipo_producto, $descripcion]);
    setFlashMessage('success', 'Producto creado correctamente.');
    redirect('index.php');
} catch (PDOException $e) {
    setFlashMessage('error', 'Error al guardar: ' . $e->getMessage());
    redirect('create.php');
}
