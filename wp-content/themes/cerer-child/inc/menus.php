<?php
/**
 * Emplacements de menu et contenu par défaut.
 *
 * Un seul emplacement "primary" à deux niveaux : l'équipe du CERER gère
 * l'intégralité de la navbar (ordre, libellés, ajout/suppression, et les
 * mega-menus Formations/Départements en ajoutant des sous-items) depuis
 * Apparence → Menus, sans toucher au code. Les deux mega-menus ne sont pas
 * des emplacements séparés : ce sont simplement des items "Formations" et
 * "Départements" avec des enfants dans ce même menu.
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

function cerer_register_menus() {
	register_nav_menus(
		array(
			'primary' => __( 'Menu principal (navbar)', 'cerer-child' ),
			'footer-nav'    => __( 'Footer — Navigation', 'cerer-child' ),
			'footer-formations' => __( 'Footer — Formations & ressources', 'cerer-child' ),
		)
	);
}
add_action( 'after_setup_theme', 'cerer_register_menus' );

/**
 * Crée le menu "Menu principal" avec le contenu exact du handoff (README
 * §4.3) si aucun menu n'est encore assigné à l'emplacement "primary".
 * Idempotent : ne recrée rien si l'équipe du CERER a déjà pris la main.
 */
function cerer_create_default_menu() {
	if ( has_nav_menu( 'primary' ) ) {
		return;
	}

	$menu_name = __( 'Menu principal', 'cerer-child' );
	$menu_id   = wp_create_nav_menu( $menu_name );
	if ( is_wp_error( $menu_id ) ) {
		return;
	}

	/**
	 * Ajoute un item et retourne son ID, pour pouvoir le référencer comme
	 * menu-item-parent-id.
	 */
	$add = function ( $args, $parent_id = 0 ) use ( $menu_id ) {
		$defaults = array(
			'menu-item-title'     => '',
			'menu-item-url'       => '#',
			'menu-item-status'    => 'publish',
			'menu-item-parent-id' => $parent_id,
		);
		return wp_update_nav_menu_item( $menu_id, 0, array_merge( $defaults, $args ) );
	};

	$add_classes = function ( $item_id, $classes ) {
		update_post_meta( $item_id, '_menu_item_classes', $classes );
	};

	// --- Niveau 1 ---
	$add( array( 'menu-item-title' => __( 'Accueil', 'cerer-child' ), 'menu-item-url' => home_url( '/' ) ) );
	$add( array( 'menu-item-title' => __( 'Qui sommes-nous', 'cerer-child' ) ) );

	$formations_id = $add( array( 'menu-item-title' => __( 'Formations', 'cerer-child' ) ) );
	$dept_id       = $add( array( 'menu-item-title' => __( 'Départements', 'cerer-child' ) ) );

	// Onglet à part entière, distinct des départements de recherche : SSIN,
	// Scolarité et Atelier ne sont pas des départements, ce sont les services
	// qui les soutiennent. Lien direct vers la section correspondante de la
	// page Départements plutôt qu'un mega-menu, faute de fiche dédiée par
	// service pour l'instant.
	$add( array( 'menu-item-title' => __( 'Services transversaux', 'cerer-child' ), 'menu-item-url' => home_url( '/departements/#services-transversaux' ) ) );

	$add( array( 'menu-item-title' => __( 'Publications', 'cerer-child' ) ) );
	$add( array( 'menu-item-title' => __( 'Webinaires', 'cerer-child' ) ) );
	$add( array( 'menu-item-title' => __( 'Données & Outils', 'cerer-child' ) ) );
	$add( array( 'menu-item-title' => __( 'Actualités', 'cerer-child' ) ) );
	$add( array( 'menu-item-title' => __( 'Contact', 'cerer-child' ) ) );

	// --- Mega-menu Formations (liste simple + séparateur) ---
	$add( array( 'menu-item-title' => __( 'Licence en Énergie Renouvelable', 'cerer-child' ) ), $formations_id );

	$master_id = $add( array( 'menu-item-title' => __( 'Master en Énergie Renouvelable', 'cerer-child' ) ), $formations_id );
	$add_classes( $master_id, array( 'badge-upcoming' ) );

	$add( array( 'menu-item-title' => __( 'Certifications professionnelles', 'cerer-child' ) ), $formations_id );

	$sep_id = $add( array( 'menu-item-title' => 'separator', 'menu-item-url' => '#' ), $formations_id );
	$add_classes( $sep_id, array( 'menu-separator' ) );

	$add( array( 'menu-item-title' => __( 'Soutenances & mémoires', 'cerer-child' ) ), $formations_id );
	$add( array( 'menu-item-title' => __( 'Offres de stage & emploi', 'cerer-child' ) ), $formations_id );
	$add( array( 'menu-item-title' => __( 'Formation continue', 'cerer-child' ) ), $formations_id );

	// --- Mega-menu Départements : uniquement les 4 départements de
	// recherche. Les services transversaux (SSIN, Scolarité, Atelier) ne
	// sont pas des départements : ils ont leur propre onglet "Services
	// transversaux" au niveau 1, ajouté plus haut, pour ne pas laisser
	// croire que ce sont des départements de recherche.
	$photovoltaique_id = $add( array( 'menu-item-title' => __( 'Photovoltaïque', 'cerer-child' ) ), $dept_id );
	$add_classes( $photovoltaique_id, array( 'dept-dot-green' ) );

	$biomasse_id = $add( array( 'menu-item-title' => __( 'Biomasse & Bioénergie', 'cerer-child' ) ), $dept_id );
	$add_classes( $biomasse_id, array( 'dept-dot-blue2' ) );

	$eolien_id = $add( array( 'menu-item-title' => __( 'Éolien', 'cerer-child' ) ), $dept_id );
	$add_classes( $eolien_id, array( 'dept-dot-blue' ) );

	$thermique_id = $add( array( 'menu-item-title' => __( 'Thermique', 'cerer-child' ) ), $dept_id );
	$add_classes( $thermique_id, array( 'dept-dot-violet' ) );

	$locations = get_theme_mod( 'nav_menu_locations' );
	$locations['primary'] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );
}
add_action( 'after_switch_theme', 'cerer_create_default_menu' );

