=== Custom Demo Content ===
Contributors:  Rocío Benítez García
Tags: demo, contenido, páginas, entradas, menú, ejemplo, generador, AJAX
Requires at least: 6.0
Tested up to: 6.4
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Crea páginas, menús y entradas de ejemplo de forma automática para agilizar el contenido inicial en tu sitio WordPress. 
Incluye una interfaz gráfica en el backend (Herramientas → Demo Content) para generar, reiniciar o eliminar el contenido demo con un solo clic.

== Description ==
Custom Demo Content es un plugin ligero diseñado para desarrolladores y agencias que necesitan 
poblar un sitio WordPress con contenido de ejemplo en cuestión de segundos. Permite:

* **Páginas de Ejemplo**:  
  - Listado editable de páginas predefinidas (Aviso Legal, Política de Privacidad, Política de Cookies, Home, Contacto, Quiénes Somos, Blog).  
  - Puedes modificar el **título** y el **slug** de cada página antes de crearlas.  
  - El plugin detecta si ya existe la página (columna “Existe: Sí/No”) y, si no existe, la crea con plantilla asignada automáticamente (`page-home.php`, `page-contact.php`, etc.).  
  - Reinicia la bandera interna para volver a regenerar las páginas si es necesario.

* **Entradas de Ejemplo**:  
  - Campo numérico “Cantidad de entradas a generar” (valor mínimo 1, máximo 50).  
  - Genera automáticamente “Entrada de prueba 1”, “Entrada de prueba 2”, … hasta el número que indiques, asignando por defecto imagen destacada si existe en `/assets/img/default-blog-image.webp`.  
  - Verifica si ya existe cada entrada por slug para no duplicar.  
  - Incluye botones para “Crear entradas”, “Reiniciar creación de entradas” (para permitir recrear) y **“Eliminar todas las entradas demo”** (borra permanentemente todas las entradas generadas por el plugin).

* **Menús de Demo**:  
  - Crea un “Menú Principal (main)” con las páginas Home, Quiénes Somos, Blog y Contacto (si existen).  
  - Crea un “Menú Legales (legal)” con las páginas Aviso Legal, Política de Privacidad y Política de Cookies (si existen).  
  - Asigna automáticamente las ubicaciones `main` y `legal` para que aparezcan en el tema.  
  - Incluye un botón “Crear Menús de Demo” y “Reiniciar Menús de Demo” para regenerarlos o actualizar su estado.

* **Interfaz AJAX mejorada**:  
  - Toda la gestión (creación, eliminación, reinicio) se realiza mediante AJAX, sin recargar la página.  
  - Mensajes de confirmación personalizados y notificaciones de éxito/error.

* **Opción de reinicio**:  
  - Cada sección tiene un botón “Reiniciar” que borra la bandera interna y permite volver a generar contenido sin eliminar manualmente nada de la base de datos.

**Nota**: Si desactivas o desinstalas el plugin, las páginas, entradas y menús creados se conservan hasta que los elimines manualmente (o vuelvas a registrarlos con otro plugin). En futuras versiones se añadirá la opción para eliminar también las páginas de demo.

== Installation ==
1. Sube la carpeta `custom-demo-content` al directorio `/wp-content/plugins/` de tu instalación de WordPress, 
   o instálalo directamente desde el panel **Plugins → Añadir Nuevo → Subir plugin**.
2. Activa el plugin desde el menú **Plugins**.
3. Ve a **Herramientas → Demo Content** para acceder a la interfaz de generación de contenido de demo.

== Usage ==
=== Crear páginas de demo ===
1. Accede a **Herramientas → Demo Content**.  
2. En la sección **Páginas de Ejemplo**, verás una tabla con cada página base. Puedes editar manualmente el **Título** y el **Slug** en sus respectivas columnas.  
3. Pulsa **Crear todas las páginas de ejemplo** para generarlas. El plugin detectará si ya existen y solo creará las que falten.  
4. Si en el futuro quieres volver a generarlas (por ejemplo, cambiaste un slug o plantilla), pulsa **Reiniciar creación de páginas** para borrar la bandera interna y luego vuelve a “Crear todas las páginas…”.

