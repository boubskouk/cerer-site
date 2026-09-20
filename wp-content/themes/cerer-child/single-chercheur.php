<?php
/**
 * Fiche chercheur (CPT "chercheur").
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$photo         = get_field( 'photo' );
	$photo_url     = $photo['sizes']['medium'] ?? ( $photo['url'] ?? ( get_the_post_thumbnail_url( get_the_ID(), 'medium' ) ?: '' ) );
	$titre         = get_field( 'titre' );
	$specialite    = get_field( 'specialite' );
	$linkedin      = get_field( 'lien_linkedin' );
	$researchgate  = get_field( 'lien_researchgate' );
	$instagram     = get_field( 'lien_instagram' );
	$publications  = get_field( 'publications_liees' );
	?>

	<article class="cerer-container cerer-section cerer-single" style="max-width:820px;">
		<div style="display:flex; gap:24px; align-items:flex-start; flex-wrap:wrap;">
			<div class="cerer-card__photo<?php echo $photo_url ? '' : ' is-placeholder'; ?>" style="width:120px; height:120px; flex-shrink:0;">
				<?php if ( $photo_url ) : ?>
					<img src="<?php echo esc_url( $photo_url ); ?>" alt="">
				<?php endif; ?>
			</div>
			<div>
				<h1><?php the_title(); ?></h1>
				<?php if ( $titre ) : ?>
					<p class="cerer-meta" style="margin-top:4px; font-size:15px;"><?php echo esc_html( $titre ); ?></p>
				<?php endif; ?>
				<?php if ( $specialite ) : ?>
					<p style="margin-top:8px; font-size:15px; color:var(--cerer-ink-2);"><?php echo esc_html( $specialite ); ?></p>
				<?php endif; ?>
				<?php if ( $linkedin || $researchgate || $instagram ) : ?>
					<div class="cerer-card__actions" style="margin-top:16px;">
						<?php if ( $linkedin ) : ?>
							<?php cerer_external_link( $linkedin, __( 'LinkedIn', 'cerer-child' ), array( 'class' => 'cerer-btn cerer-btn--tertiary cerer-btn--s' ) ); ?>
						<?php endif; ?>
						<?php if ( $researchgate ) : ?>
							<?php cerer_external_link( $researchgate, __( 'ResearchGate', 'cerer-child' ), array( 'class' => 'cerer-btn cerer-btn--tertiary cerer-btn--s' ) ); ?>
						<?php endif; ?>
						<?php if ( $instagram ) : ?>
							<?php cerer_external_link( $instagram, __( 'Instagram', 'cerer-child' ), array( 'class' => 'cerer-btn cerer-btn--tertiary cerer-btn--s' ) ); ?>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( $publications ) : ?>
			<h2 style="margin-top:48px;"><?php esc_html_e( 'Publications', 'cerer-child' ); ?></h2>
			<div class="cerer-card-grid" style="margin-top:24px;">
				<?php
				foreach ( $publications as $pub ) :
					get_template_part(
						'template-parts/carte-publication',
						null,
						array(
							'title'    => get_the_title( $pub ),
							'url'      => get_permalink( $pub ),
							'type'     => get_field( 'type', $pub->ID ) ?: 'article',
							'auteurs'  => get_field( 'auteurs', $pub->ID ),
							'revue'    => get_field( 'revue', $pub->ID ),
							'annee'    => get_field( 'annee', $pub->ID ),
							'resume'   => get_field( 'resume', $pub->ID ),
							'lien_pdf' => get_field( 'lien_pdf', $pub->ID ),
							'doi'      => get_field( 'doi', $pub->ID ),
						)
					);
				endforeach;
				?>
			</div>
		<?php endif; ?>

		<p style="margin-top:40px;">
			<a href="<?php echo esc_url( get_post_type_archive_link( 'chercheur' ) ); ?>" class="cerer-btn cerer-btn--link">← <?php esc_html_e( 'Tous les chercheurs', 'cerer-child' ); ?></a>
		</p>
	</article>

	<?php
endwhile;

get_footer();
