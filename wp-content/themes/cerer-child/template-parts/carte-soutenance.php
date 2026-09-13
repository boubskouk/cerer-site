<?php
/**
 * Carte soutenance — archive du CPT "soutenance".
 *
 * Même forme que carte-publication.php (.cerer-card--document, archives.css) :
 * une soutenance est aussi une référence bibliographique, juste avec d'autres
 * métadonnées (niveau, directeur, mention) et d'autres liens externes
 * (mémoire, vidéo) — README §6.1.
 *
 * Usage :
 *   get_template_part( 'template-parts/carte-soutenance', null, array(
 *       'title'        => get_the_title(),
 *       'url'          => get_permalink(),
 *       'niveau'       => get_field( 'niveau' ), // licence|master|certification
 *       'auteur'       => get_field( 'auteur' ),
 *       'annee'        => get_field( 'annee' ),
 *       'directeur'    => get_field( 'directeur' ),
 *       'mention'      => get_field( 'mention' ),
 *       'lien_memoire' => get_field( 'lien_memoire' ),
 *       'lien_video'   => get_field( 'lien_video' ),
 *   ) );
 *
 * @package cerer-child
 * @var array $args
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args(
	$args ?? array(),
	array(
		'title'        => '',
		'url'          => '#',
		'niveau'       => 'licence',
		'auteur'       => '',
		'annee'        => '',
		'directeur'    => '',
		'mention'      => '',
		'lien_memoire' => '',
		'lien_video'   => '',
	)
);

// Mêmes libellés que les champs ACF "niveau" et "mention" (group_cerer_soutenance).
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

$meta_parts = array_filter(
	array(
		$args['annee'] ? (string) $args['annee'] : '',
		$args['directeur'] ? sprintf( __( 'dir. %s', 'cerer-child' ), $args['directeur'] ) : '',
		$mention_labels[ $args['mention'] ] ?? '',
	)
);
?>
<article class="cerer-card cerer-card--document">
	<p class="cerer-label cerer-eyebrow"><?php echo esc_html( $niveau_labels[ $args['niveau'] ] ?? $args['niveau'] ); ?></p>
	<h3><a href="<?php echo esc_url( $args['url'] ); ?>"><?php echo esc_html( $args['title'] ); ?></a></h3>
	<?php if ( $args['auteur'] || $meta_parts ) : ?>
		<p class="cerer-meta cerer-card__meta">
			<?php
			echo esc_html( $args['auteur'] );
			if ( $args['auteur'] && $meta_parts ) {
				echo ' — ';
			}
			echo esc_html( implode( ' · ', $meta_parts ) );
			?>
		</p>
	<?php endif; ?>
	<?php if ( $args['lien_memoire'] || $args['lien_video'] ) : ?>
		<div class="cerer-card__actions">
			<?php if ( $args['lien_memoire'] ) : ?>
				<?php cerer_external_link( $args['lien_memoire'], __( 'Consulter le mémoire', 'cerer-child' ), array( 'class' => 'cerer-btn cerer-btn--tertiary cerer-btn--s' ) ); ?>
			<?php endif; ?>
			<?php if ( $args['lien_video'] ) : ?>
				<?php cerer_external_link( $args['lien_video'], __( 'Voir la vidéo', 'cerer-child' ), array( 'class' => 'cerer-btn cerer-btn--tertiary cerer-btn--s' ) ); ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</article>
