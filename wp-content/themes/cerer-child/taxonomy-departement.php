<?php
/**
 * Page de détail d'un département (PROMPT-22) — ex. /departement/photovoltaique/.
 *
 * Un seul gabarit pour les QUATRE départements : la couleur d'accent, l'icône
 * et tout le contenu viennent des champs ACF du terme (acf-json/
 * group_cerer_departement_meta.json), rien de propre au photovoltaïque n'est
 * codé en dur ici.
 *
 * Compatibilité : les départements pas encore migrés vers les nouveaux
 * champs structurés (presentation, laboratoires…) retombent sur l'ancien
 * champ "description_longue" (repli en bas de fichier) plutôt que
 * d'afficher une page à moitié vide.
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

get_header();

$term       = get_queried_object();
$fr_term_id = function_exists( 'pll_get_term' ) ? ( pll_get_term( $term->term_id, 'fr' ) ?: $term->term_id ) : $term->term_id;

$accent = cerer_term_field( 'couleur', $term->term_id, $fr_term_id ) ?: 'var(--cerer-blue)';
$icone  = cerer_term_field( 'icone', $term->term_id, $fr_term_id );

$icon_emoji_fallback = array(
	9  => '☀️',
	10 => '🌱',
	11 => '💨',
	12 => '🌡️',
);

// --- Compteurs de l'en-tête : toujours calculés, jamais saisis. ---
$count_chercheurs = count(
	get_posts(
		array(
			'post_type'      => 'chercheur',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'tax_query'      => array( array( 'taxonomy' => 'departement', 'field' => 'term_id', 'terms' => $term->term_id ) ),
		)
	)
);
$count_projets_actifs = count(
	get_posts(
		array(
			'post_type'      => 'projet',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'tax_query'      => array( array( 'taxonomy' => 'departement', 'field' => 'term_id', 'terms' => $term->term_id ) ),
			'meta_query'     => array( array( 'key' => 'statut', 'value' => 'en-cours' ) ),
		)
	)
);
$count_publications = count(
	get_posts(
		array(
			'post_type'      => 'publication',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'tax_query'      => array( array( 'taxonomy' => 'departement', 'field' => 'term_id', 'terms' => $term->term_id ) ),
		)
	)
);

// --- Sidebar a) Navigation départements ---
$all_departements = get_terms( array( 'taxonomy' => 'departement', 'hide_empty' => false ) );

// --- Sidebar b) Contact direct ---
$whatsapp_chef = cerer_term_field( 'whatsapp_chef', $term->term_id, $fr_term_id );
$whatsapp_digits = $whatsapp_chef ? preg_replace( '/\D+/', '', $whatsapp_chef ) : '';

// --- Sidebar c) Le département en bref ---
$en_bref = array();
for ( $i = 1; $i <= 6; $i++ ) {
	$libelle = cerer_term_field( "en_bref_{$i}_libelle", $term->term_id, $fr_term_id );
	$valeur  = cerer_term_field( "en_bref_{$i}_valeur", $term->term_id, $fr_term_id );
	if ( $libelle && $valeur ) {
		$en_bref[] = array( 'libelle' => $libelle, 'valeur' => $valeur );
	}
}

// --- Sidebar d) Ressources ---
$home_host = wp_parse_url( home_url(), PHP_URL_HOST );
$ressources = array();
for ( $i = 1; $i <= 6; $i++ ) {
	$intitule = cerer_term_field( "ressource_{$i}_intitule", $term->term_id, $fr_term_id );
	$url      = cerer_term_field( "ressource_{$i}_url", $term->term_id, $fr_term_id );
	if ( $intitule && $url ) {
		$ressources[] = array(
			'intitule' => $intitule,
			'url'      => $url,
			'externe'  => wp_parse_url( $url, PHP_URL_HOST ) && wp_parse_url( $url, PHP_URL_HOST ) !== $home_host,
		);
	}
}

// --- Sidebar e) Prochain rendez-vous : webinaire à venir le plus proche,
// filtré sur ce département. Pas de champ ACF dédié : une seule source pour
// la date/le lieu/l'inscription, comme le "Prochain webinaire" de l'accueil. ---
$now = current_time( 'Y-m-d H:i:s' );
$prochain_rdv_posts = get_posts(
	array(
		'post_type'      => 'webinaire',
		'posts_per_page' => 1,
		'tax_query'      => array( array( 'taxonomy' => 'departement', 'field' => 'term_id', 'terms' => $term->term_id ) ),
		'meta_query'     => array( array( 'key' => 'date', 'value' => $now, 'compare' => '>=', 'type' => 'DATETIME' ) ),
		'orderby'        => 'meta_value',
		'meta_key'       => 'date',
		'meta_type'      => 'DATETIME',
		'order'          => 'ASC',
	)
);
$prochain_rdv = $prochain_rdv_posts ? $prochain_rdv_posts[0] : null;

// --- Sidebar f) Partenaires du département ---
$partenaires_dept = array();
for ( $i = 1; $i <= 6; $i++ ) {
	$logo = cerer_term_field( "partenaire_dept_{$i}_logo", $term->term_id, $fr_term_id );
	if ( ! empty( $logo['url'] ) ) {
		$partenaires_dept[] = array(
			'logo' => $logo,
			'lien' => cerer_term_field( "partenaire_dept_{$i}_lien", $term->term_id, $fr_term_id ),
		);
	}
}

// --- Sidebar g) Rejoindre l'équipe ---
$count_offres_stage = count(
	get_posts(
		array(
			'post_type'      => 'offre_stage',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'tax_query'      => array( array( 'taxonomy' => 'departement', 'field' => 'term_id', 'terms' => $term->term_id ) ),
		)
	)
);
$count_sujets_these = count(
	get_posts(
		array(
			'post_type'      => 'sujet_these',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'tax_query'      => array( array( 'taxonomy' => 'departement', 'field' => 'term_id', 'terms' => $term->term_id ) ),
		)
	)
);

// --- Présentation + responsable ---
$presentation = cerer_term_field( 'presentation', $term->term_id, $fr_term_id );
$description_longue = cerer_term_field( 'description_longue', $term->term_id, $fr_term_id );

$mots_cles = array();
for ( $i = 1; $i <= 6; $i++ ) {
	$mot = cerer_term_field( "mot_cle_$i", $term->term_id, $fr_term_id );
	if ( $mot ) {
		$mots_cles[] = $mot;
	}
}

$chef_nom        = cerer_term_field( 'chef_nom', $term->term_id, $fr_term_id );
$chef_fonction   = cerer_term_field( 'chef_fonction', $term->term_id, $fr_term_id ) ?: __( 'Chef du département', 'cerer-child' );
$chef_photo      = cerer_term_field( 'chef_photo', $term->term_id, $fr_term_id );
$chef_linkedin   = cerer_term_field( 'chef_linkedin', $term->term_id, $fr_term_id );
$chef_researchgate = cerer_term_field( 'chef_researchgate', $term->term_id, $fr_term_id );
$chef_instagram  = cerer_term_field( 'chef_instagram', $term->term_id, $fr_term_id );

// --- Nos trois laboratoires ---
$laboratoires = array();
for ( $l = 1; $l <= 3; $l++ ) {
	$sigle = cerer_term_field( "lab_{$l}_sigle", $term->term_id, $fr_term_id );
	if ( ! $sigle ) {
		continue;
	}
	$items = array();
	for ( $it = 1; $it <= 5; $it++ ) {
		$item = cerer_term_field( "lab_{$l}_item_{$it}", $term->term_id, $fr_term_id );
		if ( $item ) {
			$items[] = $item;
		}
	}
	$laboratoires[] = array(
		'sigle'                 => $sigle,
		'nom'                   => cerer_term_field( "lab_{$l}_nom", $term->term_id, $fr_term_id ),
		'description'           => cerer_term_field( "lab_{$l}_description", $term->term_id, $fr_term_id ),
		'texte_complementaire'  => cerer_term_field( "lab_{$l}_texte_complementaire", $term->term_id, $fr_term_id ),
		'couleur'               => cerer_term_field( "lab_{$l}_couleur", $term->term_id, $fr_term_id ) ?: $accent,
		'encart_fond'           => cerer_term_field( "lab_{$l}_encart_fond", $term->term_id, $fr_term_id ) ?: '#F4F7FA',
		'libelle_liste'         => cerer_term_field( "lab_{$l}_libelle_liste", $term->term_id, $fr_term_id ),
		'items'                 => $items,
		'pleine_largeur'        => (bool) cerer_term_field( "lab_{$l}_pleine_largeur", $term->term_id, $fr_term_id ),
	);
}

// --- Projets en cours : 3 max, "en cours" en premier. ---
$projets_dept = get_posts(
	array(
		'post_type'      => 'projet',
		'posts_per_page' => -1,
		'tax_query'      => array( array( 'taxonomy' => 'departement', 'field' => 'term_id', 'terms' => $term->term_id ) ),
	)
);
$statut_ordre = array( 'en-cours' => 0, 'a-venir' => 1, 'termine' => 2 );
usort(
	$projets_dept,
	function ( $a, $b ) use ( $statut_ordre ) {
		$sa = $statut_ordre[ get_field( 'statut', $a->ID ) ?: 'en-cours' ] ?? 0;
		$sb = $statut_ordre[ get_field( 'statut', $b->ID ) ?: 'en-cours' ] ?? 0;
		return $sa <=> $sb;
	}
);
$projets_dept = array_slice( $projets_dept, 0, 3 );

// --- En images ---
$video_url   = cerer_term_field( 'video_url', $term->term_id, $fr_term_id );
$video_titre = cerer_term_field( 'video_titre', $term->term_id, $fr_term_id );
$video_duree = cerer_term_field( 'video_duree', $term->term_id, $fr_term_id );
$video_yt_id = $video_url ? cerer_youtube_id( $video_url ) : null;
$video_embed = $video_yt_id ? "https://www.youtube-nocookie.com/embed/{$video_yt_id}" : $video_url;
$video_poster = $video_yt_id ? "https://img.youtube.com/vi/{$video_yt_id}/hqdefault.jpg" : '';

$photos_dept = array();
for ( $i = 1; $i <= 8; $i++ ) {
	$photo = cerer_term_field( "photo_$i", $term->term_id, $fr_term_id );
	if ( ! empty( $photo['url'] ) ) {
		$photos_dept[] = $photo;
	}
}
$photos_visibles = array_slice( $photos_dept, 0, 5 );
$photos_surplus  = max( 0, count( $photos_dept ) - 5 );

// --- Publications récentes : 3 max, année décroissante. ---
$publications_dept = get_posts(
	array(
		'post_type'      => 'publication',
		'posts_per_page' => 3,
		'tax_query'      => array( array( 'taxonomy' => 'departement', 'field' => 'term_id', 'terms' => $term->term_id ) ),
		'meta_key'       => 'annee',
		'orderby'        => 'meta_value_num',
		'order'          => 'DESC',
	)
);
$pub_type_labels = array(
	'article' => __( 'Article', 'cerer-child' ),
	'rapport' => __( 'Rapport', 'cerer-child' ),
	'these'   => __( 'Thèse', 'cerer-child' ),
	'actes'   => __( 'Actes', 'cerer-child' ),
);

// --- L'équipe ---
$chercheurs_dept = get_posts(
	array(
		'post_type'      => 'chercheur',
		'posts_per_page' => -1,
		'tax_query'      => array( array( 'taxonomy' => 'departement', 'field' => 'term_id', 'terms' => $term->term_id ) ),
	)
);
?>

<div class="cerer-deptd-hero" style="background: #14568F;">
	<div class="cerer-container cerer-deptd-hero__inner">
		<div class="cerer-deptd-hero__main">
			<nav class="cerer-deptd-hero__breadcrumb" aria-label="<?php esc_attr_e( "Fil d'Ariane", 'cerer-child' ); ?>">
				<a href="<?php echo esc_url( cerer_home_url() ); ?>"><?php esc_html_e( 'Accueil', 'cerer-child' ); ?></a>
				<span aria-hidden="true">/</span>
				<a href="<?php echo esc_url( cerer_page_url( 78 ) ); ?>"><?php esc_html_e( 'Départements', 'cerer-child' ); ?></a>
				<span aria-hidden="true">/</span>
				<span class="cerer-deptd-hero__breadcrumb-current"><?php echo esc_html( $term->name ); ?></span>
			</nav>
			<div class="cerer-deptd-hero__titlerow">
				<div class="cerer-deptd-hero__icon" aria-hidden="true">
					<?php if ( ! empty( $icone['url'] ) ) : ?>
						<img src="<?php echo esc_url( $icone['url'] ); ?>" alt="">
					<?php else : ?>
						<span class="cerer-deptd-hero__icon-emoji"><?php echo esc_html( $icon_emoji_fallback[ $fr_term_id ] ?? '🔬' ); ?></span>
					<?php endif; ?>
				</div>
				<div>
					<p class="cerer-deptd-hero__eyebrow"><?php esc_html_e( 'Département de recherche', 'cerer-child' ); ?></p>
					<h1><?php echo esc_html( $term->name ); ?></h1>
				</div>
			</div>
			<?php if ( $term->description ) : ?>
				<p class="cerer-deptd-hero__chapo"><?php echo esc_html( $term->description ); ?></p>
			<?php endif; ?>
		</div>

		<div class="cerer-deptd-hero__stats">
			<div class="cerer-deptd-stat">
				<p class="cerer-deptd-stat__value"><?php echo esc_html( $count_chercheurs ); ?></p>
				<p class="cerer-deptd-stat__label"><?php esc_html_e( 'Chercheurs', 'cerer-child' ); ?></p>
			</div>
			<div class="cerer-deptd-stat">
				<p class="cerer-deptd-stat__value"><?php echo esc_html( $count_projets_actifs ); ?></p>
				<p class="cerer-deptd-stat__label"><?php esc_html_e( 'Projets actifs', 'cerer-child' ); ?></p>
			</div>
			<div class="cerer-deptd-stat">
				<p class="cerer-deptd-stat__value"><?php echo esc_html( $count_publications ); ?></p>
				<p class="cerer-deptd-stat__label"><?php esc_html_e( 'Publications', 'cerer-child' ); ?></p>
			</div>
		</div>
	</div>
</div>

<div class="cerer-container" style="--cerer-card-accent: <?php echo esc_attr( $accent ); ?>;">
	<div class="cerer-deptd-body">

		<aside class="cerer-deptd-side">
			<?php if ( $all_departements && ! is_wp_error( $all_departements ) ) : ?>
				<nav class="cerer-deptd-nav" aria-label="<?php esc_attr_e( 'Départements de recherche', 'cerer-child' ); ?>">
					<p class="cerer-deptd-nav__title"><?php esc_html_e( 'Départements de recherche', 'cerer-child' ); ?></p>
					<ul class="cerer-deptd-nav__list">
						<?php foreach ( $all_departements as $dept ) : ?>
							<?php
							$is_current  = $dept->term_id === $term->term_id;
							$dept_fr_id  = function_exists( 'pll_get_term' ) ? ( pll_get_term( $dept->term_id, 'fr' ) ?: $dept->term_id ) : $dept->term_id;
							$dept_color  = get_field( 'couleur', 'departement_' . $dept_fr_id ) ?: 'var(--cerer-blue)';
							?>
							<li>
								<a
									href="<?php echo esc_url( get_term_link( $dept ) ); ?>"
									class="cerer-deptd-nav__link<?php echo $is_current ? ' is-current' : ''; ?>"
									style="--cerer-card-accent: <?php echo esc_attr( $dept_color ); ?>;"
									<?php echo $is_current ? 'aria-current="page"' : ''; ?>
								>
									<span class="cerer-deptd-nav__dot" aria-hidden="true"></span>
									<?php echo esc_html( $dept->name ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</nav>
			<?php endif; ?>

			<?php if ( $whatsapp_chef ) : ?>
				<div class="cerer-deptd-side__block cerer-deptd-contact">
					<p class="cerer-deptd-contact__label"><?php esc_html_e( 'Contact direct', 'cerer-child' ); ?></p>
					<p class="cerer-deptd-contact__text"><?php esc_html_e( 'Une question sur ce département ? Écrivez directement au responsable.', 'cerer-child' ); ?></p>
					<a href="<?php echo esc_url( 'https://wa.me/' . $whatsapp_digits ); ?>" target="_blank" rel="noopener noreferrer" class="cerer-deptd-contact__btn">
						<?php esc_html_e( 'Écrire sur WhatsApp', 'cerer-child' ); ?>
					</a>
					<span class="cerer-deptd-contact__number"><?php echo esc_html( $whatsapp_chef ); ?></span>
				</div>
			<?php endif; ?>

			<?php if ( $en_bref ) : ?>
				<div class="cerer-deptd-side__block">
					<p class="cerer-deptd-side__title"><?php esc_html_e( 'Le département en bref', 'cerer-child' ); ?></p>
					<div class="cerer-deptd-bref__list">
						<?php foreach ( $en_bref as $row ) : ?>
							<div class="cerer-deptd-bref__row">
								<span class="cerer-deptd-bref__label"><?php echo esc_html( $row['libelle'] ); ?></span>
								<span class="cerer-deptd-bref__value"><?php echo esc_html( $row['valeur'] ); ?></span>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( $ressources ) : ?>
				<div class="cerer-deptd-side__block">
					<p class="cerer-deptd-side__title"><?php esc_html_e( 'Ressources', 'cerer-child' ); ?></p>
					<div class="cerer-deptd-ressources__list">
						<?php foreach ( $ressources as $res ) : ?>
							<div class="cerer-deptd-ressources__item">
								<span class="cerer-deptd-ressources__dot" aria-hidden="true"></span>
								<?php if ( $res['externe'] ) : ?>
									<a href="<?php echo esc_url( $res['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $res['intitule'] ); ?> ↗</a>
								<?php else : ?>
									<a href="<?php echo esc_url( $res['url'] ); ?>"><?php echo esc_html( $res['intitule'] ); ?></a>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( $prochain_rdv ) : ?>
				<?php
				$rdv_date = get_field( 'date', $prochain_rdv->ID );
				$rdv_lieu = get_field( 'plateforme', $prochain_rdv->ID );
				$rdv_meta = array_filter( array( $rdv_date ? date_i18n( 'j F Y — H\hi', strtotime( $rdv_date ) ) : '', $rdv_lieu ) );
				?>
				<div class="cerer-deptd-side__block cerer-deptd-rdv">
					<p class="cerer-deptd-rdv__label"><?php esc_html_e( 'Prochain rendez-vous', 'cerer-child' ); ?></p>
					<p class="cerer-deptd-rdv__title"><?php echo esc_html( get_the_title( $prochain_rdv ) ); ?></p>
					<?php if ( $rdv_meta ) : ?>
						<p class="cerer-deptd-rdv__meta"><?php echo esc_html( implode( ' · ', $rdv_meta ) ); ?></p>
					<?php endif; ?>
					<a href="<?php echo esc_url( get_field( 'lien_inscription', $prochain_rdv->ID ) ?: get_permalink( $prochain_rdv ) ); ?>" class="cerer-deptd-rdv__btn"><?php esc_html_e( "S'inscrire", 'cerer-child' ); ?></a>
				</div>
			<?php endif; ?>

			<?php if ( $partenaires_dept ) : ?>
				<div class="cerer-deptd-side__block">
					<p class="cerer-deptd-side__title"><?php esc_html_e( 'Partenaires du département', 'cerer-child' ); ?></p>
					<div class="cerer-deptd-partners__grid">
						<?php foreach ( $partenaires_dept as $p ) : ?>
							<div class="cerer-deptd-partners__item">
								<?php
								$img = sprintf(
									'<img src="%1$s" alt="%2$s" loading="lazy">',
									esc_url( $p['logo']['sizes']['medium'] ?? $p['logo']['url'] ),
									esc_attr( $p['logo']['alt'] ?? '' )
								);
								if ( $p['lien'] ) {
									printf( '<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>', esc_url( $p['lien'] ), $img ); // phpcs:ignore -- $img échappé ci-dessus.
								} else {
									echo $img; // phpcs:ignore -- échappé ci-dessus.
								}
								?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( $count_offres_stage || $count_sujets_these ) : ?>
				<div class="cerer-deptd-side__block cerer-deptd-join">
					<p class="cerer-deptd-side__title"><?php esc_html_e("Rejoindre l'équipe", 'cerer-child'); ?></p>
					<div class="cerer-deptd-join__list">
						<?php if ( $count_offres_stage ) : ?>
							<a href="<?php echo esc_url( get_post_type_archive_link( 'offre_stage' ) ); ?>" class="cerer-deptd-join__link">
								<span><?php esc_html_e( 'Offres de stage', 'cerer-child' ); ?></span>
								<span class="cerer-deptd-join__count"><?php echo esc_html( $count_offres_stage ); ?></span>
							</a>
						<?php endif; ?>
						<?php if ( $count_sujets_these ) : ?>
							<a href="<?php echo esc_url( get_post_type_archive_link( 'sujet_these' ) ); ?>" class="cerer-deptd-join__link">
								<span><?php esc_html_e( 'Sujets de thèse', 'cerer-child' ); ?></span>
								<span class="cerer-deptd-join__count"><?php echo esc_html( $count_sujets_these ); ?></span>
							</a>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>
		</aside>

		<div class="cerer-deptd-content">

			<section class="cerer-deptd-presentation">
				<div class="cerer-deptd-presentation__main">
					<p class="cerer-deptd-eyebrow"><?php esc_html_e( 'Présentation', 'cerer-child' ); ?></p>
					<h2><?php esc_html_e( 'Axes de recherche du département', 'cerer-child' ); ?></h2>
					<?php if ( $presentation ) : ?>
						<?php echo wp_kses_post( $presentation ); ?>
						<?php if ( $mots_cles ) : ?>
							<div class="cerer-deptd-keywords">
								<?php foreach ( $mots_cles as $mot ) : ?>
									<span class="cerer-deptd-keyword"><?php echo esc_html( $mot ); ?></span>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					<?php elseif ( $description_longue ) : ?>
						<div class="cerer-deptd-fallback cerer-page-generique__contenu"><?php echo wp_kses_post( $description_longue ); ?></div>
					<?php endif; ?>
				</div>

				<?php if ( $chef_nom ) : ?>
					<div class="cerer-deptd-resp">
						<div class="cerer-deptd-resp__photo">
							<?php if ( ! empty( $chef_photo['url'] ) ) : ?>
								<img src="<?php echo esc_url( $chef_photo['sizes']['medium'] ?? $chef_photo['url'] ); ?>" alt="" loading="lazy">
							<?php endif; ?>
						</div>
						<p class="cerer-deptd-resp__name"><?php echo esc_html( $chef_nom ); ?></p>
						<p class="cerer-deptd-resp__role"><?php echo esc_html( $chef_fonction ); ?></p>
						<?php if ( $chef_linkedin || $chef_researchgate || $chef_instagram ) : ?>
							<div class="cerer-deptd-resp__links">
								<?php if ( $chef_linkedin ) : ?>
									<a href="<?php echo esc_url( $chef_linkedin ); ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">in</a>
								<?php endif; ?>
								<?php if ( $chef_researchgate ) : ?>
									<a href="<?php echo esc_url( $chef_researchgate ); ?>" target="_blank" rel="noopener noreferrer" aria-label="ResearchGate">RG</a>
								<?php endif; ?>
								<?php if ( $chef_instagram ) : ?>
									<a href="<?php echo esc_url( $chef_instagram ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram">IG</a>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</section>

			<?php if ( $laboratoires ) : ?>
				<section class="cerer-deptd-labs">
					<p class="cerer-deptd-eyebrow"><?php esc_html_e( 'Plateau technique', 'cerer-child' ); ?></p>
					<h2><?php esc_html_e( 'Nos trois laboratoires', 'cerer-child' ); ?></h2>

					<?php foreach ( $laboratoires as $lab ) : ?>
						<div
							class="cerer-deptd-lab<?php echo $lab['pleine_largeur'] ? ' cerer-deptd-lab--pleine-largeur' : ''; ?>"
							style="--cerer-lab-accent: <?php echo esc_attr( $lab['couleur'] ); ?>; --cerer-lab-tint: <?php echo esc_attr( $lab['encart_fond'] ); ?>;"
						>
							<div class="cerer-deptd-lab__main">
								<p class="cerer-deptd-lab__sigle"><?php echo esc_html( $lab['sigle'] ); ?></p>
								<?php if ( $lab['nom'] ) : ?><h3><?php echo esc_html( $lab['nom'] ); ?></h3><?php endif; ?>
								<?php if ( $lab['description'] ) : ?><?php echo wp_kses_post( wpautop( $lab['description'] ) ); ?><?php endif; ?>
								<?php if ( $lab['texte_complementaire'] ) : ?><p class="cerer-deptd-lab__complement"><?php echo esc_html( $lab['texte_complementaire'] ); ?></p><?php endif; ?>
							</div>
							<?php if ( $lab['libelle_liste'] && $lab['items'] ) : ?>
								<div class="cerer-deptd-lab__aside">
									<p class="cerer-deptd-lab__aside-label"><?php echo esc_html( $lab['libelle_liste'] ); ?></p>
									<ul class="cerer-deptd-lab__items<?php echo $lab['pleine_largeur'] ? ' cerer-deptd-lab__equip' : ''; ?>">
										<?php foreach ( $lab['items'] as $item ) : ?>
											<li><?php echo esc_html( $item ); ?></li>
										<?php endforeach; ?>
									</ul>
								</div>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</section>
			<?php endif; ?>

			<?php if ( $projets_dept ) : ?>
				<section>
					<p class="cerer-deptd-eyebrow"><?php esc_html_e( 'Projets & programmes', 'cerer-child' ); ?></p>
					<h2><?php esc_html_e( 'Projets en cours', 'cerer-child' ); ?></h2>
					<div class="cerer-deptd-projects">
						<?php foreach ( $projets_dept as $post ) : ?>
							<?php
							$statut      = get_field( 'statut', $post->ID ) ?: 'en-cours';
							$statut_labels = array( 'en-cours' => __( 'En cours', 'cerer-child' ), 'a-venir' => __( 'À venir', 'cerer-child' ), 'termine' => __( 'Terminé', 'cerer-child' ) );
							$progression = get_field( 'progression', $post->ID );
							$date_debut  = get_field( 'date_debut', $post->ID );
							$date_fin    = get_field( 'date_fin', $post->ID );
							$partenaires = get_field( 'partenaires', $post->ID );
							$description = get_field( 'description', $post->ID );
							$thematique  = get_the_terms( $post->ID, 'thematique' );
							$sous_domaine = ( $thematique && ! is_wp_error( $thematique ) ) ? $thematique[0]->name : $term->name;

							$periode = array_filter( array(
								$date_debut ? date_i18n( 'M Y', strtotime( $date_debut ) ) : '',
								$date_fin ? date_i18n( 'M Y', strtotime( $date_fin ) ) : '',
							) );
							?>
							<article class="cerer-deptd-project">
								<div class="cerer-deptd-project__band">
									<span class="cerer-deptd-project__subdomain"><?php echo esc_html( $sous_domaine ); ?></span>
									<span class="cerer-deptd-project__status cerer-deptd-project__status--<?php echo esc_attr( $statut ); ?>"><?php echo esc_html( $statut_labels[ $statut ] ?? $statut ); ?></span>
								</div>
								<div class="cerer-deptd-project__body">
									<h3><a href="<?php echo esc_url( get_permalink( $post ) ); ?>"><?php echo esc_html( get_the_title( $post ) ); ?></a></h3>
									<?php if ( $description ) : ?>
										<p class="cerer-deptd-project__desc"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( $description ), 20 ) ); ?></p>
									<?php endif; ?>
									<?php if ( $periode || null !== $progression ) : ?>
										<div class="cerer-deptd-project__meta">
											<span><?php echo esc_html( implode( ' – ', $periode ) ); ?></span>
											<?php if ( null !== $progression ) : ?><span><?php echo esc_html( $progression ); ?>%</span><?php endif; ?>
										</div>
									<?php endif; ?>
									<?php if ( null !== $progression ) : ?>
										<div class="cerer-deptd-project__track">
											<div class="cerer-deptd-project__fill<?php echo 'a-venir' === $statut ? ' cerer-deptd-project__fill--a-venir' : ''; ?>" style="width: <?php echo esc_attr( (int) $progression ); ?>%;"></div>
										</div>
									<?php endif; ?>
									<?php if ( $partenaires ) : ?>
										<div class="cerer-deptd-project__partners">
											<?php foreach ( array_map( 'trim', explode( ',', $partenaires ) ) as $p ) : ?>
												<span><?php echo esc_html( $p ); ?></span>
											<?php endforeach; ?>
										</div>
									<?php endif; ?>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( $video_embed || $photos_dept ) : ?>
				<section class="cerer-deptd-media" data-photos='<?php echo esc_attr( wp_json_encode( wp_list_pluck( $photos_dept, 'url' ) ) ); ?>'>
					<div class="cerer-deptd-media__head">
						<h2><?php esc_html_e( 'En images', 'cerer-child' ); ?></h2>
						<?php if ( $photos_dept ) : ?>
							<button type="button" class="cerer-deptd-media__all" data-lightbox-index="0"><?php esc_html_e( 'Toute la médiathèque', 'cerer-child' ); ?></button>
						<?php endif; ?>
					</div>

					<div class="cerer-deptd-media__grid1">
						<?php if ( $video_embed ) : ?>
							<button
								type="button"
								class="cerer-deptd-media__video"
								style="<?php echo $video_poster ? 'background-image:url(' . esc_url( $video_poster ) . ')' : ''; ?>"
								data-lightbox-video="<?php echo esc_url( $video_embed ); ?>"
								data-lightbox-title="<?php echo esc_attr( $video_titre ); ?>"
							>
								<span class="cerer-deptd-media__badge"><?php echo $video_duree ? esc_html( $video_duree ) : esc_html__( 'Vidéo', 'cerer-child' ); ?></span>
								<span class="cerer-deptd-media__play" aria-hidden="true">▶</span>
								<?php if ( $video_titre ) : ?><span class="cerer-deptd-media__caption"><?php echo esc_html( $video_titre ); ?></span><?php endif; ?>
							</button>
						<?php endif; ?>

						<?php foreach ( array_slice( $photos_visibles, 0, 2 ) as $index => $photo ) : ?>
							<button type="button" class="cerer-deptd-media__photo" style="background-image:url(<?php echo esc_url( $photo['sizes']['medium'] ?? $photo['url'] ); ?>)" data-lightbox-index="<?php echo esc_attr( $index ); ?>" aria-label="<?php esc_attr_e( 'Agrandir la photo', 'cerer-child' ); ?>"></button>
						<?php endforeach; ?>
					</div>

					<?php if ( count( $photos_visibles ) > 2 || $photos_surplus ) : ?>
						<div class="cerer-deptd-media__grid2">
							<?php foreach ( array_slice( $photos_visibles, 2, 3 ) as $offset_index => $photo ) : ?>
								<?php $real_index = $offset_index + 2; ?>
								<button type="button" class="cerer-deptd-media__thumb" style="background-image:url(<?php echo esc_url( $photo['sizes']['medium'] ?? $photo['url'] ); ?>)" data-lightbox-index="<?php echo esc_attr( $real_index ); ?>" aria-label="<?php esc_attr_e( 'Agrandir la photo', 'cerer-child' ); ?>"></button>
							<?php endforeach; ?>
							<?php if ( $photos_surplus ) : ?>
								<button type="button" class="cerer-deptd-media__more" data-lightbox-index="5">+<?php echo esc_html( $photos_surplus ); ?> <?php esc_html_e( 'voir tout', 'cerer-child' ); ?></button>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</section>

				<div id="cerer-deptd-lightbox" class="cerer-deptd-lightbox" role="dialog" aria-modal="true" hidden>
					<button type="button" class="cerer-deptd-lightbox__backdrop" data-lightbox-close aria-label="<?php esc_attr_e( 'Fermer', 'cerer-child' ); ?>"></button>
					<div class="cerer-deptd-lightbox__dialog">
						<button type="button" class="cerer-deptd-lightbox__close" data-lightbox-close aria-label="<?php esc_attr_e( 'Fermer', 'cerer-child' ); ?>">&times;</button>
						<button type="button" class="cerer-deptd-lightbox__nav cerer-deptd-lightbox__nav--prev" aria-label="<?php esc_attr_e( 'Photo précédente', 'cerer-child' ); ?>">‹</button>
						<div class="cerer-deptd-lightbox__media"></div>
						<button type="button" class="cerer-deptd-lightbox__nav cerer-deptd-lightbox__nav--next" aria-label="<?php esc_attr_e( 'Photo suivante', 'cerer-child' ); ?>">›</button>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( $publications_dept ) : ?>
				<section>
					<p class="cerer-deptd-eyebrow"><?php esc_html_e( 'Publications', 'cerer-child' ); ?></p>
					<h2><?php esc_html_e( 'Publications récentes', 'cerer-child' ); ?></h2>
					<div class="cerer-deptd-pubs">
						<?php foreach ( $publications_dept as $post ) : ?>
							<?php
							$type     = get_field( 'type', $post->ID ) ?: 'article';
							$annee    = get_field( 'annee', $post->ID );
							$auteurs  = get_field( 'auteurs', $post->ID );
							$lien_pdf = get_field( 'lien_pdf', $post->ID );
							$doi      = get_field( 'doi', $post->ID );
							?>
							<div class="cerer-deptd-pub">
								<div class="cerer-deptd-pub__main">
									<div class="cerer-deptd-pub__meta">
										<span class="cerer-deptd-pub__tag cerer-deptd-pub__tag--<?php echo esc_attr( $type ); ?>"><?php echo esc_html( $pub_type_labels[ $type ] ?? $type ); ?></span>
										<?php if ( $annee ) : ?><span class="cerer-deptd-pub__year"><?php echo esc_html( $annee ); ?></span><?php endif; ?>
									</div>
									<p class="cerer-deptd-pub__title"><a href="<?php echo esc_url( get_permalink( $post ) ); ?>"><?php echo esc_html( get_the_title( $post ) ); ?></a></p>
									<?php if ( $auteurs ) : ?><p class="cerer-deptd-pub__authors"><?php echo esc_html( $auteurs ); ?></p><?php endif; ?>
								</div>
								<?php if ( $lien_pdf || $doi ) : ?>
									<div class="cerer-deptd-pub__actions">
										<?php if ( $lien_pdf ) : ?><a href="<?php echo esc_url( $lien_pdf ); ?>" target="_blank" rel="noopener noreferrer">PDF ↗</a><?php endif; ?>
										<?php if ( $doi ) : ?><a href="<?php echo esc_url( 'https://doi.org/' . ltrim( $doi, '/' ) ); ?>" target="_blank" rel="noopener noreferrer">DOI ↗</a><?php endif; ?>
									</div>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>

		</div>
	</div>
</div>

<?php if ( $chercheurs_dept ) : ?>
	<section class="cerer-deptd-team">
		<div class="cerer-deptd-team__circle" aria-hidden="true"></div>
		<div class="cerer-container cerer-deptd-team__inner">
			<p class="cerer-deptd-eyebrow"><?php esc_html_e( 'Chercheurs du département', 'cerer-child' ); ?></p>
			<h2><?php esc_html_e( "L'équipe", 'cerer-child' ); ?></h2>
			<div class="cerer-deptd-team__grid">
				<?php foreach ( $chercheurs_dept as $post ) : ?>
					<?php
					$photo     = get_field( 'photo', $post->ID );
					$photo_url = $photo['sizes']['medium'] ?? ( $photo['url'] ?? get_the_post_thumbnail_url( $post->ID, 'medium' ) );
					?>
					<a href="<?php echo esc_url( get_permalink( $post ) ); ?>" class="cerer-deptd-team__card">
						<div class="cerer-deptd-team__photo">
							<?php if ( $photo_url ) : ?><img src="<?php echo esc_url( $photo_url ); ?>" alt="" loading="lazy"><?php endif; ?>
						</div>
						<div class="cerer-deptd-team__body">
							<p class="cerer-deptd-team__name"><?php echo esc_html( get_the_title( $post ) ); ?></p>
							<?php $titre = get_field( 'titre', $post->ID ); ?>
							<?php if ( $titre ) : ?><p class="cerer-deptd-team__role"><?php echo esc_html( $titre ); ?></p><?php endif; ?>
						</div>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php get_footer(); ?>
