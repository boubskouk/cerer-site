<?php
/**
 * Carte "projet phare" vide — emplacement non encore pourvu dans la section
 * Nos projets phares de l'accueil (PROMPT-12). Pas de lien : rien à montrer
 * tant qu'aucun projet n'est marqué "en avant" pour ce slot.
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="cerer-project-card cerer-project-card--empty">
	<div class="cerer-project-card__body">
		<span class="cerer-project-card__empty-icon" aria-hidden="true">+</span>
		<h3><?php esc_html_e( 'Projet à venir', 'cerer-child' ); ?></h3>
		<p class="cerer-project-card__desc"><?php esc_html_e( "Cet emplacement sera prochainement réservé à un projet mis en avant.", 'cerer-child' ); ?></p>
	</div>
</div>
