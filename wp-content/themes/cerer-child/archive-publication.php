<?php
/**
 * Archive du CPT "publication" (/publications/).
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="cerer-page-hero">
	<div class="cerer-container">
		<h1><?php post_type_archive_title(); ?></h1>
		<p><?php esc_html_e( 'Articles scientifiques, rapports, actes de conférence et thèses produits par le CERER.', 'cerer-child' ); ?></p>
	</div>
</div>

<div class="cerer-container cerer-section">
	<?php if ( have_posts() ) : ?>
		<div class="cerer-card-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part(
					'template-parts/carte-publication',
					null,
					array(
						'title'    => get_the_title(),
						'url'      => get_permalink(),
						'type'     => get_field( 'type' ) ?: 'article',
						'auteurs'  => get_field( 'auteurs' ),
						'revue'    => get_field( 'revue' ),
						'annee'    => get_field( 'annee' ),
						'resume'   => get_field( 'resume' ),
						'lien_pdf' => get_field( 'lien_pdf' ),
						'doi'      => get_field( 'doi' ),
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
		<p><?php esc_html_e( 'Aucune publication pour le moment.', 'cerer-child' ); ?></p>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
