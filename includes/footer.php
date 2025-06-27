    </main>

    <!-- Footer -->
    <?php
    $no_nav_pages = ['/auth/login.php', '/auth/registro.php'];
    $current_page = $_SERVER['PHP_SELF'];
    if (isAuthenticated() && !in_array($current_page, $no_nav_pages)):
    ?>
    <footer class="bg-white border-t mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="text-center text-gray-600">
                <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>
    <?php endif; ?>

    <!-- JavaScript para funcionalidad -->
    <script>
        // Menú desplegable del usuario
        document.addEventListener('DOMContentLoaded', function() {
            const userMenuButton = document.querySelector('button');
            const userMenu = document.querySelector('.absolute');

            if (userMenuButton && userMenu) {
                userMenuButton.addEventListener('click', function() {
                    userMenu.classList.toggle('hidden');
                });

                // Cerrar menú al hacer clic fuera
                document.addEventListener('click', function(event) {
                    if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
                        userMenu.classList.add('hidden');
                    }
                });
            }

            // Auto-hide flash messages
            const flashMessage = document.querySelector('.fixed');
            if (flashMessage) {
                setTimeout(() => {
                    flashMessage.style.opacity = '0';
                    setTimeout(() => {
                        flashMessage.remove();
                    }, 300);
                }, 5000);
            }
        });
    </script>
    </body>

    </html>