<?php
/**
 * Template Name: Données & Outils
 *
 * Les ressources ne sont plus codées en dur : elles viennent du CPT
 * "ressource" (menu « Ressources » dans l'admin), triées par ordre de
 * menu — le client ajoute/retire/réordonne sans toucher au code.
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

get_header();

$chapo_page = get_field( 'chapo_page' );

$ressources = get_posts(
	array(
		'post_type'      => 'ressource',
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

<div class="cerer-container cerer-section">
	<?php if ( $ressources ) : ?>
		<div class="cerer-card-grid">
			<?php foreach ( $ressources as $ressource ) : ?>
				<?php $lien = get_field( 'lien', $ressource->ID ); ?>
				<div class="cerer-card cerer-card--theme">
					<div class="cerer-card__icon" aria-hidden="true"><?php echo esc_html( get_field( 'icone_emoji', $ressource->ID ) ); ?></div>
					<h3><?php echo esc_html( get_the_title( $ressource ) ); ?></h3>
					<p><?php echo esc_html( get_field( 'description', $ressource->ID ) ); ?></p>
					<p style="margin-top:12px;">
						<?php if ( $lien ) : ?>
							<?php cerer_external_link( $lien, __( 'Accéder à la ressource', 'cerer-child' ) ); ?>
						<?php else : ?>
							<span class="cerer-badge cerer-badge--a-venir"><?php esc_html_e( 'Lien à venir', 'cerer-child' ); ?></span>
						<?php endif; ?>
					</p>
				</div>
			<?php endforeach; ?>
		</div>
	<?php else : ?>
		<p class="cerer-meta"><?php esc_html_e( 'Ressources à renseigner depuis l’admin (menu « Ressources »).', 'cerer-child' ); ?></p>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
