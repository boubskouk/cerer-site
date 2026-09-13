<?php
/**
 * Traitement du formulaire newsletter — README §4.7.
 *
 * Placeholder fonctionnel en attendant WPForms Pro : stocke les inscriptions
 * dans une option dédiée (liste plate, suffisante en phase 1 pour ne pas
 * perdre les emails collectés). À remplacer par un formulaire WPForms
 * connecté à l'outil d'emailing du CERER dès que le plugin est disponible —
 * l'essentiel pour l'instant est de ne rien perdre et de valider proprement.
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

function cerer_handle_newsletter_subscribe() {
	check_ajax_referer( 'cerer_newsletter_subscribe', 'nonce' );

	// Honeypot : un bot qui remplit ce champ est rejeté silencieusement,
	// avec une fausse réponse de succès pour ne pas l'aider à s'adapter.
	if ( ! empty( $_POST['website'] ) ) {
		wp_send_json_success( array( 'message' => __( 'Merci, votre inscription est confirmée.', 'cerer-child' ) ) );
	}

	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

	if ( ! $email || ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'Merci de renseigner une adresse e-mail valide.', 'cerer-child' ) ), 400 );
	}

	$subscribers = get_option( 'cerer_newsletter_subscribers', array() );

	if ( in_array( $email, $subscribers, true ) ) {
		wp_send_json_success( array( 'message' => __( 'Cette adresse est déjà inscrite. Merci !', 'cerer-child' ) ) );
	}

	$subscribers[] = $email;
	update_option( 'cerer_newsletter_subscribers', $subscribers, false );

	wp_send_json_success( array( 'message' => __( 'Merci, votre inscription est confirmée.', 'cerer-child' ) ) );
}
add_action( 'wp_ajax_cerer_newsletter_subscribe', 'cerer_handle_newsletter_subscribe' );
add_action( 'wp_ajax_nopriv_cerer_newsletter_subscribe', 'cerer_handle_newsletter_subscribe' );
