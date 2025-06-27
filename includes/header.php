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
</head>

<body class="bg-base min-h-screen flex flex-col">
    <!-- Fondo Cadet gray -->
    <?php
    $no_nav_pages = ['/auth/login.php', '/auth/registro.php'];
    $current_page = $_SERVER['PHP_SELF'];
    if (isAuthenticated() && !in_array($current_page, $no_nav_pages)) {
        include __DIR__ . '/nav.php';
    }
    ?>
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

    <!-- Contenido principal -->
    <main class="flex-1 w-[90vw] mx-auto py-8 flex flex-col justify-center">
</body>

</html>