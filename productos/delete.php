<?php
require_once '../includes/config.php';
if (!isAuthenticated()) redirect('/auth/login.php');
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) redirect('index.php');
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->execute([$id]);
    setFlashMessage('success', 'Producto eliminado correctamente.');
    redirect('index.php');
} catch (PDOException $e) {
    setFlashMessage('error', 'Error al eliminar: ' . $e->getMessage());
    redirect('index.php');
}
