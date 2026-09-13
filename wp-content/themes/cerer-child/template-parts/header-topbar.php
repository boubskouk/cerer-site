<?php
/**
 * Topbar — README §4.2.
 *
 * Fond bleu foncé, 38px, masquée sous 768px (ses fonctions passent dans le
 * burger, cf. header.js). FR|EN est branché sur Polylang (README §6.3) :
 * cerer_get_language_switcher_items(), inc/helpers.php.
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

$cerer_languages = cerer_get_language_switcher_items();
?>
<div id="cerer-topbar" class="cerer-topbar">
	<div class="cerer-container cerer-topbar__inner">
		<p class="cerer-topbar__left">
			<?php esc_html_e( "Institut de l'UCAD — Dakar, Sénégal", 'cerer-child' ); ?>
		</p>

		<div class="cerer-topbar__right">
			<?php if ( $cerer_languages ) : ?>
				<div class="cerer-lang" role="group" aria-label="<?php esc_attr_e( 'Choix de la langue', 'cerer-child' ); ?>">
					<?php foreach ( $cerer_languages as $i => $lang ) : ?>
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

			<span class="cerer-topbar__divider" aria-hidden="true"></span>

			<button
				type="button"
				class="cerer-topbar__action cerer-search-toggle"
				id="cerer-search-toggle"
				aria-expanded="false"
				aria-controls="cerer-search-panel"
			>
				<svg width="14" height="14" viewBox="0 0 20 20" fill="none" aria-hidden="true">
					<circle cx="9" cy="9" r="6.5" stroke="currentColor" stroke-width="1.6"></circle>
					<line x1="14.2" y1="14.2" x2="19" y2="19" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"></line>
				</svg>
				<?php esc_html_e( 'Rechercher', 'cerer-child' ); ?>
			</button>

			<button
				type="button"
				class="cerer-topbar__action cerer-contrast-toggle"
				id="cerer-contrast-toggle"
				aria-pressed="false"
			>
				<?php esc_html_e( 'Contraste élevé', 'cerer-child' ); ?>
			</button>

			<a href="<?php echo esc_url( cerer_page_url( 76 ) ); ?>" class="cerer-topbar__action cerer-topbar__contact">
				<?php esc_html_e( 'Contact rapide', 'cerer-child' ); ?>
			</a>
		</div>
	</div>

	<div id="cerer-search-panel" class="cerer-search-panel" hidden>
		<form role="search" method="get" class="cerer-search-panel__form cerer-container" action="<?php echo esc_url( cerer_home_url() ); ?>">
			<label for="cerer-search-input" class="screen-reader-text"><?php esc_html_e( 'Rechercher sur le site', 'cerer-child' ); ?></label>
			<input type="search" id="cerer-search-input" name="s" placeholder="<?php esc_attr_e( 'Rechercher sur le site…', 'cerer-child' ); ?>">
			<button type="submit"><?php esc_html_e( 'OK', 'cerer-child' ); ?></button>
		</form>
	</div>
</div>
