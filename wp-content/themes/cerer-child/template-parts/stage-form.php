<?php
/**
 * Formulaire de demande de stage (page Stage & emploi) — AJAX, honeypot,
 * même patron que template-parts/contact-form.php. La liste des filières
 * boucle sur la taxonomie "departement" (mêmes 4 départements que
 * page-templates/departements.php) plutôt que d'être recopiée en dur, puis
 * ajoute "Administration" et "SSIN" en options fixes de part et d'autre.
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

$departement_terms = get_terms(
	array(
		'taxonomy'   => 'departement',
		'hide_empty' => false,
	)
);
?>
<form id="cerer-stage-form" class="cerer-form" novalidate>
	<div class="cerer-form__row">
		<label for="cerer-stage-nom"><?php esc_html_e( 'Nom', 'cerer-child' ); ?> *</label>
		<input type="text" id="cerer-stage-nom" name="nom" required autocomplete="name">
	</div>

	<div class="cerer-form__row">
		<label for="cerer-stage-email"><?php esc_html_e( 'E-mail', 'cerer-child' ); ?> *</label>
		<input type="email" id="cerer-stage-email" name="email" required autocomplete="email">
	</div>

	<div class="cerer-form__row">
		<label for="cerer-stage-telephone"><?php esc_html_e( 'Téléphone', 'cerer-child' ); ?></label>
		<input type="tel" id="cerer-stage-telephone" name="telephone" autocomplete="tel">
	</div>

	<div class="cerer-form__row">
		<label for="cerer-stage-filiere"><?php esc_html_e( 'Filière souhaitée', 'cerer-child' ); ?> *</label>
		<select id="cerer-stage-filiere" name="filiere" required>
			<option value=""><?php esc_html_e( 'Choisir…', 'cerer-child' ); ?></option>
			<option value="<?php esc_attr_e( 'Administration', 'cerer-child' ); ?>"><?php esc_html_e( 'Administration', 'cerer-child' ); ?></option>
			<?php if ( $departement_terms && ! is_wp_error( $departement_terms ) ) : ?>
				<?php foreach ( $departement_terms as $term ) : ?>
					<option value="<?php echo esc_attr( $term->name ); ?>"><?php echo esc_html( $term->name ); ?></option>
				<?php endforeach; ?>
			<?php endif; ?>
			<option value="SSIN">SSIN</option>
		</select>
	</div>

	<div class="cerer-form__row">
		<label for="cerer-stage-message"><?php esc_html_e( 'Message (facultatif)', 'cerer-child' ); ?></label>
		<textarea id="cerer-stage-message" name="message" rows="5" placeholder="<?php esc_attr_e( 'Parlez-nous brièvement de votre formation et de vos motivations…', 'cerer-child' ); ?>"></textarea>
	</div>

	<div class="cerer-form__hp" aria-hidden="true">
		<label for="cerer-stage-website"><?php esc_html_e( 'Site web', 'cerer-child' ); ?></label>
		<input type="text" id="cerer-stage-website" name="website" tabindex="-1" autocomplete="off">
	</div>

	<?php wp_nonce_field( 'cerer_stage_request', 'cerer_stage_nonce' ); ?>

	<button type="submit" class="cerer-btn cerer-btn--primary cerer-btn--l"><?php esc_html_e( 'Envoyer ma demande', 'cerer-child' ); ?></button>

	<p class="cerer-form__feedback" role="status" aria-live="polite" hidden></p>
</form>
