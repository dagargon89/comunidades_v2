<?php
require_once '../includes/config.php';
if (!isAuthenticated()) redirect('/auth/login.php');
$eje_id = intval($_POST['eje_id'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
if ($eje_id <= 0 || $nombre === '') {
    setFlashMessage('error', 'El eje y el nombre son obligatorios.');
    redirect('create.php');
}
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("INSERT INTO componentes (eje_id, nombre, descripcion) VALUES (?, ?, ?)");
    $stmt->execute([$eje_id, $nombre, $descripcion]);
    setFlashMessage('success', 'Componente creado correctamente.');
    redirect('index.php');
} catch (PDOException $e) {
    setFlashMessage('error', 'Error al guardar: ' . $e->getMessage());
    redirect('create.php');
}
