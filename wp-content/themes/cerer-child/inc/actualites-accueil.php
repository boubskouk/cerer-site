<?php
/**
 * Section "Dernières actualités" de l'accueil (PROMPT-14) : rendu de la
 * grille (réutilisé au chargement initial et par le filtre AJAX) + le
 * point d'entrée AJAX lui-même.
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Rend la grille des 6 dernières actualités, éventuellement filtrées par
 * une catégorie (slug de terme "categorie_actualite", ou '' / 'toutes' pour
 * tout afficher).
 *
 * @param string $categorie_slug Slug du terme, vide pour aucun filtre.
 * @return string HTML de la grille (sans le conteneur .cerer-actualites__grid lui-même).
 */
function cerer_render_actualites_grid( $categorie_slug = '' ) {
	// PROMPT-17 : les actualités n'ont pas encore de traduction anglaise —
	// plutôt que de faire disparaître toute la section sur /en/, on complète
	// avec les dernières actualités françaises (cerer_posts_with_fallback,
	// inc/helpers.php). Le terme de catégorie est retraduit pour la requête
	// de repli : son ID ne désigne pas le même terme dans l'autre langue.
	$current_lang = function_exists( 'pll_current_language' ) ? pll_current_language() : '';
	$default_lang = function_exists( 'pll_default_language' ) ? pll_default_language() : $current_lang;

	$term = ( $categorie_slug && 'toutes' !== $categorie_slug )
		? get_term_by( 'slug', $categorie_slug, 'categorie_actualite' )
		: null;

	$args = array(
		'post_type' => 'post',
		'orderby'   => 'date',
		'order'     => 'DESC',
	);
	if ( $term ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'categorie_actualite',
				'field'    => 'term_id',
				'terms'    => $term->term_id,
			),
		);
	}

	$fallback_args = $args;
	if ( $term && $default_lang && $default_lang !== $current_lang && function_exists( 'pll_get_term' ) ) {
		$fallback_term_id = pll_get_term( $term->term_id, $default_lang );
		if ( $fallback_term_id ) {
			$fallback_args['tax_query'] = array(
				array(
					'taxonomy' => 'categorie_actualite',
					'field'    => 'term_id',
					'terms'    => $fallback_term_id,
				),
			);
		}
	}

	$posts = cerer_posts_with_fallback( $args, 6, $fallback_args );

	ob_start();

	if ( ! $posts ) {
		?>
		<p class="cerer-actualites__empty"><?php esc_html_e( 'Aucune actualité dans cette catégorie pour le moment.', 'cerer-child' ); ?></p>
		<?php
		return ob_get_clean();
	}

	foreach ( $posts as $post ) {
		$cat_terms = get_the_terms( $post->ID, 'categorie_actualite' );
		$cat_term  = ( $cat_terms && ! is_wp_error( $cat_terms ) ) ? $cat_terms[0] : null;

		// Image à la une, sinon repli sur l'image par défaut de la
		// catégorie si le client en a renseigné une, sinon aucun bloc
		// visuel — jamais d'aplat gris (PROMPT-16).
		$image     = get_the_post_thumbnail_url( $post, 'medium_large' ) ?: '';
		$image_alt = $image ? get_post_meta( get_post_thumbnail_id( $post ), '_wp_attachment_image_alt', true ) : '';
		if ( ! $image && $cat_term ) {
			$defaut = get_field( 'image_defaut', 'categorie_actualite_' . $cat_term->term_id );
			if ( ! empty( $defaut['url'] ) ) {
				$image     = $defaut['sizes']['medium_large'] ?? $defaut['url'];
				$image_alt = '';
			}
		}

		get_template_part(
			'template-parts/carte-actualite-accueil',
			null,
			array(
				'title'       => get_the_title( $post ),
				'url'         => get_permalink( $post ),
				'image'       => $image,
				'image_alt'   => $image_alt,
				'date'        => get_the_date( '', $post ),
				'excerpt'     => $post->post_excerpt,
				'cat_label'   => $cat_term ? $cat_term->name : '',
				'cat_couleur' => $cat_term ? ( get_field( 'couleur', 'categorie_actualite_' . $cat_term->term_id ) ?: 'var(--cerer-blue-dark)' ) : 'var(--cerer-blue-dark)',
			)
		);
	}

	return ob_get_clean();
}

/**
 * Renvoie la liste des catégories d'actualité (langue courante) pour la
 * barre de filtres, dans un ordre fixe (README PROMPT-14) plutôt que
 * l'ordre alphabétique par défaut de get_terms().
 */
function cerer_get_actualites_categories() {
	$order = array( 'Publication', 'Partenariat', 'Distinction', 'Événement', 'Event', 'Formation', 'Training', 'Vie du centre', 'Centre life', 'Partnership', 'Award' );
	$terms = get_terms( array( 'taxonomy' => 'categorie_actualite', 'hide_empty' => false ) );
	if ( is_wp_error( $terms ) ) {
		return array();
	}
	usort(
		$terms,
		function ( $a, $b ) use ( $order ) {
			$pos_a = array_search( $a->name, $order, true );
			$pos_b = array_search( $b->name, $order, true );
			return ( false !== $pos_a ? $pos_a : 99 ) <=> ( false !== $pos_b ? $pos_b : 99 );
		}
	);
	return $terms;
}

function cerer_handle_actualites_filter() {
	check_ajax_referer( 'cerer_actualites_filter', 'nonce' );
	$categorie = isset( $_POST['categorie'] ) ? sanitize_title( wp_unslash( $_POST['categorie'] ) ) : '';
	wp_send_json_success( array( 'html' => cerer_render_actualites_grid( $categorie ) ) );
}
add_action( 'wp_ajax_cerer_actualites_filter', 'cerer_handle_actualites_filter' );
add_action( 'wp_ajax_nopriv_cerer_actualites_filter', 'cerer_handle_actualites_filter' );
