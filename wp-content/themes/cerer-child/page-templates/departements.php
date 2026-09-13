<?php
/**
 * Template Name: Départements
 *
 * Les 4 cartes de département ne sont plus codées en dur : elles bouclent
 * sur la taxonomie "departement" (nom + description WordPress natifs,
 * icône/couleur via acf-json/group_cerer_departement_meta.json), pour une
 * seule source d'édition avec la section "Nos départements" de l'accueil.
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

get_header();

// Émoji de repli tant qu'aucune icône PNG n'est importée sur le terme —
// même table que front-page.php, gardée synchronisée à la main : les deux
// n'ont pas de source commune plus simple sans complexifier l'admin.
$departement_emoji_fallback = array(
	9  => '☀️', // Photovoltaïque
	10 => '🌱', // Biomasse & Bioénergie
	11 => '💨', // Éolien
	12 => '🌡️', // Thermique
);

$departement_terms = get_terms(
	array(
		'taxonomy'   => 'departement',
		'hide_empty' => false,
	)
);

$chapo_page = get_field( 'chapo_page' );

$services_titre = get_field( 'services_transversaux_titre' );
$services       = array_filter(
	array(
		get_field( 'services_transversaux_1' ),
		get_field( 'services_transversaux_2' ),
		get_field( 'services_transversaux_3' ),
		get_field( 'services_transversaux_4' ),
		get_field( 'services_transversaux_5' ),
	)
);
?>

<div class="cerer-page-hero">
	<div class="cerer-container">
		<h1><?php the_title(); ?></h1>
		<?php if ( $chapo_page ) : ?>
			<p><?php echo esc_html( $chapo_page ); ?></p>
		<?php endif; ?>
	</div>
</div>

<div class="cerer-container cerer-section">
	<h2><?php esc_html_e( 'Départements de recherche', 'cerer-child' ); ?></h2>
	<div class="cerer-card-grid" style="margin-top:24px;">
		<?php if ( $departement_terms && ! is_wp_error( $departement_terms ) ) : ?>
			<?php foreach ( $departement_terms as $term ) : ?>
				<?php
				$icone      = get_field( 'icone', 'departement_' . $term->term_id );
				$fr_term_id = function_exists( 'pll_get_term' ) ? ( pll_get_term( $term->term_id, 'fr' ) ?: $term->term_id ) : $term->term_id;
				get_template_part(
					'template-parts/carte-departement',
					null,
					array(
						'color'       => get_field( 'couleur', 'departement_' . $term->term_id ) ?: 'var(--cerer-blue)',
						'icon_html'   => ! empty( $icone['url'] )
							? '<img src="' . esc_url( $icone['url'] ) . '" alt="" style="width:32px;height:32px;object-fit:contain;">'
							: ( $departement_emoji_fallback[ $fr_term_id ] ?? '🔬' ),
						'title'       => $term->name,
						'description' => $term->description,
						'url'         => get_term_link( $term ),
					)
				);
				?>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>

	<?php if ( $services ) : ?>
		<h2 id="services-transversaux" style="margin-top:56px;"><?php echo esc_html( $services_titre ?: __( 'Services transversaux', 'cerer-child' ) ); ?></h2>
		<ul class="cerer-services-list">
			<?php foreach ( $services as $service ) : ?>
				<li><?php echo esc_html( $service ); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
