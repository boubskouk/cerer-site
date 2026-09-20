<?php
/**
 * Template Name: Qui sommes-nous
 *
 * Reconstruite intégralement pour PROMPT-15, complétée par PROMPT-18
 * (piliers), PROMPT-20 (section "Mot du Directeur", repositionnée entre
 * "Notre histoire" et "Notre équipe" — l'alternance de fond blanc/#F0F7E4
 * a été redécalée en conséquence, cf. commentaires par section ci-dessous).
 * Tout le contenu est éditable via des champs ACF (acf-json/group_cerer_*)
 * — numérotés faute de champ Répéteur ACF Pro.
 *
 * "Coordonnées" (hors périmètre des prompts) est conservée pour ne pas
 * perdre l'adresse/tél.
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

get_header();

$titre_page = get_field( 'titre_page' ) ?: get_the_title();
$chapo_page = get_field( 'chapo_page' );

// 4 piliers (PROMPT-18 : Mission, vision, valeurs, forces) — un bloc sans
// titre NI texte est entièrement masqué plutôt que de s'afficher vide
// (valeurs_titre/texte et forces_titre/texte restent à rédiger par le client).
$piliers_source = array(
	'mission' => array(
		'label' => __( 'MISSION', 'cerer-child' ),
		'titre' => get_field( 'mission_titre' ),
		'texte' => get_field( 'mission_texte' ),
	),
	'vision'  => array(
		'label' => __( 'VISION DU CERER', 'cerer-child' ),
		'titre' => get_field( 'vision_titre' ),
		'texte' => get_field( 'vision_texte' ),
	),
	'valeurs' => array(
		'label' => __( 'NOS VALEURS', 'cerer-child' ),
		'titre' => get_field( 'valeurs_titre' ),
		'texte' => get_field( 'valeurs_texte' ),
	),
	'forces'  => array(
		'label' => __( 'FORCES DU CERER', 'cerer-child' ),
		'titre' => get_field( 'forces_titre' ),
		'texte' => get_field( 'forces_texte' ),
	),
);
$piliers = array_filter(
	$piliers_source,
	function ( $p ) {
		return $p['titre'] || $p['texte'];
	}
);

$videos   = cerer_qsn_get_videos();
$jalons   = cerer_qsn_get_jalons();
$equipe   = cerer_qsn_get_equipe();
$tutelles = cerer_qsn_get_tutelles();

// Mot du Directeur (PROMPT-20).
$directeur_nom       = get_field( 'nom' );
$directeur_fonction  = get_field( 'fonction' );
$directeur_portrait  = get_field( 'portrait' );
$directeur_message   = get_field( 'message' );
$directeur_video_url = get_field( 'video_url' );
$directeur_video_titre    = get_field( 'video_titre' );
$directeur_video_contexte = get_field( 'video_contexte' );
$directeur_linkedin     = get_field( 'lien_linkedin' );
$directeur_researchgate = get_field( 'lien_researchgate' );
$directeur_scholar      = get_field( 'lien_scholar' );
$directeur_email        = get_field( 'email_contact' );

$directeur_video_thumb = '';
if ( $directeur_video_url ) {
	$directeur_yt_id = cerer_youtube_id( $directeur_video_url );
	if ( $directeur_yt_id ) {
		$directeur_video_thumb = cerer_yt_cached_thumbnail( $directeur_yt_id );
	}
}
?>

<!-- 1. EN-TÊTE DE PAGE -->
<header class="cerer-about-hero">
	<div class="cerer-about-hero__blob" aria-hidden="true"></div>
	<div class="cerer-container cerer-about-hero__inner">
		<nav class="cerer-about-hero__breadcrumb" aria-label="<?php esc_attr_e( 'Fil d’Ariane', 'cerer-child' ); ?>">
			<a href="<?php echo esc_url( cerer_home_url() ); ?>"><?php esc_html_e( 'Accueil', 'cerer-child' ); ?></a>
			<span class="cerer-about-hero__sep" aria-hidden="true">/</span>
			<span class="cerer-about-hero__current"><?php echo esc_html( $titre_page ); ?></span>
		</nav>
		<h1><?php echo esc_html( $titre_page ); ?></h1>
		<?php if ( $chapo_page ) : ?>
			<p class="cerer-about-hero__chapo"><?php echo esc_html( $chapo_page ); ?></p>
		<?php endif; ?>
	</div>
</header>

<!-- 2. MISSION, VISION, VALEURS & FORCES (fond blanc) -->
<section class="cerer-about-section">
	<div class="cerer-container">
		<p class="cerer-about__eyebrow"><span class="cerer-about__eyebrow-rule" aria-hidden="true"></span><?php esc_html_e( "NOTRE RAISON D'ÊTRE", 'cerer-child' ); ?></p>
		<h2><?php esc_html_e( 'Mission, vision, valeurs & forces', 'cerer-child' ); ?></h2>

		<?php if ( $piliers ) : ?>
			<div class="cerer-about-mv">
				<?php foreach ( $piliers as $key => $pilier ) : ?>
					<div class="cerer-about-mv__box cerer-about-mv__box--<?php echo esc_attr( $key ); ?>">
						<p class="cerer-about-mv__label"><?php echo esc_html( $pilier['label'] ); ?></p>
						<?php if ( $pilier['titre'] ) : ?>
							<h3><?php echo esc_html( $pilier['titre'] ); ?></h3>
						<?php endif; ?>
						<?php if ( $pilier['texte'] ) : ?>
							<p><?php echo esc_html( $pilier['texte'] ); ?></p>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>

<!-- 3. LE CERER EN IMAGES (fond #F0F7E4) -->
<section class="cerer-about-section cerer-about-section--tint">
	<div class="cerer-container">
		<div class="cerer-about-videos__head">
			<div class="cerer-about-videos__head-text">
				<p class="cerer-about__eyebrow"><span class="cerer-about__eyebrow-rule" aria-hidden="true"></span><?php esc_html_e( 'EN VIDÉO', 'cerer-child' ); ?></p>
				<h2><?php esc_html_e( 'Le CERER en images', 'cerer-child' ); ?></h2>
				<p class="cerer-about__chapo"><?php esc_html_e( 'Reportages, présentations et temps forts du centre, à retrouver en intégralité sur notre chaîne.', 'cerer-child' ); ?></p>
			</div>
			<a href="https://www.youtube.com/@cerer-ucad" target="_blank" rel="noopener noreferrer" class="cerer-about-videos__channel">
				<?php esc_html_e( 'Notre chaîne YouTube', 'cerer-child' ); ?>
			</a>
		</div>

		<?php if ( $videos ) : ?>
			<div class="cerer-about-videos__grid">
				<?php foreach ( $videos as $video ) : ?>
					<a class="cerer-about-video-card" href="<?php echo esc_url( $video['url'] ); ?>" target="_blank" rel="noopener noreferrer">
						<div class="cerer-about-video-card__media">
							<?php if ( $video['thumbnail'] ) : ?>
								<img src="<?php echo esc_url( $video['thumbnail'] ); ?>" alt="">
							<?php endif; ?>
							<span class="cerer-about-video-card__play" aria-hidden="true"></span>
							<?php if ( $video['categorie'] ) : ?>
								<span class="cerer-about-video-card__badge" style="background: <?php echo esc_attr( $video['badge_bg'] ); ?>; color: <?php echo esc_attr( $video['badge_text'] ); ?>;"><?php echo esc_html( $video['categorie'] ); ?></span>
							<?php endif; ?>
						</div>
						<div class="cerer-about-video-card__body">
							<h3><?php echo esc_html( $video['titre'] ); ?></h3>
							<?php if ( $video['description'] ) : ?>
								<p><?php echo esc_html( $video['description'] ); ?></p>
							<?php endif; ?>
							<p class="cerer-about-video-card__footer"><?php esc_html_e( 'Regarder sur YouTube', 'cerer-child' ); ?> ↗</p>
						</div>
					</a>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<p class="cerer-about__empty"><?php esc_html_e( 'Aucune vidéo pour le moment.', 'cerer-child' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<!-- 4. NOTRE HISTOIRE (fond blanc) -->
<section class="cerer-about-section">
	<div class="cerer-container">
		<p class="cerer-about__eyebrow"><span class="cerer-about__eyebrow-rule" aria-hidden="true"></span><?php esc_html_e( 'DEPUIS 1980', 'cerer-child' ); ?></p>
		<h2><?php esc_html_e( 'Notre histoire', 'cerer-child' ); ?></h2>

		<?php if ( $jalons ) : ?>
			<ol class="cerer-about-timeline">
				<?php foreach ( $jalons as $jalon ) : ?>
					<li class="cerer-about-timeline__item<?php echo ! empty( $jalon['is_current'] ) ? ' is-current' : ''; ?>">
						<span class="cerer-about-timeline__dot" aria-hidden="true"></span>
						<span class="cerer-about-timeline__year"><?php echo esc_html( $jalon['annee'] ); ?></span>
						<span class="cerer-about-timeline__label"><?php echo esc_html( $jalon['libelle'] ); ?></span>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>
	</div>
</section>

<!-- 5. MOT DU DIRECTEUR (fond #F0F7E4) — PROMPT-20 -->
<section id="mot-du-directeur" class="cerer-about-section cerer-about-section--tint cerer-director-section">
	<div class="cerer-director-section__blob" aria-hidden="true"></div>
	<div class="cerer-container cerer-director-section__inner">
		<p class="cerer-about__eyebrow"><span class="cerer-about__eyebrow-rule" aria-hidden="true"></span><?php esc_html_e( 'GOUVERNANCE', 'cerer-child' ); ?></p>
		<h2><?php esc_html_e( 'Mot du Directeur', 'cerer-child' ); ?></h2>

		<div class="cerer-director-card">
			<div class="cerer-director-card__left">
				<div class="cerer-director-card__portrait<?php echo empty( $directeur_portrait['url'] ) ? ' is-placeholder' : ''; ?>">
					<?php if ( ! empty( $directeur_portrait['url'] ) ) : ?>
						<img src="<?php echo esc_url( $directeur_portrait['sizes']['medium'] ?? $directeur_portrait['url'] ); ?>" alt="" loading="lazy">
					<?php endif; ?>
				</div>
				<div>
					<h3 class="cerer-director-card__name"><?php echo esc_html( $directeur_nom ); ?></h3>
					<?php if ( $directeur_fonction ) : ?>
						<p class="cerer-director-card__role"><?php echo esc_html( $directeur_fonction ); ?></p>
					<?php endif; ?>
				</div>

				<?php if ( $directeur_linkedin || $directeur_researchgate || $directeur_scholar || $directeur_email ) : ?>
					<div>
						<p class="cerer-director-card__social-label"><?php esc_html_e( 'LE SUIVRE', 'cerer-child' ); ?></p>
						<div class="cerer-director-card__pills">
							<?php if ( $directeur_linkedin ) : ?>
								<a href="<?php echo esc_url( $directeur_linkedin ); ?>" target="_blank" rel="noopener noreferrer" class="cerer-director-card__pill">LinkedIn ↗</a>
							<?php endif; ?>
							<?php if ( $directeur_researchgate ) : ?>
								<a href="<?php echo esc_url( $directeur_researchgate ); ?>" target="_blank" rel="noopener noreferrer" class="cerer-director-card__pill">ResearchGate ↗</a>
							<?php endif; ?>
							<?php if ( $directeur_scholar ) : ?>
								<a href="<?php echo esc_url( $directeur_scholar ); ?>" target="_blank" rel="noopener noreferrer" class="cerer-director-card__pill">Google Scholar ↗</a>
							<?php endif; ?>
							<?php if ( $directeur_email ) : ?>
								<a href="mailto:<?php echo esc_attr( $directeur_email ); ?>" class="cerer-director-card__pill"><?php esc_html_e( 'E-mail', 'cerer-child' ); ?></a>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>

			<div class="cerer-director-card__right">
				<?php if ( $directeur_message ) : ?>
					<div class="cerer-director-card__message"><?php echo wp_kses_post( $directeur_message ); ?></div>
				<?php endif; ?>

				<?php if ( $directeur_video_url ) : ?>
					<a class="cerer-director-video" href="<?php echo esc_url( $directeur_video_url ); ?>" target="_blank" rel="noopener noreferrer">
						<div class="cerer-director-video__media">
							<?php if ( $directeur_video_thumb ) : ?>
								<img src="<?php echo esc_url( $directeur_video_thumb ); ?>" alt="">
							<?php endif; ?>
							<span class="cerer-director-video__play" aria-hidden="true"></span>
							<span class="cerer-director-video__badge"><?php esc_html_e( 'MESSAGE VIDÉO', 'cerer-child' ); ?></span>
						</div>
						<div class="cerer-director-video__body">
							<h3><?php echo esc_html( $directeur_video_titre ?: __( 'Message du Directeur', 'cerer-child' ) ); ?></h3>
							<?php if ( $directeur_video_contexte ) : ?>
								<p class="cerer-director-video__meta"><?php echo esc_html( $directeur_video_contexte ); ?></p>
							<?php endif; ?>
							<p class="cerer-director-video__cta"><?php esc_html_e( 'Regarder la vidéo', 'cerer-child' ); ?> ↗</p>
						</div>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>

<!-- 6. NOTRE ÉQUIPE (fond blanc — PROMPT-20 : était #F0F7E4) -->
<section class="cerer-about-section">
	<div class="cerer-container">
		<div class="cerer-about-team__head">
			<div>
				<p class="cerer-about__eyebrow"><span class="cerer-about__eyebrow-rule" aria-hidden="true"></span><?php esc_html_e( 'CHERCHEURS & ENSEIGNANTS', 'cerer-child' ); ?></p>
				<h2><?php esc_html_e( 'Notre équipe', 'cerer-child' ); ?></h2>
			</div>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'chercheur' ) ); ?>" class="cerer-about-team__all">
				<?php esc_html_e( "Voir toute l'équipe", 'cerer-child' ); ?>
			</a>
		</div>

		<?php if ( $equipe ) : ?>
			<div class="cerer-about-team__grid">
				<?php foreach ( $equipe as $chercheur ) :
					$photo       = get_field( 'photo', $chercheur->ID );
					$titre       = get_field( 'titre', $chercheur->ID );
					$dept_terms  = get_the_terms( $chercheur->ID, 'departement' );
					$dept        = ( $dept_terms && ! is_wp_error( $dept_terms ) ) ? $dept_terms[0]->name : '';
					$fonction    = trim( implode( ' — ', array_filter( array( $titre, $dept ) ) ) );
					$linkedin    = get_field( 'lien_linkedin', $chercheur->ID );
					$researchgate = get_field( 'lien_researchgate', $chercheur->ID );
					$instagram   = get_field( 'lien_instagram', $chercheur->ID );
					?>
					<div class="cerer-about-team__card">
						<div class="cerer-about-team__photo">
							<?php if ( ! empty( $photo['url'] ) ) : ?>
								<img src="<?php echo esc_url( $photo['sizes']['medium'] ?? $photo['url'] ); ?>" alt="" loading="lazy">
							<?php endif; ?>
						</div>
						<div class="cerer-about-team__body">
							<h3><?php echo esc_html( get_the_title( $chercheur ) ); ?></h3>
							<?php if ( $fonction ) : ?>
								<p class="cerer-about-team__fonction"><?php echo esc_html( $fonction ); ?></p>
							<?php endif; ?>
							<?php if ( $linkedin || $researchgate || $instagram ) : ?>
								<div class="cerer-about-team__pills">
									<?php if ( $linkedin ) : ?>
										<a href="<?php echo esc_url( $linkedin ); ?>" target="_blank" rel="noopener noreferrer" class="cerer-about-team__pill">LinkedIn</a>
									<?php endif; ?>
									<?php if ( $researchgate ) : ?>
										<a href="<?php echo esc_url( $researchgate ); ?>" target="_blank" rel="noopener noreferrer" class="cerer-about-team__pill">ResearchGate</a>
									<?php endif; ?>
									<?php if ( $instagram ) : ?>
										<a href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noopener noreferrer" class="cerer-about-team__pill">Instagram</a>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<p class="cerer-about__empty"><?php esc_html_e( "Membres de l'équipe à renseigner depuis l'admin (CPT « Chercheurs »).", 'cerer-child' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<!-- 7. NOS TUTELLES (fond #F0F7E4 — PROMPT-20 : était blanc) -->
<section class="cerer-about-section cerer-about-section--tint">
	<div class="cerer-container">
		<p class="cerer-about__eyebrow"><span class="cerer-about__eyebrow-rule" aria-hidden="true"></span><?php esc_html_e( 'RATTACHEMENT INSTITUTIONNEL', 'cerer-child' ); ?></p>
		<h2><?php esc_html_e( 'Nos tutelles', 'cerer-child' ); ?></h2>

		<?php if ( $tutelles ) : ?>
			<div class="cerer-about-tutelles__grid">
				<?php foreach ( $tutelles as $tutelle ) :
					$tag       = $tutelle['url'] ? 'a' : 'div';
					$tag_attrs = $tutelle['url'] ? ' href="' . esc_url( $tutelle['url'] ) . '" target="_blank" rel="noopener noreferrer"' : '';
					?>
					<<?php echo $tag . $tag_attrs; // phpcs:ignore -- balise + attributs déjà échappés ci-dessus. ?> class="cerer-about-tutelle">
						<?php if ( $tutelle['has_logo'] ) : ?>
							<img class="cerer-about-tutelle__logo<?php echo $tutelle['encadre'] ? ' cerer-about-tutelle__logo--encadre' : ''; ?>" src="<?php echo esc_url( $tutelle['logo_url'] ); ?>" alt="<?php echo esc_attr( $tutelle['logo_alt'] ); ?>">
						<?php else : ?>
							<span class="cerer-about-tutelle__logo cerer-about-tutelle__logo--placeholder" aria-hidden="true"></span>
						<?php endif; ?>
						<span>
							<span class="cerer-about-tutelle__sigle"><?php echo esc_html( $tutelle['sigle'] ); ?></span>
							<?php if ( $tutelle['intitule'] ) : ?>
								<span class="cerer-about-tutelle__intitule"><?php echo esc_html( $tutelle['intitule'] ); ?></span>
							<?php endif; ?>
						</span>
					</<?php echo esc_html( $tag ); ?>>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php /* RESTE À TRAITER PLUS TARD (arbitrages de design non tranchés) :
		<!-- Gouvernance : organigramme Direction / Départements / Services transversaux -->
		<!-- Carte des partenaires mondiaux -->
		*/ ?>
	</div>
</section>

<!-- Coordonnées (hors périmètre des prompts, conservée — fond blanc) -->
<section class="cerer-about-section">
	<div class="cerer-container">
		<h2><?php esc_html_e( 'Coordonnées', 'cerer-child' ); ?></h2>
		<address class="cerer-meta" style="font-style:normal; margin-top:12px; display:flex; flex-direction:column; gap:6px;">
			<span><?php esc_html_e( 'Route du Service Géographique (HB-87) x Rue HB-478, Hann Bel-Air, B.P. 476, Dakar, Sénégal', 'cerer-child' ); ?></span>
			<span><a href="tel:+221765772252">+(221) 76 577 22 52</a></span>
			<span><a href="https://cerer.ucad.sn" target="_blank" rel="noopener noreferrer">cerer.ucad.sn</a></span>
			<span style="font-style:italic; margin-top:8px;">« <?php esc_html_e( "L'Énergie, tout naturellement", 'cerer-child' ); ?> »</span>
		</address>
	</div>
</section>

<?php get_footer(); ?>
