<?php
/**
 * Fiche session de formation (CPT "formation_session").
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

get_header();

$programme_labels = array(
	'licence_enr'        => __( 'Licence en Énergie Renouvelable', 'cerer-child' ),
	'master_enr'         => __( 'Master en Énergie Renouvelable', 'cerer-child' ),
	'certifications'     => __( 'Certifications professionnelles', 'cerer-child' ),
	'formation_continue' => __( 'Formation continue', 'cerer-child' ),
);

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
	?>

	<article class="cerer-container cerer-section cerer-single" style="max-width:820px;">
		<?php if ( $programme_labels[ $programme ] ?? '' ) : ?>
			<p class="cerer-label cerer-eyebrow"><?php echo esc_html( $programme_labels[ $programme ] ); ?></p>
		<?php endif; ?>
		<h1><?php the_title(); ?></h1>
		<div style="margin-top:12px;">
			<?php get_template_part( 'template-parts/badge', null, array( 'kind' => 'status', 'variant' => $statut ) ); ?>
		</div>

		<?php if ( $meta_parts ) : ?>
			<p class="cerer-meta" style="margin-top:16px; font-size:15px;"><?php echo esc_html( implode( ' · ', $meta_parts ) ); ?></p>
		<?php endif; ?>

		<div class="cerer-card__actions" style="margin-top:32px;">
			<?php if ( 'termine' !== $statut && $inscription ) : ?>
				<?php cerer_external_link( $inscription, __( "S'inscrire", 'cerer-child' ), array( 'class' => 'cerer-btn cerer-btn--primary' ) ); ?>
			<?php elseif ( 'termine' === $statut ) : ?>
				<span class="cerer-badge cerer-badge--termine"><?php esc_html_e( 'Session terminée', 'cerer-child' ); ?></span>
			<?php endif; ?>
		</div>

		<p style="margin-top:40px;">
			<a href="<?php echo esc_url( get_post_type_archive_link( 'formation_session' ) ); ?>" class="cerer-btn cerer-btn--link">← <?php esc_html_e( 'Toutes les sessions', 'cerer-child' ); ?></a>
		</p>
	</article>

	<?php
endwhile;

get_footer();
