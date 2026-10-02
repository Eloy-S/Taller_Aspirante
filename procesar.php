<?php
/**
 * procesar.php
 * ---------------------------------------------------------------------
 * Backend del formulario de index.php. Hace, en este orden:
 *   1. Acepta solo peticiones POST
 *   2. Sanea y normaliza los textos (trim, strip_tags, formato título)
 *   3. Valida campos requeridos, fecha y edad (18 a 70 años)
 *   4. Valida la foto (error de subida, tamaño, extensión y tipo REAL)
 *   5. Guarda la foto en ./uploaded_files/ con nombre aleatorio
 *   6. Muestra el resultado (o la lista de errores)
 *
 * Patrón usado: TODA la lógica va arriba, ANTES de imprimir HTML.
 * Abajo solo se "pinta" el resultado. Así podemos usar header()/exit
 * sin el error "headers already sent".
 * ---------------------------------------------------------------------
 */

// Zona horaria: la edad se calcula contra la fecha "de hoy" del servidor.
// Sin esto, PHP usa la que tenga php.ini y la edad podría fallar cerca de medianoche.
date_default_timezone_set('America/Panama');

// ------------------------------------------------------------------
// 1. Solo POST. Si alguien abre procesar.php escribiendo la URL (GET),
//    lo devolvemos al formulario. Esto va ANTES del include del header.
// ------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit; // Obligatorio: sin exit el script seguiría ejecutándose.
}

// ------------------------------------------------------------------
// Funciones auxiliares
// ------------------------------------------------------------------

/**
 * Limpia un texto recibido del formulario.
 *  - trim(): quita espacios/tabs/saltos de línea al inicio y al final
 *  - strip_tags(): elimina etiquetas HTML/PHP (queremos texto plano)
 *  - preg_replace: colapsa espacios internos repetidos ("Ana   María" -> "Ana María")
 * Si el campo no llegó, devuelve cadena vacía (evita "undefined index").
 */
function limpiar($valor)
{
    if (!is_string($valor)) {
        return '';
    }
    $valor = trim(strip_tags($valor));
    return preg_replace('/\s+/u', ' ', $valor);
}

/**
 * Escapa un texto para IMPRIMIRLO en HTML (previene XSS).
 * htmlspecialchars() convierte < > & ' " en entidades seguras.
 *
 * Por qué se aplica AL IMPRIMIR y no antes de normalizar:
 * si escapáramos primero, "O'Neil" se volvería "O&#039;Neil" y el
 * formato título trataría "039" y "Neil" como palabras sueltas,
 * alterando el texto guardado. Además validaríamos texto ya modificado.
 * Regla práctica: limpiar al entrar, escapar al salir.
 */
function e($valor)
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

// ------------------------------------------------------------------
// 2. Recoger y normalizar los datos
// ------------------------------------------------------------------
$errores = [];

// Formato título (guía: ucwords(strtolower())).
// Usamos la versión multibyte porque strtolower/ucwords NO manejan bien
// las tildes en UTF-8: "JOSÉ" quedaría "JosÉ". mb_convert_case con
// MB_CASE_TITLE hace las dos cosas (minúsculas + primera letra mayúscula)
// respetando acentos y la ñ. Requiere la extensión mbstring (activa por defecto en WAMP).
$nombre   = mb_convert_case(limpiar(isset($_POST['nombre'])   ? $_POST['nombre']   : ''), MB_CASE_TITLE, 'UTF-8');
$apellido = mb_convert_case(limpiar(isset($_POST['apellido']) ? $_POST['apellido'] : ''), MB_CASE_TITLE, 'UTF-8');

// Identificación en mayúsculas (guía: strtoupper). Versión multibyte por consistencia.
$identificacion = mb_strtoupper(limpiar(isset($_POST['identificacion']) ? $_POST['identificacion'] : ''), 'UTF-8');

$fechaRaw = limpiar(isset($_POST['fecha_nacimiento']) ? $_POST['fecha_nacimiento'] : '');
$sexo     = limpiar(isset($_POST['sexo']) ? $_POST['sexo'] : '');

// ------------------------------------------------------------------
// 3. Validaciones de los campos de texto
// ------------------------------------------------------------------

// Nombre y apellido: requeridos + solo letras, espacios, guion y apóstrofo + largo máximo.
// \p{L} = cualquier letra Unicode (incluye tildes y ñ). La "u" final activa el modo UTF-8.
$patronNombre = '/^[\p{L}][\p{L}\s\'-]*$/u';

