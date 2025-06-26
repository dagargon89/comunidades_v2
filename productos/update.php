<?php
require_once '../includes/config.php';
if (!isAuthenticated()) redirect('/auth/login.php');
$id = intval($_POST['id'] ?? 0);
$componente_id = intval($_POST['componente_id'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');
$tipo_producto = trim($_POST['tipo_producto'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
if ($id <= 0 || $componente_id <= 0 || $nombre === '') {
    setFlashMessage('error', 'Datos inválidos.');
    redirect('edit.php?id=' . $id);
}
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("UPDATE productos SET componente_id = ?, nombre = ?, tipo_producto = ?, descripcion = ? WHERE id = ?");
    $stmt->execute([$componente_id, $nombre, $tipo_producto, $descripcion, $id]);
    setFlashMessage('success', 'Producto actualizado correctamente.');
    redirect('index.php');
} catch (PDOException $e) {
    setFlashMessage('error', 'Error al actualizar: ' . $e->getMessage());
    redirect('edit.php?id=' . $id);
}
