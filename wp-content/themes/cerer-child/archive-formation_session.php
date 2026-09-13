<?php
/**
 * Archive du CPT "formation_session" (/formation-continue/).
 *
 * Réutilise la carte horizontale formation (carte-formation.php), déjà
 * utilisée pour les webinaires et les projets — même forme de carte pour
 * toute "session à s'inscrire".
 *
 * Le CPT n'a pas de champ "statut" (contrairement à webinaire/projet) : le
 * badge est déduit des dates de début/fin.
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

get_header();

// Mêmes libellés que le champ ACF "programme" (group_cerer_formation_session).
$programme_labels = array(
	'licence_enr'        => __( 'Licence en Énergie Renouvelable', 'cerer-child' ),
	'master_enr'         => __( 'Master en Énergie Renouvelable', 'cerer-child' ),
	'certifications'     => __( 'Certifications professionnelles', 'cerer-child' ),
	'formation_continue' => __( 'Formation continue', 'cerer-child' ),
);
?>

<div class="cerer-page-hero">
	<div class="cerer-container">
		<h1><?php post_type_archive_title(); ?></h1>
		<p><?php esc_html_e( 'Sessions programmées pour les formations et certifications du CERER.', 'cerer-child' ); ?></p>
	</div>
</div>

<div class="cerer-container cerer-section">
	<?php if ( have_posts() ) : ?>
		<div class="cerer-stacked-list">
			<?php
			while ( have_posts() ) :
				the_post();

				$programme   = get_field( 'programme' );
				$date_debut  = get_field( 'date_debut' );
				$date_fin    = get_field( 'date_fin' );
				$lieu        = get_field( 'lieu' );
				$places      = get_field( 'places_dispo' );
				$inscription = get_field( 'lien_inscription' );

				$today = current_time( 'Y-m-d' );
				if ( $date_debut && $today < $date_debut ) {
					$statut = 'a-venir';
				} elseif ( $date_fin && $today > $date_fin ) {
					$statut = 'termine';
				} else {
					$statut = 'en-cours';
				}

				$dates = '';
				if ( $date_debut ) {
					$dates = date_i18n( 'j F Y', strtotime( $date_debut ) );
					if ( $date_fin && $date_fin !== $date_debut ) {
						$dates .= ' – ' . date_i18n( 'j F Y', strtotime( $date_fin ) );
					}
				}

				$meta_parts = array_filter(
					array(
						$dates,
						$lieu,
						( null !== $places ) ? sprintf( _n( '%d place disponible', '%d places disponibles', $places, 'cerer-child' ), $places ) : '',
					)
				);

				get_template_part(
					'template-parts/carte-formation',
					null,
					array(
						'icon_html'   => '🎓',
						'title'       => get_the_title(),
						'title_url'   => get_permalink(),
						'badge'       => array( 'kind' => 'status', 'variant' => $statut ),
						'meta'        => implode( ' · ', $meta_parts ),
						'description' => $programme_labels[ $programme ] ?? '',
						'cta_label'   => 'termine' === $statut ? __( 'Session terminée', 'cerer-child' ) : __( "S'inscrire", 'cerer-child' ),
						'cta_url'     => 'termine' === $statut ? '#' : ( $inscription ?: '#' ),
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
		<p><?php esc_html_e( 'Aucune session programmée pour le moment.', 'cerer-child' ); ?></p>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
