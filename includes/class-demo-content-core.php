<?php
/**
 * Clase con la lógica “core” para crear páginas, entradas y menús de demo.
 *
 * @package CustomDemoContent
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Demo_Content_Core {

    /**
     * Array con la definición de páginas predeterminadas
     * (slug => [title, content, template])
     *
     * @var array
     */
    private $default_pages = array(
        'aviso-legal' => array(
            'title'    => 'Aviso legal',
            'content'  => 'Contenido de ejemplo para la página de Aviso Legal.',
            'template' => 'page.php',
        ),
        'politica-privacidad' => array(
            'title'    => 'Política de privacidad',
            'content'  => 'Contenido de ejemplo para la página de Política de Privacidad.',
            'template' => 'page.php',
        ),
        'politica-cookies' => array(
            'title'    => 'Política de cookies',
            'content'  => 'Contenido de ejemplo para la página de Política de Cookies.',
            'template' => 'page.php',
        ),
        'home' => array(
            'title'    => 'Home',
            'content'  => 'Contenido de ejemplo para la página de inicio.',
            'template' => 'page-home.php',
        ),
        'contacto' => array(
            'title'    => 'Contacto',
            'content'  => 'Contenido de ejemplo para la página de Contacto.',
            'template' => 'page-contact.php',
        ),
        'quienes-somos' => array(
            'title'    => 'Quiénes somos',
            'content'  => 'Contenido de ejemplo para la página de Quiénes Somos.',
            'template' => 'page-about.php',
        ),
        'blog' => array(
            'title'    => 'Blog',
            'content'  => 'Contenido de ejemplo para la página del blog.',
            'template' => 'page-blog.php',
        ),
    );

    /**
     * Método para obtener las páginas predeterminadas.
     */
    public function get_default_pages() {
        return $this->default_pages;
    }

    /**
     * Array con la definición de entradas de ejemplo.
     *
     * @var array
     */
    private $default_posts = array(
        'Entrada de prueba 1',
        'Entrada de prueba 2',
        'Entrada de prueba 3',
    );

    /**
     * Método para obtener los títulos de entradas de demo.
     */
    public function get_default_posts() {
        return $this->default_posts;
    }

    /**
     * Claves en la tabla wp_options para marcar creación de páginas, posts y menús.
     *
     * @var string
     */
    private $option_pages_created      = 'cdc_default_pages_created';
    private $option_posts_created      = 'cdc_sample_posts_created';
    private $option_menus_created      = 'cdc_menus_created';

    /**
     * Crea todas las páginas de ejemplo si no existen.
     *
     * @return void
     */
    public function create_all_pages() {
        if ( get_option( $this->option_pages_created ) ) {
            return;
        }

        foreach ( $this->default_pages as $slug => $page ) {
            $this->create_page( $slug, $page );
        }

        // Asignar Home como página de inicio
        $home_page = get_page_by_path( 'home' );
        if ( $home_page ) {
            update_option( 'page_on_front', absint( $home_page->ID ) );
            update_option( 'show_on_front', 'page' );
        }

        add_option( $this->option_pages_created, 1 );
    }

    /**
     * Crea páginas según los datos proporcionados (título y slug personalizados).
     *
     * @param array $pages_data Array de arrays con keys: default_slug, title, slug.
     * @return void
     */
    public function create_pages_from_data( $pages_data ) {
        // Recorremos cada elemento enviado
        foreach ( $pages_data as $page_data ) {
            $default_slug = sanitize_text_field( $page_data['default_slug'] ?? '' );
            $title        = sanitize_text_field( $page_data['title'] ?? '' );
            $slug         = sanitize_title( $page_data['slug'] ?? '' );
            $content      = $this->default_pages[ $default_slug ]['content'] ?? '';
            $template     = $this->default_pages[ $default_slug ]['template'] ?? '';

            if ( $title && $slug ) {
                // Si no existe página con ese slug, la creamos
                if ( ! get_page_by_path( $slug, OBJECT, 'page' ) ) {
                    $page_id = wp_insert_post( array(
                        'post_title'   => wp_strip_all_tags( $title ),
                        'post_name'    => $slug,
                        'post_content' => wp_kses_post( $content ),
                        'post_status'  => 'publish',
                        'post_type'    => 'page',
                    ) );

                    if ( $page_id && $template ) {
                        update_post_meta( $page_id, '_wp_page_template', sanitize_text_field( $template ) );
                    }
                }
            }
        }

        // Si aún no se había marcado, agregamos la opción
        if ( ! get_option( $this->option_pages_created ) ) {
            add_option( $this->option_pages_created, 1 );
        }
    }

    /**
     * Crea una sola página a partir de un slug y su definición predeterminada.
     *
     * @param string $slug Slug de la página (clave en default_pages).
     * @return bool Verdadero si la creó o ya existía, false si no hay definición.
     */
    public function create_single_page_by_slug( $slug ) {
        if ( isset( $this->default_pages[ $slug ] ) ) {
            $this->create_page( $slug, $this->default_pages[ $slug ] );
            return true;
        }
        return false;
    }

    /**
     * Método interno que inserta la página según slug y definición.
     *
     * @param string $slug Slug de la página.
     * @param array  $page Array con índices: title, content, template.
     * @return void
     */
    public function create_page( $slug, $page ) {
        $sanitized_slug = sanitize_title( $slug );
        if ( get_page_by_path( $sanitized_slug, OBJECT, 'page' ) ) {
            return;
        }

        $page_id = wp_insert_post( array(
            'post_title'   => wp_strip_all_tags( $page['title'] ),
            'post_name'    => $sanitized_slug,
            'post_content' => wp_kses_post( $page['content'] ),
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ) );

        if ( $page_id && ! empty( $page['template'] ) ) {
            update_post_meta( $page_id, '_wp_page_template', sanitize_text_field( $page['template'] ) );
        }
    }

    /**
     * Crea un número dinámico de entradas de ejemplo.
     *
     * @param int $count Cantidad de entradas a crear.
     * @return void
     */
    public function create_n_posts( $count = 3 ) {
        // Si ya se crearon (flag) y count coincide con el anterior, no hacemos nada
        if ( get_option( $this->option_posts_created ) ) {
            return;
        }

        // Cargar funciones de media
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        // Imagen por defecto para las entradas demo
        $default_image_path = CDC_PLUGIN_DIR . 'assets/img/default-blog-image.webp';
        $default_image_uri  = CDC_PLUGIN_URL . 'assets/img/default-blog-image.webp';

        // Antes de entrar en el bucle, comprobamos si ya tenemos un “default image ID” guardado
        $default_attachment_id = get_option( 'cdc_default_image_id' );

        if ( ! $default_attachment_id ) {
            // Solo si no existe, subimos el archivo una vez
            if ( file_exists( $default_image_path ) ) {
                $tmp = media_sideload_image( $default_image_uri, 0, null, 'src' );
                if ( ! is_wp_error( $tmp ) ) {
                    global $wpdb;
                    $default_attachment_id = $wpdb->get_var( "
                        SELECT ID FROM {$wpdb->posts}
                        WHERE post_type = 'attachment'
                        ORDER BY post_date DESC
                        LIMIT 1
                    " );
                    if ( $default_attachment_id ) {
                        update_option( 'cdc_default_image_id', $default_attachment_id );
                    }
                }
            }
        }

        for ( $i = 1; $i <= $count; $i++ ) {
            // Generar título y slug según posición
            $title_template = 'Entrada de prueba %d';
            $title = sprintf( $title_template, $i );
            $slug  = sanitize_title( $title );

            // Si ya existe un post con ese slug, saltar
            if ( get_page_by_path( $slug, OBJECT, 'post' ) ) {
                continue;
            }

            // Crear la entrada
            $post_id = wp_insert_post( array(
                'post_title'   => wp_strip_all_tags( $title ),
                'post_name'    => $slug,
                'post_content' => wp_kses_post( sprintf( 'Este es el contenido de ejemplo para %s', $title ) ),
                'post_status'  => 'publish',
                'post_type'    => 'post',
            ) );

            // Asignar imagen destacada si existe
            if ( $post_id && $default_attachment_id ) {
                set_post_thumbnail( $post_id, $default_attachment_id );
            }
        }

        add_option( $this->option_posts_created, 1 );
    }

    /**
     * Elimina todas las entradas demo basadas en el slug generado.
     *
     * @return void
     */
    public function delete_all_posts() {
        foreach ( $this->default_posts as $title ) {
            $slug     = sanitize_title( $title );
            $post_obj = get_page_by_path( $slug, OBJECT, 'post' );
            if ( $post_obj ) {
                wp_delete_post( $post_obj->ID, true );
            }
        }

        // Resetear flag para que se puedan volver a crear
        delete_option( $this->option_posts_created );
    }

    /**
     * Crea menús de demo: “main” y “legal” si no existen.
     *
     * @return void
     */
    public function create_demo_menus() {
        if ( ! get_option( $this->option_menus_created ) ) {
            // Menú Principal
            if ( ! wp_get_nav_menu_object( 'main' ) ) {
                $main_menu_id = wp_create_nav_menu( 'main' );
                $main_pages   = array(
                    'home',
                    'quienes-somos',
                    'blog',
                    'contacto',
                );
                foreach ( $main_pages as $slug ) {
                    $page = get_page_by_path( sanitize_title( $slug ) );
                    if ( $page ) {
                        wp_update_nav_menu_item( $main_menu_id, 0, array(
                            'menu-item-title'     => $page->post_title,
                            'menu-item-object'    => 'page',
                            'menu-item-object-id' => $page->ID,
                            'menu-item-type'      => 'post_type',
                            'menu-item-status'    => 'publish',
                        ) );
                    }
                }
                $locations = get_theme_mod( 'nav_menu_locations' );
                if ( ! is_array( $locations ) ) {
                    $locations = array();
                }
                $locations['main'] = $main_menu_id;
                set_theme_mod( 'nav_menu_locations', $locations );
            }

            // Menú Legales
            if ( ! wp_get_nav_menu_object( 'legal' ) ) {
                $legal_menu_id = wp_create_nav_menu( 'legal' );
                $legal_pages   = array(
                    'aviso-legal',
                    'politica-privacidad',
                    'politica-cookies',
                );
                foreach ( $legal_pages as $slug ) {
                    $page = get_page_by_path( sanitize_title( $slug ) );
                    if ( $page ) {
                        wp_update_nav_menu_item( $legal_menu_id, 0, array(
                            'menu-item-title'     => $page->post_title,
                            'menu-item-object'    => 'page',
                            'menu-item-object-id' => $page->ID,
                            'menu-item-type'      => 'post_type',
                            'menu-item-status'    => 'publish',
                        ) );
                    }
                }
                $locations = get_theme_mod( 'nav_menu_locations' );
                if ( ! is_array( $locations ) ) {
                    $locations = array();
                }
                $locations['legal'] = $legal_menu_id;
                set_theme_mod( 'nav_menu_locations', $locations );
            }

            add_option( $this->option_menus_created, 1 );
        }
    }

    /**
     * Resetea la opción para recrear páginas.
     *
     * @return void
     */
    public function reset_pages_flag() {
        delete_option( $this->option_pages_created );
    }

    /**
     * Resetea la opción para recrear entradas.
     *
     * @return void
     */
    public function reset_posts_flag() {
        delete_option( $this->option_posts_created );
    }

    /**
     * Resetea la opción para recrear menús.
     *
     * @return void
     */
    public function reset_menus_flag() {
        delete_option( $this->option_menus_created );
    }

    /**
     * Indica si las páginas demo ya han sido creadas.
     *
     * @return bool
     */
    public function pages_created() {
        return (bool) get_option( $this->option_pages_created );
    }

    /**
     * Indica si las entradas demo ya han sido creadas.
     *
     * @return bool
     */
    public function posts_created() {
        return (bool) get_option( $this->option_posts_created );
    }

    /**
     * Indica si los menús demo ya han sido creados.
     *
     * @return bool
     */
    public function menus_created() {
        return (bool) get_option( $this->option_menus_created );
    }
}