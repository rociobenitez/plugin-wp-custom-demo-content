<?php
/**
 * Plugin Name:     Custom Demo Content
 * Plugin URI:      https://github.com/rociobenitez/plugin-wp-custom-demo-content
 * Description:     Crea páginas, menús y entradas de ejemplo para agilizar el contenido inicial. Gestión desde el admin.
 * Version:         1.0.0
 * Author:          Rocío Benítez García
 * Author URI:      https://github.com/rociobenitez
 * Text Domain:     custom-demo-content
 * Domain Path:     /languages
 * Icon:            icon-128x128.png
 * License:         GPLv2 or later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package         CustomDemoContent
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Evita acceso directo
}

// Definir constantes del plugin
define( 'CDC_PLUGIN_VERSION', '1.0.0' );
define( 'CDC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CDC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CDC_TEXTDOMAIN', 'custom-demo-content' );

// Cargar clases
require_once CDC_PLUGIN_DIR . 'includes/class-demo-content-core.php';
require_once CDC_PLUGIN_DIR . 'includes/class-demo-content-admin.php';

/**
 * Instanciamos las clases
 */
function cdc_init_plugin() {
    // Instanciar la clase "Core"
    $core = new Demo_Content_Core();

    // Solo si estamos en admin, instanciar la clase "Admin" y pasarle “core”
    if ( is_admin() ) {
        new Demo_Content_Admin( $core );
    }
}
add_action( 'plugins_loaded', 'cdc_init_plugin' );
