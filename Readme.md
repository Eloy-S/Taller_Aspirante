# Taller Aspirantes — Registro de Aspirantes (Laboratorio #3)

Formulario web de registro de aspirantes desarrollado con **PHP, HTML5 y Bootstrap 5.3.8**
para el curso de Desarrollo Web de la Universidad Tecnológica de Panamá (UTP).

## Funcionalidades

- Formulario con nombre, apellido, identificación, fecha de nacimiento, sexo y fotografía.
- Validación en el servidor (`procesar.php`):
  - Campos obligatorios y saneamiento de texto (`trim`, `strip_tags`).
  - Nombre y apellido normalizados a formato título; identificación en mayúsculas.
  - Cálculo de la edad a partir de la fecha de nacimiento; solo se aceptan **18 a 70 años**.
  - Fotografía: extensiones `jpg`, `jpeg`, `png`, `gif`, `webp`, máximo 2 MB, y comprobación del tipo real del archivo.
- Fotografías guardadas en `uploaded_files/` con nombre aleatorio, **sin base de datos**.
- Cabecera (menú y breadcrumb dinámico) y pie de página modulares con `include`.

## Estructura del proyecto

```text
Taller-Aspirantes/
├── includes/
│   ├── header.php        # <header>, navbar y breadcrumb dinámico
│   └── footer.php        # <footer> con enlaces y año dinámico
├── uploaded_files/
│   ├── .gitkeep          # conserva la carpeta vacía en Git
│   └── .htaccess         # bloquea el acceso desde el navegador
├── index.php             # formulario de registro
├── procesar.php          # validación, procesamiento y guardado de la foto
└── README.md
```

## Requisitos

- Apache 2.4 con PHP 8.x (probado con WAMP: Apache 2.4 y PHP 8.3).
- Extensiones de PHP `mbstring` y `fileinfo` activas (vienen activas por defecto en WAMP).
- Conexión a internet (Bootstrap se carga desde CDN).

## Instalación y ejecución (WAMP)

1. Clona el repositorio dentro de la carpeta `www` de WAMP:
   ```bash
   cd C:\wamp64\www
   git clone https://github.com/TU-USUARIO/NOMBRE-DEL-REPOSITORIO.git Taller-Aspirantes
   ```
2. Inicia WAMP y verifica que Apache esté en verde.
3. Abre `http://localhost/Taller-Aspirantes/index.php` en el navegador.

## Seguridad aplicada

- Salida escapada con `htmlspecialchars()` para prevenir XSS.
- Nombre de archivo generado por el servidor (no se usa el nombre original).
- Extensión validada contra lista blanca y tipo MIME real detectado con `finfo`.
- `uploaded_files/` bloqueada desde el navegador mediante `.htaccess` (`Require all denied`).
- Las fotos subidas están excluidas del repositorio mediante `.gitignore`.

## Capturas



## Autor

- **Nombre:** Eloy Samaniego
- **Carrera:** Licenciatura en Ciberseguridad — Universidad Tecnológica de Panamá
- **Curso:** Desarrollo Web — Laboratorio #3