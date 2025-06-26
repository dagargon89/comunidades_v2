<?php
require_once '../includes/config.php';
if (!isAuthenticated()) redirect('/auth/login.php');
$nombre = trim($_POST['nombre'] ?? '');
if ($nombre === '') {
    setFlashMessage('error', 'El nombre es obligatorio.');
    redirect('create.php');
}
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("INSERT INTO tipos_actividad (nombre) VALUES (?)");
    $stmt->execute([$nombre]);
    setFlashMessage('success', 'Tipo de actividad creado correctamente.');
    redirect('index.php');
} catch (PDOException $e) {
    setFlashMessage('error', 'Error al guardar: ' . $e->getMessage());
    redirect('create.php');
}
