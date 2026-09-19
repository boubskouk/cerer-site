<?php
/**
 * Navbar — README §4.1 (lockup des logos) et §4.3 (menu principal + mega-menus).
 *
 * Deux lignes : logos (space-between, jamais accolés) puis menu. La règle du
 * lockup des logos (§4.1) est réutilisée à l'identique dans le footer
 * (Prompt 4), via le même balisage de classes .cerer-logo-unit.
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;
?>
<header id="cerer-navbar" class="cerer-navbar">
	<div class="cerer-container cerer-navbar__logos">
		<?php get_template_part( 'template-parts/logo-lockup' ); ?>

		<div class="cerer-navbar__brand-wrap" aria-hidden="true">
			<span class="cerer-navbar__brand-shine cerer-navbar__brand-shine--<?php echo esc_attr( cerer_brand_shine_color() ); ?>">C.E.R.E.R</span>
			<span class="cerer-navbar__brand-subtitle"><?php esc_html_e( "Centre d'Études et de Recherches sur les Énergies Renouvelables", 'cerer-child' ); ?></span>
		</div>

		<button
			type="button"
			class="cerer-burger"
			id="cerer-burger-toggle"
			aria-expanded="false"
			aria-controls="cerer-nav-menu"
		>
			<span aria-hidden="true"></span>
			<span aria-hidden="true"></span>
			<span aria-hidden="true"></span>
			<span class="screen-reader-text"><?php esc_html_e( 'Ouvrir le menu', 'cerer-child' ); ?></span>
		</button>
	</div>

	<nav class="cerer-nav" id="cerer-nav-menu" aria-label="<?php esc_attr_e( 'Menu principal', 'cerer-child' ); ?>">
		<div class="cerer-container">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'items_wrap'     => '<ul id="%1$s" class="cerer-nav__list %2$s">%3$s</ul>',
					'walker'         => new Cerer_Nav_Walker(),
					'fallback_cb'    => false,
					'depth'          => 2,
				)
			);
			?>

			<?php
			/**
			 * < 768px uniquement (masqué en desktop par CSS) : les fonctions
			 * de la topbar (§4.2) passent dans le burger, comme demandé au
			 * Prompt 2 point 6. Boutons liés par classe (pas id, réutilisées
			 * en double sur la page) : header.js les cible via querySelectorAll.
			 */
			?>
			<div class="cerer-nav__mobile-extra">
				<?php $cerer_languages_mobile = cerer_get_language_switcher_items(); ?>
				<?php if ( $cerer_languages_mobile ) : ?>
					<div class="cerer-lang" role="group" aria-label="<?php esc_attr_e( 'Choix de la langue', 'cerer-child' ); ?>">
						<?php foreach ( $cerer_languages_mobile as $i => $lang ) : ?>
							<?php if ( $i > 0 ) : ?>
								<span class="cerer-lang__sep" aria-hidden="true">|</span>
							<?php endif; ?>
							<a
								href="<?php echo esc_url( $lang['url'] ); ?>"
								class="cerer-lang__item<?php echo $lang['is_active'] ? ' is-active' : ''; ?>"
								<?php echo $lang['is_active'] ? 'aria-current="true"' : ''; ?>
							><?php echo esc_html( $lang['label'] ); ?></a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<button type="button" class="cerer-nav__mobile-action cerer-search-toggle" aria-expanded="false" aria-controls="cerer-search-panel">
					<?php esc_html_e( 'Rechercher', 'cerer-child' ); ?>
				</button>
				<button type="button" class="cerer-nav__mobile-action cerer-contrast-toggle" aria-pressed="false">
					<?php esc_html_e( 'Contraste élevé', 'cerer-child' ); ?>
				</button>
				<a href="<?php echo esc_url( cerer_page_url( 76 ) ); ?>" class="cerer-nav__mobile-action">
					<?php esc_html_e( 'Contact rapide', 'cerer-child' ); ?>
				</a>
			</div>
		</div>
	</nav>
</header>
