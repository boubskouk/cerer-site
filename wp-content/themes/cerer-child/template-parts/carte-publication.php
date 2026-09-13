<?php
/**
 * Carte publication — archive du CPT "publication".
 *
 * Pas de visuel (une référence bibliographique n'en a pas) : étiquette de
 * type, titre, auteurs/revue/année, résumé, puis les liens externes (PDF,
 * DOI) via cerer_external_link() — README §6.1.
 *
 * Utilise la classe .cerer-card--document (archives.css), partagée avec
 * carte-soutenance.php : même forme de carte pour toute référence
 * bibliographique (étiquette + titre + métadonnées + liens externes).
 *
 * Usage :
 *   get_template_part( 'template-parts/carte-publication', null, array(
 *       'title'   => get_the_title(),
 *       'url'     => get_permalink(),
 *       'type'    => get_field( 'type' ),   // article|rapport|actes|these
 *       'auteurs' => get_field( 'auteurs' ),
 *       'revue'   => get_field( 'revue' ),
 *       'annee'   => get_field( 'annee' ),
 *       'resume'  => get_field( 'resume' ),
 *       'lien_pdf'=> get_field( 'lien_pdf' ),
 *       'doi'     => get_field( 'doi' ),
 *   ) );
 *
 * @package cerer-child
 * @var array $args
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args(
	$args ?? array(),
	array(
		'title'    => '',
		'url'      => '#',
		'type'     => 'article',
		'auteurs'  => '',
		'revue'    => '',
		'annee'    => '',
		'resume'   => '',
		'lien_pdf' => '',
		'doi'      => '',
	)
);

// Mêmes libellés que le champ ACF "type" (group_cerer_publication).
$type_labels = array(
	'article' => __( 'Article scientifique', 'cerer-child' ),
	'rapport' => __( 'Rapport', 'cerer-child' ),
	'actes'   => __( 'Actes de conférence', 'cerer-child' ),
	'these'   => __( 'Thèse', 'cerer-child' ),
);

$meta_parts = array_filter( array( $args['revue'], $args['annee'] ? (string) $args['annee'] : '' ) );
?>
<article class="cerer-card cerer-card--document">
	<p class="cerer-label cerer-eyebrow"><?php echo esc_html( $type_labels[ $args['type'] ] ?? $args['type'] ); ?></p>
	<h3><a href="<?php echo esc_url( $args['url'] ); ?>"><?php echo esc_html( $args['title'] ); ?></a></h3>
	<?php if ( $args['auteurs'] || $meta_parts ) : ?>
		<p class="cerer-meta cerer-card__meta">
			<?php
			echo esc_html( $args['auteurs'] );
			if ( $args['auteurs'] && $meta_parts ) {
				echo ' — ';
			}
			echo esc_html( implode( ', ', $meta_parts ) );
			?>
		</p>
	<?php endif; ?>
	<?php if ( $args['resume'] ) : ?>
		<p class="cerer-card__description"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( $args['resume'] ), 28 ) ); ?></p>
	<?php endif; ?>
	<?php if ( $args['lien_pdf'] || $args['doi'] ) : ?>
		<div class="cerer-card__actions">
			<?php if ( $args['lien_pdf'] ) : ?>
				<?php cerer_external_link( $args['lien_pdf'], __( 'Télécharger le PDF', 'cerer-child' ), array( 'class' => 'cerer-btn cerer-btn--tertiary cerer-btn--s' ) ); ?>
			<?php endif; ?>
			<?php if ( $args['doi'] ) : ?>
				<span class="cerer-meta">DOI : <?php echo esc_html( $args['doi'] ); ?></span>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</article>
