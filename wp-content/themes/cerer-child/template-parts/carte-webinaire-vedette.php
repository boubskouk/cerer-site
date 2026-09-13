<?php
/**
 * Carte "prochain webinaire" — accueil, section Webinaires (PROMPT-13).
 * Deux colonnes : présentation à gauche, formulaire d'inscription natif à
 * droite (template-parts/webinar-registration-form.php — pas de WPForms Pro
 * installé). Le lien Zoom n'apparaît jamais ici, il part par e-mail après
 * inscription.
 *
 * @package cerer-child
 * @var array $args
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args(
	$args ?? array(),
	array(
		'post_id' => 0,
	)
);

$post_id = $args['post_id'];
if ( ! $post_id ) {
	return;
}

$theme_terms = get_the_terms( $post_id, 'thematique' );
$theme_label = ( $theme_terms && ! is_wp_error( $theme_terms ) ) ? $theme_terms[0]->name : '';

$date_brute = get_field( 'date', $post_id );
$timestamp  = $date_brute ? strtotime( $date_brute ) : 0;
$date_fmt   = $timestamp ? date_i18n( 'j F Y', $timestamp ) : '';
$duree      = get_field( 'duree', $post_id );
$heure_fmt  = $timestamp ? trim( date_i18n( 'H\hi', $timestamp ) . ' GMT' . ( $duree ? ' · ' . $duree : '' ) ) : '';
$plateforme = get_field( 'plateforme', $post_id );
$langue     = get_field( 'langue', $post_id ) ?: __( 'Français', 'cerer-child' );

$speakers = array();
for ( $i = 1; $i <= 3; $i++ ) {
	$nom = get_field( "intervenant_{$i}_nom", $post_id );
	if ( ! $nom ) {
		continue;
	}
	$speakers[] = array(
		'nom'         => $nom,
		'fonction'    => get_field( "intervenant_{$i}_fonction", $post_id ),
		'institution' => get_field( "intervenant_{$i}_institution", $post_id ),
		'photo'       => get_field( "intervenant_{$i}_photo", $post_id ),
	);
}

$resume = get_field( 'resume', $post_id );
?>
<div class="cerer-webinar-feature">
	<div class="cerer-webinar-feature__main">
		<div class="cerer-webinar-feature__badges">
			<span class="cerer-webinar-feature__badge-next"><?php esc_html_e( 'Prochain webinaire', 'cerer-child' ); ?></span>
			<span class="cerer-webinar-feature__badge-status"><?php esc_html_e( 'Inscriptions ouvertes', 'cerer-child' ); ?></span>
		</div>

		<?php if ( $theme_label ) : ?>
			<p class="cerer-webinar-feature__theme"><?php echo esc_html( $theme_label ); ?></p>
		<?php endif; ?>

		<h3><?php echo esc_html( get_the_title( $post_id ) ); ?></h3>

		<?php if ( $resume ) : ?>
			<p class="cerer-webinar-feature__resume"><?php echo esc_html( wp_strip_all_tags( $resume ) ); ?></p>
		<?php endif; ?>

		<div class="cerer-webinar-feature__meta">
			<div class="cerer-webinar-feature__meta-cell">
				<span class="cerer-webinar-feature__meta-label"><?php esc_html_e( 'Date', 'cerer-child' ); ?></span>
				<span class="cerer-webinar-feature__meta-value"><?php echo esc_html( $date_fmt ); ?></span>
			</div>
			<div class="cerer-webinar-feature__meta-cell">
				<span class="cerer-webinar-feature__meta-label"><?php esc_html_e( 'Heure', 'cerer-child' ); ?></span>
				<span class="cerer-webinar-feature__meta-value"><?php echo esc_html( $heure_fmt ); ?></span>
			</div>
			<div class="cerer-webinar-feature__meta-cell">
				<span class="cerer-webinar-feature__meta-label"><?php esc_html_e( 'Plateforme', 'cerer-child' ); ?></span>
				<span class="cerer-webinar-feature__meta-value"><?php echo esc_html( $plateforme ); ?></span>
			</div>
			<div class="cerer-webinar-feature__meta-cell">
				<span class="cerer-webinar-feature__meta-label"><?php esc_html_e( 'Langue', 'cerer-child' ); ?></span>
				<span class="cerer-webinar-feature__meta-value"><?php echo esc_html( $langue ); ?></span>
			</div>
		</div>

		<?php if ( $speakers ) : ?>
			<div class="cerer-webinar-feature__speakers">
				<p class="cerer-webinar-feature__speakers-label"><?php esc_html_e( 'Intervenants', 'cerer-child' ); ?></p>
				<div class="cerer-webinar-feature__speakers-list">
					<?php foreach ( $speakers as $speaker ) : ?>
						<div class="cerer-webinar-speaker">
							<span class="cerer-webinar-speaker__photo">
								<?php if ( ! empty( $speaker['photo']['url'] ) ) : ?>
									<img src="<?php echo esc_url( $speaker['photo']['sizes']['thumbnail'] ?? $speaker['photo']['url'] ); ?>" alt="" width="40" height="40" loading="lazy">
								<?php endif; ?>
							</span>
							<span class="cerer-webinar-speaker__text">
								<span class="cerer-webinar-speaker__nom"><?php echo esc_html( $speaker['nom'] ); ?></span>
								<?php
								$fonction_institution = array_filter( array( $speaker['fonction'], $speaker['institution'] ) );
								if ( $fonction_institution ) :
									?>
									<span class="cerer-webinar-speaker__fonction"><?php echo esc_html( implode( ' · ', $fonction_institution ) ); ?></span>
								<?php endif; ?>
							</span>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>

	<div class="cerer-webinar-feature__form">
		<?php get_template_part( 'template-parts/webinar-registration-form', null, array( 'webinaire_id' => $post_id ) ); ?>
	</div>
</div>
