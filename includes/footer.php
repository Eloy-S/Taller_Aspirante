<?php
/**
 * includes/footer.php
 * ---------------------------------------------------------------------
 * Pie de página MODULAR: se incluye al final de index.php y procesar.php.
 *
 * Cumple los 4 elementos que pide la guía para un <footer> completo:
 *   1. Copyright con año dinámico (PHP)
 *   2. Redes sociales y contacto (GitHub, LinkedIn, correo)
 *   3. Enlaces rápidos (opcional)
 *   4. Eslogan institucional (carrera + UTP)
 *
 * IMPORTANTE: este archivo CIERRA </body> y </html>, que fueron abiertos
 * en includes/header.php. No cierres esas etiquetas en otro lugar.
 * ---------------------------------------------------------------------
 */
?>

<!-- mt-auto: junto con "d-flex flex-column min-vh-100" del <body> (en header.php),
     empuja el footer al fondo de la ventana aunque haya poco contenido. -->
<footer class="bg-dark text-white text-center py-4 mt-auto">
    <div class="container">

        <!-- 4. Eslogan institucional: propósito del portal + carrera + UTP.
             Ajusta el nombre de la carrera si el tuyo es distinto. -->
        <p class="mb-1 fw-semibold">
            Portal de Gestión de Aspirantes — Licenciatura en Ciberseguridad — Universidad Tecnológica de Panamá
        </p>

        <!-- 3. Enlaces rápidos (opcional según la guía).
             href="#" es un marcador: reemplázalo cuando existan las páginas reales. -->
        <div class="mb-2">
            <a href="index.php" class="text-white text-decoration-none mx-2 small">Inicio</a> |
            <a href="#" class="text-white text-decoration-none mx-2 small">Política de Privacidad</a> |
            <a href="#" class="text-white text-decoration-none mx-2 small">Términos de Uso</a>
        </div>

        <!-- 2. Redes sociales y contacto.
             target="_blank" abre en otra pestaña; rel="noopener noreferrer" es obligatorio
             de buena práctica: evita que la página destino controle la tuya (window.opener).
             CAMBIA "TU-USUARIO" y el correo por tus datos reales. -->
        <div class="mb-2">
            <a href="https://github.com/Eloy-S" target="_blank" rel="noopener noreferrer"
               class="text-white text-decoration-none mx-2 small">GitHub</a> |
            <a href="https://www.linkedin.com/in/" target="_blank" rel="noopener noreferrer"
               class="text-white text-decoration-none mx-2 small">LinkedIn</a> |
            <a href="mailto:correo@utp.ac.pa" class="text-white text-decoration-none mx-2 small">Contacto</a>
        </div>

        <!-- 1. Copyright con año dinámico.
             date('Y') devuelve el año actual del servidor (ej. 2026), así que
             nunca queda desactualizado. &copy; es la entidad HTML del símbolo ©.
             text-opacity-75 atenúa el blanco (equivale al antiguo text-white-50). -->
        <p class="text-white text-opacity-75 small mb-0">
            &copy; <?php echo date('Y'); ?> Universidad Tecnológica de Panamá. Todos los derechos reservados.
        </p>

    </div>
</footer>

<!-- Cierre de las etiquetas abiertas en includes/header.php -->
</body>
</html>