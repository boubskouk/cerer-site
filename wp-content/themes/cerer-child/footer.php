<?php
/**
 * Pied de page CERER — remplace footer.php de Blocksy pour livrer le
 * bandeau newsletter (§4.7) et le footer (§4.8), à la place de
 * blocksy_output_footer(). Ferme exactement les mêmes balises que
 * le parent (</main>, </div>#main-container), pour rester cohérent avec le
 * header.php du thème enfant.
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

blocksy_after_current_template();
do_action( 'blocksy:content:bottom' );
?>
	</main>

	<?php
	do_action( 'blocksy:content:after' );
	do_action( 'blocksy:footer:before' );

	get_template_part( 'template-parts/footer', 'newsletter' );
	get_template_part( 'template-parts/footer', 'main' );

	do_action( 'blocksy:footer:after' );
	?>
</div>

<?php get_template_part( 'template-parts/whatsapp-button' ); ?>

<?php wp_footer(); ?>

</body>
</html>
