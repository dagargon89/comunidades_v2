<?php
$page_title = "Iniciar Sesión";
require_once '../includes/config.php';
// Si ya está autenticado, redirigir al dashboard
if (isAuthenticated()) {
    redirect('/');
}
require_once '../includes/header.php';
?>

<div class="flex flex-col items-center justify-center min-h-[80vh]">
    <div class="bg-white rounded-2xl shadow-2xl p-8 border border-cadet/40 w-full max-w-md mx-auto">
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
                <label for="email" class="block text-sm font-semibold text-darkpurple mb-1">Correo electrónico</label>
                <input id="email" name="email" type="email" required class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-white placeholder-cadet text-darkpurple shadow-sm" placeholder="Correo electrónico" autofocus>
            </div>
            <div>
                <label for="password" class="block text-sm font-semibold text-darkpurple mb-1">Contraseña</label>
                <input id="password" name="password" type="password" required class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-white placeholder-cadet text-darkpurple shadow-sm" placeholder="Contraseña">
            </div>
            <div class="flex items-center justify-between">
                <label class="flex items-center text-sm text-primary">
                    <input id="remember_me" name="remember_me" type="checkbox" class="mr-2 accent-primary rounded">
                    Recordarme
                </label>
                <a href="#" class="text-sm text-secondary hover:underline">¿Olvidaste tu contraseña?</a>
            </div>
            <button type="submit" class="bg-primary text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 shadow hover:bg-secondary focus:ring-2 focus:ring-accent focus:outline-none w-full flex items-center justify-center gap-2">
                <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
            </button>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>