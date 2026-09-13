<?php
/**
 * Carte thématique (département) — README §4.6 "Carte thématique".
 * Liseré supérieur 3px + pastille d'icône teintée dans la couleur du
 * département fournie en argument.
 *
 * Usage :
 *   get_template_part( 'template-parts/carte-departement', null, array(
 *       'color'       => 'var(--cerer-green)', // ou un hex, ex. '#4CB848'
 *       'icon_html'   => '☀️',
 *       'title'       => 'Photovoltaïque',
 *       'description' => 'Texte de description…',
 *       'url'         => '#',
 *   ) );
 *
 * @package cerer-child
 * @var array $args
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args(
	$args ?? array(),
	array(
		'color'       => 'var(--cerer-blue)',
		'icon_html'   => '',
		'title'       => '',
		'description' => '',
		'url'         => '#',
	)
);
?>
<a
	href="<?php echo esc_url( $args['url'] ); ?>"
	class="cerer-card cerer-card--theme"
	style="--cerer-card-accent: <?php echo esc_attr( $args['color'] ); ?>"
>
	<?php if ( $args['icon_html'] ) : ?>
		<div class="cerer-card__icon" aria-hidden="true"><?php echo $args['icon_html']; // phpcs:ignore -- contenu de confiance fourni par le thème ?></div>
	<?php endif; ?>
	<h3><?php echo esc_html( $args['title'] ); ?></h3>
	<?php if ( $args['description'] ) : ?>
		<p><?php echo esc_html( $args['description'] ); ?></p>
	<?php endif; ?>
</a>
