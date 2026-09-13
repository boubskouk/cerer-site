<?php
/**
 * Page "Qui sommes-nous" (PROMPT-15) : helpers pour les sections "Le CERER
 * en images", "Notre histoire" et "Nos tutelles" — toutes construites sur
 * des jeux de champs ACF numérotés (pas de champ Répéteur, réservé à ACF
 * Pro, cf. acf-json/group_cerer_qsn_*.json).
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Autorise l'upload de SVG en médiathèque (PROMPT-19 : le drapeau du Sénégal
 * de la section "Nos tutelles" est un SVG). Bloqué par défaut par
 * WordPress ; sans risque ici, la médiathèque n'étant alimentée que par des
 * comptes admin de confiance.
 */
add_filter(
	'upload_mimes',
	function ( $mimes ) {
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	}
);

add_filter(
	'wp_check_filetype_and_ext',
	function ( $data, $file, $filename, $mimes ) {
		if ( ! $data['type'] && preg_match( '/\.svg$/i', $filename ) ) {
			$data['ext']  = 'svg';
			$data['type'] = 'image/svg+xml';
		}
		return $data;
	},
	10,
	4
);

/**
 * Renvoie l'URL de la miniature YouTube d'un ID vidéo, en la déposant dans
 * la médiathèque au premier accès puis en réutilisant l'attachement créé
 * (jamais plus d'un appel réseau vers i.ytimg.com par vidéo).
 *
 * @param string $yt_id
 * @return string URL de l'image en médiathèque, ou '' si le téléchargement
 *                échoue — jamais l'URL i.ytimg.com brute : un hotlink direct
 *                s'appellerait à chaque chargement de page (PROMPT-17), à
 *                l'opposé de la règle "récupérée une seule fois".
 */
function cerer_yt_cached_thumbnail( $yt_id ) {
	if ( ! $yt_id ) {
		return '';
	}

	$remote_url   = "https://i.ytimg.com/vi/{$yt_id}/hqdefault.jpg";
	$option_key   = 'cerer_yt_thumb_' . $yt_id;
	$attachment_id = get_option( $option_key );

	if ( $attachment_id && wp_attachment_is_image( $attachment_id ) ) {
		$url = wp_get_attachment_image_url( $attachment_id, 'medium_large' );
		if ( $url ) {
			return $url;
		}
	}

	if ( ! function_exists( 'media_sideload_image' ) ) {
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
	}

	$tmp = download_url( $remote_url );
	if ( is_wp_error( $tmp ) ) {
		return '';
	}

	$file_array = array(
		'name'     => 'youtube-' . $yt_id . '.jpg',
		'tmp_name' => $tmp,
	);

	$new_attachment_id = media_handle_sideload( $file_array, 0, "Miniature YouTube {$yt_id}" );

	if ( is_wp_error( $new_attachment_id ) ) {
		@unlink( $tmp );
		return '';
	}

	update_option( $option_key, $new_attachment_id, false );

	return wp_get_attachment_image_url( $new_attachment_id, 'medium_large' ) ?: '';
}

/**
 * Couleurs de badge disponibles pour une carte vidéo (section "Le CERER en
 * images") : hex + couleur de texte garantissant le contraste (README
 * PROMPT-15 : le texte est sombre sur le vert, blanc sur les bleus).
 *
 * @return array<string,array{bg:string,text:string}>
 */
function cerer_qsn_badge_colors() {
	return array(
		'vert'       => array( 'bg' => '#8CC63F', 'text' => '#22350A' ),
		'bleu'       => array( 'bg' => '#1565A3', 'text' => '#FFFFFF' ),
		'bleu_fonce' => array( 'bg' => '#14568F', 'text' => '#FFFFFF' ),
	);
}

/**
 * Vidéos de la section "Le CERER en images" — jusqu'à 6 (PROMPT-18), seules
 * celles avec une URL YouTube renseignée sont renvoyées.
 *
 * @return array<int,array<string,mixed>>
 */
