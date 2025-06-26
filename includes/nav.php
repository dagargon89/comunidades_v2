<?php
// Este archivo debe ser incluido en el header para mostrar el menú de navegación
?>
<nav class="bg-white border-b border-cadet/20 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-14">
            <div class="flex items-center gap-8">
                <a href="/" class="text-primary font-bold text-lg flex items-center gap-2 hover:text-secondary transition-colors">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="/actividades/" class="text-primary hover:text-secondary px-2 py-1 rounded-md text-sm font-medium">Actividades</a>
                <a href="/beneficiarios/" class="text-primary hover:text-secondary px-2 py-1 rounded-md text-sm font-medium">Beneficiarios</a>
                <a href="/organizaciones/" class="text-primary hover:text-secondary px-2 py-1 rounded-md text-sm font-medium">Organizaciones</a>
                <a href="/gantt.php" class="text-primary hover:text-secondary px-2 py-1 rounded-md text-sm font-medium">Gantt</a>
                <!-- Menú desplegable de catálogos -->
                <div class="relative group">
                    <button class="text-primary hover:text-secondary px-2 py-1 rounded-md text-sm font-medium flex items-center gap-1 focus:outline-none">
                        <i class="fas fa-cogs"></i> Administración
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                    <div class="absolute left-0 mt-2 w-56 bg-white rounded-xl shadow-lg py-2 z-50 hidden group-hover:block group-focus-within:block border border-cadet/20">
                        <div class="px-4 py-2 text-xs text-cadet uppercase tracking-wider font-semibold">Catálogos</div>
                        <a href="/poligonos/" class="block px-4 py-2 text-sm text-primary hover:bg-base rounded-md flex items-center gap-2"><i class="fas fa-draw-polygon"></i> Polígonos</a>
                        <a href="/tipos_actividad/" class="block px-4 py-2 text-sm text-primary hover:bg-base rounded-md flex items-center gap-2"><i class="fas fa-tasks"></i> Tipos de Actividad</a>
                        <a href="/tipos_poblacion/" class="block px-4 py-2 text-sm text-primary hover:bg-base rounded-md flex items-center gap-2"><i class="fas fa-users"></i> Tipos de Población</a>
                        <a href="/estados_actividad/" class="block px-4 py-2 text-sm text-primary hover:bg-base rounded-md flex items-center gap-2"><i class="fas fa-flag"></i> Estados de Actividad</a>
                    </div>
                </div>
            </div>
            <!-- Aquí puedes agregar el menú de usuario si lo deseas -->
        </div>
    </div>
</nav>