<?php
require_once '../includes/config.php';
if (!isAuthenticated()) redirect('/auth/login.php');
$nombre = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
if ($nombre === '') {
    setFlashMessage('error', 'El nombre es obligatorio.');
    redirect('create.php');
}
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("INSERT INTO ejes (nombre, descripcion) VALUES (?, ?)");
    $stmt->execute([$nombre, $descripcion]);
    setFlashMessage('success', 'Eje estratégico creado correctamente.');
    redirect('index.php');
} catch (PDOException $e) {
    setFlashMessage('error', 'Error al guardar: ' . $e->getMessage());
    redirect('create.php');
}
