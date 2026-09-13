<?php
/**
 * Badge réutilisable — statut (pill) ou tag de type de contenu (rectangle).
 * README §4.5.
 *
 * Usage :
 *   get_template_part( 'template-parts/badge', null, array(
 *       'kind'    => 'status',           // 'status' | 'tag'
 *       'variant' => 'en-cours',         // voir $labels ci-dessous
 *       'label'   => 'Libellé perso',    // optionnel, sinon libellé par défaut
 *   ) );
 *
 * @package cerer-child
 * @var array $args
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args(
	$args ?? array(),
	array(
		'kind'    => 'status',
		'variant' => 'en-cours',
		'label'   => '',
	)
);

$status_labels = array(
	'en-cours'               => __( 'En cours', 'cerer-child' ),
	'a-venir'                => __( 'À venir', 'cerer-child' ),
	'termine'                => __( 'Terminé', 'cerer-child' ),
	'inscriptions-ouvertes'  => __( 'Inscriptions ouvertes', 'cerer-child' ),
);

$tag_labels = array(
	'publication' => __( 'Publication', 'cerer-child' ),
	'evenement'   => __( 'Événement', 'cerer-child' ),
	'distinction' => __( 'Distinction', 'cerer-child' ),
	'partenariat' => __( 'Partenariat', 'cerer-child' ),
	'formation'   => __( 'Formation', 'cerer-child' ),
);

$is_tag = 'tag' === $args['kind'];
$labels = $is_tag ? $tag_labels : $status_labels;
$label  = $args['label'] ? $args['label'] : ( $labels[ $args['variant'] ] ?? $args['variant'] );
$class  = $is_tag ? 'cerer-tag cerer-tag--' . sanitize_html_class( $args['variant'] )
                  : 'cerer-badge cerer-badge--' . sanitize_html_class( $args['variant'] );
?>
<span class="<?php echo esc_attr( $class ); ?>"><?php echo esc_html( $label ); ?></span>