if ($nombre === '') {
    $errores[] = 'El nombre es obligatorio.';
} elseif (mb_strlen($nombre, 'UTF-8') > 50 || !preg_match($patronNombre, $nombre)) {
    $errores[] = 'El nombre solo puede contener letras, espacios, guiones o apóstrofos (máx. 50 caracteres).';
}

if ($apellido === '') {
    $errores[] = 'El apellido es obligatorio.';
} elseif (mb_strlen($apellido, 'UTF-8') > 50 || !preg_match($patronNombre, $apellido)) {
    $errores[] = 'El apellido solo puede contener letras, espacios, guiones o apóstrofos (máx. 50 caracteres).';
}

// Identificación: letras, números y guiones (ej. 8-123-456, PE-12-345, E-8-12345).
if ($identificacion === '') {
    $errores[] = 'La identificación es obligatoria.';
} elseif (!preg_match('/^[A-Z0-9-]{5,20}$/', $identificacion)) {
    $errores[] = 'La identificación solo puede contener letras, números y guiones (5 a 20 caracteres).';
}

// Sexo: lista blanca. Aunque el HTML solo ofrezca dos radios, alguien puede
// enviar cualquier valor modificando la petición; nunca confiar en el cliente.
if (!in_array($sexo, ['Hombre', 'Mujer'], true)) {
    $errores[] = 'Debes seleccionar el sexo (Hombre o Mujer).';
}

// ------------------------------------------------------------------
// 3b. Fecha de nacimiento y cálculo de edad (rango 18 a 70)
// ------------------------------------------------------------------
$edad = null;

if ($fechaRaw === '') {
    $errores[] = 'La fecha de nacimiento es obligatoria.';
} else {
    // createFromFormat devuelve false si el texto no cumple el formato Y-m-d.
    // Además comparamos con format(): "2024-02-31" se "corrige" a marzo sin dar error,
    // y esta comparación detecta ese caso (fecha inexistente).
    $nacimiento = DateTime::createFromFormat('Y-m-d', $fechaRaw);

    if ($nacimiento === false || $nacimiento->format('Y-m-d') !== $fechaRaw) {
        $errores[] = 'La fecha de nacimiento no es válida.';
    } else {
        $hoy = new DateTime('today');

        if ($nacimiento > $hoy) {
            $errores[] = 'La fecha de nacimiento no puede ser futura.';
        } else {
            // diff() devuelve un DateInterval; ->y son los AÑOS CUMPLIDOS
            // (tiene en cuenta mes y día, no solo restar años).
            $edad = $nacimiento->diff($hoy)->y;

            if ($edad < 18 || $edad > 70) {
                $errores[] = 'La edad debe estar entre 18 y 70 años (edad calculada: ' . $edad . ').';
            }
        }
    }
}

// ------------------------------------------------------------------
// 4. Validación de la foto
//    $_FILES['foto'] trae: name, type, tmp_name, error, size.
//    OJO: 'name' y 'type' los escribe el CLIENTE -> no son confiables.
// ------------------------------------------------------------------
$extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

// Relación extensión -> tipo MIME real esperado.
$mimePorExtension = [
    'jpg'  => ['image/jpeg'],
    'jpeg' => ['image/jpeg'],
    'png'  => ['image/png'],
    'gif'  => ['image/gif'],
    'webp' => ['image/webp'],
];

$tamanoMaximo = 2 * 1024 * 1024; // 2 MB (también limitado por upload_max_filesize en php.ini)
$extension    = '';
$foto         = isset($_FILES['foto']) ? $_FILES['foto'] : null;

