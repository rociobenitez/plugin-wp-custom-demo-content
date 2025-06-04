<?php
/**
 * Template para la página de administración “Demo Content”
 *
 * Variables disponibles:
 *   - $data['pages_created'] (bool)
 *   - $data['posts_created'] (bool)
 *   - $data['default_pages'] (array asociativo: slug => [ 'title', 'content', 'template' ])
 *   - $data['default_posts'] (array de títulos de posts de ejemplo)
 *
 * @package CustomDemoContent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

# Extraer variables
$pages_created = $data['pages_created'];
$posts_created = $data['posts_created'];
$menus_created = $data['menus_created'];
$default_pages = $data['default_pages'];
$default_posts = $data['default_posts'];
?>

<div class="wrap cdc-wrap">
	<h1><?php esc_html_e( 'Demo Content', CDC_TEXTDOMAIN ); ?></h1>
	<p><?php esc_html_e( 'Aquí puedes crear o regenerar el contenido de prueba (páginas y entradas).', CDC_TEXTDOMAIN ); ?></p>

	<hr class="cdc-separator">

	<!-- SECCIÓN 1: PÁGINAS GENERALES -->
	<h2><?php esc_html_e( 'Páginas Generales', CDC_TEXTDOMAIN ); ?></h2>

	<?php if ( $pages_created ) : ?>
		<div class="notice notice-success">
			<p><?php esc_html_e( 'Las páginas generales ya fueron creadas.', CDC_TEXTDOMAIN ); ?></p>
		</div>
	<?php else : ?>
		<div class="notice notice-warning">
			<p><?php esc_html_e( 'Aún no has creado las páginas generales.', CDC_TEXTDOMAIN ); ?></p>
		</div>
	<?php endif; ?>

	<table class="widefat fixed striped cdc-table">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Título', CDC_TEXTDOMAIN ); ?></th>
				<th><?php esc_html_e( 'Slug', CDC_TEXTDOMAIN ); ?></th>
				<th><?php esc_html_e( 'Existe', CDC_TEXTDOMAIN ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ( $default_pages as $slug => $page ) :
				$post_obj = get_page_by_path( $slug, OBJECT, 'post' );
				$exists   = $post_obj ? __( 'Sí', CDC_TEXTDOMAIN ) : __( 'No', CDC_TEXTDOMAIN );
			?>
			<tr data-default-slug="<?php echo esc_attr( $slug ); ?>">
				<td>
					<input 
						type="text" 
						class="cdc-page-title" 
						value="<?php echo esc_attr( $page['title'] ); ?>" 
						placeholder="<?php esc_attr_e( 'Título de la página', CDC_TEXTDOMAIN ); ?>"
					/>
				</td>
				<td>
					<input 
						type="text" 
						class="cdc-page-slug" 
						value="<?php echo esc_attr( $slug ); ?>" 
						placeholder="<?php esc_attr_e( 'slug-de-pagina', CDC_TEXTDOMAIN ); ?>"
					/>
				</td>
				<td><?php echo esc_html( $exists ); ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<p>
		<button id="cdc-create-pages" class="button button-primary">
			<?php esc_html_e( 'Crear todas las páginas', CDC_TEXTDOMAIN ); ?>
		</button>
		<button id="cdc-reset-pages" class="button">
			<?php esc_html_e( 'Reiniciar creación de páginas', CDC_TEXTDOMAIN ); ?>
		</button>
	</p>

	<hr class="cdc-separator">

	<!-- SECCIÓN 2: ENTRADAS DE EJEMPLO -->
	<h2><?php esc_html_e( 'Entradas Demo', CDC_TEXTDOMAIN ); ?></h2>

	<?php if ( $posts_created ) : ?>
		<div class="notice notice-success">
			<p><?php esc_html_e( 'Las entradas de ejemplo ya fueron creadas.', CDC_TEXTDOMAIN ); ?></p>
		</div>
	<?php else : ?>
		<div class="notice notice-warning">
			<p><?php esc_html_e( 'Aún no has creado las entradas de ejemplo.', CDC_TEXTDOMAIN ); ?></p>
		</div>
	<?php endif; ?>

	<table class="widefat fixed striped cdc-table">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Título', CDC_TEXTDOMAIN ); ?></th>
				<th><?php esc_html_e( 'Existe', CDC_TEXTDOMAIN ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $default_posts as $title ) : 
				$post_obj = get_page_by_path( sanitize_title( $title ), OBJECT, 'post' );
				$exists   = $post_obj ? esc_html__( 'Sí', CDC_TEXTDOMAIN ) : esc_html__( 'No', CDC_TEXTDOMAIN );
			?>
			<tr>
				<td><?php echo esc_html( $title ); ?></td>
				<td><?php echo esc_html( $exists ); ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<p>
		<label for="cdc-post-count">
			<?php esc_html_e( 'Cantidad de entradas a generar:', CDC_TEXTDOMAIN ); ?>
			<input type="number" 
				id="cdc-post-count" 
				name="cdc_post_count" 
				value="3" 
				min="1" 
				max="50" 
				style="width: 60px; margin-left: 10px;" />
		</label>
	</p>

	<p>
		<button id="cdc-create-posts" class="button button-primary">
			<?php esc_html_e( 'Crear entradas demo', CDC_TEXTDOMAIN ); ?>
		</button>
		<button id="cdc-reset-posts" class="button">
			<?php esc_html_e( 'Reiniciar creación de entradas', CDC_TEXTDOMAIN ); ?>
		</button>
		<button id="cdc-delete-posts" class="button button-secondary">
			<?php esc_html_e( 'Eliminar todas las entradas demo', CDC_TEXTDOMAIN ); ?>
		</button>
	</p>

	<hr class="cdc-separator">

	<!-- SECCIÓN 3: MENÚS -->
	<h2><?php esc_html_e( 'Menús', CDC_TEXTDOMAIN ); ?></h2>

	<?php if ( ! $menus_created ) : ?>
		<div class="notice notice-info">
			<p><?php esc_html_e( 'Crea el Menú Principal y el Menú de Páginas Legales automáticamente si no existen.', CDC_TEXTDOMAIN ); ?></p>
		</div>
	<?php else : ?>
		<div class="notice notice-success">
			<p><?php esc_html_e( 'Los menús de demo ya fueron creados.', CDC_TEXTDOMAIN ); ?></p>
		</div>
	<?php endif; ?>

	<table class="widefat fixed striped cdc-table">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Menú', CDC_TEXTDOMAIN ); ?></th>
				<th><?php esc_html_e( 'Existe', CDC_TEXTDOMAIN ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$main_menu_obj  = wp_get_nav_menu_object( 'main' );
			$legal_menu_obj = wp_get_nav_menu_object( 'legal' );
			?>
			<tr>
				<td><?php esc_html_e( 'Menú Principal (main)', CDC_TEXTDOMAIN ); ?></td>
				<td><?php echo $main_menu_obj ? esc_html__( 'Sí', CDC_TEXTDOMAIN ) : esc_html__( 'No', CDC_TEXTDOMAIN ); ?></td>
			</tr>
			<tr>
				<td><?php esc_html_e( 'Menú Legales (legal)', CDC_TEXTDOMAIN ); ?></td>
				<td><?php echo $legal_menu_obj ? esc_html__( 'Sí', CDC_TEXTDOMAIN ) : esc_html__( 'No', CDC_TEXTDOMAIN ); ?></td>
			</tr>
		</tbody>
	</table>

	<p>
		<button id="cdc-create-menus" class="button button-primary">
			<?php esc_html_e( 'Crear Menús', CDC_TEXTDOMAIN ); ?>
		</button>
		<button id="cdc-reset-menus" class="button">
			<?php esc_html_e( 'Reiniciar Menús', CDC_TEXTDOMAIN ); ?>
		</button>
	</p>

	<hr class="cdc-separator">

	<p class="description">
		<?php esc_html_e( 'Usa “Reiniciar” para volver a regenerar el contenido de demo o los menús si quieres actualizarlos.', CDC_TEXTDOMAIN ); ?>
	</p>
</div>
