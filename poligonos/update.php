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
    $stmt = $pdo->prepare("UPDATE poligonos SET nombre = ?, descripcion = ? WHERE id = ?");
    $stmt->execute([$nombre, $descripcion, $id]);
    setFlashMessage('success', 'Polígono actualizado correctamente.');
    redirect('index.php');
} catch (PDOException $e) {
    setFlashMessage('error', 'Error al actualizar el polígono: ' . $e->getMessage());
    redirect('edit.php?id=' . $id);
}
