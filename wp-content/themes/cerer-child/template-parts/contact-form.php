<?php
/**
 * Formulaire de contact — AJAX, honeypot, même patron que la newsletter.
 * Pas de WPForms Pro installé pour l'instant (cf. note similaire sur la
 * newsletter) : formulaire natif, balisage prévu pour être repris tel quel
 * par WPForms plus tard sans changer le CSS.
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;
?>
<form id="cerer-contact-form" class="cerer-form" novalidate>
	<div class="cerer-form__row">
		<label for="cerer-contact-nom"><?php esc_html_e( 'Nom', 'cerer-child' ); ?> *</label>
		<input type="text" id="cerer-contact-nom" name="nom" required autocomplete="name">
	</div>

	<div class="cerer-form__row">
		<label for="cerer-contact-email"><?php esc_html_e( 'E-mail', 'cerer-child' ); ?> *</label>
		<input type="email" id="cerer-contact-email" name="email" required autocomplete="email">
	</div>

	<div class="cerer-form__row">
		<label for="cerer-contact-sujet"><?php esc_html_e( 'Sujet', 'cerer-child' ); ?></label>
		<select id="cerer-contact-sujet" name="sujet">
			<option value=""><?php esc_html_e( 'Choisir un sujet…', 'cerer-child' ); ?></option>
			<option value="<?php esc_attr_e( 'Inscription / admission', 'cerer-child' ); ?>"><?php esc_html_e( 'Inscription / admission', 'cerer-child' ); ?></option>
			<option value="<?php esc_attr_e( 'Partenariat', 'cerer-child' ); ?>"><?php esc_html_e( 'Partenariat', 'cerer-child' ); ?></option>
			<option value="<?php esc_attr_e( 'Stage / emploi', 'cerer-child' ); ?>"><?php esc_html_e( 'Stage / emploi', 'cerer-child' ); ?></option>
			<option value="<?php esc_attr_e( 'Presse / médias', 'cerer-child' ); ?>"><?php esc_html_e( 'Presse / médias', 'cerer-child' ); ?></option>
			<option value="<?php esc_attr_e( 'Autre', 'cerer-child' ); ?>"><?php esc_html_e( 'Autre', 'cerer-child' ); ?></option>
		</select>
	</div>

	<div class="cerer-form__row">
		<label for="cerer-contact-message"><?php esc_html_e( 'Message', 'cerer-child' ); ?> *</label>
		<textarea id="cerer-contact-message" name="message" rows="6" required></textarea>
	</div>

	<div class="cerer-form__hp" aria-hidden="true">
		<label for="cerer-contact-website"><?php esc_html_e( 'Site web', 'cerer-child' ); ?></label>
		<input type="text" id="cerer-contact-website" name="website" tabindex="-1" autocomplete="off">
	</div>

	<?php wp_nonce_field( 'cerer_contact_form', 'cerer_contact_nonce' ); ?>

	<button type="submit" class="cerer-btn cerer-btn--primary cerer-btn--l"><?php esc_html_e( 'Envoyer le message', 'cerer-child' ); ?></button>

	<p class="cerer-form__feedback" role="status" aria-live="polite" hidden></p>
</form>
