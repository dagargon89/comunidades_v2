<?php
require_once '../includes/config.php';
if (!isAuthenticated()) redirect('/auth/login.php');
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) redirect('index.php');
try {
    $pdo = getDBConnection();

    // Verificar si hay usuarios asociados
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM usuarios WHERE organizacion_id = ?");
    $stmt->execute([$id]);
    $result = $stmt->fetch();

    if ($result['count'] > 0) {
        setFlashMessage('error', 'No se puede eliminar la organización porque tiene usuarios asociados. Primero asigna los usuarios a otra organización.');
        redirect('index.php');
    }

    $stmt = $pdo->prepare("DELETE FROM organizaciones WHERE id = ?");
    $stmt->execute([$id]);
    setFlashMessage('success', 'Organización eliminada correctamente.');
    redirect('index.php');
} catch (PDOException $e) {
    setFlashMessage('error', 'Error al eliminar: ' . $e->getMessage());
    redirect('index.php');
}
