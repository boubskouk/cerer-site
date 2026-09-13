<?php
/**
 * Fiche soutenance (CPT "soutenance").
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

get_header();

$niveau_labels = array(
	'licence'       => __( 'Licence', 'cerer-child' ),
	'master'        => __( 'Master', 'cerer-child' ),
	'certification' => __( 'Certification', 'cerer-child' ),
);
$mention_labels = array(
	'passable'   => __( 'Passable', 'cerer-child' ),
	'assez_bien' => __( 'Assez bien', 'cerer-child' ),
	'bien'       => __( 'Bien', 'cerer-child' ),
	'tres_bien'  => __( 'Très bien', 'cerer-child' ),
	'excellent'  => __( 'Excellent', 'cerer-child' ),
);

while ( have_posts() ) :
	the_post();

	$niveau       = get_field( 'niveau' ) ?: 'licence';
	$auteur       = get_field( 'auteur' );
	$annee        = get_field( 'annee' );
	$directeur    = get_field( 'directeur' );
	$mention      = get_field( 'mention' );
	$lien_memoire = get_field( 'lien_memoire' );
	$lien_video   = get_field( 'lien_video' );

	$meta_parts = array_filter(
		array(
			$annee ? (string) $annee : '',
			$directeur ? sprintf( __( 'dir. %s', 'cerer-child' ), $directeur ) : '',
			$mention_labels[ $mention ] ?? '',
		)
	);
	?>

	<article class="cerer-container cerer-section cerer-single" style="max-width:820px;">
		<p class="cerer-label cerer-eyebrow"><?php echo esc_html( $niveau_labels[ $niveau ] ?? $niveau ); ?></p>
		<h1><?php the_title(); ?></h1>
		<?php if ( $auteur || $meta_parts ) : ?>
			<p class="cerer-meta" style="margin-top:8px; font-size:15px;">
				<?php
				echo esc_html( $auteur );
				if ( $auteur && $meta_parts ) {
					echo ' — ';
				}
				echo esc_html( implode( ' · ', $meta_parts ) );
				?>
			</p>
		<?php endif; ?>

		<?php if ( $lien_memoire || $lien_video ) : ?>
			<div class="cerer-card__actions" style="margin-top:32px;">
				<?php if ( $lien_memoire ) : ?>
					<?php cerer_external_link( $lien_memoire, __( 'Consulter le mémoire', 'cerer-child' ), array( 'class' => 'cerer-btn cerer-btn--primary' ) ); ?>
				<?php endif; ?>
				<?php if ( $lien_video ) : ?>
					<?php cerer_external_link( $lien_video, __( 'Voir la vidéo', 'cerer-child' ), array( 'class' => 'cerer-btn cerer-btn--secondary' ) ); ?>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<p style="margin-top:40px;">
			<a href="<?php echo esc_url( get_post_type_archive_link( 'soutenance' ) ); ?>" class="cerer-btn cerer-btn--link">← <?php esc_html_e( 'Toutes les soutenances', 'cerer-child' ); ?></a>
		</p>
	</article>

	<?php
endwhile;

get_footer();
