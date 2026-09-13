<?php
/**
 * Carte horizontale formation — README §4.6 "Carte horizontale formation".
 * Passe en colonne sous 768px (géré par components.css).
 *
 * Usage :
 *   get_template_part( 'template-parts/carte-formation', null, array(
 *       'icon_html'  => '🎓',                 // HTML de confiance (emoji, <span class="dashicons ...">…)
 *       'title'      => 'Licence en Énergie Renouvelable',
 *       'title_url'  => '',                   // optionnel : rend le titre cliquable (fiche single, etc.)
 *       'badge'      => array( 'kind' => 'status', 'variant' => 'inscriptions-ouvertes' ), // ou null
 *       'meta'       => 'Bac+3 · 3 ans · Campus UCAD',
 *       'description'=> 'Texte de description…',
 *       'cta_label'  => 'En savoir plus',
 *       'cta_url'    => '#',
 *   ) );
 *
 * @package cerer-child
 * @var array $args
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args(
	$args ?? array(),
	array(
		'icon_html'   => '',
		'title'       => '',
		'title_url'   => '',
		'badge'       => null,
		'meta'        => '',
		'description' => '',
		'cta_label'   => __( 'En savoir plus', 'cerer-child' ),
		'cta_url'     => '#',
	)
);

$icon_html = $args['icon_html'] ? $args['icon_html'] : mb_strtoupper( mb_substr( $args['title'], 0, 1 ) );
?>
<div class="cerer-card cerer-card--formation">
	<div class="cerer-card__icon" aria-hidden="true"><?php echo $icon_html; // phpcs:ignore -- contenu de confiance fourni par le thème ?></div>
	<div class="cerer-card__content">
		<div class="cerer-card__heading">
			<h3>
				<?php if ( $args['title_url'] ) : ?>
					<a href="<?php echo esc_url( $args['title_url'] ); ?>"><?php echo esc_html( $args['title'] ); ?></a>
				<?php else : ?>
					<?php echo esc_html( $args['title'] ); ?>
				<?php endif; ?>
			</h3>
			<?php if ( is_array( $args['badge'] ) ) : ?>
				<?php get_template_part( 'template-parts/badge', null, $args['badge'] ); ?>
			<?php endif; ?>
		</div>
		<?php if ( $args['meta'] ) : ?>
			<p class="cerer-card__meta"><?php echo esc_html( $args['meta'] ); ?></p>
		<?php endif; ?>
		<?php if ( $args['description'] ) : ?>
			<p class="cerer-card__description"><?php echo esc_html( $args['description'] ); ?></p>
		<?php endif; ?>
		<div class="cerer-card__action">
			<a href="<?php echo esc_url( $args['cta_url'] ); ?>" class="cerer-btn cerer-btn--secondary cerer-btn--s">
				<?php echo esc_html( $args['cta_label'] ); ?>
			</a>
		</div>
	</div>
</div>
