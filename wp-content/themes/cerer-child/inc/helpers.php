<?php
/**
 * Helpers transversaux — README §6.1.
 *
 * "Liens externes — mémoires, replays, DOI, fichiers Drive, outils
 * Streamlit : target="_blank" rel="noopener noreferrer" + icône ↗ après le
 * libellé. Aucun fichier lourd n'est hébergé sur le serveur CERER."
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Couleur ('blue' ou 'green') du "C.E.R.E.R" scintillant de la navbar
 * (template-parts/header-navbar.php), affiché sur toutes les pages avec une
 * teinte qui varie selon la page — sur demande explicite, choix limité à
 * ces deux couleurs. Départements : reprend l'association déjà utilisée par
 * les cartes de département (taxonomy-departement.php), Thermique et Éolien
 * n'ayant pas de bleu/vert dédié dans cette palette-là sont répartis pour
 * équilibrer les deux teintes. Reste du site : alternance stable par ID de
 * page/post, pour que chaque page garde toujours la même couleur.
 *
 * @return string 'blue'|'green'
 */
function cerer_brand_shine_color() {
	if ( is_front_page() ) {
		return 'blue';
	}

	if ( is_tax( 'departement' ) ) {
		$term = get_queried_object();
		// Clé par ID du terme FRANÇAIS (stable dans les deux langues, cf.
		// front-page.php $dept_class_map) plutôt que par nom affiché : le nom
		// change (PROMPT-22, "Photovoltaïque" → "Energie Solaire
		// Photovoltaïque") sans que l'association de couleur doive suivre.
		$fr_term_id = function_exists( 'pll_get_term' ) ? ( pll_get_term( $term->term_id, 'fr' ) ?: $term->term_id ) : $term->term_id;
		$map        = array(
			9  => 'green', // Photovoltaïque
			10 => 'blue',  // Biomasse & Bioénergie
			11 => 'blue',  // Éolien
			12 => 'green', // Thermique
		);
		if ( isset( $map[ $fr_term_id ] ) ) {
			return $map[ $fr_term_id ];
		}
	}

	$id = get_queried_object_id();
	return ( 0 === $id % 2 ) ? 'blue' : 'green';
}

/**
 * ID d'un élément de get_posts(), que ce soit un WP_Post (défaut) ou un
 * entier (get_posts appelé avec 'fields' => 'ids'). Utilisé par
 * cerer_posts_with_fallback() pour rester agnostique du format demandé.
 *
 * @param WP_Post|int $post
 * @return int
 */
function cerer_extract_post_id( $post ) {
	return is_object( $post ) ? (int) $post->ID : (int) $post;
}

/**
 * Complète une liste de posts dans la langue courante avec des posts de la
 * langue par défaut quand il n'y en a pas assez (PROMPT-17) : mieux vaut du
 * contenu dans l'autre langue qu'une section de l'accueil qui disparaît ou
 * une grille à moitié vide faute de traduction. Un post déjà affiché via sa
 * traduction dans la langue courante n'est jamais dupliqué.
 *
 * @param array      $primary_args  Arguments get_posts() pour la langue courante (sans 'posts_per_page'/'lang', ajoutés ici).
 * @param int        $needed        Nombre de posts désirés au total.
 * @param array|null $fallback_args Arguments pour la requête de repli, si différents de $primary_args
 *                                  (utile quand un tax_query filtre sur un terme qui n'existe pas dans la
 *                                  langue de repli sous le même ID — actualites-accueil.php par ex.).
 * @return WP_Post[]
 */
