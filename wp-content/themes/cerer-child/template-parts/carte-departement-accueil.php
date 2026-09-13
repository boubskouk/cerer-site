<?php
/**
 * Carte département — section "Nos départements de recherche" de l'accueil
 * (PROMPT-11 départements-accueil). Toute la carte est un seul <a> : nom,
 * icône, description, tout mène à la page du département. Le repère "Voir
 * le département" en pied est visuel, pas un second lien.
 *
 * Usage :
 *   get_template_part( 'template-parts/carte-departement-accueil', null, array(
 *       'url'         => get_term_link( $term ),
 *       'title'       => $term->name,
 *       'description' => $term->description,
 *       'couleur'     => get_field( 'couleur', 'departement_' . $term->term_id ) ?: 'var(--cerer-blue-dark)',
 *       'teinte'      => get_field( 'teinte', 'departement_' . $term->term_id ) ?: 'var(--cerer-bg)',
 *       'icone'       => get_field( 'icone', 'departement_' . $term->term_id ), // tableau ACF ou null
 *       'icone_repli' => '☀️', // émoji affiché tant qu'aucune icône n'est importée
 *   ) );
 *
 * @package cerer-child
 * @var array $args
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args(
	$args ?? array(),
	array(
		'url'         => '#',
		'title'       => '',
		'description' => '',
		'couleur'     => 'var(--cerer-blue-dark)',
		'teinte'      => 'var(--cerer-bg)',
		'icone'       => null,
		'icone_repli' => '🔬',
	)
);
?>
<a
	href="<?php echo esc_url( $args['url'] ); ?>"
	class="cerer-dept-card"
	style="--cerer-dept-color: <?php echo esc_attr( $args['couleur'] ); ?>; --cerer-dept-tint: <?php echo esc_attr( $args['teinte'] ); ?>;"
>
	<div class="cerer-dept-card__head">
		<span class="cerer-dept-card__tile" aria-hidden="true">
			<?php if ( ! empty( $args['icone']['url'] ) ) : ?>
				<img src="<?php echo esc_url( $args['icone']['url'] ); ?>" alt="" width="64" height="64" loading="lazy">
			<?php else : ?>
				<?php echo $args['icone_repli']; // phpcs:ignore -- émoji de confiance fourni par le thème. ?>
			<?php endif; ?>
		</span>
		<h3><?php echo esc_html( $args['title'] ); ?></h3>
	</div>

	<?php if ( $args['description'] ) : ?>
		<p class="cerer-dept-card__desc"><?php echo esc_html( $args['description'] ); ?></p>
	<?php endif; ?>

	<span class="cerer-dept-card__link"><?php esc_html_e( 'Voir le département', 'cerer-child' ); ?></span>
</a>
