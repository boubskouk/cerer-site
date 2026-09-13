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
			while ( have_posts() ) :
				the_post();
				$tag = get_post_meta( get_the_ID(), 'cerer_actu_tag', true );
				get_template_part(
					'template-parts/carte-actualite',
					null,
					array(
						'title'       => get_the_title(),
						'url'         => get_permalink(),
						'image'       => get_the_post_thumbnail_url( get_the_ID(), 'medium' ) ?: '',
						'date'        => get_the_date(),
						'tag_variant' => $tag ?: 'publication',
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