function cerer_posts_with_fallback( array $primary_args, $needed, $fallback_args = null ) {
	$current_lang = function_exists( 'pll_current_language' ) ? pll_current_language() : '';
	$default_lang = function_exists( 'pll_default_language' ) ? pll_default_language() : $current_lang;

	$primary_args = array_merge( $primary_args, array( 'posts_per_page' => $needed ) );
	if ( $current_lang && ! isset( $primary_args['lang'] ) ) {
		$primary_args['lang'] = $current_lang;
	}
	$primary = get_posts( $primary_args );

	if ( count( $primary ) >= $needed || ! $current_lang || $current_lang === $default_lang ) {
		return $primary;
	}

	$missing = $needed - count( $primary );
	// 'fields' => 'ids' (ou 'id=>parent') renvoie des entiers, pas des
	// WP_Post — get_the_ID() gère les deux formes sans avertissement.
	$primary_ids = array_map( 'cerer_extract_post_id', $primary );

	$fallback_args = array_merge( $fallback_args ?? $primary_args, array(
		'lang'           => $default_lang,
		'posts_per_page' => $missing + count( $primary_ids ) + 10, // marge pour filtrer les traductions déjà affichées ci-dessous.
	) );
	unset( $fallback_args['offset'] );

	$candidates = get_posts( $fallback_args );

	$backfill = array();
	foreach ( $candidates as $candidate ) {
		if ( count( $backfill ) >= $missing ) {
			break;
		}
		$candidate_id  = cerer_extract_post_id( $candidate );
		$translated_id = function_exists( 'pll_get_post' ) ? pll_get_post( $candidate_id, $current_lang ) : 0;
		if ( $translated_id && in_array( $translated_id, $primary_ids, true ) ) {
			continue; // déjà affiché via sa traduction dans la langue courante.
		}
		$backfill[] = $candidate;
	}

	return array_merge( $primary, $backfill );
}

/**
 * Construit le HTML d'un lien externe conforme (nouvel onglet sécurisé + ↗),
 * sans l'afficher — pratique quand le lien doit être composé avant d'être
 * inséré ailleurs (ex. dans un tableau, une carte…).
 *
 * @param string $url     URL externe (mémoire, replay, DOI, Drive, Streamlit…).
 * @param string $label   Libellé visible du lien.
 * @param array  $attrs   Attributs HTML additionnels, ex. array('class' => 'cerer-btn').
 * @return string HTML échappé, ou chaîne vide si $url est vide.
 */
function cerer_get_external_link( $url, $label, $attrs = array() ) {
	if ( ! $url ) {
		return '';
	}

	$attrs['href']   = esc_url( $url );
	$attrs['target'] = '_blank';
	$attrs['rel']    = 'noopener noreferrer';

	$attr_html = '';
	foreach ( $attrs as $name => $value ) {
		$attr_html .= sprintf( ' %s="%s"', esc_attr( $name ), esc_attr( $value ) );
	}

	return sprintf(
		'<a%s>%s <span class="cerer-external-icon" aria-hidden="true">↗</span></a>',
		$attr_html,
		esc_html( $label )
	);
}

/**
 * Affiche un lien externe conforme. Voir cerer_get_external_link().
 *
 * @param string $url
 * @param string $label
 * @param array  $attrs
 */
function cerer_external_link( $url, $label, $attrs = array() ) {
	echo cerer_get_external_link( $url, $label, $attrs ); // phpcs:ignore -- déjà échappé dans cerer_get_external_link().
}

/**
 * Bascule FR|EN de la topbar/burger — README §6.3. Branché sur Polylang :
 * retourne un tableau simple (slug, url, label, is_active) prêt à boucler
 * dans le gabarit, ou un tableau vide si Polylang n'est pas actif (le
 * sélecteur disparaît alors proprement plutôt que d'afficher des liens
 * morts).
 *
 * @return array<int, array{slug: string, url: string, label: string, is_active: bool}>
 */
