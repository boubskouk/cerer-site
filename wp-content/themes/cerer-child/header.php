<?php
/**
 * En-tête CERER — remplace entièrement le header.php de Blocksy pour livrer
 * la topbar + navbar bespoke du handoff (README §4.1 à §4.3), tout en
 * conservant exactement le même squelette de wrapper (#main-container,
 * <main>, hooks blocksy:*) que le parent, pour rester compatible avec
 * footer.php (non modifié) et les extensions Blocksy.
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

?><!doctype html>
<html <?php language_attributes(); ?><?php echo blocksy_html_attr(); ?>>
<head>
	<?php do_action( 'blocksy:head:start' ); ?>

	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, viewport-fit=cover">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
	<?php do_action( 'blocksy:head:end' ); ?>
</head>

<body <?php body_class(); ?> <?php echo blocksy_body_attr(); ?>>

<?php
if ( function_exists( 'wp_body_open' ) ) {
	wp_body_open();
}
?>

<div id="main-container">
	<?php do_action( 'blocksy:header:before' ); ?>

	<?php get_template_part( 'template-parts/header', 'topbar' ); ?>
	<?php get_template_part( 'template-parts/header', 'navbar' ); ?>

	<?php
	do_action( 'blocksy:header:after' );
	do_action( 'blocksy:content:before' );
	?>

	<main <?php echo blocksy_main_attr(); ?>>

		<?php
		do_action( 'blocksy:content:top' );
		blocksy_before_current_template();
		?>
