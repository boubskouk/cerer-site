<?php
/**
 * Template Name: Formations
 *
 * Les programmes ne sont plus codés en dur : ils viennent du CPT
 * "programme" (menu « Programmes de formation » dans l'admin), triés par
 * ordre de menu — le client ajoute/retire/réordonne sans toucher au code.
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

get_header();

$chapo_page = get_field( 'chapo_page' );

$programmes = get_posts(
	array(
		'post_type'      => 'programme',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	)
);
?>

<div class="cerer-page-hero">
	<div class="cerer-container">
		<h1><?php the_title(); ?></h1>
		<?php if ( $chapo_page ) : ?>
			<p><?php echo esc_html( $chapo_page ); ?></p>
		<?php endif; ?>
	</div>
</div>

<div class="cerer-container cerer-section" style="display:flex; flex-direction:column; gap:16px;">
	<?php if ( $programmes ) : ?>
		<?php foreach ( $programmes as $programme ) : ?>
			<?php
			$badge_variant = get_field( 'badge_variant', $programme->ID );
			get_template_part(
				'template-parts/carte-formation',
				null,
				array(
					'icon_html'   => get_field( 'icone_emoji', $programme->ID ),
					'title'       => get_the_title( $programme ),
					'badge'       => $badge_variant ? array( 'kind' => 'status', 'variant' => $badge_variant ) : null,
					'meta'        => get_field( 'meta', $programme->ID ),
					'description' => get_field( 'description', $programme->ID ),
					'cta_label'   => __( 'En savoir plus', 'cerer-child' ),
					'cta_url'     => get_field( 'lien', $programme->ID ) ?: '#',
				)
			);
			?>
		<?php endforeach; ?>
	<?php else : ?>
		<p class="cerer-meta"><?php esc_html_e( 'Programmes à renseigner depuis l’admin (menu « Programmes de formation »).', 'cerer-child' ); ?></p>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
