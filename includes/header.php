<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . APP_NAME : APP_NAME; ?></title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Configuración personalizada de Tailwind -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        xanthous: {
                            DEFAULT: '#FFBA49',
                        },
                        seagreen: {
                            DEFAULT: '#20A39E',
                        },
                        bittersweet: {
                            DEFAULT: '#EF5B5B',
                        },
                        darkpurple: {
                            DEFAULT: '#23001E',
                        },
                        cadet: {
                            DEFAULT: '#A4A9AD',
                        },
                        primary: {
                            DEFAULT: '#23001E', // Dark purple
                        },
                        secondary: {
                            DEFAULT: '#20A39E', // Light sea green
                        },
                        accent: {
                            DEFAULT: '#FFBA49', // Xanthous
                        },
                        error: {
                            DEFAULT: '#EF5B5B', // Bittersweet
                        },
                        base: {
                            DEFAULT: '#A4A9AD', // Cadet gray
                        }
                    }
                }
            }
        }
    </script>

    <!-- Estilos personalizados -->
    <style>
        .btn-primary {
            @apply bg-primary text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 shadow hover:bg-secondary focus:ring-2 focus:ring-accent focus:outline-none;
        }

        .btn-secondary {
            @apply bg-secondary text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 shadow hover:bg-primary focus:ring-2 focus:ring-accent focus:outline-none;
        }

        .btn-accent {
            @apply bg-accent text-darkpurple font-medium py-2 px-4 rounded-lg transition-colors duration-200 shadow hover:bg-xanthous/80 focus:ring-2 focus:ring-primary focus:outline-none;
        }

        .form-input {
            @apply w-full px-4 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-white placeholder-cadet text-darkpurple shadow-sm;
        }

        .form-label {
            @apply block text-sm font-semibold text-darkpurple mb-1;
        }

        .card {
            @apply bg-white rounded-2xl shadow-2xl p-8 border border-cadet/40;
        }
    </style>
</head>

<body class="bg-base min-h-screen flex flex-col">
    <!-- Mensajes flash -->
    <?php
    $flash = getFlashMessage();
    if ($flash):
    ?>
        <div class="fixed top-4 right-4 z-50">
            <div class="<?php echo $flash['type'] === 'success' ? 'bg-secondary' : 'bg-error'; ?> text-white px-6 py-3 rounded-lg shadow-lg font-semibold">
                <?php echo htmlspecialchars($flash['message']); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Header principal -->
    <header class="bg-white shadow-sm border-b border-cadet/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo y título -->
                <div class="flex items-center">
                    <a href="/" class="flex items-center space-x-3">
                        <i class="fas fa-users text-primary text-2xl"></i>
                        <span class="text-xl font-bold text-primary tracking-wide"><?php echo APP_NAME; ?></span>
                    </a>
                </div>

                <!-- Navegación -->
                <?php if (isAuthenticated()): ?>
                    <nav class="hidden md:flex space-x-8">
                        <a href="/actividades/" class="text-primary hover:text-secondary px-3 py-2 rounded-md text-sm font-medium">Actividades</a>
                        <a href="/beneficiarios/" class="text-primary hover:text-secondary px-3 py-2 rounded-md text-sm font-medium">Beneficiarios</a>
                        <a href="/organizaciones/" class="text-primary hover:text-secondary px-3 py-2 rounded-md text-sm font-medium">Organizaciones</a>
                        <a href="/gantt.php" class="text-primary hover:text-secondary px-3 py-2 rounded-md text-sm font-medium">Gantt</a>
                    </nav>

                    <!-- Menú de usuario -->
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <button class="flex items-center space-x-2 text-primary hover:text-secondary">
                                <i class="fas fa-user-circle text-xl"></i>
                                <span class="text-sm font-medium">
                                    <?php
                                    $user = getCurrentUser();
                                    echo htmlspecialchars($user['nombre'] . ' ' . $user['apellido_paterno']);
                                    ?>
                                </span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden">
                                <a href="/usuarios/perfil.php" class="block px-4 py-2 text-sm text-primary hover:bg-base">
                                    <i class="fas fa-user mr-2"></i>Mi Perfil
                                </a>
                                <hr class="my-1">
                                <a href="/auth/logout.php" class="block px-4 py-2 text-sm text-error hover:bg-base">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Sesión
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="flex items-center space-x-4">
                        <a href="/auth/login.php" class="text-primary hover:text-secondary px-3 py-2 rounded-md text-sm font-medium">Iniciar Sesión</a>
                        <a href="/auth/registro.php" class="btn-primary text-sm">Registrarse</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Contenido principal -->
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col justify-center">
</body>

</html>