=== Crear entradas de demo ===
1. En la misma pantalla, ve a **Entradas de Ejemplo**.  
2. Ajusta el campo **Cantidad de entradas a generar** (por defecto 3, mínimo 1, máximo 50).  
3. Pulsa **Crear entradas de ejemplo**. El plugin generará “Entrada de prueba 1”, … “Entrada de prueba N” con imagen destacada por defecto.  
4. Si las publicaciones ya existen (por slug), no se duplican.  
5. Para recrear nuevas (por ejemplo, cambias el número a 5 y quieres que se creen las entradas 4 y 5), primero pulsa **Reiniciar creación de entradas** y luego **Crear entradas de ejemplo**.  
6. Para eliminar por completo todas las entradas generadas por el plugin, pulsa **Eliminar todas las entradas demo**.

=== Crear menús de demo ===
1. Desplázate a **Menús de Demo**. Verás si el “Menú Principal (main)” y el “Menú Legales (legal)” existen actualmente.  
2. Pulsa **Crear Menús de Demo** para generarlos (solo se añaden si no existen).  
3. Para volver a generarlos o actualizarlos (por ejemplo, si agregaste páginas nuevas), pulsa **Reiniciar Menús de Demo** para borrar la bandera interna y luego **Crear Menús de Demo** de nuevo.

== Frequently Asked Questions ==
= ¿Puedo crear más de 3 entradas de demo? =
Sí. Hay un campo numérico “Cantidad de entradas a generar” que te permite indicar hasta 50. El plugin generará ese número de publicaciones tituladas “Entrada de prueba 1”, … “Entrada de prueba N”.

= ¿Cómo editar título y slug de las páginas antes de crearlas? =
En la sección **Páginas de Ejemplo**, cada fila tiene dos campos de texto (Título y Slug) prellenados con valores predeterminados. Modifica informalmente antes de pulsar el botón **Crear todas las páginas de ejemplo**.

= ¿Cómo borro las entradas de demo? =
Pulsa **Eliminar todas las entradas demo** en la sección **Entradas de Ejemplo**. Esto eliminará permanentemente todas las publicaciones generadas por el plugin y reseteará la bandera interna para permitir volver a crearlas.

= ¿Cómo borro las páginas de demo? =
Actualmente el plugin no incluye un botón específico para eliminar las páginas de demo de forma masiva. Puedes eliminarlas manualmente desde **Páginas → Todas las páginas**, o bien **Reiniciar creación de páginas** y luego volver a generar según necesites.

= ¿Puedo usar el plugin con cualquier tema? =
Sí. El plugin no impone estilos frontend ni plantillas propias. Asegúrate de que tu tema contenga los archivos `page-home.php`, `page-contact.php`, `page-about.php` y `page-blog.php` para que las páginas de demo usen su plantilla correcta; de lo contrario, se usará `page.php`.

== Changelog ==
= 1.0.0 =
* Versión inicial.  
* Añade creación de Páginas de Demo (Aviso Legal, Política de Privacidad, Política de Cookies, Home, Contacto, Quiénes Somos, Blog) con títulos y slugs editables.  
* Añade creación dinámica de Entradas de Demo (“Entrada de prueba 1” … “Entrada de prueba N”), con opción de indicar cuántas generar (hasta 50).  
* Botón “Eliminar todas las entradas demo” para borrarlas permanentemente.  
* Añade creación de Menús de Demo (“main” y “legal”).  
* Interfaz AJAX en “Herramientas → Demo Content”.  
* Opción de “Reiniciar” para regenerar páginas, entradas o menús.  
* Traducciones básicas con textdomain `custom-demo-content`.  
* Control de no duplicar contenido si ya existe slug/título.

== Upgrade Notice ==
= 1.0.0 =
Primera versión pública.

== Installation Notes ==
1. El plugin requiere PHP 7.4+ y WordPress 6.0+.  
2. Para traducciones, crea el archivo `languages/custom-demo-content-es_ES.mo`.

== Screenshots ==
1. Pantalla “Herramientas → Demo Content” mostrando las secciones de Páginas, Entradas y Menús.  
2. Inputs de Título y Slug en Páginas de Ejemplo.  
3. Input numérico para Cantidad de Entradas a Generar.  
4. Botón “Eliminar todas las entradas demo”.  
5. Mensajes AJAX de éxito/ error.

== License ==
GPL v2 or later
