<?php
/**
 * Clase que añade la interfaz de administración para el Demo Content.
 *
 * @package CustomDemoContent
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Demo_Content_Admin {

    /**
     * Instancia de Demo_Content_Core
     *
     * @var Demo_Content_Core
     */
    private $core;

    /**
     * Slug de la página de opciones
     *
     * @var string
     */
    private $menu_slug = 'cdc-demo-content';

    public function __construct( $core_instance ) {
        $this->core = $core_instance;

        // Añadir menú bajo “Herramientas”
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );

        // Encolar estilos y scripts en nuestra página
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

        // Registrar acciones AJAX y asociarlas a métodos de esta clase
        add_action( 'wp_ajax_cdc_create_pages',          array( $this, 'ajax_create_pages' ) );
        add_action( 'wp_ajax_cdc_create_all_posts',      array( $this, 'ajax_create_all_posts' ) );
        add_action( 'wp_ajax_cdc_delete_all_posts',      array( $this, 'ajax_delete_all_posts' ) );
        add_action( 'wp_ajax_cdc_create_demo_menus',     array( $this, 'ajax_create_demo_menus' ) );
        add_action( 'wp_ajax_cdc_reset_pages_flag',      array( $this, 'ajax_reset_pages_flag' ) );
        add_action( 'wp_ajax_cdc_reset_posts_flag',      array( $this, 'ajax_reset_posts_flag' ) );
        add_action( 'wp_ajax_cdc_reset_demo_menus_flag', array( $this, 'ajax_reset_demo_menus_flag' ) );
    }

    /**
     * Añade el submenú “Demo Content” bajo “Herramientas”.
     */
    public function add_admin_menu() {
        add_management_page(
            esc_html__( 'Demo Content', CDC_TEXTDOMAIN ),
            esc_html__( 'Demo Content', CDC_TEXTDOMAIN ),
            'manage_options',
            $this->menu_slug,
            array( $this, 'render_admin_page' )
        );
    }

    /**
     * Encolar CSS/JS en la página de Demo Content.
     *
     * @param string $hook_suffix Sufijo del hook (por ejemplo, tools_page_cdc-demo-content).
     */
    public function enqueue_admin_assets( $hook_suffix ) {
        // Solo en nuestra página
        if ( 'tools_page_' . $this->menu_slug !== $hook_suffix ) {
            return;
        }

        // Estilos
        wp_enqueue_style(
            'cdc-admin-styles',
            CDC_PLUGIN_URL . 'assets/css/admin-demo-content.css',
            array(),
            CDC_PLUGIN_VERSION
        );

        // Scripts (para AJAX y comportamientos en la UI)
        wp_enqueue_script(
            'cdc-admin-scripts',
            CDC_PLUGIN_URL . 'assets/js/admin-demo-content.js',
            array( 'jquery' ),
            CDC_PLUGIN_VERSION,
            true
        );

        // Pasar datos de AJAX y textos traducibles a JS
        wp_localize_script(
            'cdc-admin-scripts',
            'cdcAjax',
            array(
                'ajax_url'           => admin_url( 'admin-ajax.php' ),
                'nonce'              => wp_create_nonce( 'cdc_demo_content_nonce' ),
                'confirmMsg'         => esc_html__( '¿Estás seguro de que deseas realizar esta acción?', CDC_TEXTDOMAIN ),
                'createPagesText'    => esc_html__( 'Crear todas las páginas de ejemplo', CDC_TEXTDOMAIN ),
                'creatingPagesText'  => esc_html__( 'Creando...', CDC_TEXTDOMAIN ),
                'resetPagesText'     => esc_html__( 'Reiniciar creación de páginas', CDC_TEXTDOMAIN ),
                'resettingPagesText' => esc_html__( 'Reiniciando...', CDC_TEXTDOMAIN ),
                'createPostsText'    => esc_html__( 'Crear todas las entradas de ejemplo', CDC_TEXTDOMAIN ),
                'creatingPostsText'  => esc_html__( 'Creando...', CDC_TEXTDOMAIN ),
                'resetPostsText'     => esc_html__( 'Reiniciar creación de entradas', CDC_TEXTDOMAIN ),
                'resettingPostsText' => esc_html__( 'Reiniciando...', CDC_TEXTDOMAIN ),
                'deletePostsText'    => esc_html__( 'Eliminar todas las entradas demo', CDC_TEXTDOMAIN ),
                'deletingPostsText'  => esc_html__( 'Eliminando...', CDC_TEXTDOMAIN ),
                'createMenusText'    => esc_html__( 'Crear Menús de Demo', CDC_TEXTDOMAIN ),
                'creatingMenusText'  => esc_html__( 'Creando...', CDC_TEXTDOMAIN ),
                'resetMenusText'     => esc_html__( 'Reiniciar Menús de Demo', CDC_TEXTDOMAIN ),
                'resettingMenusText' => esc_html__( 'Reiniciando...', CDC_TEXTDOMAIN ),
                'noPagesDataMsg'     => esc_html__( 'No hay datos de páginas para crear.', CDC_TEXTDOMAIN ),
            )
        );
    }

    /**
     * Renderiza la página de administración (incluye la plantilla PHP).
     */
    public function render_admin_page() {
        $data = array(
            'pages_created' => $this->core->pages_created(),
            'posts_created' => $this->core->posts_created(),
            'menus_created' => $this->core->menus_created(),
            'default_pages' => $this->core->get_default_pages(),
            'default_posts' => $this->core->get_default_posts(),
        );

        include CDC_PLUGIN_DIR . 'includes/templates/admin-page.php';
    }

    
    /* -------------------------------------------------------
     * Métodos AJAX
     * ------------------------------------------------------- */

    /**
     * AJAX: crear páginas según datos enviados (title + slug).
     */
    public function ajax_create_pages() {
        check_ajax_referer( 'cdc_demo_content_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Sin permisos', CDC_TEXTDOMAIN ) ) );
        }

        // Recogemos el JSON de páginas
        $pages_json = wp_unslash( $_POST['pages'] ?? '' );
        $pages_data = json_decode( $pages_json, true );

        if ( ! is_array( $pages_data ) ) {
            wp_send_json_error( array( 'message' => __( 'Datos de páginas inválidos.', CDC_TEXTDOMAIN ) ) );
        }

        // Llamamos al core para procesar
        $this->core->create_pages_from_data( $pages_data );

        wp_send_json_success( array( 'message' => __( 'Páginas creadas correctamente.', CDC_TEXTDOMAIN ) ) );
    }

    /**
     * AJAX: crear todas las entradas de ejemplo.
     */
    public function ajax_create_all_posts() {
        check_ajax_referer( 'cdc_demo_content_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Sin permisos', CDC_TEXTDOMAIN ) ) );
        }

        // Leer el parámetro 'count' (ej. "3", "10", etc.)
        $count = isset( $_POST['count'] ) ? absint( $_POST['count'] ) : 3;
        if ( $count < 1 ) {
            $count = 3;
        }
        // Límite de máximo 50 (para evitar abusos)
        $count = min( $count, 50 );

        // Llamamos al método core correspondiente
        $this->core->create_n_posts( $count );

        wp_send_json_success( array( 'message' => sprintf(
            /* translators: %d número de entradas creadas */
            esc_html__( '%d entradas creadas correctamente.', CDC_TEXTDOMAIN ), 
            $count 
        ) ) );
    }

    /**
     * AJAX: eliminar todas las entradas demo.
     */
    public function ajax_delete_all_posts() {
        check_ajax_referer( 'cdc_demo_content_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Sin permisos', CDC_TEXTDOMAIN ) ) );
        }

        $this->core->delete_all_posts();
        wp_send_json_success( array( 'message' => __( 'Todas las entradas demo han sido eliminadas.', CDC_TEXTDOMAIN ) ) );
    }

    /**
     * AJAX: crear menús de demo (main y legal).
     */
    public function ajax_create_demo_menus() {
        check_ajax_referer( 'cdc_demo_content_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Sin permisos', CDC_TEXTDOMAIN ) ) );
        }

        $this->core->create_demo_menus();
        wp_send_json_success( array( 'message' => __( 'Menús de demo creados correctamente.', CDC_TEXTDOMAIN ) ) );
    }

    /**
     * AJAX: resetear bandera de páginas.
     */
    public function ajax_reset_pages_flag() {
        check_ajax_referer( 'cdc_demo_content_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Sin permisos', CDC_TEXTDOMAIN ) ) );
        }

        $this->core->reset_pages_flag();
        wp_send_json_success( array( 'message' => __( 'Flag de páginas reiniciado.', CDC_TEXTDOMAIN ) ) );
    }

    /**
     * AJAX: resetear bandera de entradas.
     */
    public function ajax_reset_posts_flag() {
        check_ajax_referer( 'cdc_demo_content_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Sin permisos', CDC_TEXTDOMAIN ) ) );
        }

        $this->core->reset_posts_flag();
        wp_send_json_success( array( 'message' => __( 'Flag de entradas reiniciado.', CDC_TEXTDOMAIN ) ) );
    }

    /**
     * AJAX: resetear bandera de menús.
     */
    public function ajax_reset_demo_menus_flag() {
        check_ajax_referer( 'cdc_demo_content_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Sin permisos', CDC_TEXTDOMAIN ) ) );
        }

        $this->core->reset_menus_flag();
        wp_send_json_success( array( 'message' => __( 'Flag de menús reiniciado.', CDC_TEXTDOMAIN ) ) );
    }
}