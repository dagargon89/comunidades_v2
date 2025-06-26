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
    $stmt = $pdo->prepare("INSERT INTO poligonos (nombre, descripcion, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([$nombre, $descripcion]);
    setFlashMessage('success', 'Polígono creado correctamente.');
    redirect('index.php');
} catch (PDOException $e) {
    setFlashMessage('error', 'Error al guardar el polígono: ' . $e->getMessage());
    redirect('create.php');
}
