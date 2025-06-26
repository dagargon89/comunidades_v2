<?php
require_once '../includes/config.php';
if (!isAuthenticated()) redirect('/auth/login.php');
$id = intval($_POST['id'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
if ($id <= 0 || $nombre === '') {
    setFlashMessage('error', 'Datos inválidos.');
    redirect('edit.php?id=' . $id);
}
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("UPDATE ejes SET nombre = ?, descripcion = ? WHERE id = ?");
    $stmt->execute([$nombre, $descripcion, $id]);
    setFlashMessage('success', 'Eje estratégico actualizado correctamente.');
    redirect('index.php');
} catch (PDOException $e) {
    setFlashMessage('error', 'Error al actualizar: ' . $e->getMessage());
    redirect('edit.php?id=' . $id);
}
