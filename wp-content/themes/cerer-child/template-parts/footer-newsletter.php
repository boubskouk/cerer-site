<?php
/**
 * Bandeau newsletter — README §4.7. Affiché sur toutes les pages juste
 * avant le footer (appelé depuis footer.php).
 *
 * Le formulaire soumet en AJAX vers admin-ajax.php (action
 * "cerer_newsletter_subscribe", cf. inc/newsletter.php) plutôt que vers
 * WPForms : WPForms Pro n'est pas encore installé sur cet environnement de
 * dev. Le balisage (classes, structure du champ + bouton) est prévu pour
 * être repris tel quel par un bloc WPForms le jour où le plugin est en
 * place, sans rien changer au CSS.
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="cerer-container cerer-newsletter">
	<div class="cerer-newsletter__text">
		<h3><?php esc_html_e( 'Restez informé des travaux du CERER', 'cerer-child' ); ?></h3>
		<p><?php esc_html_e( 'Actualités, publications et webinaires chaque mois.', 'cerer-child' ); ?></p>
	</div>

	<form class="cerer-newsletter__form" id="cerer-newsletter-form" novalidate>
		<label for="cerer-newsletter-email" class="screen-reader-text">
			<?php esc_html_e( 'Adresse e-mail', 'cerer-child' ); ?>
		</label>
		<input
			type="email"
			id="cerer-newsletter-email"
			name="email"
			required
			autocomplete="email"
			placeholder="<?php esc_attr_e( 'Votre adresse e-mail', 'cerer-child' ); ?>"
		>

		<?php
		/* Honeypot anti-spam : champ masqué visuellement + aria-hidden,
		   tout bot qui le remplit est silencieusement rejeté côté serveur. */
		?>
		<div class="cerer-newsletter__hp" aria-hidden="true">
			<label for="cerer-newsletter-website">Site web</label>
			<input type="text" id="cerer-newsletter-website" name="website" tabindex="-1" autocomplete="off">
		</div>

		<?php wp_nonce_field( 'cerer_newsletter_subscribe', 'cerer_newsletter_nonce' ); ?>

		<button type="submit" class="cerer-btn cerer-btn--primary cerer-btn--l">
			<?php esc_html_e( "S'abonner", 'cerer-child' ); ?>
		</button>
	</form>

	<p class="cerer-newsletter__feedback" role="status" aria-live="polite" hidden></p>
</div>
