<?php
/**
 * Archive du CPT "soutenance" (/soutenances/).
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="cerer-page-hero">
	<div class="cerer-container">
		<h1><?php post_type_archive_title(); ?></h1>
		<p><?php esc_html_e( 'Mémoires de licence, master et certifications soutenus au CERER.', 'cerer-child' ); ?></p>
	</div>
</div>

<div class="cerer-container cerer-section">
	<?php if ( have_posts() ) : ?>
		<div class="cerer-card-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part(
					'template-parts/carte-soutenance',
					null,
					array(
						'title'        => get_the_title(),
						'url'          => get_permalink(),
						'niveau'       => get_field( 'niveau' ) ?: 'licence',
						'auteur'       => get_field( 'auteur' ),
						'annee'        => get_field( 'annee' ),
						'directeur'    => get_field( 'directeur' ),
						'mention'      => get_field( 'mention' ),
						'lien_memoire' => get_field( 'lien_memoire' ),
						'lien_video'   => get_field( 'lien_video' ),
					)
				);
			endwhile;
			?>
		</div>

		<div class="cerer-pagination">
			<?php
			the_posts_pagination(
				array(
					'prev_text' => __( '← Précédent', 'cerer-child' ),
					'next_text' => __( 'Suivant →', 'cerer-child' ),
				)
			);
			?>
		</div>
	<?php else : ?>
		<p><?php esc_html_e( 'Aucune soutenance pour le moment.', 'cerer-child' ); ?></p>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
