<?php
$page_title = "Iniciar Sesión";
require_once '../includes/header.php';

// Si ya está autenticado, redirigir al dashboard
if (isAuthenticated()) {
    redirect('/');
}
?>

<div class="flex flex-col items-center justify-center min-h-[80vh]">
    <div class="card w-full max-w-md mx-auto">
        <div class="flex flex-col items-center mb-6">
            <div class="w-16 h-16 rounded-full bg-accent flex items-center justify-center mb-2 shadow">
                <i class="fas fa-users text-3xl text-primary"></i>
            </div>
            <h2 class="text-2xl font-bold text-primary mb-1">Iniciar Sesión</h2>
            <p class="text-sm text-cadet">
                O <a href="registro.php" class="text-secondary font-semibold hover:underline">regístrate si no tienes una cuenta</a>
            </p>
        </div>
        <form class="space-y-5" action="proceso_login.php" method="POST" autocomplete="on">
            <div>
                <label for="email" class="form-label">Correo electrónico</label>
                <input id="email" name="email" type="email" required class="form-input" placeholder="Correo electrónico" autofocus>
            </div>
            <div>
                <label for="password" class="form-label">Contraseña</label>
                <input id="password" name="password" type="password" required class="form-input" placeholder="Contraseña">
            </div>
            <div class="flex items-center justify-between">
                <label class="flex items-center text-sm text-primary">
                    <input id="remember_me" name="remember_me" type="checkbox" class="mr-2 accent-primary rounded">
                    Recordarme
                </label>
                <a href="#" class="text-sm text-secondary hover:underline">¿Olvidaste tu contraseña?</a>
            </div>
            <button type="submit" class="btn-primary w-full flex items-center justify-center gap-2">
                <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
            </button>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>