<?php
/**
 * cerer-child — fonctions du thème enfant.
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

require_once get_stylesheet_directory() . '/inc/class-cerer-nav-walker.php';
require_once get_stylesheet_directory() . '/inc/menus.php';
require_once get_stylesheet_directory() . '/inc/block-patterns.php';
require_once get_stylesheet_directory() . '/inc/newsletter.php';
require_once get_stylesheet_directory() . '/inc/helpers.php';
require_once get_stylesheet_directory() . '/inc/post-types.php';
require_once get_stylesheet_directory() . '/inc/taxonomies.php';
require_once get_stylesheet_directory() . '/inc/about-videos.php';
require_once get_stylesheet_directory() . '/inc/about-page.php';
require_once get_stylesheet_directory() . '/inc/contact-form.php';
require_once get_stylesheet_directory() . '/inc/stage-form.php';
require_once get_stylesheet_directory() . '/inc/webinar-registration-form.php';
require_once get_stylesheet_directory() . '/inc/actualites-accueil.php';
require_once get_stylesheet_directory() . '/inc/auto-translate.php';

/**
 * Taille d'image dédiée au diaporama du hero (PROMPT-9 point 5) : "large"
 * (1024px) étiré sur un hero pleine largeur (jusqu'à ~1900px) rendait la
 * photo réellement floue, indépendamment du voile. 2400px de large couvre
 * les écrans desktop les plus larges sans agrandissement.
 */
function cerer_add_image_sizes() {
	add_image_size( 'hero_slide', 2400, 1200, true );
}
add_action( 'after_setup_theme', 'cerer_add_image_sizes' );

/**
 * Version de cache-busting basée sur la date de modification du fichier :
 * pas besoin de bumper un numéro de version à la main pendant le dev, et le
 * navigateur revalide dès qu'un fichier CSS/JS change.
 *
 * @param string $relative_path Chemin relatif à la racine du thème enfant.
 * @return string
 */
function cerer_asset_version( $relative_path ) {
	$full_path = get_stylesheet_directory() . '/' . ltrim( $relative_path, '/' );
	return file_exists( $full_path ) ? (string) filemtime( $full_path ) : wp_get_theme()->get( 'Version' );
}

/**
 * Enqueue de la feuille parente puis des feuilles enfant, dans l'ordre :
 * tokens (variables) → fonts (@font-face self-hostées) → base (reset,
 * typographie, conteneur, rythme). components.css viendra en Prompt 3.
 */