/**
 * Crée les deux menus de colonnes du footer (README §4.8, colonnes 2 et 3)
 * si aucun menu n'est encore assigné à ces emplacements. Idempotent, comme
 * cerer_create_default_menu() ci-dessus.
 */
function cerer_create_default_footer_menus() {
	$to_create = array();
	if ( ! has_nav_menu( 'footer-nav' ) ) {
		$to_create['footer-nav'] = array(
			'name'  => __( 'Footer — Navigation', 'cerer-child' ),
			'items' => array(
				array( __( 'Accueil', 'cerer-child' ), home_url( '/' ) ),
				array( __( 'Qui sommes-nous', 'cerer-child' ), '#' ),
				array( __( 'Départements', 'cerer-child' ), '#' ),
				array( __( 'Publications', 'cerer-child' ), '#' ),
				array( __( 'Actualités', 'cerer-child' ), '#' ),
				array( __( 'Contact', 'cerer-child' ), '#' ),
			),
		);
	}

	if ( ! has_nav_menu( 'footer-formations' ) ) {
		$to_create['footer-formations'] = array(
			'name'  => __( 'Footer — Formations & ressources', 'cerer-child' ),
			'items' => array(
				array( __( 'Licence ENR', 'cerer-child' ), '#' ),
				array( __( 'Master ENR', 'cerer-child' ), '#' ),
				array( __( 'Certifications', 'cerer-child' ), '#' ),
				array( __( 'Webinaires', 'cerer-child' ), '#' ),
				array( __( 'Données & Outils', 'cerer-child' ), '#' ),
				array( __( 'Soutenances & mémoires', 'cerer-child' ), '#' ),
			),
		);
	}

	if ( ! $to_create ) {
		return;
	}

	$locations = get_theme_mod( 'nav_menu_locations', array() );

	foreach ( $to_create as $location => $menu_def ) {
		$menu_id = wp_create_nav_menu( $menu_def['name'] );
		if ( is_wp_error( $menu_id ) ) {
			continue;
		}
		foreach ( $menu_def['items'] as $item ) {
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'  => $item[0],
					'menu-item-url'    => $item[1],
					'menu-item-status' => 'publish',
				)
			);
		}
		$locations[ $location ] = $menu_id;
	}

	set_theme_mod( 'nav_menu_locations', $locations );
}
add_action( 'after_switch_theme', 'cerer_create_default_footer_menus' );
