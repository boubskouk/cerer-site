<?php
/**
 * Template Name: Stage & emploi
 *
 * Deux blocs : offres d'emploi (0 à 3, ACF — état vide géré explicitement
 * tant que le CERER n'a aucune offre à publier) puis formulaire de demande
 * de stage (Administration, départements, SSIN).
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

get_header();

$chapo_page = get_field( 'chapo_page' );

$offres_titre        = get_field( 'offres_titre' );
$offres_vide_message = get_field( 'offres_vide_message' );

$offres = array();
for ( $i = 1; $i <= 3; $i++ ) {
	$titre = get_field( "offre_{$i}_titre" );
	if ( ! $titre ) {
		continue;
	}
	$offres[] = array(
		'titre'        => $titre,
		'type'         => get_field( "offre_{$i}_type" ),
		'description'  => get_field( "offre_{$i}_description" ),
		'date_limite'  => get_field( "offre_{$i}_date_limite" ),
		'lien'         => get_field( "offre_{$i}_lien" ),
	);
}

$stage_titre = get_field( 'stage_titre' );
$stage_texte = get_field( 'stage_texte' );
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
	<h2><?php echo esc_html( $offres_titre ?: __( 'Offres actuelles', 'cerer-child' ) ); ?></h2>

	<?php if ( $offres ) : ?>
		<div class="cerer-card-grid" style="margin-top:24px;">
			<?php foreach ( $offres as $offre ) : ?>
				<?php
				$meta = array_filter( array( $offre['type'], $offre['date_limite'] ? sprintf( __( 'Limite : %s', 'cerer-child' ), $offre['date_limite'] ) : '' ) );
				// À défaut d'un lien de candidature saisi, le bouton "Postuler"
				// retombe sur un mailto pré-rempli plutôt que de pointer vers "#"
				// (carte-formation.php affiche toujours son bouton d'action).
				$offre_cta_url = $offre['lien'] ?: ( 'mailto:' . cerer_notification_email() . '?subject=' . rawurlencode( 'Candidature : ' . $offre['titre'] ) );
				get_template_part(
					'template-parts/carte-formation',
					null,
					array(
						'title'       => $offre['titre'],
						'meta'        => implode( ' · ', $meta ),
						'description' => $offre['description'],
						'cta_label'   => __( 'Postuler', 'cerer-child' ),
						'cta_url'     => $offre_cta_url,
					)
				);
				?>
			<?php endforeach; ?>
		</div>
	<?php else : ?>
		<p class="cerer-empty-note" style="margin-top:20px;">
			<?php echo esc_html( $offres_vide_message ?: __( "Aucune offre d'emploi n'est actuellement disponible.", 'cerer-child' ) ); ?>
		</p>
	<?php endif; ?>

</div>

<section class="cerer-section cerer-section--tint">
	<div class="cerer-container">
		<h2><?php echo esc_html( $stage_titre ?: __( 'Faire une demande de stage', 'cerer-child' ) ); ?></h2>
		<?php if ( $stage_texte ) : ?>
			<p style="margin-top:8px;max-width:640px;"><?php echo esc_html( $stage_texte ); ?></p>
		<?php endif; ?>

		<div class="cerer-contact-layout" style="margin-top:32px;">
			<div>
				<?php get_template_part( 'template-parts/stage-form' ); ?>
			</div>

			<aside class="cerer-contact-sidebar">
				<div class="cerer-contact-sidebar__block">
					<h3><?php esc_html_e( 'Comment ça marche ?', 'cerer-child' ); ?></h3>
					<ol class="cerer-contact-sidebar__steps">
						<li><?php esc_html_e( 'Vous choisissez la filière qui vous intéresse et envoyez le formulaire.', 'cerer-child' ); ?></li>
						<li><?php esc_html_e( 'Le service concerné étudie votre profil.', 'cerer-child' ); ?></li>
						<li><?php esc_html_e( 'Nous vous recontactons si une opportunité correspond.', 'cerer-child' ); ?></li>
					</ol>
				</div>

				<div class="cerer-contact-sidebar__block">
					<h3><?php esc_html_e( 'Nous contacter directement', 'cerer-child' ); ?></h3>
					<address>
						<span><a href="mailto:<?php echo esc_attr( cerer_notification_email() ); ?>"><?php echo esc_html( cerer_notification_email() ); ?></a></span>
						<span><a href="https://wa.me/221765772252" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'WhatsApp : +221 76 577 22 52', 'cerer-child' ); ?></a></span>
					</address>
				</div>
			</aside>
		</div>
	</div>
</section>

<?php get_footer(); ?>
