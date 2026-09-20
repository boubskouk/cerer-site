<?php
/**
 * Archive du CPT "chercheur" (/equipe/) — réunit la direction/administration
 * (posts du CPT "chercheur") ET les 4 chefs de département (champs ACF sur
 * la taxonomie "departement", cf. taxonomy-departement.php) dans une seule
 * grille, sans dupliquer la donnée : les chefs de département restent
 * édités depuis leur fiche département, lue ici en direct.
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

get_header();

$departement_terms = is_paged() ? array() : get_terms( array( 'taxonomy' => 'departement', 'hide_empty' => false ) );
if ( is_wp_error( $departement_terms ) ) {
	$departement_terms = array();
}
?>

<div class="cerer-page-hero">
	<div class="cerer-container">
		<h1><?php post_type_archive_title(); ?></h1>
		<p><?php esc_html_e( "La direction, l'administration et les chefs de département du CERER.", 'cerer-child' ); ?></p>
	</div>
</div>

<div class="cerer-container cerer-section">
	<?php if ( have_posts() || $departement_terms ) : ?>
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
						'instagram'    => get_field( 'lien_instagram' ),
					)
				);
			endwhile;

			foreach ( $departement_terms as $term ) :
				$fr_term_id = function_exists( 'pll_get_term' ) ? ( pll_get_term( $term->term_id, 'fr' ) ?: $term->term_id ) : $term->term_id;
				$chef_nom   = cerer_term_field( 'chef_nom', $term->term_id, $fr_term_id );
				if ( ! $chef_nom ) {
					continue; // Pas de fiche "Responsable" renseignée sur ce département.
				}
				$chef_photo = cerer_term_field( 'chef_photo', $term->term_id, $fr_term_id );
				get_template_part(
					'template-parts/carte-chercheur',
					null,
					array(
						'nom'          => $chef_nom,
						'url'          => get_term_link( $term ),
						'photo'        => ! empty( $chef_photo['sizes']['medium'] ) ? $chef_photo['sizes']['medium'] : ( $chef_photo['url'] ?? '' ),
						'titre'        => cerer_term_field( 'chef_fonction', $term->term_id, $fr_term_id ) ?: __( 'Chef du département', 'cerer-child' ),
						'specialite'   => $term->name,
						'linkedin'     => cerer_term_field( 'chef_linkedin', $term->term_id, $fr_term_id ),
						'researchgate' => cerer_term_field( 'chef_researchgate', $term->term_id, $fr_term_id ),
						'instagram'    => cerer_term_field( 'chef_instagram', $term->term_id, $fr_term_id ),
					)
				);
			endforeach;
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
		<p><?php esc_html_e( 'Aucun membre référencé pour le moment.', 'cerer-child' ); ?></p>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
