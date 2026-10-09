<?php
/**
 * Carte "publication" vide — un type de publication pas encore représenté,
 * affichée tant que le CERER n'a encore renseigné aucune publication
 * réelle (archive-publication.php). Pas de lien : rien à montrer, juste le
 * type de contenu attendu à cet emplacement.
 *
 * Usage :
 *   get_template_part( 'template-parts/carte-publication-vide', null, array(
 *       'label'       => 'Article scientifique',
 *       'description' => '...',
 *   ) );
 *
 * @package cerer-child
 * @var array $args
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args(
	$args ?? array(),
	array(
		'label'       => '',
		'description' => '',
	)
);
?>
<article class="cerer-card cerer-card--document cerer-card--empty">
	<p class="cerer-label cerer-eyebrow"><?php echo esc_html( $args['label'] ); ?></p>
	<p class="cerer-card__description"><?php echo esc_html( $args['description'] ); ?></p>
</article>
