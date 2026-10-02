<?php


// basename() recorta la ruta y deja solo el nombre del archivo.
// $_SERVER['PHP_SELF'] = "/Taller-Aspirantes/procesar.php"  ->  "procesar.php"
// Lo usamos solo para COMPARAR, nunca para imprimirlo en pantalla.
// (Si algún día se imprime, hay que pasarlo por htmlspecialchars():
//  PHP_SELF puede contener texto manipulado desde la URL -> riesgo XSS.)
$paginaActual = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <!--  METADATOS (guía: "Agregar los Metadatos")  -->

    <!-- Codificación: evita problemas con tildes y la ñ -->
    <meta charset="UTF-8">

    <!-- Viewport: initial-scale=1.0 = zoom inicial del 100% en móviles.
         Sin esta etiqueta, el diseño responsive de Bootstrap no se adapta. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sistema de Admisión de la UTP</title>

    <meta name="description" content="Sistema de admisión de datos para aspirantes">

    <!-- Quién creó el sitio.3 -->
    <meta name="author" content="Universidad Tecnológica de Panamá / Eloy Samaniego">

    <!-- robots: evita que los buscadores indexen esta página de pruebas/formulario -->
    <meta name="robots" content="noindex, nofollow">

    <!-- theme-color: color de la barra del navegador móvil (mismo tono que bg-dark) -->
    <meta name="theme-color" content="#212529">

    <!-- Bootstrap 5.3.8 por CDN (versión indicada en la guía) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<!-- Clases del <body> (trabajan en conjunto con el footer):
     d-flex flex-column min-vh-100 -> el body ocupa al menos el 100% del alto de la ventana
     y apila sus hijos en columna. Con "mt-auto" en el <footer>, este queda pegado
     abajo aunque la página tenga poco contenido. -->
<body class="bg-light d-flex flex-column min-vh-100">

    <!-- Etiqueta semántica <header>: agrupa la navegación del sitio -->
    <header>

        <!-- Navbar principal -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
            <div class="container">
                <a class="navbar-brand fw-bold" href="index.php">PortalU</a>
            </div>
        </nav>

        <!-- Breadcrumb dinámico (migas de pan) -->
        <div class="bg-white border-bottom py-2">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 small">

                        <!-- Siempre aparece "Inicio", con enlace -->
                        <li class="breadcrumb-item">
                            <a href="index.php" class="text-decoration-none">Inicio</a>
                        </li>

                        <?php if ($paginaActual == 'procesar.php'): ?>
                            <!-- Estamos en procesar.php: paso intermedio con enlace de regreso -->
                            <li class="breadcrumb-item">
                                <a href="index.php" class="text-decoration-none">Registro</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Procesando Datos</li>
                        <?php else: ?>
                            <!-- Cualquier otra página (index.php): última miga, sin enlace -->
                            <li class="breadcrumb-item active" aria-current="page">Registro de Aspirante</li>
                        <?php endif; ?>

                    </ol>
                </nav>
            </div>
        </div>

    </header>
    <!-- Aquí termina el header. Cada página continúa con su <main> -->