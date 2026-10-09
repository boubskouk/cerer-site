<?php
/**
 * Page des articles ("Actualités") — Réglages > Lecture > Page des articles.
 *
 * WordPress utilise toujours home.php pour cette page, quel que soit le
 * modèle choisi dans l'éditeur (un page-templates/actualites.php serait
 * ignoré ici) : c'est pourquoi ce gabarit vit à la racine du thème plutôt
 * que dans page-templates/.
 *
 * Structure inspirée de la page Actualités de l'ANER (aner.sn/news) : en-tête
 * + intro, grille de cartes actualité (carte-actualite.php, README §4.6),
 * pagination. Le bandeau newsletter et le footer sont globaux (footer.php).
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

get_header();

$page_for_posts = (int) get_option( 'page_for_posts' );
$page_title      = $page_for_posts ? get_the_title( $page_for_posts ) : __( 'Actualités', 'cerer-child' );
?>

<div class="cerer-page-hero">
	<div class="cerer-container">
		<h1><?php echo esc_html( $page_title ); ?></h1>
		<p><?php esc_html_e( 'Suivez les dernières actualités, publications et temps forts du CERER.', 'cerer-child' ); ?></p>
	</div>
</div>

<div class="cerer-container cerer-section">
	<?php if ( have_posts() ) : ?>
		<div class="cerer-card-grid">
			<?php
			// Variante du badge dérivée du terme "categorie_actualite" réel
			// (par nom, pas par slug : Polylang suffixe certains slugs pour
			// éviter les collisions, ex. "publication-fr") plutôt qu'une
			// meta "cerer_actu_tag" qui n'était jamais renseignée nulle part
			// et faisait retomber chaque carte sur "Publication" par défaut.
			$tag_variant_by_name = array(
				'Publication' => 'publication',
				'Partenariat' => 'partenariat',
				'Partnership' => 'partenariat',
				'Distinction' => 'distinction',
				'Award'       => 'distinction',
				'Événement'   => 'evenement',
				'Event'       => 'evenement',
				'Formation'   => 'formation',
				'Training'    => 'formation',
			);
			while ( have_posts() ) :
				the_post();
				$cat_terms   = get_the_terms( get_the_ID(), 'categorie_actualite' );
				$cat_term    = ( $cat_terms && ! is_wp_error( $cat_terms ) ) ? $cat_terms[0] : null;
				$tag_variant = $cat_term ? ( $tag_variant_by_name[ $cat_term->name ] ?? 'publication' ) : 'publication';
				get_template_part(
					'template-parts/carte-actualite',
					null,
					array(
						'title'       => get_the_title(),
						'url'         => get_permalink(),
						'image'       => get_the_post_thumbnail_url( get_the_ID(), 'medium' ) ?: '',
						'date'        => get_the_date(),
						'tag_variant' => $tag_variant,
					)
				);
			endwhile;
			?>
		</div>

		<div class="cerer-pagination">
			<?php
			the_posts_pagination(
				array(
					'prev_text' => __( '← Précédent', 'cerer-child' ),
					'next_text' => __( 'Suivant →', 'cerer-child' ),
				)
			);
			?>
		</div>
	<?php else : ?>
		<p><?php esc_html_e( 'Aucune actualité publiée pour le moment.', 'cerer-child' ); ?></p>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
