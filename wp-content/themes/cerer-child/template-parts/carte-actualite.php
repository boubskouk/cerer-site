<?php
/**
 * Carte actualité — README §4.6 "Carte actualité".
 *
 * Usage :
 *   get_template_part( 'template-parts/carte-actualite', null, array(
 *       'title'        => 'Titre de l\'actualité',
 *       'url'          => get_permalink(),
 *       'image'        => '' ,           // URL, vide = placeholder rayé
 *       'date'         => 'Ex. 12 sept. 2026',
 *       'tag_variant'  => 'publication', // publication|evenement|distinction|partenariat|formation
 *   ) );
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
		'date'        => '',
		'tag_variant' => 'publication',
	)
);
?>
<a href="<?php echo esc_url( $args['url'] ); ?>" class="cerer-card cerer-card--news">
	<div class="cerer-card__media<?php echo $args['image'] ? '' : ' is-placeholder'; ?>">
		<?php if ( $args['image'] ) : ?>
			<img src="<?php echo esc_url( $args['image'] ); ?>" alt="" loading="lazy">
		<?php endif; ?>
	</div>
	<div class="cerer-card__body">
		<?php
		get_template_part(
			'template-parts/badge',
			null,
			array(
				'kind'    => 'tag',
				'variant' => $args['tag_variant'],
			)
		);
		?>
		<h3><?php echo esc_html( $args['title'] ); ?></h3>
		<?php if ( $args['date'] ) : ?>
			<p class="cerer-card__date"><?php echo esc_html( $args['date'] ); ?></p>
		<?php endif; ?>
	</div>
</a>
