<?php
/**
 * Template Name: Bibliothèque de composants
 *
 * Page de recette : affiche tous les composants du Prompt 3 dans tous leurs
 * états, pour comparaison visuelle avec design_handoff_cerer_phase1/Charte
 * graphique.dc.html (cf. Prompt 6). Page destinée à rester en statut privé.
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="cerer-container cerer-section" style="display:flex; flex-direction:column; gap:56px;">

	<header>
		<h1><?php the_title(); ?></h1>
		<p class="cerer-meta"><?php esc_html_e( 'Référence de recette — comparer à Charte graphique.dc.html avant validation.', 'cerer-child' ); ?></p>
	</header>

	<!-- Boutons -->
	<section>
		<h2>Boutons</h2>
		<div style="display:flex; flex-wrap:wrap; gap:12px; align-items:center; margin-block:16px;">
			<a href="#" class="cerer-btn cerer-btn--primary cerer-btn--l">Primaire L</a>
			<a href="#" class="cerer-btn cerer-btn--primary cerer-btn--s">Primaire S</a>
			<a href="#" class="cerer-btn cerer-btn--secondary cerer-btn--l">Secondaire clair</a>
			<a href="#" class="cerer-btn cerer-btn--tertiary cerer-btn--l">Tertiaire</a>
			<a href="#" class="cerer-btn cerer-btn--link">Toutes les actualités →</a>
			<button type="button" class="cerer-btn cerer-btn--primary cerer-btn--l" disabled>Désactivé</button>
		</div>
		<div style="background:var(--cerer-blue); padding:20px; border-radius:var(--cerer-radius-card); display:flex; gap:12px;">
			<a href="#" class="cerer-btn cerer-btn--secondary-blue cerer-btn--l">Secondaire sur bleu</a>
		</div>
	</section>

	<!-- Badges de statut -->
	<section>
		<h2>Badges de statut</h2>
		<div style="display:flex; flex-wrap:wrap; gap:10px; margin-block:16px;">
			<?php
			foreach ( array( 'en-cours', 'a-venir', 'termine', 'inscriptions-ouvertes' ) as $variant ) {
				get_template_part( 'template-parts/badge', null, array( 'kind' => 'status', 'variant' => $variant ) );
			}
			?>
		</div>
	</section>

	<!-- Tags de type de contenu -->
	<section>
		<h2>Tags de type de contenu</h2>
		<div style="display:flex; flex-wrap:wrap; gap:10px; margin-block:16px;">
			<?php
			foreach ( array( 'publication', 'evenement', 'distinction', 'partenariat', 'formation' ) as $variant ) {
				get_template_part( 'template-parts/badge', null, array( 'kind' => 'tag', 'variant' => $variant ) );
			}
			?>
		</div>
	</section>

	<!-- Pills de filtre -->
	<section>
		<h2>Pills de filtre</h2>
		<div style="display:flex; flex-wrap:wrap; gap:10px; margin-block:16px;">
			<button type="button" class="cerer-filter-pill is-active">Tous</button>
			<button type="button" class="cerer-filter-pill">Photovoltaïque</button>
			<button type="button" class="cerer-filter-pill">Biomasse &amp; Bioénergie</button>
			<button type="button" class="cerer-filter-pill">Éolien</button>
		</div>
	</section>

	<!-- Cartes thématiques -->
	<section>
		<h2>Cartes thématiques (départements)</h2>
		<div class="cerer-card-grid" style="margin-block:16px;">
			<?php
			$depts = array(
				array( 'color' => 'var(--cerer-green)', 'icon_html' => '☀️', 'title' => 'Photovoltaïque' ),
				array( 'color' => 'var(--cerer-blue-2)', 'icon_html' => '🌱', 'title' => 'Biomasse & Bioénergie' ),
				array( 'color' => 'var(--cerer-blue)', 'icon_html' => '💨', 'title' => 'Éolien' ),
				array( 'color' => 'var(--cerer-violet)', 'icon_html' => '🌡️', 'title' => 'Thermique' ),
			);
			foreach ( $depts as $d ) {
				get_template_part(
					'template-parts/carte-departement',
					null,
					array(
						'color'       => $d['color'],
						'icon_html'   => $d['icon_html'],
						'title'       => $d['title'],
						'description' => 'Texte de description du département, deux lignes environ pour l’aperçu.',
						'url'         => '#',
					)
				);
			}
			?>
		</div>
	</section>

	<!-- Cartes actualité -->
	<section>
		<h2>Cartes actualité</h2>
		<div class="cerer-card-grid" style="margin-block:16px;">
			<?php
			$news = array(
				array( 'tag_variant' => 'publication', 'title' => 'Nouvelle étude sur le potentiel solaire au Sénégal' ),
				array( 'tag_variant' => 'evenement', 'title' => 'Webinaire : financer la transition énergétique' ),
				array( 'tag_variant' => 'distinction', 'title' => 'Un chercheur du CERER récompensé à la COP' ),
			);
			foreach ( $news as $n ) {
				get_template_part(
					'template-parts/carte-actualite',
					null,
					array(
						'title'       => $n['title'],
						'url'         => '#',
						'date'        => '12 septembre 2026',
						'tag_variant' => $n['tag_variant'],
					)
				);
			}
			?>
		</div>
	</section>

	<!-- Carte horizontale formation -->
	<section>
		<h2>Cartes horizontales formation</h2>
		<div style="display:flex; flex-direction:column; gap:16px; margin-block:16px;">
			<?php
			get_template_part(
				'template-parts/carte-formation',
				null,
				array(
					'icon_html'   => '🎓',
					'title'       => 'Licence en Énergie Renouvelable',
					'badge'       => array( 'kind' => 'status', 'variant' => 'inscriptions-ouvertes' ),
					'meta'        => 'Bac+3 · 3 ans · Campus UCAD',
					'description' => 'Formation généraliste couvrant les fondamentaux des énergies renouvelables.',
					'cta_url'     => '#',
				)
			);
			get_template_part(
				'template-parts/carte-formation',
				null,
				array(
					'icon_html'   => '🎓',
					'title'       => 'Master en Énergie Renouvelable',
					'badge'       => array( 'kind' => 'status', 'variant' => 'a-venir' ),
					'meta'        => 'Bac+5 · 2 ans',
					'description' => 'Ouverture prévue à la prochaine rentrée universitaire.',
					'cta_url'     => '#',
				)
			);
			?>
		</div>
	</section>

</div>

<?php get_footer(); ?>
