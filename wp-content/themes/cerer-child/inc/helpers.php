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
