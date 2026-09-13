<?php
/**
 * Chatbot FAQ — README §4.9. Injecté une fois, en fin de <body>, sur toutes
 * les pages (cf. footer.php).
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

$cerer_faqs = cerer_get_chatbot_faqs();
?>
<div id="cerer-chatbot" class="cerer-chatbot">
	<button
		type="button"
		id="cerer-chatbot-toggle"
		class="cerer-chatbot__bubble"
		aria-expanded="false"
		aria-controls="cerer-chatbot-panel"
	>
		<svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
			<path d="M4 5h16a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H9l-4 4v-4H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z" fill="#fff"/>
		</svg>
		<span class="screen-reader-text"><?php esc_html_e( "Ouvrir l'assistant CERER", 'cerer-child' ); ?></span>
	</button>

	<div
		id="cerer-chatbot-panel"
		class="cerer-chatbot__panel"
		role="dialog"
		aria-modal="true"
		aria-labelledby="cerer-chatbot-title"
		hidden
	>
		<div class="cerer-chatbot__header">
			<div>
				<h2 id="cerer-chatbot-title"><?php esc_html_e( 'Assistant CERER', 'cerer-child' ); ?></h2>
				<p><?php esc_html_e( 'Réponses aux questions fréquentes', 'cerer-child' ); ?></p>
			</div>
			<button type="button" id="cerer-chatbot-close" class="cerer-chatbot__close" aria-label="<?php esc_attr_e( 'Fermer', 'cerer-child' ); ?>">
				&times;
			</button>
		</div>

		<div class="cerer-chatbot__body">
			<ul class="cerer-chatbot__questions">
				<?php foreach ( $cerer_faqs as $i => $faq ) : ?>
					<li class="cerer-chatbot__item">
						<button
							type="button"
							class="cerer-chatbot__question"
							aria-expanded="false"
							aria-controls="cerer-chatbot-answer-<?php echo (int) $i; ?>"
						>
							<?php echo esc_html( $faq['question'] ); ?>
						</button>
						<div id="cerer-chatbot-answer-<?php echo (int) $i; ?>" class="cerer-chatbot__answer" hidden>
							<?php echo esc_html( $faq['answer'] ); ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</div>
