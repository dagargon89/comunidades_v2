<?php
require_once '../includes/config.php';
if (!isAuthenticated()) redirect('/auth/login.php');

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) redirect('index.php');

try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("DELETE FROM poligonos WHERE id = ?");
    $stmt->execute([$id]);
    setFlashMessage('success', 'Polígono eliminado correctamente.');
    redirect('index.php');
} catch (PDOException $e) {
    setFlashMessage('error', 'Error al eliminar el polígono: ' . $e->getMessage());
    redirect('index.php');
}
