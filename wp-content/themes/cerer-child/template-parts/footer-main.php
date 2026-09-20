<?php
/**
 * Footer — README §4.8 (colonnes) + §4.1 (règle absolue du lockup des logos,
 * bandeau pleine largeur au-dessus des colonnes, jamais dans une colonne).
 *
 * Colonnes 2 et 3 = menus WordPress éditables depuis Apparence → Menus
 * ("footer-nav" et "footer-formations", enregistrés dans inc/menus.php).
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

// Réseaux sociaux — pas d'écran d'admin dédié (pas d'ACF Pro/Options Pages
// sur ce site), donc un tableau ici : chaque entrée avec 'url' vide s'affiche
// en texte simple non cliquable + badge (compte pas encore actif/en attente
// du lien), au lieu d'un lien mort vers "#". Pour ajouter un réseau
// supplémentaire (LinkedIn, YouTube, ResearchGate…), dupliquer une ligne.
$cerer_social_links = array(
	array(
		'label' => 'Facebook',
		'url'   => 'https://www.facebook.com/profile.php?id=61575920205179',
	),
	array(
		'label' => 'Instagram',
		'url'   => '',
	),
	array(
		'label' => 'TikTok',
		'url'   => '',
		'badge' => __( 'à venir', 'cerer-child' ),
	),
);
?>
<footer id="cerer-footer" class="cerer-footer">

	<div class="cerer-container cerer-footer__logos">
		<?php get_template_part( 'template-parts/logo-lockup' ); ?>
	</div>

	<div class="cerer-container cerer-footer__columns">

		<div class="cerer-footer__col">
			<h4 class="cerer-footer__title"><?php esc_html_e( 'Coordonnées', 'cerer-child' ); ?></h4>
			<address class="cerer-footer__address">
				<p>CERER — UCAD</p>
				<p><?php esc_html_e( 'Route du Service Géographique (HB-87) x Rue HB-478, Hann Bel-Air, Dakar, Sénégal', 'cerer-child' ); ?></p>
				<p><a href="mailto:cerer@ucad.edu.sn">cerer@ucad.edu.sn</a></p>
				<p><a href="tel:+221765772252">+(221) 76 577 22 52</a></p>
			</address>
		</div>

		<div class="cerer-footer__col">
			<h4 class="cerer-footer__title"><?php esc_html_e( 'Navigation', 'cerer-child' ); ?></h4>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer-nav',
					'container'      => false,
					'items_wrap'     => '<ul class="cerer-footer__list">%3$s</ul>',
					'fallback_cb'    => false,
					'depth'          => 1,
				)
			);
			?>
		</div>

		<div class="cerer-footer__col">
			<h4 class="cerer-footer__title"><?php esc_html_e( 'Formations & ressources', 'cerer-child' ); ?></h4>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer-formations',
					'container'      => false,
					'items_wrap'     => '<ul class="cerer-footer__list">%3$s</ul>',
					'fallback_cb'    => false,
					'depth'          => 1,
				)
			);
			?>
		</div>

		<div class="cerer-footer__col">
			<h4 class="cerer-footer__title"><?php esc_html_e( 'Suivez-nous', 'cerer-child' ); ?></h4>
			<ul class="cerer-footer__list">
				<?php foreach ( $cerer_social_links as $social ) : ?>
					<li>
						<?php if ( $social['url'] ) : ?>
							<a href="<?php echo esc_url( $social['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $social['label'] ); ?> ↗</a>
						<?php else : ?>
							<span class="cerer-footer__list-soon">
								<?php echo esc_html( $social['label'] ); ?>
								<span class="cerer-footer__list-badge"><?php echo esc_html( $social['badge'] ?? __( 'bientôt', 'cerer-child' ) ); ?></span>
							</span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>

	</div>

	<div class="cerer-footer__copyright">
		<div class="cerer-container cerer-footer__copyright-inner">
			<p>
				<?php
				printf(
					/* translators: %d: année en cours */
					esc_html__( 'Copyright © %d CERER. All rights reserved.', 'cerer-child' ),
					(int) current_time( 'Y' )
				);
				?>
			</p>
			<p><?php esc_html_e( 'By Service informatique du C.E.R.E.R', 'cerer-child' ); ?></p>
		</div>
	</div>
</footer>
