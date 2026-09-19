<?php
/**
 * Inscription au prochain webinaire (accueil, PROMPT-13) — même patron que
 * inc/contact-form.php : AJAX, honeypot, nonce, pas de plugin de formulaires
 * (WPForms Pro demandé par le prompt n'est pas installé sur ce site).
 * Stocke les inscriptions comme CPT privé "webinar_signup", avec
 * l'ID du webinaire en meta pour les relier.
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

function cerer_register_webinar_registration_type() {
	register_post_type(
		'webinar_signup',
		array(
			'label'           => __( 'Inscriptions webinaires', 'cerer-child' ),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_icon'       => 'dashicons-video-alt3',
			'supports'        => array( 'title' ),
			'capability_type' => 'page',
		)
	);
}
add_action( 'init', 'cerer_register_webinar_registration_type' );

function cerer_handle_webinar_registration() {
	check_ajax_referer( 'cerer_webinar_registration', 'nonce' );

	if ( ! empty( $_POST['website'] ) ) {
		wp_send_json_success( array( 'message' => __( 'Merci, votre inscription est confirmée.', 'cerer-child' ) ) );
	}

	$webinaire_id = isset( $_POST['webinaire_id'] ) ? absint( $_POST['webinaire_id'] ) : 0;
	$nom          = isset( $_POST['nom'] ) ? sanitize_text_field( wp_unslash( $_POST['nom'] ) ) : '';
	$email        = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$institution  = isset( $_POST['institution'] ) ? sanitize_text_field( wp_unslash( $_POST['institution'] ) ) : '';
	$profil       = isset( $_POST['profil'] ) ? sanitize_text_field( wp_unslash( $_POST['profil'] ) ) : '';

	// webinaire_id à 0 : inscription générale (« être informé du prochain »),
	// aucun webinaire précis n'étant programmé au moment de l'envoi.
	if ( ! $nom || ! $email || ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'Merci de remplir au minimum votre nom et votre e-mail.', 'cerer-child' ) ), 400 );
	}

	$webinaire_titre = $webinaire_id ? get_the_title( $webinaire_id ) : __( 'Prochain webinaire (non encore programmé)', 'cerer-child' );

	// Une adresse déjà inscrite à CE webinaire n'est pas ré-enregistrée deux fois.
	$existing = get_posts(
		array(
			'post_type'      => 'webinar_signup',
			'posts_per_page' => 1,
			'post_status'    => 'private',
			'meta_query'     => array(
				'relation' => 'AND',
				array( 'key' => 'webinaire_id', 'value' => $webinaire_id ),
				array( 'key' => 'email', 'value' => $email ),
			),
			'fields' => 'ids',
		)
	);
	if ( $existing ) {
		wp_send_json_success( array( 'message' => __( 'Cette adresse est déjà inscrite. Merci !', 'cerer-child' ) ) );
	}

	$post_id = wp_insert_post(
		array(
			'post_type'   => 'webinar_signup',
			'post_title'  => sprintf( '%s — %s', $nom, $webinaire_titre ),
			'post_status' => 'private',
		)
	);

	if ( ! $post_id || is_wp_error( $post_id ) ) {
		wp_send_json_error( array( 'message' => __( 'Une erreur est survenue, réessayez plus tard.', 'cerer-child' ) ), 500 );
	}

	update_post_meta( $post_id, 'webinaire_id', $webinaire_id );
	update_post_meta( $post_id, 'nom', $nom );
	update_post_meta( $post_id, 'email', $email );
	update_post_meta( $post_id, 'institution', $institution );
	update_post_meta( $post_id, 'profil', $profil );

	// Best effort — n'empêche pas la réponse de succès si l'envoi échoue
	// faute de SMTP configuré sur l'environnement local.
	wp_mail(
		get_option( 'admin_email' ),
		sprintf( '[Inscription webinaire] %s', $webinaire_titre ),
		sprintf( "%s <%s>\nInstitution : %s\nProfil : %s\n\nLe lien de connexion doit lui être envoyé manuellement (le lien Zoom n'est jamais affiché sur le site).", $nom, $email, $institution, $profil )
	);

	wp_send_json_success( array( 'message' => __( 'Merci, votre inscription est confirmée.', 'cerer-child' ) ) );
}
add_action( 'wp_ajax_cerer_webinar_registration', 'cerer_handle_webinar_registration' );
add_action( 'wp_ajax_nopriv_cerer_webinar_registration', 'cerer_handle_webinar_registration' );
