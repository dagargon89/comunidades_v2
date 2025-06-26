<?php
require_once '../includes/config.php';
if (!isAuthenticated()) redirect('/auth/login.php');
$id = intval($_POST['id'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');
if ($id <= 0 || $nombre === '') {
    setFlashMessage('error', 'Datos inválidos.');
    redirect('edit.php?id=' . $id);
}
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("UPDATE estados_actividad SET nombre = ? WHERE id = ?");
    $stmt->execute([$nombre, $id]);
    setFlashMessage('success', 'Estado de actividad actualizado correctamente.');
    redirect('index.php');
} catch (PDOException $e) {
    setFlashMessage('error', 'Error al actualizar: ' . $e->getMessage());
    redirect('edit.php?id=' . $id);
}
