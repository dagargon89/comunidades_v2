<?php
$page_title = "Nueva Organización";
require_once '../includes/header.php';
if (!isAuthenticated()) redirect('/auth/login.php');
?>
<div class="flex flex-col items-center justify-center min-h-[70vh]">
    <div class="bg-white rounded-2xl shadow-2xl p-8 border border-cadet/40 w-full max-w-lg mx-auto">
        <h1 class="text-2xl font-bold text-primary flex items-center gap-2 mb-6">
            <i class="fas fa-building text-accent"></i> Nueva Organización
        </h1>
        <form action="store.php" method="POST" class="space-y-6">
            <div>
                <label for="nombre" class="block text-sm font-semibold text-darkpurple mb-1">Nombre de la Organización *</label>
                <input type="text" id="nombre" name="nombre" required maxlength="255" class="w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white placeholder-cadet text-darkpurple shadow-sm" placeholder="Nombre de la organización">
                <p class="text-xs text-cadet mt-1">El nombre debe ser único en el sistema.</p>
            </div>
            <div class="flex items-center justify-between mt-4">
                <a href="index.php" class="text-sm text-cadet hover:text-primary flex items-center gap-1"><i class="fas fa-arrow-left"></i> Volver al listado</a>
                <button type="submit" class="bg-primary text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 shadow hover:bg-secondary focus:ring-2 focus:ring-accent focus:outline-none flex items-center gap-2">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>