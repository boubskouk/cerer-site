<?php
/**
 * Galerie vidéo filtrable + modale — page "Qui sommes-nous".
 * Vidéos gérées depuis l'admin (CPT "video" + ACF), cf. inc/about-videos.php.
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

$cerer_videos     = cerer_get_videos();
$cerer_categories = cerer_get_video_categories();
?>
<div class="cerer-video-gallery">
	<div class="cerer-video-gallery__filters" role="group" aria-label="<?php esc_attr_e( 'Filtrer les vidéos par catégorie', 'cerer-child' ); ?>">
		<button type="button" class="cerer-filter-pill is-active" data-video-filter="all">
			<?php esc_html_e( 'Tous', 'cerer-child' ); ?>
		</button>
		<?php foreach ( $cerer_categories as $slug => $label ) : ?>
			<button type="button" class="cerer-filter-pill" data-video-filter="<?php echo esc_attr( $slug ); ?>">
				<?php echo esc_html( $label ); ?>
			</button>
		<?php endforeach; ?>
	</div>

	<?php if ( $cerer_videos ) : ?>
		<div class="cerer-video-gallery__grid">
			<?php foreach ( $cerer_videos as $video ) : ?>
				<div
					class="cerer-video-card<?php echo $video['disponible'] ? '' : ' is-disabled'; ?>"
					data-video-categorie="<?php echo esc_attr( $video['categorie'] ); ?>"
					<?php if ( $video['disponible'] ) : ?>
						data-video-embed="<?php echo esc_url( $video['embed_url'] ); ?>"
						data-video-titre="<?php echo esc_attr( $video['titre'] ); ?>"
						data-video-description="<?php echo esc_attr( $video['description'] ); ?>"
						role="button"
						tabindex="0"
					<?php endif; ?>
				>
					<div class="cerer-video-card__media<?php echo $video['thumbnail'] ? '' : ' is-placeholder'; ?>">
						<?php if ( $video['thumbnail'] ) : ?>
							<img src="<?php echo esc_url( $video['thumbnail'] ); ?>" alt="" loading="lazy">
						<?php endif; ?>
						<?php if ( $video['disponible'] ) : ?>
							<span class="cerer-video-card__play" aria-hidden="true">▶</span>
						<?php else : ?>
							<span class="cerer-badge cerer-badge--a-venir cerer-video-card__soon"><?php esc_html_e( 'Bientôt disponible', 'cerer-child' ); ?></span>
						<?php endif; ?>
					</div>
					<div class="cerer-video-card__body">
						<?php
						get_template_part(
							'template-parts/badge',
							null,
							array(
								'kind'    => 'tag',
								'variant' => $video['categorie'],
								'label'   => isset( $cerer_categories[ $video['categorie'] ] ) ? $cerer_categories[ $video['categorie'] ] : $video['categorie'],
							)
						);
						?>
						<h3><?php echo esc_html( $video['titre'] ); ?></h3>
						<?php if ( $video['description'] ) : ?>
							<p><?php echo esc_html( $video['description'] ); ?></p>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php else : ?>
		<p class="cerer-meta"><?php esc_html_e( 'Aucune vidéo pour le moment.', 'cerer-child' ); ?></p>
	<?php endif; ?>
</div>

<div id="cerer-video-modal" class="cerer-video-modal" role="dialog" aria-modal="true" aria-labelledby="cerer-video-modal-title" hidden>
	<div class="cerer-video-modal__backdrop" data-video-modal-close></div>
	<div class="cerer-video-modal__dialog">
		<button type="button" class="cerer-video-modal__close" data-video-modal-close aria-label="<?php esc_attr_e( 'Fermer', 'cerer-child' ); ?>">&times;</button>
		<div class="cerer-video-modal__player"></div>
		<h3 id="cerer-video-modal-title" class="cerer-video-modal__title"></h3>
		<p class="cerer-video-modal__description"></p>
	</div>
</div>
