<?php
/**
 * Taxonomies partagées — README §5, dernier paragraphe : "thematique" et
 * "departement" sont rattachées à plusieurs CPT pour que les filtres
 * fonctionnent d'un CPT à l'autre (ex. toutes les publications ET webinaires
 * du département Photovoltaïque).
 *
 * C'est pourquoi "thematique" et "departement", bien que listés dans le
 * détail des champs de certains CPT au §5, ne sont PAS des champs ACF ici :
 * ce sont des taxonomies assignées au post, cf. inc/post-types.php.
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

function cerer_register_taxonomies() {
	// "offre_stage" et "sujet_these" (PROMPT-22, encart "Rejoindre l'équipe")
	// rejoignent la liste pour que leur décompte par département fonctionne.
	$content_types = array( 'publication', 'webinaire', 'projet', 'soutenance', 'formation_session', 'chercheur', 'offre_stage', 'sujet_these' );

	register_taxonomy(
		'thematique',
		$content_types,
		array(
			'label'             => __( 'Thématiques', 'cerer-child' ),
			'labels'            => array(
				'name'          => __( 'Thématiques', 'cerer-child' ),
				'singular_name' => __( 'Thématique', 'cerer-child' ),
			),
			'hierarchical'      => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'thematique' ),
		)
	);

	register_taxonomy(
		'departement',
		$content_types,
		array(
			'label'             => __( 'Départements', 'cerer-child' ),
			'labels'            => array(
				'name'          => __( 'Départements', 'cerer-child' ),
				'singular_name' => __( 'Département', 'cerer-child' ),
			),
			'hierarchical'      => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'departement' ),
		)
	);

	// Catégories d'actualité (PROMPT-14) — taxonomie dédiée sur les articles
	// plutôt que la taxonomie "category" native : chaque terme porte sa
	// propre couleur de badge en champ ACF (acf-json/group_cerer_categorie_actualite.json),
	// ce que "category" ne permet pas sans détourner son sens.
	register_taxonomy(
		'categorie_actualite',
		array( 'post' ),
		array(
			'label'             => __( "Catégories d'actualité", 'cerer-child' ),
			'labels'            => array(
				'name'          => __( "Catégories d'actualité", 'cerer-child' ),
				'singular_name' => __( "Catégorie d'actualité", 'cerer-child' ),
			),
			'hierarchical'      => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'categorie-actualite' ),
		)
	);
}
add_action( 'init', 'cerer_register_taxonomies' );

/**
 * Termes par défaut — README §4.3 (mêmes intitulés que le mega-menu
 * Départements) et §2 (couleurs de thématique). Idempotent.
 */
function cerer_create_default_terms() {
	$thematiques = array( 'Photovoltaïque', 'Biomasse & Bioénergie', 'Éolien', 'Thermique' );
	foreach ( $thematiques as $name ) {
		if ( ! term_exists( $name, 'thematique' ) ) {
			wp_insert_term( $name, 'thematique' );
		}
	}

	$departements = array(
		'Photovoltaïque',
		'Biomasse & Bioénergie',
		'Éolien',
		'Thermique',
	);
	foreach ( $departements as $name ) {
		if ( ! term_exists( $name, 'departement' ) ) {
			wp_insert_term( $name, 'departement' );
		}
	}

	// Catégories d'actualité (PROMPT-14) — couleur de badge posée à part sur
	// chaque terme, cf. inc/actualites-accueil.php.
	$categories_actualite = array(
		'Publication'    => '#1565A3',
		'Partenariat'    => '#14568F',
		'Distinction'    => '#6B47B8',
		'Événement'      => '#4F7A16',
		'Formation'      => '#A85D0A',
		'Vie du centre'  => '#3A4152',
	);
	foreach ( $categories_actualite as $name => $couleur ) {
		$term = term_exists( $name, 'categorie_actualite' );
		if ( ! $term ) {
			$term = wp_insert_term( $name, 'categorie_actualite' );
		}
		$term_id = is_array( $term ) ? $term['term_id'] : $term;
		if ( $term_id && function_exists( 'get_field' ) && ! get_field( 'couleur', 'categorie_actualite_' . $term_id ) ) {
			update_field( 'couleur', $couleur, 'categorie_actualite_' . $term_id );
		}
	}
}
add_action( 'after_switch_theme', 'cerer_create_default_terms' );
