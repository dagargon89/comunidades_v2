<?php
require_once '../includes/config.php';
if (!isAuthenticated()) redirect('/auth/login.php');
$id = intval($_GET['id'] ?? 0);
$actividad_id = intval($_GET['actividad_id'] ?? 0);
if ($id <= 0 || $actividad_id <= 0) redirect('index.php?actividad_id=' . $actividad_id);
try {
    $pdo = getDBConnection();
    $pdo->beginTransaction();
    // Eliminar la relación con la actividad
    $stmt = $pdo->prepare("DELETE FROM actividad_beneficiario WHERE actividad_id = ? AND beneficiario_id = ?");
    $stmt->execute([$actividad_id, $id]);
    // Verificar si el beneficiario está asociado a otra actividad
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM actividad_beneficiario WHERE beneficiario_id = ?");
    $stmt->execute([$id]);
    $count = $stmt->fetchColumn();
    if ($count == 0) {
        // Eliminar beneficiario si no tiene más asociaciones
        $stmt = $pdo->prepare("DELETE FROM beneficiarios WHERE id = ?");
        $stmt->execute([$id]);
    }
    $pdo->commit();
    setFlashMessage('success', 'Beneficiario eliminado correctamente.');
    redirect('index.php?actividad_id=' . $actividad_id);
} catch (PDOException $e) {
    $pdo->rollBack();
    setFlashMessage('error', 'Error al eliminar: ' . $e->getMessage());
    redirect('index.php?actividad_id=' . $actividad_id);
}
