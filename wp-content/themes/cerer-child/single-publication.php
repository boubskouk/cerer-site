<?php
/**
 * Fiche publication (CPT "publication").
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

get_header();

$type_labels = array(
	'article' => __( 'Article scientifique', 'cerer-child' ),
	'rapport' => __( 'Rapport', 'cerer-child' ),
	'actes'   => __( 'Actes de conférence', 'cerer-child' ),
	'these'   => __( 'Thèse', 'cerer-child' ),
);

while ( have_posts() ) :
	the_post();

	$type     = get_field( 'type' ) ?: 'article';
	$auteurs  = get_field( 'auteurs' );
	$revue    = get_field( 'revue' );
	$annee    = get_field( 'annee' );
	$resume   = get_field( 'resume' );
	$lien_pdf = get_field( 'lien_pdf' );
	$doi      = get_field( 'doi' );

	$meta_parts = array_filter( array( $revue, $annee ? (string) $annee : '' ) );
	?>

	<article class="cerer-container cerer-section cerer-single" style="max-width:820px;">
		<p class="cerer-label cerer-eyebrow"><?php echo esc_html( $type_labels[ $type ] ?? $type ); ?></p>
		<h1><?php the_title(); ?></h1>
		<?php if ( $auteurs || $meta_parts ) : ?>
			<p class="cerer-meta" style="margin-top:8px; font-size:15px;">
				<?php
				echo esc_html( $auteurs );
				if ( $auteurs && $meta_parts ) {
					echo ' — ';
				}
				echo esc_html( implode( ', ', $meta_parts ) );
				?>
			</p>
		<?php endif; ?>

		<?php if ( $resume ) : ?>
			<div class="cerer-single__body" style="margin-top:32px;">
				<h2><?php esc_html_e( 'Résumé', 'cerer-child' ); ?></h2>
				<div style="margin-top:12px; font-size:15px; color:var(--cerer-ink-2);"><?php echo wp_kses_post( $resume ); ?></div>
			</div>
		<?php endif; ?>

		<?php if ( $lien_pdf || $doi ) : ?>
			<div class="cerer-card__actions" style="margin-top:32px;">
				<?php if ( $lien_pdf ) : ?>
					<?php cerer_external_link( $lien_pdf, __( 'Télécharger le PDF', 'cerer-child' ), array( 'class' => 'cerer-btn cerer-btn--primary' ) ); ?>
				<?php endif; ?>
				<?php if ( $doi ) : ?>
					<span class="cerer-meta">DOI : <?php echo esc_html( $doi ); ?></span>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<p style="margin-top:40px;">
			<a href="<?php echo esc_url( get_post_type_archive_link( 'publication' ) ); ?>" class="cerer-btn cerer-btn--link">← <?php esc_html_e( 'Toutes les publications', 'cerer-child' ); ?></a>
		</p>
	</article>

	<?php
endwhile;

get_footer();