function cerer_get_language_switcher_items() {
	if ( ! function_exists( 'pll_the_languages' ) ) {
		return array();
	}

	$raw = pll_the_languages(
		array(
			'raw'    => 1,
			'hide_if_empty' => false,
		)
	);

	if ( ! is_array( $raw ) ) {
		return array();
	}

	$items = array();
	foreach ( $raw as $lang ) {
		$items[] = array(
			'slug'      => $lang['slug'],
			'url'       => $lang['url'],
			'label'     => strtoupper( $lang['slug'] ),
			'is_active' => ! empty( $lang['current_lang'] ),
		);
	}

	return $items;
}

/**
 * URL de la page d'accueil dans la langue courante. Un simple home_url('/')
 * reste figé sur la version française même en navigation anglaise —
 * pll_home_url() est la fonction dédiée de Polylang pour ce cas précis.
 *
 * @return string
 */
function cerer_home_url() {
	return function_exists( 'pll_home_url' ) ? pll_home_url() : home_url( '/' );
}

/**
 * URL, dans la langue courante, de la page dont l'ID français est
 * $fr_page_id. Les liens internes du thème sont câblés en dur vers les
 * slugs français (home_url('/qui-sommes-nous/')…) : ça reste correct en
 * français, mais renvoie vers la version française même depuis le site
 * anglais tant qu'on ne passe pas par cette fonction — pll_get_post()
 * résout la traduction correspondante si Polylang est actif.
 *
 * @param int $fr_page_id ID de la page (ou de l'article "page des articles") en français.
 * @return string
 */
function cerer_page_url( $fr_page_id ) {
	if ( function_exists( 'pll_get_post' ) ) {
		$translated_id = pll_get_post( $fr_page_id );
		if ( $translated_id ) {
			$fr_page_id = $translated_id;
		}
	}
	return get_permalink( $fr_page_id ) ?: home_url( '/' );
}

/**
 * Adresse e-mail de réception des notifications de formulaire (contact,
 * inscription webinaire, demande de stage…). Fixe plutôt que
 * get_option('admin_email') : cette dernière pointe vers l'adresse du
 * compte WordPress (dev/hébergeur), pas forcément celle que le CERER
 * surveille — cf. demande explicite du client, toutes les demandes doivent
 * arriver sur cerer@ucad.edu.sn quel que soit l'admin_email configuré.
 *
 * @return string
 */
function cerer_notification_email() {
	return 'cerer@ucad.edu.sn';
}

/**
 * Un champ ACF est-il réellement renseigné ? Un true_false jamais enregistré
 * renvoie NULL (cf. mémoire "ACF default_value not returned"), donc 0/false
 * explicitement saisi compte comme une valeur ; seuls NULL, '' et [] comptent
 * comme vides.
 *
 * @param mixed $value
 * @return bool
 */
function cerer_field_has_value( $value ) {
	if ( is_array( $value ) ) {
		return ! empty( $value );
	}
	return null !== $value && '' !== $value;
}

/**
 * Champ ACF d'un terme de taxonomie, avec repli langue : lit d'abord le
 * terme de la langue courante ($current_term_id), et retombe sur le terme
 * FRANÇAIS ($fr_term_id) si ce champ y est vide — utile tant que l'équipe du
 * CERER n'a traduit qu'une partie des champs d'un terme (taxonomy-departement.php,
 * PROMPT-22) : mieux vaut du contenu français que rien, mais le contenu
 * anglais déjà saisi doit être prioritaire dès qu'il existe.
 *
 * @param string $field_name
 * @param int    $current_term_id ID du terme dans la langue affichée.
 * @param int    $fr_term_id      ID du terme français (repli).
 * @param string $taxonomy        Nom de la taxonomie (défaut : "departement").
 * @return mixed
 */
function cerer_term_field( $field_name, $current_term_id, $fr_term_id, $taxonomy = 'departement' ) {
	if ( $current_term_id !== $fr_term_id ) {
		$value = get_field( $field_name, $taxonomy . '_' . $current_term_id );
		if ( cerer_field_has_value( $value ) ) {
			return $value;
		}
	}
	return get_field( $field_name, $taxonomy . '_' . $fr_term_id );
}
