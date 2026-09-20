<?php
/**
 * Template Name: Page générique (style Qui sommes-nous)
 *
 * Gabarit réutilisable pour toute page de contenu simple qui doit reprendre
 * le système visuel de "Qui sommes-nous" (en-tête bleu, sections en fond
 * alterné blanc / #F0F7E4) sans en avoir la structure spécifique à 6
 * sections. Tout le contenu est éditable via ACF (acf-json/
 * group_cerer_page_generique.json).
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

get_header();

$titre_page = get_field( 'titre_page' ) ?: get_the_title();
$chapo_page = get_field( 'chapo_page' );
$contenu    = get_field( 'contenu' );

// Brochure téléchargeable (bouton dans l'en-tête) — ex. brochures d'adhésion
// Licence / Master / Certifications professionnelles. get_field() renvoie
// NULL tant que le champ n'a jamais été enregistré, y compris pour le
// libellé qui a une valeur par défaut ACF (jamais lue avant un premier
// enregistrement) : on retombe donc explicitement sur le texte ici.
$brochure_url   = get_field( 'brochure_url' );
$brochure_label = get_field( 'brochure_label' ) ?: __( 'Télécharger la brochure', 'cerer-child' );

// Services par département — cartes icône/couleur (mêmes champs ACF que la
// section départements de l'accueil/page Départements) + services en
// badges saisis pour CETTE page (champs numérotés, ACF Free sans repeater).
$services_departement_emoji_fallback = array(
	9  => '☀️',
	10 => '🌱',
	11 => '💨',
	12 => '🌡️',
);
$services_departements = array(
	9  => 'photovoltaique',
	10 => 'biomasse',
	11 => 'eolien',
	12 => 'thermique',
);
$services_cards = array();
foreach ( $services_departements as $dept_term_id => $slug ) {
	$term = get_term( $dept_term_id, 'departement' );
	if ( ! $term || is_wp_error( $term ) ) {
		continue;
	}
	$services = array();
	for ( $i = 1; $i <= 12; $i++ ) {
		$service = get_field( "services_{$slug}_{$i}" );
		if ( $service ) {
			$services[] = $service;
		}
	}
	if ( ! $services ) {
		continue; // Carte masquée si aucun service saisi pour ce département.
	}
	$services_cards[] = array(
		'term'     => $term,
		'services' => $services,
	);
}

$services_transversaux_titre = get_field( 'services_transversaux_titre' ) ?: __( 'Autres services transversaux', 'cerer-child' );
$services_transversaux       = array();
for ( $i = 1; $i <= 8; $i++ ) {
	$service = get_field( "services_transversaux_{$i}" );
	if ( $service ) {
		$services_transversaux[] = $service;
	}
}
$show_services = $services_cards || $services_transversaux;

$points_titre = get_field( 'points_cles_titre' );
$points       = array_filter(
	array(
		get_field( 'points_cles_1' ),
		get_field( 'points_cles_2' ),
		get_field( 'points_cles_3' ),
		get_field( 'points_cles_4' ),
		get_field( 'points_cles_5' ),
		get_field( 'points_cles_6' ),
	)
);
// Titre ET points vides -> bloc masqué entièrement (même règle que les
// piliers de "Qui sommes-nous", PROMPT-18).
$show_points = $points_titre || $points;

$cta_titre = get_field( 'cta_titre' );
$cta_texte = get_field( 'cta_texte' );
$cta_label = get_field( 'cta_bouton_label' );
$cta_url   = get_field( 'cta_bouton_url' );
$show_cta  = $cta_titre && $cta_url;
?>

<!-- EN-TÊTE DE PAGE -->
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
		<?php if ( $brochure_url ) : ?>
			<a href="<?php echo esc_url( $brochure_url ); ?>" class="cerer-btn cerer-btn--secondary-blue cerer-btn--l cerer-about-hero__brochure" target="_blank" rel="noopener noreferrer" download>
				<?php echo esc_html( $brochure_label ); ?> ↓
			</a>
		<?php endif; ?>
	</div>
</header>

<!-- CONTENU PRINCIPAL (fond blanc) -->
<?php if ( $contenu ) : ?>
	<section class="cerer-about-section">
		<div class="cerer-container cerer-page-generique__contenu">
			<?php echo wp_kses_post( $contenu ); ?>
		</div>
	</section>
<?php endif; ?>

<!-- SERVICES PAR DÉPARTEMENT (fond blanc) -->
<?php if ( $show_services ) : ?>
	<section class="cerer-about-section">
		<div class="cerer-container">
			<div class="cerer-pg-services__grid">
				<?php foreach ( $services_cards as $card ) : ?>
					<?php
					$dept_term  = $card['term'];
					$dept_icone = get_field( 'icone', 'departement_' . $dept_term->term_id );
					$dept_color = get_field( 'couleur', 'departement_' . $dept_term->term_id ) ?: 'var(--cerer-blue)';
					$dept_tint  = get_field( 'teinte', 'departement_' . $dept_term->term_id ) ?: 'var(--cerer-bg)';
					?>
					<div class="cerer-pg-services__card" style="--cerer-pg-accent: <?php echo esc_attr( $dept_color ); ?>; --cerer-pg-tint: <?php echo esc_attr( $dept_tint ); ?>;">
						<div class="cerer-pg-services__head">
							<div class="cerer-pg-services__icon" aria-hidden="true">
								<?php if ( ! empty( $dept_icone['url'] ) ) : ?>
									<img src="<?php echo esc_url( $dept_icone['url'] ); ?>" alt="">
								<?php else : ?>
									<?php echo esc_html( $services_departement_emoji_fallback[ $dept_term->term_id ] ?? '🔬' ); ?>
								<?php endif; ?>
							</div>
							<h3><?php echo esc_html( $dept_term->name ); ?></h3>
							<a href="<?php echo esc_url( get_term_link( $dept_term ) ); ?>" class="cerer-pg-services__link"><?php esc_html_e( 'Voir le département', 'cerer-child' ); ?> →</a>
						</div>
						<div class="cerer-pg-services__tags">
							<?php foreach ( $card['services'] as $service ) : ?>
								<span class="cerer-pg-services__tag"><?php echo esc_html( $service ); ?></span>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endforeach; ?>

				<?php if ( $services_transversaux ) : ?>
					<div class="cerer-pg-services__card cerer-pg-services__card--transversal" style="--cerer-pg-accent: var(--cerer-green);">
						<div class="cerer-pg-services__head">
							<div class="cerer-pg-services__icon" aria-hidden="true">🛠️</div>
							<h3><?php echo esc_html( $services_transversaux_titre ); ?></h3>
						</div>
						<div class="cerer-pg-services__tags">
							<?php foreach ( $services_transversaux as $service ) : ?>
								<span class="cerer-pg-services__tag"><?php echo esc_html( $service ); ?></span>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<!-- LISTE À PUCES (fond #F0F7E4) -->
<?php if ( $show_points ) : ?>
	<section class="cerer-about-section cerer-about-section--tint">
		<div class="cerer-container">
			<?php if ( $points_titre ) : ?>
				<h2><?php echo esc_html( $points_titre ); ?></h2>
			<?php endif; ?>
			<?php if ( $points ) : ?>
				<ul class="cerer-page-generique__points">
					<?php foreach ( $points as $point ) : ?>
						<li><?php echo esc_html( $point ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</section>
<?php endif; ?>

<!-- APPEL À L'ACTION (fond blanc) -->
<?php if ( $show_cta ) : ?>
	<section class="cerer-about-section">
		<div class="cerer-container">
			<div class="cerer-page-generique__cta">
				<div>
					<h2><?php echo esc_html( $cta_titre ); ?></h2>
					<?php if ( $cta_texte ) : ?>
						<p><?php echo esc_html( $cta_texte ); ?></p>
					<?php endif; ?>
				</div>
				<a href="<?php echo esc_url( $cta_url ); ?>" class="cerer-btn cerer-btn--primary">
					<?php echo esc_html( $cta_label ?: __( 'En savoir plus', 'cerer-child' ) ); ?>
				</a>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php get_footer(); ?>