function cerer_enqueue_assets() {
	// Feuille parente Blocksy.
	wp_enqueue_style(
		'blocksy-parent-style',
		get_template_directory_uri() . '/style.css',
		array(),
		cerer_asset_version_parent()
	);

	// Feuilles enfant CERER, chacune avec sa propre dépendance en chaîne
	// pour garantir l'ordre de chargement.
	wp_enqueue_style(
		'cerer-tokens',
		get_stylesheet_directory_uri() . '/assets/css/tokens.css',
		array( 'blocksy-parent-style' ),
		cerer_asset_version( 'assets/css/tokens.css' )
	);

	wp_enqueue_style(
		'cerer-fonts',
		get_stylesheet_directory_uri() . '/assets/css/fonts.css',
		array( 'cerer-tokens' ),
		cerer_asset_version( 'assets/css/fonts.css' )
	);

	wp_enqueue_style(
		'cerer-base',
		get_stylesheet_directory_uri() . '/assets/css/base.css',
		array( 'cerer-fonts' ),
		cerer_asset_version( 'assets/css/base.css' )
	);

	wp_enqueue_style(
		'cerer-header',
		get_stylesheet_directory_uri() . '/assets/css/header.css',
		array( 'cerer-base' ),
		cerer_asset_version( 'assets/css/header.css' )
	);

	wp_enqueue_style(
		'cerer-components',
		get_stylesheet_directory_uri() . '/assets/css/components.css',
		array( 'cerer-header' ),
		cerer_asset_version( 'assets/css/components.css' )
	);

	// Feuille de style du thème enfant lui-même (style.css) — vide pour
	// l'instant (voir commentaire dans le fichier), enqueue par convention
	// WordPress pour que le Customizer et wp_get_theme() la voient.
	wp_enqueue_style(
		'cerer-child-style',
		get_stylesheet_uri(),
		array( 'cerer-components' ),
		cerer_asset_version( 'style.css' )
	);

	wp_enqueue_script(
		'cerer-header',
		get_stylesheet_directory_uri() . '/assets/js/header.js',
		array(),
		cerer_asset_version( 'assets/js/header.js' ),
		true
	);

	wp_enqueue_style(
		'cerer-footer',
		get_stylesheet_directory_uri() . '/assets/css/footer.css',
		array( 'cerer-components' ),
		cerer_asset_version( 'assets/css/footer.css' )
	);

	wp_enqueue_script(
		'cerer-footer',
		get_stylesheet_directory_uri() . '/assets/js/footer.js',
		array(),
		cerer_asset_version( 'assets/js/footer.js' ),
		true
	);

	wp_localize_script(
		'cerer-footer',
		'cererNewsletter',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'cerer_newsletter_subscribe' ),
		)
	);

	// Chargé sur "Qui sommes-nous" et tout gabarit qui en reprend le système
	// visuel (en-tête bleu, sections en fond alterné) — cf.
	// page-templates/page-generique.php, demandé pour les nouvelles pages
	// (programmes de formation…) sans dupliquer le CSS. L'archive Webinaires
	// (/webinaires/) et les archives de département (/departement/xxx/,
	// carte "Chef de département") reprennent aussi cet habillage.
	if ( is_page_template( array( 'page-templates/qui-sommes-nous.php', 'page-templates/page-generique.php' ) ) || is_post_type_archive( array( 'webinaire', 'publication' ) ) || is_tax( 'departement' ) ) {
		wp_enqueue_style(
			'cerer-about',
			get_stylesheet_directory_uri() . '/assets/css/about.css',
			array( 'cerer-components' ),
			cerer_asset_version( 'assets/css/about.css' )
		);
	}

	$cerer_simple_pages = array(
		'page-templates/contact.php',
		'page-templates/formations.php',
		'page-templates/departements.php',
		'page-templates/donnees-outils.php',
		'page-templates/stage-emploi.php',
	);
	// is_home() : la page "Actualités" (Réglages > Lecture > Page des
	// articles) rend toujours via home.php, jamais via un modèle de page —
	// elle a donc besoin de pages.css (cerer-page-hero) sans passer par
	// is_page_template(). Idem pour les archives/fiches Publications,
	// Webinaires, Soutenances, Chercheurs et Sessions de formation
	// (archive-*.php / single-*.php) et pour l'archive d'un département
	// (taxonomy-departement.php), toutes à la racine du thème.
	$cerer_archive_post_types = array( 'publication', 'webinaire', 'soutenance', 'chercheur', 'formation_session' );
	$cerer_uses_archives_css  = is_post_type_archive( $cerer_archive_post_types ) || is_singular( $cerer_archive_post_types ) || is_tax( 'departement' );
	if ( is_page_template( $cerer_simple_pages ) || is_home() || $cerer_uses_archives_css ) {
		wp_enqueue_style(
			'cerer-pages',
			get_stylesheet_directory_uri() . '/assets/css/pages.css',
			array( 'cerer-components' ),
			cerer_asset_version( 'assets/css/pages.css' )
		);
	}

	if ( $cerer_uses_archives_css ) {
		wp_enqueue_style(
			'cerer-archives',
			get_stylesheet_directory_uri() . '/assets/css/archives.css',
			array( 'cerer-pages' ),
			cerer_asset_version( 'assets/css/archives.css' )
		);
	}

	// Page de détail d'un département (PROMPT-22) — gabarit propre, chargé en
	// dernier dans la chaîne pour pouvoir surcharger about.css/archives.css
	// sans !important. Dépend aussi des bundles Blocksy ('ct-main-styles',
	// 'ct-page-title-styles') quand ils sont enregistrés : ces bundles sont
	// imprimés APRÈS les feuilles du thème enfant dans <head> (cf. leur ordre
	// dans le <head> rendu), donc sans cette dépendance leurs règles
	// l'emportent sur les nôtres à spécificité égale. wp_enqueue_style()
	// ignore silencieusement un handle listé mais pas encore enregistré.
	if ( is_tax( 'departement' ) ) {
		wp_enqueue_style(
			'cerer-departement-detail',
			get_stylesheet_directory_uri() . '/assets/css/departement-detail.css',
			array( 'cerer-archives', 'ct-main-styles', 'ct-page-title-styles' ),
			cerer_asset_version( 'assets/css/departement-detail.css' )
		);
		wp_enqueue_script(
			'cerer-departement-detail',
			get_stylesheet_directory_uri() . '/assets/js/departement-detail.js',
			array(),
			cerer_asset_version( 'assets/js/departement-detail.js' ),
			true
		);
	}

	// Formulaire d'inscription webinaire (PROMPT-13) : accueil (carte du
	// prochain webinaire) et archive /webinaires/ (réorganisée avec ce même
	// formulaire à droite).
	if ( is_front_page() || is_post_type_archive( 'webinaire' ) ) {
		wp_enqueue_script(
			'cerer-webinar-form',
			get_stylesheet_directory_uri() . '/assets/js/webinar-registration-form.js',
			array(),
			cerer_asset_version( 'assets/js/webinar-registration-form.js' ),
			true
		);
		wp_localize_script(
			'cerer-webinar-form',
			'cererWebinar',
			array(
				'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
				'nonce'        => wp_create_nonce( 'cerer_webinar_registration' ),
				'i18nRequired' => __( 'Merci de remplir les champs obligatoires (*).', 'cerer-child' ),
				'i18nError'    => __( 'Une erreur est survenue, réessayez.', 'cerer-child' ),
			)
		);
	}

	if ( is_page_template( 'page-templates/contact.php' ) ) {
		wp_enqueue_script(
			'cerer-contact-form',
			get_stylesheet_directory_uri() . '/assets/js/contact-form.js',
			array(),
			cerer_asset_version( 'assets/js/contact-form.js' ),
			true
		);
		wp_localize_script(
			'cerer-contact-form',
			'cererContact',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'cerer_contact_form' ),
			)
		);
	}

	if ( is_page_template( 'page-templates/stage-emploi.php' ) ) {
		wp_enqueue_script(
			'cerer-stage-form',
			get_stylesheet_directory_uri() . '/assets/js/stage-form.js',
			array(),
			cerer_asset_version( 'assets/js/stage-form.js' ),
			true
		);
		wp_localize_script(
			'cerer-stage-form',
			'cererStage',
			array(
				'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
				'nonce'        => wp_create_nonce( 'cerer_stage_request' ),
				'i18nRequired' => __( 'Merci de remplir les champs obligatoires (*).', 'cerer-child' ),
				'i18nError'    => __( 'Une erreur est survenue, réessayez.', 'cerer-child' ),
			)
		);
	}

	// Chargés uniquement sur la page d'accueil.
	if ( is_front_page() ) {
		// .cerer-form (styles de formulaire) vit dans pages.css, chargée pour
		// les pages de contenu classiques — l'accueil en a aussi besoin
		// depuis le formulaire d'inscription webinaire (PROMPT-13).
		wp_enqueue_style(
			'cerer-pages',
			get_stylesheet_directory_uri() . '/assets/css/pages.css',
			array( 'cerer-components' ),
			cerer_asset_version( 'assets/css/pages.css' )
		);

		wp_enqueue_style(
			'cerer-home',
			get_stylesheet_directory_uri() . '/assets/css/home.css',
			array( 'cerer-pages' ),
			cerer_asset_version( 'assets/css/home.css' )
		);

		wp_enqueue_script(
			'cerer-home',
			get_stylesheet_directory_uri() . '/assets/js/home.js',
			array(),
			cerer_asset_version( 'assets/js/home.js' ),
			true
		);

		wp_enqueue_script(
			'cerer-hero-slider',
			get_stylesheet_directory_uri() . '/assets/js/hero-slider.js',
			array(),
			cerer_asset_version( 'assets/js/hero-slider.js' ),
			true
		);

		wp_enqueue_script(
			'cerer-actualites-filter',
			get_stylesheet_directory_uri() . '/assets/js/actualites-filter.js',
			array(),
			cerer_asset_version( 'assets/js/actualites-filter.js' ),
			true
		);
		wp_localize_script(
			'cerer-actualites-filter',
			'cererActualites',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'cerer_actualites_filter' ),
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'cerer_enqueue_assets' );

/**
 * Version de la feuille parente, basée sur son propre filemtime pour ne pas
 * dépendre du numéro de version affiché dans son en-tête.
 *
 * @return string
 */
function cerer_asset_version_parent() {
	$parent_style = get_template_directory() . '/style.css';
	return file_exists( $parent_style ) ? (string) filemtime( $parent_style ) : wp_get_theme( get_template() )->get( 'Version' );
}
