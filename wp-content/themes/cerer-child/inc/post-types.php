<?php
/**
 * Custom Post Types — README §5.
 *
 * "titre"/"nom" ne sont volontairement pas des champs ACF : ce sont le
 * titre natif de l'article (post_title) pour chaque CPT. Les champs
 * ACF réels sont déclarés séparément dans acf-json/ (cf. Prompt 5 point 2).
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

function cerer_register_post_types() {

	register_post_type(
		'publication',
		array(
			'label'        => __( 'Publications', 'cerer-child' ),
			'labels'       => array(
				'name'          => __( 'Publications', 'cerer-child' ),
				'singular_name' => __( 'Publication', 'cerer-child' ),
				'add_new_item'  => __( 'Ajouter une publication', 'cerer-child' ),
				'edit_item'     => __( 'Modifier la publication', 'cerer-child' ),
			),
			'public'       => true,
			'has_archive'  => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-media-document',
			'supports'     => array( 'title', 'thumbnail' ),
			'rewrite'      => array( 'slug' => 'publications' ),
		)
	);

	register_post_type(
		'webinaire',
		array(
			'label'        => __( 'Webinaires', 'cerer-child' ),
			'labels'       => array(
				'name'          => __( 'Webinaires', 'cerer-child' ),
				'singular_name' => __( 'Webinaire', 'cerer-child' ),
				'add_new_item'  => __( 'Ajouter un webinaire', 'cerer-child' ),
				'edit_item'     => __( 'Modifier le webinaire', 'cerer-child' ),
			),
			'public'       => true,
			'has_archive'  => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-video-alt3',
			'supports'     => array( 'title', 'thumbnail' ),
			'rewrite'      => array( 'slug' => 'webinaires' ),
		)
	);

	register_post_type(
		'projet',
		array(
			'label'        => __( 'Projets', 'cerer-child' ),
			'labels'       => array(
				'name'          => __( 'Projets', 'cerer-child' ),
				'singular_name' => __( 'Projet', 'cerer-child' ),
				'add_new_item'  => __( 'Ajouter un projet', 'cerer-child' ),
				'edit_item'     => __( 'Modifier le projet', 'cerer-child' ),
			),
			'public'       => true,
			'has_archive'  => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-chart-line',
			'supports'     => array( 'title', 'thumbnail' ),
			'rewrite'      => array( 'slug' => 'projets' ),
		)
	);

	register_post_type(
		'soutenance',
		array(
			'label'        => __( 'Soutenances', 'cerer-child' ),
			'labels'       => array(
				'name'          => __( 'Soutenances', 'cerer-child' ),
				'singular_name' => __( 'Soutenance', 'cerer-child' ),
				'add_new_item'  => __( 'Ajouter une soutenance', 'cerer-child' ),
				'edit_item'     => __( 'Modifier la soutenance', 'cerer-child' ),
			),
			'public'       => true,
			'has_archive'  => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-awards',
			'supports'     => array( 'title' ),
			'rewrite'      => array( 'slug' => 'soutenances' ),
		)
	);

	register_post_type(
		'formation_session',
		array(
			'label'        => __( 'Sessions de formation', 'cerer-child' ),
			'labels'       => array(
				'name'          => __( 'Sessions de formation', 'cerer-child' ),
				'singular_name' => __( 'Session de formation', 'cerer-child' ),
				'add_new_item'  => __( 'Ajouter une session', 'cerer-child' ),
				'edit_item'     => __( 'Modifier la session', 'cerer-child' ),
			),
			'public'       => true,
			'has_archive'  => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-calendar-alt',
			'supports'     => array( 'title' ),
			'rewrite'      => array( 'slug' => 'formation-continue' ),
		)
	);

	register_post_type(
		'chercheur',
		array(
			'label'        => __( 'Chercheurs', 'cerer-child' ),
			'labels'       => array(
				'name'          => __( 'Chercheurs', 'cerer-child' ),
				'singular_name' => __( 'Chercheur', 'cerer-child' ),
				'add_new_item'  => __( 'Ajouter un chercheur', 'cerer-child' ),
				'edit_item'     => __( 'Modifier le chercheur', 'cerer-child' ),
			),
			'public'       => true,
			'has_archive'  => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-admin-users',
			// "page-attributes" ajoute le champ numérique "Ordre" dans l'admin :
			// c'est ainsi que le client réordonne l'équipe affichée sur
			// "Qui sommes-nous" (PROMPT-15), faute de glisser-déposer natif.
			'supports'     => array( 'title', 'thumbnail', 'page-attributes' ),
			'rewrite'      => array( 'slug' => 'chercheurs' ),
		)
	);

	// Partenaires de la section "Nos partenaires" de l'accueil — remplace un
	// champ répétable sur une page d'options (Repeater + Options Page sont
	// des fonctionnalités ACF Pro, or seule la version gratuite est
	// installée ici, cf. front-page.php). Un CPT + un groupe de champs ACF
	// classique reste gérable depuis l'admin sans toucher au code, comme le
	// reste du site.
	register_post_type(
		'partenaire',
		array(
			'label'        => __( 'Partenaires', 'cerer-child' ),
			'labels'       => array(
				'name'          => __( 'Partenaires', 'cerer-child' ),
				'singular_name' => __( 'Partenaire', 'cerer-child' ),
				'add_new_item'  => __( 'Ajouter un partenaire', 'cerer-child' ),
				'edit_item'     => __( 'Modifier le partenaire', 'cerer-child' ),
			),
			'public'       => true,
			'has_archive'  => false,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-groups',
			'supports'     => array( 'title' ),
			'rewrite'      => array( 'slug' => 'partenaires' ),
		)
	);

	// Programmes de la page "Formations" — remplace un tableau codé en dur
	// dans page-templates/formations.php (demande client : "je veux pouvoir
	// tout modifier moi-même"). "page-attributes" ajoute le champ "Ordre"
	// pour que le client réordonne les programmes depuis l'admin.
	register_post_type(
		'programme',
		array(
			'label'        => __( 'Programmes de formation', 'cerer-child' ),
			'labels'       => array(
				'name'          => __( 'Programmes de formation', 'cerer-child' ),
				'singular_name' => __( 'Programme', 'cerer-child' ),
				'add_new_item'  => __( 'Ajouter un programme', 'cerer-child' ),
				'edit_item'     => __( 'Modifier le programme', 'cerer-child' ),
			),
			'public'       => true,
			'has_archive'  => false,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-welcome-learn-more',
			'supports'     => array( 'title', 'page-attributes' ),
			'rewrite'      => array( 'slug' => 'programmes' ),
		)
	);

	// Ressources de la page "Données & Outils" — même logique que "programme"
	// ci-dessus : remplace un tableau codé en dur.
	register_post_type(
		'ressource',
		array(
			'label'        => __( 'Ressources (Données & Outils)', 'cerer-child' ),
			'labels'       => array(
				'name'          => __( 'Ressources', 'cerer-child' ),
				'singular_name' => __( 'Ressource', 'cerer-child' ),
				'add_new_item'  => __( 'Ajouter une ressource', 'cerer-child' ),
				'edit_item'     => __( 'Modifier la ressource', 'cerer-child' ),
			),
			'public'       => true,
			'has_archive'  => false,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-chart-area',
			'supports'     => array( 'title', 'page-attributes' ),
			'rewrite'      => array( 'slug' => 'ressources' ),
		)
	);

	// Vidéos de la page "Qui sommes-nous" (galerie filtrable) — README
	// n'en parlait pas en Phase 1, ajouté sur demande pour rester gérable
	// depuis l'admin (ACF) sans toucher au code.
	register_post_type(
		'video',
		array(
			'label'        => __( 'Vidéos', 'cerer-child' ),
			'labels'       => array(
				'name'          => __( 'Vidéos', 'cerer-child' ),
				'singular_name' => __( 'Vidéo', 'cerer-child' ),
				'add_new_item'  => __( 'Ajouter une vidéo', 'cerer-child' ),
				'edit_item'     => __( 'Modifier la vidéo', 'cerer-child' ),
			),
			'public'       => true,
			'has_archive'  => false,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-video-alt2',
			'supports'     => array( 'title', 'page-attributes' ),
			'rewrite'      => array( 'slug' => 'videos' ),
		)
	);
}
add_action( 'init', 'cerer_register_post_types' );
