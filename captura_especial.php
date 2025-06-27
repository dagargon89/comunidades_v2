<?php
require_once 'includes/header.php';
if (!isAuthenticated() || !hasRole('capturista')) redirect('/auth/login.php');
?>
<div class="flex flex-col items-center justify-center min-h-[60vh]">
    <div class="bg-white rounded-2xl shadow-2xl p-8 border border-cadet/40 w-full max-w-xl mx-auto text-center">
        <h1 class="text-2xl font-bold text-primary mb-4">
            <i class="fas fa-keyboard text-accent"></i> Panel de Captura Especial
        </h1>
        <p class="text-lg text-darkpurple mb-6">Has iniciado sesión como <span class="font-semibold">capturista</span>. Aquí podrás capturar información especial (próximamente).</p>
        <a href="/usuarios/perfil.php" class="bg-primary text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 shadow hover:bg-secondary focus:ring-2 focus:ring-accent focus:outline-none flex items-center gap-2 mb-2">
            <i class="fas fa-user"></i> Ver mi perfil
        </a>
        <a href="/usuarios/update_perfil.php" class="bg-secondary text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 shadow hover:bg-primary focus:ring-2 focus:ring-accent focus:outline-none flex items-center gap-2 mb-2">
            <i class="fas fa-edit"></i> Editar mi perfil
        </a>
        <a href="/auth/logout.php" class="bg-error text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 shadow hover:bg-primary focus:ring-2 focus:ring-accent focus:outline-none flex items-center gap-2">
            <i class="fas fa-sign-out-alt"></i> Cerrar sesión
        </a>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?> 