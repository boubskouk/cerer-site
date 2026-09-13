<?php
/**
 * Template Name: Contact
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

get_header();

$chapo_page = get_field( 'chapo_page' );
$adresse    = get_field( 'adresse' );
$telephone  = get_field( 'telephone' );
$email      = get_field( 'email' );

$liens_titre = get_field( 'liens_utiles_titre' );
$liens       = array();
for ( $i = 1; $i <= 3; $i++ ) {
	$label = get_field( "liens_utiles_{$i}_label" );
	$url   = get_field( "liens_utiles_{$i}_url" );
	if ( $label && $url ) {
		$liens[] = array( 'label' => $label, 'url' => $url );
	}
}

// Carte Google Maps : un lien "Intégrer une carte" fourni par le client
// prime toujours (repère précis) ; à défaut, dérivée automatiquement de
// l'adresse postale. Le collage habituel depuis Google Maps est le <iframe>
// entier, pas seulement l'URL — on accepte les deux formes.
$maps_override = get_field( 'carte_google_maps_url' );
$maps_src       = '';
if ( $maps_override ) {
	$maps_src = ( preg_match( '/src="([^"]+)"/', $maps_override, $m ) ) ? $m[1] : $maps_override;
} elseif ( $adresse ) {
	$maps_src = 'https://www.google.com/maps?q=' . rawurlencode( $adresse ) . '&output=embed';
}
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
	<div class="cerer-contact-layout">
		<div>
			<h2><?php esc_html_e( 'Envoyez-nous un message', 'cerer-child' ); ?></h2>
			<div style="margin-top:20px;">
				<?php get_template_part( 'template-parts/contact-form' ); ?>
			</div>
		</div>

		<aside class="cerer-contact-sidebar">
			<?php if ( $adresse || $telephone || $email ) : ?>
				<div class="cerer-contact-sidebar__block">
					<h3><?php esc_html_e( 'Coordonnées', 'cerer-child' ); ?></h3>
					<address>
						<?php if ( $adresse ) : ?>
							<span><?php echo esc_html( $adresse ); ?></span>
						<?php endif; ?>
						<?php if ( $telephone ) : ?>
							<span><a href="tel:<?php echo esc_attr( preg_replace( '/[^+0-9]/', '', $telephone ) ); ?>"><?php echo esc_html( $telephone ); ?></a></span>
						<?php endif; ?>
						<?php if ( $email ) : ?>
							<span><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></span>
						<?php endif; ?>
					</address>
				</div>
			<?php endif; ?>

			<?php if ( $liens ) : ?>
				<div class="cerer-contact-sidebar__block">
					<h3><?php echo esc_html( $liens_titre ?: __( 'Vous cherchez plutôt…', 'cerer-child' ) ); ?></h3>
					<ul class="cerer-footer__list" style="margin-top:8px;">
						<?php foreach ( $liens as $lien ) : ?>
							<li><a href="<?php echo esc_url( $lien['url'] ); ?>" style="color:var(--cerer-blue-2);"><?php echo esc_html( $lien['label'] ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
		</aside>
	</div>

	<?php if ( $maps_src ) : ?>
		<div class="cerer-contact-map">
			<iframe
				src="<?php echo esc_url( $maps_src ); ?>"
				loading="lazy"
				referrerpolicy="no-referrer-when-downgrade"
				title="<?php esc_attr_e( 'Localisation du CERER', 'cerer-child' ); ?>"
			></iframe>
		</div>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
