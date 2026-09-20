<?php
/**
 * Carte service transversal (page Départements, #services-transversaux).
 * Variante non cliquable de carte-departement.php : même habillage
 * (liseré + pastille d'icône teintée), mais un <div> au lieu d'un <a>
 * puisqu'un service transversal ne mène nulle part.
 *
 * Usage :
 *   get_template_part( 'template-parts/carte-service', null, array(
 *       'color'       => 'var(--cerer-blue)',
 *       'title'       => 'SSIN',
 *       'description' => 'Texte de description…',
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
		'title'       => '',
		'description' => '',
	)
);

$icon_html = mb_strtoupper( mb_substr( $args['title'], 0, 1 ) );
?>
<div
	class="cerer-card cerer-card--theme"
	style="--cerer-card-accent: <?php echo esc_attr( $args['color'] ); ?>"
>
	<div class="cerer-card__icon" aria-hidden="true"><?php echo esc_html( $icon_html ); ?></div>
	<h3><?php echo esc_html( $args['title'] ); ?></h3>
	<?php if ( $args['description'] ) : ?>
		<p><?php echo esc_html( $args['description'] ); ?></p>
	<?php endif; ?>
</div>
