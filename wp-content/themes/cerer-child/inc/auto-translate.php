<?php
/**
 * Traduction automatique FR→EN des articles (CPT "post") via l'API DeepL,
 * reliée à la traduction Polylang existante du site (fr/en).
 *
 * Déclenchement : à la publication (ou republication) d'un article en
 * français qui n'a pas encore de traduction anglaise, une version EN est
 * générée et liée automatiquement via pll_save_post_translations(). La
 * clé API (CERER_DEEPL_API_KEY) est définie dans wp-config.php, jamais
 * committée (cf. .gitignore).
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Appelle l'API DeepL pour traduire un texte. Le suffixe ":fx" de la clé
 * indique un compte "DeepL API Free", qui utilise un sous-domaine dédié
 * (api-free.deepl.com) différent du plan payant (api.deepl.com).
 *
 * @param string $text        Texte source (brut ou HTML).
 * @param string $target_lang Code cible DeepL (ex. 'EN-US').
 * @param string $source_lang Code source DeepL (ex. 'FR').
 * @param bool   $is_html     true pour préserver les balises HTML (post_content).
 * @return string Texte traduit, ou '' en cas d'échec (jamais de faux contenu).
 */
function cerer_deepl_translate( $text, $target_lang = 'EN-US', $source_lang = 'FR', $is_html = false ) {
	$text = trim( (string) $text );
	if ( '' === $text ) {
		return '';
	}

	if ( ! defined( 'CERER_DEEPL_API_KEY' ) || ! CERER_DEEPL_API_KEY ) {
		return '';
	}

	$endpoint = ( false !== strpos( CERER_DEEPL_API_KEY, ':fx' ) )
		? 'https://api-free.deepl.com/v2/translate'
		: 'https://api.deepl.com/v2/translate';

	$body = array(
		'text'        => $text,
		'source_lang' => $source_lang,
		'target_lang' => $target_lang,
	);
	if ( $is_html ) {
		$body['tag_handling'] = 'html';
	}

	$response = wp_remote_post(
		$endpoint,
		array(
			'timeout' => 30,
			'headers' => array(
				'Authorization' => 'DeepL-Auth-Key ' . CERER_DEEPL_API_KEY,
			),
			'body'    => $body,
		)
	);

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		return '';
	}

	$data = json_decode( wp_remote_retrieve_body( $response ), true );
	return $data['translations'][0]['text'] ?? '';
}

/**
 * Crée (ou complète) la traduction anglaise d'un article FR et la relie via
 * Polylang. N'écrase jamais une traduction déjà reliée — cf. l'appelant.
 *
 * @param int $post_id ID de l'article source (langue FR).
 * @return int|false ID du nouvel article EN, ou false en cas d'échec.
 */
function cerer_create_english_translation( $post_id ) {
	$source = get_post( $post_id );
	if ( ! $source ) {
		return false;
	}

	$title_en = cerer_deepl_translate( $source->post_title, 'EN-US', 'FR' );
	if ( '' === $title_en ) {
		// Échec API (quota, réseau…) : on ne crée pas de traduction à moitié
		// vide, un prochain enregistrement de l'article réessaiera.
		return false;
	}

	$excerpt_en = $source->post_excerpt ? cerer_deepl_translate( $source->post_excerpt, 'EN-US', 'FR' ) : '';
	$content_en = $source->post_content ? cerer_deepl_translate( $source->post_content, 'EN-US', 'FR', true ) : '';

	$new_id = wp_insert_post(
		array(
			'post_type'    => 'post',
			'post_title'   => $title_en,
			'post_excerpt' => $excerpt_en,
			'post_content' => $content_en,
			'post_status'  => $source->post_status,
			'post_date'    => $source->post_date,
		),
		true
	);

	if ( is_wp_error( $new_id ) || ! $new_id ) {
		return false;
	}

	// Même catégorie d'actualité que l'article source (les termes eux-mêmes
	// sont partagés entre langues par Polylang, pas besoin de les traduire).
	$cats = wp_get_post_terms( $post_id, 'categorie_actualite', array( 'fields' => 'ids' ) );
	if ( $cats ) {
		wp_set_object_terms( $new_id, $cats, 'categorie_actualite' );
	}

	// Image mise en avant : reprise telle quelle, une photo n'a pas de
	// langue. Les images/vidéos DANS le corps de l'article (balises <img>,
	// <iframe>, blocs d'intégration YouTube…) sont, elles, déjà préservées
	// par l'appel DeepL ci-dessus via 'tag_handling' => 'html' : seul le
	// texte lisible est traduit, les balises et leurs attributs (src, href)
	// ne sont jamais touchés.
	$thumbnail_id = get_post_thumbnail_id( $post_id );
	if ( $thumbnail_id ) {
		set_post_thumbnail( $new_id, $thumbnail_id );
	}

	if ( function_exists( 'pll_set_post_language' ) ) {
		if ( ! pll_get_post_language( $post_id ) ) {
			pll_set_post_language( $post_id, 'fr' );
		}
		pll_set_post_language( $new_id, 'en' );
		pll_save_post_translations( array( 'fr' => $post_id, 'en' => $new_id ) );
	}

	update_post_meta( $new_id, '_cerer_auto_translated', 1 );
	update_post_meta( $new_id, '_cerer_auto_translated_from', $post_id );

	return $new_id;
}

/**
 * Hook save_post : déclenche la traduction automatique d'un article FR
 * publié qui n'a pas encore de pendant EN. Le drapeau statique évite une
 * boucle infinie lorsque wp_insert_post() ci-dessus déclenche lui-même ce
 * hook pour l'article EN qu'on vient de créer.
 */
function cerer_maybe_auto_translate_post( $post_id, $post, $update ) {
	static $is_translating = false;

	if ( $is_translating ) {
		return;
	}

	if ( 'post' !== $post->post_type || 'publish' !== $post->post_status ) {
		return;
	}

	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}

	if ( ! function_exists( 'pll_get_post_language' ) || ! function_exists( 'pll_get_post' ) ) {
		return;
	}

	$lang = pll_get_post_language( $post_id );
	if ( $lang && 'fr' !== $lang ) {
		// Article déjà en anglais (ou une traduction saisie à la main) :
		// on ne retraduit jamais une traduction.
		return;
	}

	if ( pll_get_post( $post_id, 'en' ) ) {
		return; // déjà traduit.
	}

	$is_translating = true;
	cerer_create_english_translation( $post_id );
	$is_translating = false;
}
add_action( 'save_post', 'cerer_maybe_auto_translate_post', 20, 3 );
