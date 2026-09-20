<?php
/**
 * Demande de stage (page Stage & emploi, page-templates/stage-emploi.php) —
 * même patron que inc/contact-form.php et inc/webinar-registration-form.php :
 * AJAX, honeypot, nonce. Stocke les demandes comme CPT privé "demande_stage".
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

function cerer_register_stage_request_type() {
	register_post_type(
		'demande_stage',
		array(
			'label'           => __( 'Demandes de stage', 'cerer-child' ),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_icon'       => 'dashicons-id-alt',
			'supports'        => array( 'title', 'editor' ),
			'capability_type' => 'page',
		)
	);
}
add_action( 'init', 'cerer_register_stage_request_type' );

function cerer_handle_stage_request() {
	check_ajax_referer( 'cerer_stage_request', 'nonce' );

	if ( ! empty( $_POST['website'] ) ) {
		wp_send_json_success( array( 'message' => __( 'Merci, votre demande a bien été envoyée.', 'cerer-child' ) ) );
	}

	$nom        = isset( $_POST['nom'] ) ? sanitize_text_field( wp_unslash( $_POST['nom'] ) ) : '';
	$email      = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$telephone  = isset( $_POST['telephone'] ) ? sanitize_text_field( wp_unslash( $_POST['telephone'] ) ) : '';
	$filiere    = isset( $_POST['filiere'] ) ? sanitize_text_field( wp_unslash( $_POST['filiere'] ) ) : '';
	$message    = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	if ( ! $nom || ! $email || ! is_email( $email ) || ! $filiere ) {
		wp_send_json_error( array( 'message' => __( 'Merci de remplir au minimum votre nom, votre e-mail et la filière souhaitée.', 'cerer-child' ) ), 400 );
	}

	$post_id = wp_insert_post(
		array(
			'post_type'    => 'demande_stage',
			'post_title'   => sprintf( '%s — %s', $nom, $filiere ),
			'post_content' => $message,
			'post_status'  => 'private',
		)
	);

	if ( is_wp_error( $post_id ) ) {
		wp_send_json_error( array( 'message' => __( 'Une erreur est survenue, réessayez plus tard.', 'cerer-child' ) ), 500 );
	}

	update_post_meta( $post_id, 'nom', $nom );
	update_post_meta( $post_id, 'email', $email );
	update_post_meta( $post_id, 'telephone', $telephone );
	update_post_meta( $post_id, 'filiere', $filiere );

	// Best effort — n'empêche pas la réponse de succès si l'envoi échoue
	// faute de SMTP configuré sur l'environnement local.
	wp_mail(
		cerer_notification_email(),
		sprintf( '[Demande de stage CERER] %s — %s', $nom, $filiere ),
		sprintf(
			"De : %s <%s>\nTéléphone : %s\nFilière souhaitée : %s\n\n%s",
			$nom,
			$email,
			$telephone ?: '—',
			$filiere,
			$message ?: '—'
		)
	);

	wp_send_json_success( array( 'message' => __( 'Merci, votre demande a bien été envoyée. Nous vous répondrons si une opportunité correspond à votre profil.', 'cerer-child' ) ) );
}
add_action( 'wp_ajax_cerer_stage_request', 'cerer_handle_stage_request' );
add_action( 'wp_ajax_nopriv_cerer_stage_request', 'cerer_handle_stage_request' );
