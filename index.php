<?php
/**
 * index.php
 * 
 * Página principal: formulario de registro de aspirantes.
 *
 * Requerimientos de la rúbrica que cubre este archivo:
 *   - Maqueta con Bootstrap y etiquetas semánticas (<header>, <main>,
 *     <section>, <footer>)
 *   - El formulario va dentro de <main><section>
 *   - Menú y breadcrumb modulares con include (header.php / footer.php)
 *
 * Este archivo NO procesa nada: solo muestra el formulario y envía los
 * datos a procesar.php.
 *
 */

// include: carga includes/header.php (abre <html>, <head>, <body> y pinta el <header>).
// Si el archivo no existe, PHP solo emite una advertencia y sigue.
// (require lo detendría con un error fatal; para una pieza imprescindible
//  como el layout, require sería una alternativa válida. La guía usa include.)
include 'includes/header.php';
?>

<!-- Etiqueta semántica <main>: contenido principal único de la página.
     flex-grow-1 hace que <main> ocupe el espacio libre y el footer quede abajo. -->
<main class="flex-grow-1 py-4">
    <div class="container">

        <!-- <section>: bloque temático dentro del <main>. Aquí va el formulario. -->
        <section class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">

                <h1 class="h3 fw-bold mb-3">Formulario de Registro de Aspirantes</h1>

                <div class="card shadow-sm">
                    <div class="card-body p-4">

                        <!--
                            action="procesar.php"  -> archivo que recibe los datos.
                            method="POST"          -> los datos viajan en el cuerpo de la petición,
                                                      no en la URL (GET los mostraría en la barra de
                                                      direcciones; inadecuado para datos personales).
                            enctype="multipart/form-data" -> OBLIGATORIO para enviar archivos.
                                                      Sin este atributo, la foto NO llega al servidor
                                                      ($_FILES quedaría vacío).
                        -->
                        <form action="procesar.php" method="POST" enctype="multipart/form-data">

                            <!-- Nombre: type="text", required y placeholder (guía, diapositiva 12).
                                 El atributo name="nombre" es la clave que leerás en $_POST['nombre']. -->
                            <div class="mb-3">
                                <label for="nombre" class="form-label fw-bold">Nombre (Requerido):</label>
                                <input type="text" class="form-control" id="nombre" name="nombre"
                                       placeholder="Ej: Sofía" required>
                            </div>

                            <!-- Apellido -->
                            <div class="mb-3">
                                <label for="apellido" class="form-label fw-bold">Apellido (Requerido):</label>
                                <input type="text" class="form-control" id="apellido" name="apellido"
                                       placeholder="Ej: Rodríguez" required>
                            </div>

                            <!-- Identificación: la guía pide type="text" (no "number"), porque
                                 las cédulas panameñas llevan guiones y letras (ej. 8-123-456). -->
                            <div class="mb-3">
                                <label for="identificacion" class="form-label fw-bold">Identificación (Requerido):</label>
                                <input type="text" class="form-control" id="identificacion" name="identificacion"
                                       placeholder="Ej: 8-123-456" required>
                            </div>

                            <!-- Fecha de nacimiento: type="date" (guía).
                                 max = hoy, generado con PHP, para que el selector no deje elegir
                                 fechas futuras. OJO: esto es solo comodidad del cliente; la
                                 validación REAL (edad 18-70) se hace en procesar.php, porque
                                 cualquiera puede saltarse el HTML desde las herramientas del navegador. -->
                            <div class="mb-3">
                                <label for="fecha_nacimiento" class="form-label fw-bold">Fecha de Nacimiento (Requerido):</label>
                                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento"
                                       max="<?php echo date('Y-m-d'); ?>" required>
                            </div>

                            <!-- Sexo: dos radio buttons con aspecto de botón (btn-check de Bootstrap).
                                 Ambos comparten name="sexo" -> solo se puede elegir uno.
                                 "required" en un grupo de radios obliga a elegir una opción. -->
                            <div class="mb-3">
                                <span class="form-label fw-bold d-block">Sexo (Requerido):</span>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="sexo" id="sexo_hombre"
                                               value="Hombre" autocomplete="off" required>
                                        <label class="btn btn-outline-secondary w-100" for="sexo_hombre">Hombre</label>
                                    </div>
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="sexo" id="sexo_mujer"
                                               value="Mujer" autocomplete="off">
                                        <label class="btn btn-outline-secondary w-100" for="sexo_mujer">Mujer</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Fotografía: type="file".
                                 accept= solo FILTRA lo que ofrece el diálogo de selección; no es
                                 seguridad. Un atacante puede enviar cualquier archivo, así que
                                 procesar.php debe volver a validar la extensión (y el tipo real). -->
                            <div class="mb-4">
                                <label for="foto" class="form-label fw-bold">Fotografía del Aspirante (png, jpg, jpeg, gif, webp):</label>
                                <input type="file" class="form-control" id="foto" name="foto"
                                       accept=".png,.jpg,.jpeg,.gif,.webp" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Registrar Aspirante</button>

                        </form>

                    </div>
                </div>

            </div>
        </section>

    </div>
</main>

<?php
// Carga includes/footer.php: pinta el <footer> y CIERRA </body></html>.
include 'includes/footer.php';
?>