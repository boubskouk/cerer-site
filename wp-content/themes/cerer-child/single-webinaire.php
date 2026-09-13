<?php
/**
 * Fiche webinaire (CPT "webinaire").
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$statut      = get_field( 'statut' ) ?: 'a_venir';
	$intervenant = get_field( 'intervenant' );
	$date_brute  = get_field( 'date' );
	$date_fmt    = $date_brute ? date_i18n( 'j F Y — H\hi', strtotime( $date_brute ) ) : '';
	$plateforme  = get_field( 'plateforme' );
	$duree       = get_field( 'duree' );
	$inscription = get_field( 'lien_inscription' );
	$replay      = get_field( 'lien_replay' );
	$a_venir     = 'a_venir' === $statut;
	?>

	<article class="cerer-container cerer-section cerer-single" style="max-width:820px;">
		<?php get_template_part( 'template-parts/badge', null, array( 'kind' => 'status', 'variant' => $a_venir ? 'inscriptions-ouvertes' : 'termine' ) ); ?>
		<h1 style="margin-top:12px;"><?php the_title(); ?></h1>

		<p class="cerer-meta" style="margin-top:8px; font-size:15px;">
			<?php
			echo esc_html( trim( $date_fmt . ( $plateforme ? ' · ' . $plateforme : '' ) . ( $duree ? ' · ' . $duree : '' ), ' ·' ) );
			?>
		</p>

		<?php if ( $intervenant ) : ?>
			<p style="margin-top:16px; font-size:15px;"><?php echo esc_html( sprintf( __( 'Avec %s', 'cerer-child' ), $intervenant ) ); ?></p>
		<?php endif; ?>

		<div class="cerer-card__actions" style="margin-top:32px;">
			<?php if ( $a_venir && $inscription ) : ?>
				<?php cerer_external_link( $inscription, __( "S'inscrire", 'cerer-child' ), array( 'class' => 'cerer-btn cerer-btn--primary' ) ); ?>
			<?php elseif ( $replay ) : ?>
				<?php cerer_external_link( $replay, __( 'Voir le replay', 'cerer-child' ), array( 'class' => 'cerer-btn cerer-btn--primary' ) ); ?>
			<?php else : ?>
				<span class="cerer-badge cerer-badge--a-venir"><?php esc_html_e( 'Replay bientôt disponible', 'cerer-child' ); ?></span>
			<?php endif; ?>
		</div>

		<p style="margin-top:40px;">
			<a href="<?php echo esc_url( get_post_type_archive_link( 'webinaire' ) ); ?>" class="cerer-btn cerer-btn--link">← <?php esc_html_e( 'Tous les webinaires', 'cerer-child' ); ?></a>
		</p>
	</article>

	<?php
endwhile;

get_footer();
