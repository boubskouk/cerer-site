<?php
/**
 * Lockup des logos CERER + UCAD — RÈGLE ABSOLUE, README §4.1.
 *
 * Structure partagée entre navbar (Prompt 2) et footer (Prompt 4) : les deux
 * logos sont toujours aux extrémités opposées d'un conteneur
 * `justify-content:space-between`, jamais accolés. La taille (38px navbar /
 * 44px footer) et la couleur du texte ne changent rien à cette structure :
 * elles sont pilotées par le sélecteur ancêtre (.cerer-navbar / .cerer-footer)
 * dans header.css / footer.css, pas ici.
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

$cerer_img_dir = get_stylesheet_directory_uri() . '/assets/img/';
?>
<a href="<?php echo esc_url( cerer_home_url() ); ?>" class="cerer-logo-unit cerer-logo-unit--left">
	<picture>
		<source srcset="<?php echo esc_url( $cerer_img_dir . 'logo-cerer.webp' ); ?>" type="image/webp">
		<img
			src="<?php echo esc_url( $cerer_img_dir . 'logo-cerer.png' ); ?>"
			alt="<?php esc_attr_e( 'Logo CERER', 'cerer-child' ); ?>"
			width="227" height="164"
			class="cerer-logo-unit__img"
			loading="lazy"
		>
	</picture>
	<span class="cerer-logo-unit__name">
		<?php esc_html_e( "Centre d'Études et de Recherches sur les Énergies Renouvelables", 'cerer-child' ); ?>
	</span>
</a>

<a href="<?php echo esc_url( cerer_home_url() ); ?>" class="cerer-logo-unit cerer-logo-unit--right">
	<picture>
		<source srcset="<?php echo esc_url( $cerer_img_dir . 'logo-ucad.webp' ); ?>" type="image/webp">
		<img
			src="<?php echo esc_url( $cerer_img_dir . 'logo-ucad.png' ); ?>"
			alt="<?php esc_attr_e( 'Logo UCAD — Lux Mea Lex', 'cerer-child' ); ?>"
			width="165" height="144"
			class="cerer-logo-unit__img"
			loading="lazy"
		>
	</picture>
	<span class="cerer-logo-unit__name">
		<?php esc_html_e( 'Université Cheikh Anta Diop de Dakar', 'cerer-child' ); ?>
	</span>
</a>
