<?php
/**
 * Bouton flottant WhatsApp — visible sur tout le site, en bas à gauche.
 * Message pré-rempli, envoyé au numéro du CERER.
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

// Numéro du CERER (README, page Contact) au format wa.me : indicatif +
// numéro, sans "+" ni espaces.
$cerer_whatsapp_numero  = '221765772252';
$cerer_whatsapp_message = __( 'Bonjour, je voudrais avoir des informations sur .....', 'cerer-child' );

$cerer_whatsapp_url = 'https://wa.me/' . $cerer_whatsapp_numero . '?text=' . rawurlencode( $cerer_whatsapp_message );
?>
<a
	href="<?php echo esc_url( $cerer_whatsapp_url ); ?>"
	target="_blank"
	rel="noopener noreferrer"
	class="cerer-whatsapp-float"
	aria-label="<?php esc_attr_e( 'Contacter le CERER sur WhatsApp', 'cerer-child' ); ?>"
>
	<svg width="28" height="28" viewBox="0 0 24 24" fill="none" aria-hidden="true">
		<path d="M12 2C6.48 2 2 6.48 2 12c0 1.85.5 3.58 1.36 5.07L2 22l5.08-1.33A9.94 9.94 0 0 0 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2Zm0 18.06c-1.6 0-3.1-.44-4.38-1.2l-.31-.18-3.02.79.8-2.95-.2-.31A8.06 8.06 0 1 1 20.06 12 8.07 8.07 0 0 1 12 20.06Zm4.42-5.98c-.24-.12-1.44-.71-1.66-.79-.22-.08-.39-.12-.55.12-.16.24-.63.79-.77.95-.14.16-.28.18-.53.06-.24-.12-1.02-.38-1.95-1.2-.72-.64-1.2-1.44-1.35-1.68-.14-.24-.02-.37.11-.49.11-.11.24-.28.36-.42.12-.14.16-.24.24-.4.08-.16.04-.3-.02-.42-.06-.12-.55-1.32-.75-1.81-.2-.48-.4-.41-.55-.42h-.47c-.16 0-.42.06-.64.3-.22.24-.84.82-.84 2s.86 2.32.98 2.48c.12.16 1.7 2.6 4.11 3.64.57.25 1.02.4 1.37.51.58.18 1.1.16 1.51.1.46-.07 1.44-.59 1.64-1.16.2-.57.2-1.06.14-1.16-.06-.1-.22-.16-.46-.28Z" fill="#fff"/>
	</svg>
	<span class="screen-reader-text"><?php esc_html_e( 'Contacter le CERER sur WhatsApp', 'cerer-child' ); ?></span>
</a>
