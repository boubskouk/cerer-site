<?php
/**
 * Block patterns Gutenberg — équivalents visuels des partials PHP pour que
 * la rédaction (actualités, formations, départements) se fasse sans code,
 * directement dans l'éditeur. Construits uniquement avec des blocs core
 * stables (group/heading/paragraph) : pas de bloc image/bouton vide, pour
 * éviter tout risque de "contenu invalide" à l'insertion.
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

function cerer_register_block_patterns() {
	register_block_pattern_category(
		'cerer',
		array( 'label' => __( 'CERER', 'cerer-child' ) )
	);

	register_block_pattern(
		'cerer/carte-actualite',
		array(
			'title'       => __( 'CERER : Carte actualité', 'cerer-child' ),
			'description' => __( 'Visuel 16:9 (placeholder), tag de type, titre, date — README §4.6.', 'cerer-child' ),
			'categories'  => array( 'cerer' ),
			'content'     =>
				'<!-- wp:group {"className":"cerer-card cerer-card--news"} -->
<div class="wp-block-group cerer-card cerer-card--news">
<!-- wp:group {"className":"cerer-card__media is-placeholder"} -->
<div class="wp-block-group cerer-card__media is-placeholder"></div>
<!-- /wp:group -->

<!-- wp:group {"className":"cerer-card__body"} -->
<div class="wp-block-group cerer-card__body">
<!-- wp:paragraph {"className":"cerer-tag cerer-tag--publication"} -->
<p class="cerer-tag cerer-tag--publication">' . esc_html__( 'Publication', 'cerer-child' ) . '</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">' . esc_html__( "Titre de l'actualité", 'cerer-child' ) . '</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"cerer-card__date"} -->
<p class="cerer-card__date">12 septembre 2026</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->',
		)
	);

	register_block_pattern(
		'cerer/carte-departement',
		array(
			'title'       => __( 'CERER : Carte département', 'cerer-child' ),
			'description' => __( 'Liseré coloré + pastille icône — README §4.6. Change la classe "cerer-card--solaire/biomasse/eolien/efficacite" dans les réglages avancés du bloc pour changer la couleur.', 'cerer-child' ),
			'categories'  => array( 'cerer' ),
			'content'     =>
				'<!-- wp:group {"className":"cerer-card cerer-card--theme cerer-card--solaire"} -->
<div class="wp-block-group cerer-card cerer-card--theme cerer-card--solaire">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">' . esc_html__( 'Photovoltaïque', 'cerer-child' ) . '</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>' . esc_html__( 'Description du département…', 'cerer-child' ) . '</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->',
		)
	);

	register_block_pattern(
		'cerer/grille-cartes',
		array(
			'title'       => __( 'CERER : Grille de cartes (3 colonnes)', 'cerer-child' ),
			'description' => __( 'repeat(auto-fit, minmax(260px,1fr)) — README §4.6. Insère 3 cartes vides à dupliquer/adapter.', 'cerer-child' ),
			'categories'  => array( 'cerer' ),
			'content'     =>
				'<!-- wp:group {"className":"cerer-card-grid"} -->
<div class="wp-block-group cerer-card-grid">
<!-- wp:group {"className":"cerer-card"} -->
<div class="wp-block-group cerer-card">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">' . esc_html__( 'Titre', 'cerer-child' ) . '</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>' . esc_html__( 'Description…', 'cerer-child' ) . '</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"cerer-card"} -->
<div class="wp-block-group cerer-card">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">' . esc_html__( 'Titre', 'cerer-child' ) . '</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>' . esc_html__( 'Description…', 'cerer-child' ) . '</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"cerer-card"} -->
<div class="wp-block-group cerer-card">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">' . esc_html__( 'Titre', 'cerer-child' ) . '</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>' . esc_html__( 'Description…', 'cerer-child' ) . '</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->',
		)
	);
}
add_action( 'init', 'cerer_register_block_patterns' );
