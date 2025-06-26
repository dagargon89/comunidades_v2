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

    // Verificar que el nombre sea único
    $stmt = $pdo->prepare("SELECT id FROM organizaciones WHERE nombre = ?");
    $stmt->execute([$nombre]);
    if ($stmt->fetch()) {
        setFlashMessage('error', 'Ya existe una organización con ese nombre.');
        redirect('create.php');
    }

    $stmt = $pdo->prepare("INSERT INTO organizaciones (nombre) VALUES (?)");
    $stmt->execute([$nombre]);
    setFlashMessage('success', 'Organización creada correctamente.');
    redirect('index.php');
} catch (PDOException $e) {
    setFlashMessage('error', 'Error al guardar: ' . $e->getMessage());
    redirect('create.php');
}
