<?php
/**
 * Formulaire d'inscription au prochain webinaire — carte vedette de
 * l'accueil (PROMPT-13). Même patron que template-parts/contact-form.php :
 * pas de WPForms Pro installé, formulaire natif AJAX + honeypot, prévu pour
 * être repris tel quel par WPForms plus tard sans changer le CSS.
 *
 * Le lien Zoom n'est jamais affiché ici : il part par e-mail après
 * inscription (README PROMPT-13).
 *
 * @package cerer-child
 * @var array $args {
 *     @type int $webinaire_id ID du webinaire concerné (champ caché).
 * }
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args( $args ?? array(), array( 'webinaire_id' => 0 ) );
?>
<form id="cerer-webinar-form" class="cerer-form cerer-form--on-dark" novalidate>
	<h3><?php esc_html_e( "S'inscrire", 'cerer-child' ); ?></h3>
	<p class="cerer-form__intro"><?php esc_html_e( 'Le lien de connexion vous sera envoyé par e-mail après inscription.', 'cerer-child' ); ?></p>

	<div class="cerer-form__row">
		<label for="cerer-webinar-nom"><?php esc_html_e( 'Nom complet', 'cerer-child' ); ?> *</label>
		<input type="text" id="cerer-webinar-nom" name="nom" required autocomplete="name">
	</div>

	<div class="cerer-form__row">
		<label for="cerer-webinar-email"><?php esc_html_e( 'Adresse email', 'cerer-child' ); ?> *</label>
		<input type="email" id="cerer-webinar-email" name="email" required autocomplete="email">
	</div>

	<div class="cerer-form__row">
		<label for="cerer-webinar-institution"><?php esc_html_e( 'Institution / Organisation', 'cerer-child' ); ?></label>
		<input type="text" id="cerer-webinar-institution" name="institution" autocomplete="organization">
	</div>

	<div class="cerer-form__row">
		<label for="cerer-webinar-profil"><?php esc_html_e( 'Vous êtes…', 'cerer-child' ); ?></label>
		<select id="cerer-webinar-profil" name="profil">
			<option value=""><?php esc_html_e( 'Choisir un sujet…', 'cerer-child' ); ?></option>
			<option value="<?php esc_attr_e( 'Chercheur', 'cerer-child' ); ?>"><?php esc_html_e( 'Chercheur', 'cerer-child' ); ?></option>
			<option value="<?php esc_attr_e( 'Étudiant', 'cerer-child' ); ?>"><?php esc_html_e( 'Étudiant', 'cerer-child' ); ?></option>
			<option value="<?php esc_attr_e( 'Institution', 'cerer-child' ); ?>"><?php esc_html_e( 'Institution', 'cerer-child' ); ?></option>
			<option value="<?php esc_attr_e( 'Professionnel', 'cerer-child' ); ?>"><?php esc_html_e( 'Professionnel', 'cerer-child' ); ?></option>
			<option value="<?php esc_attr_e( 'Autre', 'cerer-child' ); ?>"><?php esc_html_e( 'Autre', 'cerer-child' ); ?></option>
		</select>
	</div>

	<div class="cerer-form__hp" aria-hidden="true">
		<label for="cerer-webinar-website"><?php esc_html_e( 'Site web', 'cerer-child' ); ?></label>
		<input type="text" id="cerer-webinar-website" name="website" tabindex="-1" autocomplete="off">
	</div>

	<input type="hidden" name="webinaire_id" value="<?php echo esc_attr( $args['webinaire_id'] ); ?>">
	<?php wp_nonce_field( 'cerer_webinar_registration', 'cerer_webinar_nonce' ); ?>

	<button type="submit" class="cerer-btn cerer-btn--l cerer-form__submit"><?php esc_html_e( 'Confirmer mon inscription', 'cerer-child' ); ?></button>

	<p class="cerer-form__mention"><?php esc_html_e( "Inscription gratuite. Vos coordonnées ne servent qu'à l'organisation de ce webinaire.", 'cerer-child' ); ?></p>

	<p class="cerer-form__feedback" role="status" aria-live="polite" hidden></p>
</form>
