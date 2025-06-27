<?php
// Este archivo debe ser incluido en el header para mostrar el menú de navegación
?>
<nav class="bg-white border-b border-cadet/20 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-14">
            <div class="flex items-center gap-8">
                <?php if (hasRole('admin')): ?>
                    <a href="/index.php" class="text-primary font-bold text-lg flex items-center gap-2 hover:text-secondary transition-colors">
                        <i class="fas fa-home"></i> Dashboard Admin
                    </a>
                <?php elseif (hasRole('financiadora')): ?>
                    <a href="/financiadora_dashboard.php" class="text-primary font-bold text-lg flex items-center gap-2 hover:text-secondary transition-colors">
                        <i class="fas fa-home"></i> Dashboard Financiadora
                    </a>
                <?php elseif (hasRole('coordinador')): ?>
                    <a href="/coordinador_dashboard.php" class="text-primary font-bold text-lg flex items-center gap-2 hover:text-secondary transition-colors">
                        <i class="fas fa-home"></i> Dashboard Coordinador
                    </a>
                <?php elseif (hasRole('usuario')): ?>
                    <a href="/bienvenida_usuario.php" class="text-primary font-bold text-lg flex items-center gap-2 hover:text-secondary transition-colors">
                        <i class="fas fa-home"></i> Bienvenida
                    </a>
                <?php elseif (hasRole('capturista')): ?>
                    <a href="/captura_especial.php" class="text-primary font-bold text-lg flex items-center gap-2 hover:text-secondary transition-colors">
                        <i class="fas fa-keyboard"></i> Captura Especial
                    </a>
                <?php endif; ?>

                <?php if (hasRole('admin') || hasRole('coordinador')): ?>
                <!-- Menú de planificación, gestión y administración solo para admin y coordinador -->
                <div class="relative group">
                    <button class="text-primary hover:text-secondary px-2 py-1 rounded-md text-sm font-medium flex items-center gap-1 focus:outline-none">
                        <i class="fas fa-sitemap"></i> Planificación
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                    <div class="absolute left-0 mt-2 w-56 bg-white rounded-xl shadow-lg py-2 z-50 hidden group-hover:block group-focus-within:block border border-cadet/20">
                        <div class="px-4 py-2 text-xs text-cadet uppercase tracking-wider font-semibold">Jerarquía Estratégica</div>
                        <a href="/ejes/" class="block px-4 py-2 text-sm text-primary hover:bg-base rounded-md flex items-center gap-2"><i class="fas fa-sitemap"></i> Ejes Estratégicos</a>
                        <a href="/componentes/" class="block px-4 py-2 text-sm text-primary hover:bg-base rounded-md flex items-center gap-2"><i class="fas fa-cubes"></i> Componentes</a>
                        <a href="/productos/" class="block px-4 py-2 text-sm text-primary hover:bg-base rounded-md flex items-center gap-2"><i class="fas fa-box"></i> Productos</a>
                        <hr class="my-1">
                        <div class="px-4 py-2 text-xs text-cadet uppercase tracking-wider font-semibold">Operativo</div>
                        <a href="/actividades/" class="block px-4 py-2 text-sm text-primary hover:bg-base rounded-md flex items-center gap-2"><i class="fas fa-tasks"></i> Actividades</a>
                    </div>
                </div>
                <div class="relative group">
                    <button class="text-primary hover:text-secondary px-2 py-1 rounded-md text-sm font-medium flex items-center gap-1 focus:outline-none">
                        <i class="fas fa-users"></i> Gestión
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                    <div class="absolute left-0 mt-2 w-56 bg-white rounded-xl shadow-lg py-2 z-50 hidden group-hover:block group-focus-within:block border border-cadet/20">
                        <div class="px-4 py-2 text-xs text-cadet uppercase tracking-wider font-semibold">Participantes</div>
                        <a href="/beneficiarios/" class="block px-4 py-2 text-sm text-primary hover:bg-base rounded-md flex items-center gap-2"><i class="fas fa-user-friends"></i> Beneficiarios</a>
                        <a href="/organizaciones/" class="block px-4 py-2 text-sm text-primary hover:bg-base rounded-md flex items-center gap-2"><i class="fas fa-building"></i> Organizaciones</a>
                        <hr class="my-1">
                        <div class="px-4 py-2 text-xs text-cadet uppercase tracking-wider font-semibold">Herramientas</div>
                        <a href="/gantt.php" class="block px-4 py-2 text-sm text-primary hover:bg-base rounded-md flex items-center gap-2"><i class="fas fa-chart-gantt"></i> Diagrama Gantt</a>
                    </div>
                </div>
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
                <?php endif; ?>

                <?php if (hasRole('admin')): ?>
                    <div class="relative group">
                        <button class="text-primary hover:text-secondary px-2 py-1 rounded-md text-sm font-medium flex items-center gap-1 focus:outline-none">
                            <i class="fas fa-users-cog"></i> Administración de Usuarios
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="absolute left-0 mt-2 w-56 bg-white rounded-xl shadow-lg py-2 z-50 hidden group-hover:block group-focus-within:block border border-cadet/20">
                            <a href="/usuarios/index.php" class="block px-4 py-2 text-sm text-primary hover:bg-base rounded-md flex items-center gap-2"><i class="fas fa-users"></i> Usuarios</a>
                            <a href="/roles/index.php" class="block px-4 py-2 text-sm text-primary hover:bg-base rounded-md flex items-center gap-2"><i class="fas fa-user-shield"></i> Roles</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Menú de usuario alineado a la derecha -->
            <div class="flex items-center gap-4">
                <?php if (isAuthenticated()): $user = getCurrentUser(); ?>
                    <div class="relative group">
                        <button class="flex items-center gap-3 text-primary font-medium hover:text-secondary focus:outline-none p-2 rounded-lg hover:bg-base transition-colors">
                            <div class="flex-shrink-0">
                                <?php if ($user['foto_perfil']): ?>
                                    <img src="/<?php echo htmlspecialchars($user['foto_perfil']); ?>"
                                        alt="Foto de perfil"
                                        class="w-8 h-8 rounded-full object-cover border-2 border-cadet/30">
                                <?php else: ?>
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary to-accent flex items-center justify-center text-white text-sm font-bold border-2 border-cadet/30">
                                        <?php echo getInitials($user); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="hidden sm:block text-left">
                                <div class="text-sm font-medium"><?php echo htmlspecialchars($user['nombre'] . ' ' . $user['apellido_paterno']); ?></div>
                                <div class="text-xs text-cadet"><?php echo htmlspecialchars($user['email']); ?></div>
                            </div>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg py-2 z-50 hidden group-hover:block group-focus-within:block border border-cadet/20">
                            <div class="px-4 py-2 border-b border-cadet/20">
                                <div class="text-sm font-medium text-darkpurple"><?php echo htmlspecialchars(getFullName($user)); ?></div>
                                <div class="text-xs text-cadet"><?php echo htmlspecialchars($user['email']); ?></div>
                            </div>
                            <a href="/usuarios/perfil.php" class="block px-4 py-2 text-sm text-primary hover:bg-base rounded-md flex items-center gap-2">
                                <i class="fas fa-user"></i> Mi Perfil
                            </a>
                            <a href="/usuarios/update_perfil.php" class="block px-4 py-2 text-sm text-primary hover:bg-base rounded-md flex items-center gap-2">
                                <i class="fas fa-edit"></i> Editar Perfil
                            </a>
                            <hr class="my-1">
                            <a href="/auth/logout.php" class="block px-4 py-2 text-sm text-error hover:bg-base rounded-md flex items-center gap-2">
                                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="/auth/login.php" class="text-primary hover:text-secondary px-3 py-2 rounded-md text-sm font-medium">Iniciar Sesión</a>
                    <a href="/auth/registro.php" class="bg-primary text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 shadow hover:bg-secondary focus:ring-2 focus:ring-accent focus:outline-none text-sm">Registrarse</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>