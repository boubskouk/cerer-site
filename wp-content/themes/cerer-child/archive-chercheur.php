<?php
/**
 * Archive du CPT "chercheur" (/chercheurs/).
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="cerer-page-hero">
	<div class="cerer-container">
		<h1><?php post_type_archive_title(); ?></h1>
		<p><?php esc_html_e( 'Les chercheurs et enseignants-chercheurs du CERER.', 'cerer-child' ); ?></p>
	</div>
</div>

<div class="cerer-container cerer-section">
	<?php if ( have_posts() ) : ?>
		<div class="cerer-card-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				$photo     = get_field( 'photo' );
				$photo_url = $photo['sizes']['medium'] ?? ( $photo['url'] ?? ( get_the_post_thumbnail_url( get_the_ID(), 'medium' ) ?: '' ) );
				get_template_part(
					'template-parts/carte-chercheur',
					null,
					array(
						'nom'          => get_the_title(),
						'url'          => get_permalink(),
						'photo'        => $photo_url,
						'titre'        => get_field( 'titre' ),
						'specialite'   => get_field( 'specialite' ),
						'linkedin'     => get_field( 'lien_linkedin' ),
						'researchgate' => get_field( 'lien_researchgate' ),
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
		<p><?php esc_html_e( 'Aucun chercheur référencé pour le moment.', 'cerer-child' ); ?></p>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
