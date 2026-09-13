<?php
/**
 * Traitement du formulaire de contact — même patron que la newsletter
 * (inc/newsletter.php) : AJAX, honeypot, nonce. Stocke les messages comme
 * CPT privé "message_contact" (pas de plugin de formulaires pour l'instant,
 * cf. note dans template-parts/contact-form.php).
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

function cerer_register_contact_message_type() {
	register_post_type(
		'message_contact',
		array(
			'label'           => __( 'Messages de contact', 'cerer-child' ),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_icon'       => 'dashicons-email',
			'supports'        => array( 'title', 'editor' ),
			'capability_type' => 'page',
		)
	);
}
add_action( 'init', 'cerer_register_contact_message_type' );

function cerer_handle_contact_form() {
	check_ajax_referer( 'cerer_contact_form', 'nonce' );

	if ( ! empty( $_POST['website'] ) ) {
		wp_send_json_success( array( 'message' => __( 'Merci, votre message a bien été envoyé.', 'cerer-child' ) ) );
	}

	$nom     = isset( $_POST['nom'] ) ? sanitize_text_field( wp_unslash( $_POST['nom'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$sujet   = isset( $_POST['sujet'] ) ? sanitize_text_field( wp_unslash( $_POST['sujet'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	if ( ! $nom || ! $email || ! is_email( $email ) || ! $message ) {
		wp_send_json_error( array( 'message' => __( 'Merci de remplir au minimum votre nom, votre e-mail et votre message.', 'cerer-child' ) ), 400 );
	}

	$post_id = wp_insert_post(
		array(
			'post_type'    => 'message_contact',
			'post_title'   => $sujet ? $sujet : sprintf( __( 'Message de %s', 'cerer-child' ), $nom ),
			'post_content' => $message,
			'post_status'  => 'private',
		)
	);

	if ( is_wp_error( $post_id ) ) {
		wp_send_json_error( array( 'message' => __( 'Une erreur est survenue, réessayez plus tard.', 'cerer-child' ) ), 500 );
	}

	update_post_meta( $post_id, 'nom', $nom );
	update_post_meta( $post_id, 'email', $email );
	update_post_meta( $post_id, 'sujet', $sujet );

	// Notifie l'administrateur — best effort, n'empeche pas la reponse de succes
	// si l'envoi de mail echoue sur l'environnement local (pas de SMTP configure).
	wp_mail(
		get_option( 'admin_email' ),
		sprintf( '[Contact CERER] %s', $sujet ? $sujet : __( 'Nouveau message', 'cerer-child' ) ),
		sprintf( "De : %s <%s>\n\n%s", $nom, $email, $message )
	);

	wp_send_json_success( array( 'message' => __( 'Merci, votre message a bien été envoyé. Nous vous répondrons rapidement.', 'cerer-child' ) ) );
}
add_action( 'wp_ajax_cerer_contact_form', 'cerer_handle_contact_form' );
add_action( 'wp_ajax_nopriv_cerer_contact_form', 'cerer_handle_contact_form' );
