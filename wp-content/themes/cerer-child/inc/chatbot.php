<?php
/**
 * Contenu du chatbot FAQ — README §4.9.
 *
 * Phase 1 : réponses statiques, définies ici et filtrables. Phase 2 :
 * remplacer cerer_get_chatbot_faqs() par un appel à une API Claude côté
 * serveur (jamais de clé côté client) qui répondrait au même contrat de
 * données ({question, answer}[]), sans toucher à assets/js/chatbot.js ni au
 * balisage de template-parts/chatbot.php.
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

/**
 * @return array<int,array{question:string,answer:string}>
 */
function cerer_get_chatbot_faqs() {
	$faqs = array(
		array(
			'question' => __( 'Comment m’inscrire à une formation du CERER ?', 'cerer-child' ),
			'answer'   => __( "Les inscriptions se font en ligne depuis la page Formations, sur la fiche de chaque parcours (Licence, Master, certifications). Les périodes d'ouverture sont indiquées par le badge « Inscriptions ouvertes ».", 'cerer-child' ),
		),
		array(
			'question' => __( 'Comment postuler à un stage ou une offre d’emploi ?', 'cerer-child' ),
			'answer'   => __( 'Les offres de stage et d’emploi sont publiées dans le mega-menu Formations → « Offres de stage & emploi », avec les modalités de candidature propres à chaque offre.', 'cerer-child' ),
		),
		array(
			'question' => __( 'Comment devenir partenaire du CERER ?', 'cerer-child' ),
			'answer'   => __( 'Le service Relations internationales & Partenariats coordonne les nouveaux partenariats. Utilisez le formulaire de la page Contact en précisant l’objet de votre demande.', 'cerer-child' ),
		),
		array(
			'question' => __( 'Comment accéder aux données et outils du CERER ?', 'cerer-child' ),
			'answer'   => __( "La page Données & Outils centralise les jeux de données et outils (dont les applications Streamlit) en accès libre, avec leurs conditions d'utilisation propres à chacun.", 'cerer-child' ),
		),
		array(
			'question' => __( 'Comment participer à un webinaire ?', 'cerer-child' ),
			'answer'   => __( 'Chaque webinaire à venir a sa propre fiche avec un lien d’inscription ; les replays des sessions passées y sont ajoutés ensuite.', 'cerer-child' ),
		),
	);

	return apply_filters( 'cerer_chatbot_faqs', $faqs );
}
