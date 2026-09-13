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
				<p><?php esc_html_e( 'Route de Ouakam, Dakar, Sénégal', 'cerer-child' ); ?></p>
				<p><a href="mailto:cerer@ucad.edu.sn">cerer@ucad.edu.sn</a></p>
				<p><a href="tel:+22133825XXXX">+221 33 825 XX XX</a></p>
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
				<li><a href="#" target="_blank" rel="noopener noreferrer">LinkedIn ↗</a></li>
				<li><a href="#" target="_blank" rel="noopener noreferrer">Twitter / X ↗</a></li>
				<li><a href="#" target="_blank" rel="noopener noreferrer">YouTube ↗</a></li>
				<li><a href="#" target="_blank" rel="noopener noreferrer">ResearchGate ↗</a></li>
			</ul>
		</div>

	</div>

	<div class="cerer-footer__copyright">
		<div class="cerer-container cerer-footer__copyright-inner">
			<p>
				<?php
				printf(
					/* translators: %d: année en cours */
					esc_html__( '© %d CERER — UCAD. Tous droits réservés.', 'cerer-child' ),
					(int) current_time( 'Y' )
				);
				?>
			</p>
			<p>cerer.ucad.sn</p>
		</div>
	</div>
</footer>
