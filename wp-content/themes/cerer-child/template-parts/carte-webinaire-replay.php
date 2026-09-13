<?php
/**
 * Carte replay — section "Les deux derniers webinaires" de l'accueil
 * (PROMPT-13). Toute la carte est un seul <a> externe (target="_blank"),
 * pas de second lien imbriqué.
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

$url = get_field( 'lien_replay', $post_id );
if ( ! $url ) {
	return; // Pas de replay renseigné : rien à afficher, pas de carte vers nulle part.
}

$theme_terms = get_the_terms( $post_id, 'thematique' );
$theme_term  = ( $theme_terms && ! is_wp_error( $theme_terms ) ) ? $theme_terms[0] : null;

$date_brute = get_field( 'date', $post_id );
$date_fmt   = $date_brute ? date_i18n( 'j F Y', strtotime( $date_brute ) ) : '';
$duree      = get_field( 'duree', $post_id );

$miniature     = get_field( 'miniature', $post_id );
$miniature_url = ! empty( $miniature['sizes']['medium'] ) ? $miniature['sizes']['medium'] : ( $miniature['url'] ?? '' );

// PROMPT-17 : champ "miniature" vide -> dérivée de l'URL YouTube et importée
// une seule fois en médiathèque (cerer_yt_cached_thumbnail, inc/about-page.php)
// plutôt que de laisser le bloc en aplat sombre.
if ( ! $miniature_url && $url ) {
	$yt_id = cerer_youtube_id( $url );
	if ( $yt_id ) {
		$miniature_url = cerer_yt_cached_thumbnail( $yt_id );
	}
}

$resume = get_field( 'resume', $post_id );

$speakers = array();
for ( $i = 1; $i <= 3; $i++ ) {
	$nom = get_field( "intervenant_{$i}_nom", $post_id );
	if ( ! $nom ) {
		continue;
	}
	$fonction_institution = array_filter( array( get_field( "intervenant_{$i}_fonction", $post_id ), get_field( "intervenant_{$i}_institution", $post_id ) ) );
	$speakers[]           = $fonction_institution ? $nom . ' — ' . implode( ' · ', $fonction_institution ) : $nom;
}
?>
<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer" class="cerer-webinar-replay">
	<div class="cerer-webinar-replay__thumb">
		<?php if ( $miniature_url ) : ?>
			<img src="<?php echo esc_url( $miniature_url ); ?>" alt="" loading="lazy">
		<?php endif; ?>
		<span class="cerer-webinar-replay__play" aria-hidden="true">
			<span class="cerer-webinar-replay__play-triangle"></span>
		</span>
		<?php if ( $theme_term ) : ?>
			<span class="cerer-webinar-replay__theme"><?php echo esc_html( $theme_term->name ); ?></span>
		<?php endif; ?>
		<?php if ( $duree ) : ?>
			<span class="cerer-webinar-replay__duree"><?php echo esc_html( $duree ); ?></span>
		<?php endif; ?>
	</div>
	<div class="cerer-webinar-replay__body">
		<p class="cerer-webinar-replay__date"><?php echo esc_html( $date_fmt ); ?><?php if ( $date_fmt ) : ?> · YouTube<?php endif; ?></p>
		<h3><?php echo esc_html( get_the_title( $post_id ) ); ?></h3>
		<?php if ( $resume ) : ?>
			<p class="cerer-webinar-replay__resume"><?php echo esc_html( wp_strip_all_tags( $resume ) ); ?></p>
		<?php endif; ?>
		<?php if ( $speakers ) : ?>
			<div class="cerer-webinar-replay__speakers">
				<p class="cerer-webinar-replay__speakers-label"><?php esc_html_e( 'Intervenants', 'cerer-child' ); ?></p>
				<p class="cerer-webinar-replay__speakers-names"><?php echo esc_html( implode( ' · ', $speakers ) ); ?></p>
			</div>
		<?php endif; ?>
		<span class="cerer-webinar-replay__link"><?php esc_html_e( 'Voir le replay', 'cerer-child' ); ?> ↗</span>
	</div>
</a>