function cerer_qsn_get_videos() {
	$badge_colors = cerer_qsn_badge_colors();
	$videos       = array();

	for ( $i = 1; $i <= 6; $i++ ) {
		$url = trim( (string) get_field( "videos_presentation_{$i}_url_youtube" ) );
		if ( ! $url ) {
			continue;
		}

		$yt_id     = cerer_youtube_id( $url );
		$titre     = trim( (string) get_field( "videos_presentation_{$i}_titre" ) );
		$thumbnail = get_field( "videos_presentation_{$i}_miniature" );
		$couleur   = get_field( "videos_presentation_{$i}_couleur_badge" ) ?: 'bleu';

		$videos[] = array(
			'url'          => $url,
			'titre'        => $titre ?: __( 'Vidéo — titre à renseigner', 'cerer-child' ),
			'description'  => trim( (string) get_field( "videos_presentation_{$i}_description" ) ),
			'categorie'    => trim( (string) get_field( "videos_presentation_{$i}_categorie" ) ),
			'badge_bg'     => $badge_colors[ $couleur ]['bg'] ?? $badge_colors['bleu']['bg'],
			'badge_text'   => $badge_colors[ $couleur ]['text'] ?? $badge_colors['bleu']['text'],
			'thumbnail'    => ! empty( $thumbnail['url'] ) ? $thumbnail['url'] : cerer_yt_cached_thumbnail( $yt_id ),
		);
	}

	return $videos;
}

/**
 * Jalons de la section "Notre histoire" — jusqu'à 6, seuls ceux avec une
 * année renseignée sont renvoyés. Le dernier reçoit 'is_current' => true
 * (style "étape actuelle" automatique, README PROMPT-15).
 *
 * @return array<int,array<string,mixed>>
 */
function cerer_qsn_get_jalons() {
	$jalons = array();

	for ( $i = 1; $i <= 6; $i++ ) {
		$annee = trim( (string) get_field( "jalons_histoire_{$i}_annee" ) );
		if ( ! $annee ) {
			continue;
		}

		$jalons[] = array(
			'annee'   => $annee,
			'libelle' => trim( (string) get_field( "jalons_histoire_{$i}_libelle" ) ),
		);
	}

	if ( $jalons ) {
		$last_index                       = count( $jalons ) - 1;
		$jalons[ $last_index ]['is_current'] = true;
	}

	return $jalons;
}

/**
 * Tutelles de la section "Nos tutelles" — jusqu'à 4, seules celles avec un
 * sigle renseigné sont renvoyées. Logo manquant : la carte le signale via
 * 'has_logo' => false, le template affiche alors un aplat neutre.
 *
 * @return array<int,array<string,mixed>>
 */
function cerer_qsn_get_tutelles() {
	$tutelles = array();

	for ( $i = 1; $i <= 4; $i++ ) {
		$sigle = trim( (string) get_field( "tutelles_{$i}_sigle" ) );
		if ( ! $sigle ) {
			continue;
		}

		$logo = get_field( "tutelles_{$i}_logo" );

		$tutelles[] = array(
			'sigle'    => $sigle,
			'intitule' => trim( (string) get_field( "tutelles_{$i}_intitule" ) ),
			'url'      => trim( (string) get_field( "tutelles_{$i}_url" ) ),
			'has_logo' => ! empty( $logo['url'] ),
			'logo_url' => $logo['url'] ?? '',
			'logo_alt' => $logo['alt'] ?? $sigle,
			// PROMPT-19 : encadré (bordure + ombre + largeur fixe) pour un
			// visuel de type drapeau, plutôt que le traitement "logo" standard.
			'encadre'  => (bool) get_field( "tutelles_{$i}_logo_encadre" ),
		);
	}

	return $tutelles;
}

/**
 * 4 premiers chercheurs par ordre de menu (section "Notre équipe",
 * PROMPT-15) — le client ajoute/retire/réordonne depuis l'admin
 * (CPT "chercheur", champ "Ordre" ajouté via "page-attributes").
 *
 * @return WP_Post[]
 */
function cerer_qsn_get_equipe() {
	return get_posts(
		array(
			'post_type'      => 'chercheur',
			'posts_per_page' => 4,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		)
	);
}
