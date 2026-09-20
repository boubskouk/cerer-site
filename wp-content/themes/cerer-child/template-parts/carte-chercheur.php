<?php
/**
 * Carte chercheur — archive du CPT "chercheur".
 *
 * Nouvelle forme de carte (portrait), le CPT n'ayant pas de forme prévue au
 * README §4.6 (Phase 1 s'arrêtait aux composants Actualité/Formation/
 * Département) : photo circulaire ou placeholder, nom, titre académique,
 * spécialité, liens externes.
 *
 * Usage :
 *   get_template_part( 'template-parts/carte-chercheur', null, array(
 *       'nom'        => get_the_title(),
 *       'url'        => get_permalink(),
 *       'photo'      => get_the_post_thumbnail_url( get_the_ID(), 'medium' ) ou get_field('photo')['sizes']['medium'],
 *       'titre'      => get_field( 'titre' ),
 *       'specialite' => get_field( 'specialite' ),
 *       'linkedin'   => get_field( 'lien_linkedin' ),
 *       'researchgate' => get_field( 'lien_researchgate' ),
 *       'instagram'  => get_field( 'lien_instagram' ),
 *   ) );
 *
 * @package cerer-child
 * @var array $args
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args(
	$args ?? array(),
	array(
		'nom'          => '',
		'url'          => '#',
		'photo'        => '',
		'titre'        => '',
		'specialite'   => '',
		'linkedin'     => '',
		'researchgate' => '',
		'instagram'    => '',
	)
);
?>
<article class="cerer-card cerer-card--chercheur">
	<div class="cerer-card__photo<?php echo $args['photo'] ? '' : ' is-placeholder'; ?>">
		<?php if ( $args['photo'] ) : ?>
			<img src="<?php echo esc_url( $args['photo'] ); ?>" alt="">
		<?php endif; ?>
	</div>
	<h3><a href="<?php echo esc_url( $args['url'] ); ?>"><?php echo esc_html( $args['nom'] ); ?></a></h3>
	<?php if ( $args['titre'] ) : ?>
		<p class="cerer-meta cerer-card__meta"><?php echo esc_html( $args['titre'] ); ?></p>
	<?php endif; ?>
	<?php if ( $args['specialite'] ) : ?>
		<p class="cerer-card__description"><?php echo esc_html( $args['specialite'] ); ?></p>
	<?php endif; ?>
	<?php if ( $args['linkedin'] || $args['researchgate'] || $args['instagram'] ) : ?>
		<div class="cerer-card__actions">
			<?php if ( $args['linkedin'] ) : ?>
				<?php cerer_external_link( $args['linkedin'], __( 'LinkedIn', 'cerer-child' ), array( 'class' => 'cerer-btn cerer-btn--tertiary cerer-btn--s' ) ); ?>
			<?php endif; ?>
			<?php if ( $args['researchgate'] ) : ?>
				<?php cerer_external_link( $args['researchgate'], __( 'ResearchGate', 'cerer-child' ), array( 'class' => 'cerer-btn cerer-btn--tertiary cerer-btn--s' ) ); ?>
			<?php endif; ?>
			<?php if ( $args['instagram'] ) : ?>
				<?php cerer_external_link( $args['instagram'], __( 'Instagram', 'cerer-child' ), array( 'class' => 'cerer-btn cerer-btn--tertiary cerer-btn--s' ) ); ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</article>
