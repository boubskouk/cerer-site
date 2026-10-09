<?php
/**
 * Page d'accueil — utilisé automatiquement par WordPress pour le front
 * (hiérarchie de templates), que "Afficher sur la page d'accueil" soit
 * réglé sur une page statique ou les derniers articles.
 *
 * Structure inspirée de la page d'accueil de l'ANER (aner.sn) : Hero →
 * Chiffres clés (avec intro + CTA) → Actualités → Projets phares →
 * Départements → Agenda (webinaires) → Partenaires. Contenu et visuels
 * restent propres au CERER ; seul l'enchaînement des blocs est repris.
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

get_header();

// Départements de recherche (accueil) — PROMPT-11 (départements-accueil) :
// boucle sur la taxonomie "departement" plutôt que 4 cartes en dur ; icône,
// couleur, teinte et description viennent des champs du terme
// (acf-json/group_cerer_departement_meta.json). Émoji de repli tant
// qu'aucun PNG définitif n'est importé sur le terme.
$departement_emoji_fallback = array(
	9  => '☀️', // Photovoltaïque
	10 => '🌱', // Biomasse & Bioénergie
	11 => '💨', // Éolien
	12 => '🌡️', // Thermique
);

// Dernières actualités (PROMPT-14) : le rendu de la grille est mutualisé
// avec le filtre AJAX, cf. inc/actualites-accueil.php.
$actualites_categories = cerer_get_actualites_categories();
// PROMPT-17 : la section ne doit jamais disparaître faute de traduction —
// elle reste affichée si la langue par défaut a du contenu à défaut de la
// langue courante (cerer_render_actualites_grid applique le même repli).
$actualites_has_any = (bool) cerer_posts_with_fallback( array( 'post_type' => 'post', 'fields' => 'ids' ), 1 );

// Projets phares (PROMPT-12) — uniquement les projets marqués "en_avant"
// (champ ACF booléen), triés par date de début décroissante. Pas de repli
// sur les projets récents : tant qu'aucun n'est marqué "en avant", les
// emplacements restent vides plutôt que de montrer un projet choisi au
// hasard (contenu réel pas encore décidé).
$projets_phares = get_posts(
	array(
		'post_type'      => 'projet',
		'posts_per_page' => -1,
		'meta_key'       => 'en_avant',
		'meta_value'     => '1',
	)
);
// Tri par date de début décroissante — un simple 'orderby' => 'date' trierait
// sur la date de publication, pas la date de début du projet (champ ACF).
usort(
	$projets_phares,
	function ( $a, $b ) {
		return strtotime( get_field( 'date_debut', $b->ID ) ?: '1970-01-01' ) <=> strtotime( get_field( 'date_debut', $a->ID ) ?: '1970-01-01' );
	}
);
$projets_phares = array_slice( $projets_phares, 0, 3 );

// Couleur du bandeau/de la piste d'avancement par département (PROMPT-12) —
// résolue via l'ID du terme FRANÇAIS (stable dans les deux langues, Polylang
// lie les traductions entre elles) pour ne pas dépendre du nom affiché.
$dept_class_map = array(
	9  => 'dept-photovoltaique', // Photovoltaïque
	10 => 'dept-biomasse',       // Biomasse & Bioénergie
	11 => 'dept-eolien',         // Éolien
	12 => 'dept-thermique',      // Thermique
);

// Webinaires (accueil) — PROMPT-13 : le tri à venir/passé se fait sur la
// date, pas sur le champ "statut" (un webinaire dont la date est dépassée
// bascule seul en replay, sans qu'un éditeur ait à changer un statut à la main).
$now = current_time( 'Y-m-d H:i:s' );

// PROMPT-17 : les deux requêtes complètent la langue courante avec la
// langue par défaut si besoin (cerer_posts_with_fallback, inc/helpers.php)
// — sans quoi un webinaire non encore traduit fait retomber la grille de
// replays à une seule carte au lieu de deux.
$webinaire_prochain = cerer_posts_with_fallback(
	array(
		'post_type'  => 'webinaire',
		'meta_query' => array(
			array(
				'key'     => 'date',
				'value'   => $now,
				'compare' => '>=',
				'type'    => 'DATETIME',
			),
		),
		'orderby'        => 'meta_value',
		'meta_key'       => 'date',
		'meta_type'      => 'DATETIME',
		'order'          => 'ASC',
	),
	1
);
$webinaire_prochain = $webinaire_prochain ? $webinaire_prochain[0] : null;

$webinaires_replays = cerer_posts_with_fallback(
	array(
		'post_type'      => 'webinaire',
		'meta_query'     => array(
			array(
				'key'     => 'date',
				'value'   => $now,
				'compare' => '<',
				'type'    => 'DATETIME',
			),
		),
		'orderby'        => 'meta_value',
		'meta_key'       => 'date',
		'meta_type'      => 'DATETIME',
		'order'          => 'DESC',
	),
	2
);

// Partenaires — CPT "partenaire" (logo + lien en champs ACF), alimenté par
// l'équipe du CERER depuis l'admin. Section masquée tant qu'elle est vide :
// pas de logo ni de nom de partenaire inventé.
// Remplace un champ répétable sur une page d'options : Repeater et Options
// Page sont des fonctionnalités ACF Pro, or seule la version gratuite est
// installée sur ce site (vérifié via acf_get_field_types() — ni "repeater"
// ni "gallery" n'y sont déclarés). Même bug que celui trouvé sur le
// diaporama du hero (PROMPT-8), corrigé ici de la même façon : un CPT
// classique plutôt qu'une fonctionnalité Pro absente.
$partenaires_accueil = get_posts(
	array(
		'post_type'      => 'partenaire',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	)
);

// Diaporama du hero (PROMPT-8) — 5 champs Image séparés plutôt qu'un champ
// Galerie : ACF Galerie/Repeater sont des fonctionnalités Pro, or seule la
// version gratuite est installée sur ce site (vérifié : acf_get_field_types()
// ne déclare ni "gallery" ni "repeater"). Champs tous vides ou ACF absent :
// le hero retombe sur l'aplat bleu, pas d'écran cassé.
$hero_slides = array();
if ( function_exists( 'get_field' ) ) {
	for ( $i = 1; $i <= 5; $i++ ) {
		$slide = get_field( 'hero_slide_' . $i );
		if ( $slide ) {
			$hero_slides[] = $slide;
		}
	}
}
?>

<!-- Hero -->
<section class="cerer-hero<?php echo $hero_slides ? ' cerer-hero--slider' : ''; ?>">
	<?php if ( $hero_slides ) : ?>
		<div class="cerer-hero__photos" aria-hidden="true">
			<?php foreach ( $hero_slides as $i => $slide ) : ?>
				<img
					src="<?php echo esc_url( $slide['sizes']['hero_slide'] ?? $slide['url'] ); ?>"
					alt="<?php echo esc_attr( $slide['alt'] ); ?>"
					class="cerer-hero__photo<?php echo 0 === $i ? ' is-active' : ''; ?>"
					<?php if ( 0 === $i ) : ?>
						loading="eager" fetchpriority="high"
					<?php else : ?>
						loading="lazy"
					<?php endif; ?>
				>
			<?php endforeach; ?>
		</div>
		<div class="cerer-hero__veil"></div>
	<?php endif; ?>

	<div class="cerer-container cerer-hero__inner">
		<span class="cerer-badge-hero"><?php esc_html_e( "Institut de l'UCAD depuis 1980", 'cerer-child' ); ?></span>
		<h1><?php esc_html_e( "L'énergie, tout naturellement", 'cerer-child' ); ?></h1>
		<p><?php esc_html_e( "Le Centre d'Études et de Recherches sur les Énergies Renouvelables développe la recherche, la formation et l'innovation au service de la transition énergétique au Sénégal et en Afrique.", 'cerer-child' ); ?></p>
		<div class="cerer-hero__actions">
			<a href="<?php echo esc_url( cerer_page_url( 66 ) ); ?>" class="cerer-btn cerer-btn--primary cerer-btn--l"><?php esc_html_e( 'Qui sommes-nous', 'cerer-child' ); ?></a>
			<a href="<?php echo esc_url( cerer_page_url( 78 ) ); ?>" class="cerer-btn cerer-btn--secondary-blue cerer-btn--l"><?php esc_html_e( 'Découvrir nos départements', 'cerer-child' ); ?></a>
		</div>
	</div>

	<?php if ( count( $hero_slides ) > 1 ) : ?>
		<div class="cerer-hero__dots" role="tablist" aria-label="<?php esc_attr_e( 'Visuels du hero', 'cerer-child' ); ?>">
			<?php foreach ( $hero_slides as $i => $slide ) : ?>
				<button
					type="button"
					class="cerer-hero__dot<?php echo 0 === $i ? ' is-active' : ''; ?>"
					aria-label="<?php echo esc_attr( sprintf( __( 'Afficher le visuel %d', 'cerer-child' ), $i + 1 ) ); ?>"
					<?php echo 0 === $i ? 'aria-current="true"' : ''; ?>
				></button>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>

<!-- Chiffres clés — PROMPT-11. Contenu entièrement en champs ACF (texte
     simples : pas de champ répétable, ACF Pro non installé sur ce site,
     cf. acf-json/group_cerer_home_stats.json), avec repli sur les valeurs
     par défaut si un champ est vide. -->
<?php
$stats_eyebrow = get_field( 'home_stats_eyebrow' ) ?: __( 'Chiffres clés', 'cerer-child' );
$stats_title   = get_field( 'home_stats_title' ) ?: __( 'Le CERER en quelques chiffres', 'cerer-child' );
$stats_intro   = get_field( 'home_stats_intro' ) ?: __( 'Depuis 1980, le CERER forme, publie et innove au service de la transition énergétique du Sénégal et de la sous-région.', 'cerer-child' );

$stats_defaults = array(
	1 => array( 'value' => '45', 'suffix' => '+', 'label' => __( "Années d'existence", 'cerer-child' ) ),
	2 => array( 'value' => '4', 'suffix' => '', 'label' => __( 'Départements de recherche', 'cerer-child' ) ),
	3 => array( 'value' => '120', 'suffix' => '+', 'label' => __( 'Publications scientifiques', 'cerer-child' ) ),
	4 => array( 'value' => '30', 'suffix' => '+', 'label' => __( 'Chercheurs et enseignants-chercheurs', 'cerer-child' ) ),
);
?>
<section class="cerer-stats">
	<div class="cerer-container cerer-stats__row">
		<div class="cerer-stats__col-text">
			<p class="cerer-stats__eyebrow"><span class="cerer-stats__eyebrow-rule" aria-hidden="true"></span><?php echo esc_html( $stats_eyebrow ); ?></p>
			<h2><?php echo esc_html( $stats_title ); ?></h2>
			<p class="cerer-stats__chapo"><?php echo esc_html( $stats_intro ); ?></p>
			<div class="cerer-stats__actions">
				<a href="<?php echo esc_url( cerer_page_url( 66 ) ); ?>" class="cerer-stats__btn"><?php esc_html_e( 'Découvrir qui nous sommes', 'cerer-child' ); ?></a>
				<a href="<?php echo esc_url( cerer_page_url( 78 ) ); ?>" class="cerer-stats__btn cerer-stats__btn--secondary"><?php esc_html_e( 'Voir nos départements', 'cerer-child' ); ?></a>
			</div>
		</div>
		<div class="cerer-stats__col-numbers">
			<div class="cerer-stats__grid">
				<?php foreach ( $stats_defaults as $i => $default ) : ?>
					<?php
					$value  = get_field( "home_stats_{$i}_value" ) ?: $default['value'];
					$suffix = get_field( "home_stats_{$i}_suffix" );
					$suffix = ( '' !== $suffix && null !== $suffix ) ? $suffix : $default['suffix'];
					$label  = get_field( "home_stats_{$i}_label" ) ?: $default['label'];
					?>
					<div class="cerer-stat">
						<p class="cerer-stat__number">
							<span class="cerer-stat__value" data-count-to="<?php echo esc_attr( (int) $value ); ?>">0</span><?php if ( $suffix ) : ?><span class="cerer-stat__suffix"><?php echo esc_html( $suffix ); ?></span><?php endif; ?>
						</p>
						<p class="cerer-stat__label"><?php echo esc_html( $label ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>

<!-- Dernières actualités — PROMPT-14 (déplacée juste après Chiffres clés) -->
<?php if ( $actualites_has_any ) : ?>
<?php
$news_eyebrow = get_field( 'home_news_eyebrow' ) ?: __( 'Actualités', 'cerer-child' );
$news_title   = get_field( 'home_news_title' ) ?: __( 'Dernières actualités', 'cerer-child' );
$news_intro   = get_field( 'home_news_intro' );
?>
<section class="cerer-news">
	<div class="cerer-news__blob" aria-hidden="true"></div>
	<div class="cerer-container cerer-news__inner">
		<div class="cerer-projects__head">
			<div class="cerer-projects__head-text">
				<p class="cerer-stats__eyebrow"><span class="cerer-stats__eyebrow-rule" aria-hidden="true"></span><?php echo esc_html( $news_eyebrow ); ?></p>
				<h2><?php echo esc_html( $news_title ); ?></h2>
				<?php if ( $news_intro ) : ?>
					<p class="cerer-stats__chapo"><?php echo esc_html( $news_intro ); ?></p>
				<?php endif; ?>
			</div>
			<a href="<?php echo esc_url( cerer_page_url( 80 ) ); ?>" class="cerer-projects__all"><?php esc_html_e( 'Toutes les actualités', 'cerer-child' ); ?></a>
		</div>

		<?php if ( $actualites_categories ) : ?>
			<div class="cerer-news__filters" role="group" aria-label="<?php esc_attr_e( "Filtrer les actualités par catégorie", 'cerer-child' ); ?>">
				<button type="button" class="cerer-news__pill is-active" data-categorie="" aria-pressed="true"><?php esc_html_e( 'Toutes', 'cerer-child' ); ?></button>
				<?php foreach ( $actualites_categories as $cat ) : ?>
					<button type="button" class="cerer-news__pill" data-categorie="<?php echo esc_attr( $cat->slug ); ?>" aria-pressed="false"><?php echo esc_html( $cat->name ); ?></button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<div class="cerer-news__grid" id="cerer-news-grid">
			<?php echo cerer_render_actualites_grid( '' ); // phpcs:ignore -- gabarit interne, déjà échappé. ?>
		</div>
	</div>
</section>
<?php endif; ?>

<!-- Projets phares — PROMPT-12. Toujours affichée, même sans aucun projet
     "en avant" : les emplacements non pourvus montrent une carte vide
     plutôt que de faire disparaître la section ou d'afficher un projet
     choisi au hasard. -->
<?php
$projects_eyebrow = get_field( 'home_projects_eyebrow' ) ?: __( 'Projets & programmes', 'cerer-child' );
$projects_title   = get_field( 'home_projects_title' ) ?: __( 'Nos projets phares', 'cerer-child' );
$projects_intro   = get_field( 'home_projects_intro' );
?>
<section class="cerer-projects">
	<div class="cerer-projects__blob" aria-hidden="true"></div>
	<div class="cerer-container cerer-projects__inner">
		<div class="cerer-projects__head">
			<div class="cerer-projects__head-text">
				<p class="cerer-stats__eyebrow"><span class="cerer-stats__eyebrow-rule" aria-hidden="true"></span><?php echo esc_html( $projects_eyebrow ); ?></p>
				<h2><?php echo esc_html( $projects_title ); ?></h2>
				<?php if ( $projects_intro ) : ?>
					<p class="cerer-stats__chapo"><?php echo esc_html( $projects_intro ); ?></p>
				<?php endif; ?>
			</div>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'projet' ) ); ?>" class="cerer-projects__all"><?php esc_html_e( 'Tous les projets', 'cerer-child' ); ?> →</a>
		</div>

		<div class="cerer-projects__grid">
			<?php for ( $i = 0; $i < 3; $i++ ) : ?>
				<?php if ( isset( $projets_phares[ $i ] ) ) : ?>
					<?php
					$post       = $projets_phares[ $i ];
					$dept_terms = get_the_terms( $post->ID, 'departement' );
					$dept_term  = ( $dept_terms && ! is_wp_error( $dept_terms ) ) ? $dept_terms[0] : null;
					$dept_label = $dept_term ? $dept_term->name : '';
					$dept_fr_id = $dept_term ? $dept_term->term_id : 0;
					if ( $dept_term && function_exists( 'pll_get_term' ) ) {
						$dept_fr_id = pll_get_term( $dept_term->term_id, 'fr' ) ?: $dept_term->term_id;
					}

					get_template_part(
						'template-parts/carte-projet-phare',
						null,
						array(
							'url'         => get_permalink( $post ),
							'title'       => get_the_title( $post ),
							'description' => get_field( 'description', $post->ID ),
							'statut'      => get_field( 'statut', $post->ID ) ?: 'en-cours',
							'progression' => get_field( 'progression', $post->ID ),
							'date_debut'  => get_field( 'date_debut', $post->ID ),
							'date_fin'    => get_field( 'date_fin', $post->ID ),
							'partenaires' => get_field( 'partenaires', $post->ID ),
							'dept_label'  => $dept_label,
							'dept_class'  => $dept_class_map[ $dept_fr_id ] ?? '',
						)
					);
					?>
				<?php else : ?>
					<?php get_template_part( 'template-parts/carte-projet-phare-vide' ); ?>
				<?php endif; ?>
			<?php endfor; ?>
		</div>
	</div>
</section>

<!-- Départements de recherche — PROMPT-11 (départements-accueil) -->
<?php
$dept_home_eyebrow = get_field( 'home_departments_eyebrow' ) ?: __( 'Domaines de recherche', 'cerer-child' );
$dept_home_title   = get_field( 'home_departments_title' ) ?: __( 'Nos départements de recherche', 'cerer-child' );
$dept_home_intro   = get_field( 'home_departments_intro' );
$departement_terms = get_terms(
	array(
		'taxonomy'   => 'departement',
		'hide_empty' => false,
		'orderby'    => 'term_id',
		'order'      => 'ASC',
	)
);
?>
<?php if ( $departement_terms && ! is_wp_error( $departement_terms ) ) : ?>
<section id="cerer-departements" class="cerer-departments">
	<div class="cerer-departments__blob" aria-hidden="true"></div>
	<div class="cerer-container cerer-departments__inner">
		<div class="cerer-projects__head">
			<div class="cerer-projects__head-text">
				<p class="cerer-stats__eyebrow"><span class="cerer-stats__eyebrow-rule" aria-hidden="true"></span><?php echo esc_html( $dept_home_eyebrow ); ?></p>
				<h2><?php echo esc_html( $dept_home_title ); ?></h2>
				<?php if ( $dept_home_intro ) : ?>
					<p class="cerer-stats__chapo"><?php echo esc_html( $dept_home_intro ); ?></p>
				<?php endif; ?>
			</div>
			<a href="<?php echo esc_url( cerer_page_url( 78 ) ); ?>" class="cerer-projects__all"><?php esc_html_e( 'Tous les départements', 'cerer-child' ); ?></a>
		</div>

		<div class="cerer-departments__grid">
			<?php foreach ( $departement_terms as $term ) : ?>
				<?php
				$fr_term_id = $term->term_id;
				if ( function_exists( 'pll_get_term' ) ) {
					$fr_term_id = pll_get_term( $term->term_id, 'fr' ) ?: $term->term_id;
				}
				get_template_part(
					'template-parts/carte-departement-accueil',
					null,
					array(
						'url'         => get_term_link( $term ),
						'title'       => $term->name,
						'description' => $term->description,
						'couleur'     => get_field( 'couleur', 'departement_' . $term->term_id ) ?: 'var(--cerer-blue-dark)',
						'teinte'      => get_field( 'teinte', 'departement_' . $term->term_id ) ?: 'var(--cerer-bg)',
						'icone'       => get_field( 'icone', 'departement_' . $term->term_id ),
						'icone_repli' => $departement_emoji_fallback[ $fr_term_id ] ?? '🔬',
					)
				);
				?>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<!-- Webinaires — PROMPT-13 -->
<?php if ( $webinaire_prochain || $webinaires_replays ) : ?>
<?php
$webinars_eyebrow = get_field( 'home_webinars_eyebrow' ) ?: __( 'Agenda', 'cerer-child' );
$webinars_title   = get_field( 'home_webinars_title' ) ?: __( 'Nos webinaires', 'cerer-child' );
$webinars_intro   = get_field( 'home_webinars_intro' );
?>
<section class="cerer-webinars">
	<div class="cerer-webinars__blob" aria-hidden="true"></div>
	<div class="cerer-container cerer-webinars__inner">
		<div class="cerer-projects__head">
			<div class="cerer-projects__head-text">
				<p class="cerer-stats__eyebrow"><span class="cerer-stats__eyebrow-rule" aria-hidden="true"></span><?php echo esc_html( $webinars_eyebrow ); ?></p>
				<h2><?php echo esc_html( $webinars_title ); ?></h2>
				<?php if ( $webinars_intro ) : ?>
					<p class="cerer-stats__chapo"><?php echo esc_html( $webinars_intro ); ?></p>
				<?php endif; ?>
			</div>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'webinaire' ) ); ?>" class="cerer-projects__all"><?php esc_html_e( 'Tous les webinaires', 'cerer-child' ); ?></a>
		</div>

		<?php if ( $webinaire_prochain ) : ?>
			<?php get_template_part( 'template-parts/carte-webinaire-vedette', null, array( 'post_id' => $webinaire_prochain->ID ) ); ?>
		<?php endif; ?>

		<?php if ( $webinaires_replays ) : ?>
			<div class="cerer-webinars__replays-head">
				<h3><?php esc_html_e( 'Les deux derniers webinaires', 'cerer-child' ); ?></h3>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'webinaire' ) ); ?>" class="cerer-webinars__replays-link"><?php esc_html_e( 'Tous les replays', 'cerer-child' ); ?></a>
			</div>
			<div class="cerer-webinars__replays-grid">
				<?php foreach ( $webinaires_replays as $post ) : ?>
					<?php get_template_part( 'template-parts/carte-webinaire-replay', null, array( 'post_id' => $post->ID ) ); ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>

<!-- Partenaires — CPT "Partenaires" dans l'admin, masqué si vide. -->
<?php if ( $partenaires_accueil ) : ?>
<section class="cerer-container cerer-section cerer-partners">
	<div class="cerer-section__head">
		<div>
			<p class="cerer-label cerer-eyebrow"><?php esc_html_e( 'Ils nous accompagnent', 'cerer-child' ); ?></p>
			<h2><?php esc_html_e( 'Nos partenaires', 'cerer-child' ); ?></h2>
		</div>
	</div>
	<div class="cerer-partners__grid">
		<?php foreach ( $partenaires_accueil as $partenaire ) : ?>
			<?php
			$nom  = get_the_title( $partenaire );
			$logo = get_field( 'logo', $partenaire->ID );
			$lien = get_field( 'lien', $partenaire->ID );
			if ( ! $logo ) {
				continue;
			}
			$logo_html = sprintf(
				'<img src="%1$s" alt="%2$s" loading="lazy">',
				esc_url( $logo['sizes']['medium'] ?? $logo['url'] ),
				esc_attr( $nom )
			);
			?>
			<div class="cerer-partners__item">
				<?php if ( $lien ) : ?>
					<a href="<?php echo esc_url( $lien ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $nom ); ?>">
						<?php echo $logo_html; // phpcs:ignore -- balise img construite et échappée ci-dessus. ?>
					</a>
				<?php else : ?>
					<?php echo $logo_html; // phpcs:ignore -- balise img construite et échappée ci-dessus. ?>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
</section>
<?php endif; ?>

<?php get_footer(); ?>