if ($foto === null || $foto['error'] === UPLOAD_ERR_NO_FILE) {
    $errores[] = 'Debes seleccionar una fotografía.';
} elseif ($foto['error'] === UPLOAD_ERR_INI_SIZE || $foto['error'] === UPLOAD_ERR_FORM_SIZE) {
    $errores[] = 'La fotografía excede el tamaño máximo permitido.';
} elseif ($foto['error'] !== UPLOAD_ERR_OK) {
    $errores[] = 'Ocurrió un error al subir la fotografía (código ' . (int) $foto['error'] . ').';
} else {
    // Tamaño
    if ($foto['size'] > $tamanoMaximo) {
        $errores[] = 'La fotografía no puede pesar más de 2 MB.';
    }

    // Extensión (pathinfo + minúsculas, para aceptar "FOTO.JPG")
    $extension = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $extensionesPermitidas, true)) {
        $errores[] = 'Formato de imagen no permitido. Usa: ' . implode(', ', $extensionesPermitidas) . '.';
    } else {
        // Tipo REAL: finfo lee los primeros bytes del archivo (firma / "magic bytes")
        // y deduce qué es en realidad, sin fiarse del nombre.
        // Un "virus.php" renombrado a "virus.jpg" se detecta aquí (text/x-php, text/plain...).
        // finfo requiere la extensión php_fileinfo (activa por defecto en WAMP).
        $finfo    = new finfo(FILEINFO_MIME_TYPE);
        $mimeReal = $finfo->file($foto['tmp_name']);

        if (!in_array($mimeReal, $mimePorExtension[$extension], true)) {
            $errores[] = 'El archivo no es una imagen válida (el contenido no coincide con la extensión).';
        }
    }

    // Comprobación extra: el archivo realmente vino de una subida HTTP POST.
    if (!is_uploaded_file($foto['tmp_name'])) {
        $errores[] = 'El archivo recibido no es una subida válida.';
    }
}

// ------------------------------------------------------------------
// 5. Guardar la foto (SOLO si no hay ningún error previo)
// ------------------------------------------------------------------
$nombreArchivo = '';

if (empty($errores)) {
    $directorio = __DIR__ . '/uploaded_files/'; // __DIR__ = carpeta de este archivo

    if (!is_dir($directorio) || !is_writable($directorio)) {
        $errores[] = 'No se pudo guardar la fotografía: la carpeta de destino no existe o no tiene permisos de escritura.';
    } else {
        // NUNCA usar el nombre original del usuario: podría contener "../" (path traversal),
        // caracteres raros o sobrescribir fotos de otros aspirantes.
        // random_bytes -> 16 caracteres hexadecimales aleatorios e impredecibles.
        // La extensión sale de nuestra lista blanca, no del usuario.
        $nombreArchivo = bin2hex(random_bytes(8)) . '.' . $extension;

        // move_uploaded_file: mueve el archivo desde la carpeta temporal de PHP al destino.
        if (!move_uploaded_file($foto['tmp_name'], $directorio . $nombreArchivo)) {
            $errores[] = 'No se pudo guardar la fotografía en el servidor.';
            $nombreArchivo = '';
        }
    }
}

// ------------------------------------------------------------------
// 6. Salida HTML (el header abre <html>, el footer lo cierra)
// ------------------------------------------------------------------
include 'includes/header.php';
?>

<main class="flex-grow-1 py-4">
    <div class="container">
        <section class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">

                <?php if (!empty($errores)): ?>

                    <!-- Caso con errores: lista de problemas + botón para regresar -->
                    <div class="alert alert-danger" role="alert">
                        <h1 class="h5 alert-heading">No se pudo completar el registro</h1>
                        <ul class="mb-0">
                            <?php foreach ($errores as $error): ?>
                                <!-- e(): aunque los mensajes los escribimos nosotros, escapar
                                     siempre es un buen hábito (algunos incluyen datos calculados). -->
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <a href="index.php" class="btn btn-secondary">Volver al formulario</a>

                <?php else: ?>

                    <!-- Caso exitoso: resumen de los datos ya normalizados -->
                    <div class="alert alert-success" role="alert">
                        <h1 class="h5 alert-heading mb-0">Aspirante registrado correctamente</h1>
                    </div>

                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr><th scope="row">Nombre</th><td><?php echo e($nombre); ?></td></tr>
                                    <tr><th scope="row">Apellido</th><td><?php echo e($apellido); ?></td></tr>
                                    <tr><th scope="row">Identificación</th><td><?php echo e($identificacion); ?></td></tr>
                                    <tr><th scope="row">Fecha de nacimiento</th><td><?php echo e($fechaRaw); ?></td></tr>
                                    <tr><th scope="row">Edad</th><td><?php echo e($edad); ?> años</td></tr>
                                    <tr><th scope="row">Sexo</th><td><?php echo e($sexo); ?></td></tr>
                                    <tr><th scope="row">Fotografía guardada como</th><td><?php echo e($nombreArchivo); ?></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <a href="index.php" class="btn btn-primary">Registrar otro aspirante</a>

                <?php endif; ?>

            </div>
        </section>
    </div>
</main>

<?php include 'includes/footer.php'; ?>