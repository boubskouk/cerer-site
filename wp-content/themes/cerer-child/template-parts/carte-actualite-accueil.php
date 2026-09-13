<?php
/**
 * Carte actualité — accueil, section "Dernières actualités" (PROMPT-14,
 * corrigée PROMPT-16). Toute la carte est un seul <a>, pas de bouton
 * "Lire la suite" (six boutons pour six cartes ferait du bruit et
 * doublerait les arrêts de tabulation).
 *
 * Sans image (ni à la une, ni de repli sur la catégorie) : pas d'aplat
 * gris — la carte n'affiche aucun bloc visuel, juste un liseré de couleur
 * en haut pour garder un ancrage visuel (README PROMPT-16).
 *
 * @package cerer-child
 * @var array $args
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args(
	$args ?? array(),
	array(
		'title'       => '',
		'url'         => '#',
		'image'       => '',
		'image_alt'   => '',
		'date'        => '',
		'excerpt'     => '',
		'cat_label'   => '',
		'cat_couleur' => 'var(--cerer-blue-dark)',
	)
);

$has_image = (bool) $args['image'];
?>
<a
	href="<?php echo esc_url( $args['url'] ); ?>"
	class="cerer-actu-card<?php echo $has_image ? '' : ' cerer-actu-card--no-image'; ?>"
	<?php echo $has_image ? '' : 'style="--cerer-actu-color: ' . esc_attr( $args['cat_couleur'] ) . ';"'; ?>
>
	<?php if ( $has_image ) : ?>
		<div class="cerer-actu-card__media">
			<img src="<?php echo esc_url( $args['image'] ); ?>" alt="<?php echo esc_attr( $args['image_alt'] ); ?>" loading="lazy">
		</div>
	<?php endif; ?>
	<div class="cerer-actu-card__body">
		<?php if ( $args['cat_label'] ) : ?>
			<span class="cerer-actu-card__badge" style="background-color: <?php echo esc_attr( $args['cat_couleur'] ); ?>;"><?php echo esc_html( $args['cat_label'] ); ?></span>
		<?php endif; ?>
		<h3><?php echo esc_html( $args['title'] ); ?></h3>
		<?php if ( $args['excerpt'] ) : ?>
			<p class="cerer-actu-card__excerpt"><?php echo esc_html( $args['excerpt'] ); ?></p>
		<?php endif; ?>
		<p class="cerer-actu-card__date"><?php echo esc_html( $args['date'] ); ?></p>
	</div>
</a>
