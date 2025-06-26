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

    // Verificar que el nombre sea único (excluyendo la organización actual)
    $stmt = $pdo->prepare("SELECT id FROM organizaciones WHERE nombre = ? AND id != ?");
    $stmt->execute([$nombre, $id]);
    if ($stmt->fetch()) {
        setFlashMessage('error', 'Ya existe una organización con ese nombre.');
        redirect('edit.php?id=' . $id);
    }

    $stmt = $pdo->prepare("UPDATE organizaciones SET nombre = ? WHERE id = ?");
    $stmt->execute([$nombre, $id]);
    setFlashMessage('success', 'Organización actualizada correctamente.');
    redirect('index.php');
} catch (PDOException $e) {
    setFlashMessage('error', 'Error al actualizar: ' . $e->getMessage());
    redirect('edit.php?id=' . $id);
}
