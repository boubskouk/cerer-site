<?php
/**
 * Vidéos de la page "Qui sommes-nous" — CPT "video" + ACF (gérable depuis
 * l'admin, comme demandé : "sans toucher au code"), cf. inc/post-types.php
 * pour l'enregistrement du CPT et acf-json/group_cerer_video.json pour les
 * champs (description, catégorie, url_youtube).
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extrait l'ID YouTube d'une URL (watch?v=, youtu.be/, embed/), ou null.
 *
 * @param string $url
 * @return string|null
 */
function cerer_youtube_id( $url ) {
	if ( $url && preg_match( '#(?:youtube\.com/(?:watch\?v=|embed/)|youtu\.be/)([A-Za-z0-9_-]{6,})#', $url, $m ) ) {
		return $m[1];
	}
	return null;
}

/**
 * @return array<string,string> slug (valeur du champ ACF "categorie") => libellé, pour les pills de filtre.
 */
function cerer_get_video_categories() {
	return array(
		'presentation' => __( 'Présentation', 'cerer-child' ),
		'recherche'    => __( 'Recherche', 'cerer-child' ),
		'formation'    => __( 'Formation', 'cerer-child' ),
		'evenement'    => __( 'Événement', 'cerer-child' ),
	);
}

/**
 * Récupère les vidéos publiées (CPT "video"), enrichies de thumbnail/
 * embed_url/disponible calculés à l'affichage — pour qu'ajouter un lien
 * YouTube depuis l'admin suffise, sans jamais toucher au code.
 *
 * @return array<int,array<string,mixed>>
 */
function cerer_get_videos() {
	$posts = get_posts(
		array(
			'post_type'      => 'video',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order date',
			'order'          => 'ASC',
		)
	);

	$videos = array();

	foreach ( $posts as $post ) {
		$url   = (string) get_field( 'url_youtube', $post->ID );
		$yt_id = cerer_youtube_id( $url );

		$videos[] = array(
			'id'          => $post->ID,
			'titre'       => get_the_title( $post ),
			'description' => (string) get_field( 'description', $post->ID ),
			'categorie'   => (string) get_field( 'categorie', $post->ID ),
			'url'         => $url,
			'disponible'  => (bool) $yt_id,
			'thumbnail'   => $yt_id ? "https://img.youtube.com/vi/{$yt_id}/hqdefault.jpg" : '',
			'embed_url'   => $yt_id ? "https://www.youtube-nocookie.com/embed/{$yt_id}" : '',
		);
	}

	return $videos;
}
