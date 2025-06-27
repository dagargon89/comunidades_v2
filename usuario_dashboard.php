<?php
require_once 'includes/config.php';
if (!isAuthenticated() || !hasRole('usuario')) redirect('/index.php');
$page_title = "Mi Panel";
require_once 'includes/header.php';
?>
<div class="flex flex-col items-center justify-center min-h-[70vh]">
    <div class="bg-white rounded-2xl shadow-2xl p-8 border border-cadet/40 w-full max-w-2xl mx-auto text-center">
        <h1 class="text-3xl font-bold text-primary mb-4">Panel de Usuario</h1>
        <p class="text-lg text-cadet">Bienvenida/o al panel exclusivo para el rol <span class="font-bold text-secondary">Usuario</span>.</p>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?> 