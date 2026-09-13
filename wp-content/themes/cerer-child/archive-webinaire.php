<?php
/**
 * Archive du CPT "webinaire" (/webinaires/), séparée en deux sections
 * (À venir / Passés) plutôt qu'une simple pagination chronologique : c'est
 * ce distinguo qui intéresse le visiteur (s'inscrire vs revoir un replay).
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

get_header();

$a_venir = get_posts(
	array(
		'post_type'      => 'webinaire',
		'posts_per_page' => -1,
		'meta_query'     => array(
			'relation'      => 'AND',
			'statut_clause' => array(
				'key'   => 'statut',
				'value' => 'a_venir',
			),
			'date_clause'   => array(
				'key'  => 'date',
				'type' => 'DATETIME',
			),
		),
		'orderby'        => array( 'date_clause' => 'ASC' ),
	)
);

$passes = get_posts(
	array(
		'post_type'      => 'webinaire',
		'posts_per_page' => -1,
		'meta_query'     => array(
			'relation'      => 'AND',
			'statut_clause' => array(
				'key'   => 'statut',
				'value' => 'passe',
			),
			'date_clause'   => array(
				'key'  => 'date',
				'type' => 'DATETIME',
			),
		),
		'orderby'        => array( 'date_clause' => 'DESC' ),
	)
);
?>

<div class="cerer-page-hero">
	<div class="cerer-container">
		<h1><?php post_type_archive_title(); ?></h1>
		<p><?php esc_html_e( 'Sessions en ligne animées par le CERER : inscrivez-vous aux prochaines, ou revoyez les replays.', 'cerer-child' ); ?></p>
	</div>
</div>

<div class="cerer-container cerer-section">
	<h2><?php esc_html_e( 'À venir', 'cerer-child' ); ?></h2>
	<div class="cerer-stacked-list">
		<?php if ( $a_venir ) : ?>
			<?php
			foreach ( $a_venir as $post ) :
				$date_brute = get_field( 'date', $post->ID );
				$date_fmt   = $date_brute ? date_i18n( 'j F Y — H\hi', strtotime( $date_brute ) ) : '';
				$intervenant = get_field( 'intervenant', $post->ID );
				get_template_part(
					'template-parts/carte-formation',
					null,
					array(
						'icon_html'   => '🎥',
						'title'       => get_the_title( $post ),
						'title_url'   => get_permalink( $post ),
						'badge'       => array( 'kind' => 'status', 'variant' => 'inscriptions-ouvertes' ),
						'meta'        => trim( $date_fmt . ( get_field( 'plateforme', $post->ID ) ? ' · ' . get_field( 'plateforme', $post->ID ) : '' ), ' ·' ),
						'description' => $intervenant ? sprintf( __( 'Avec %s', 'cerer-child' ), $intervenant ) : '',
						'cta_label'   => __( "S'inscrire", 'cerer-child' ),
						'cta_url'     => get_field( 'lien_inscription', $post->ID ) ?: '#',
					)
				);
			endforeach;
			?>
		<?php else : ?>
			<p><?php esc_html_e( 'Aucun webinaire programmé pour le moment.', 'cerer-child' ); ?></p>
		<?php endif; ?>
	</div>

	<h2 style="margin-top:56px;"><?php esc_html_e( 'Replays', 'cerer-child' ); ?></h2>
	<div class="cerer-stacked-list">
		<?php if ( $passes ) : ?>
			<?php
			foreach ( $passes as $post ) :
				$date_brute  = get_field( 'date', $post->ID );
				$date_fmt    = $date_brute ? date_i18n( 'j F Y', strtotime( $date_brute ) ) : '';
				$intervenant = get_field( 'intervenant', $post->ID );
				$replay      = get_field( 'lien_replay', $post->ID );
				get_template_part(
					'template-parts/carte-formation',
					null,
					array(
						'icon_html'   => '🎥',
						'title'       => get_the_title( $post ),
						'title_url'   => get_permalink( $post ),
						'badge'       => array( 'kind' => 'status', 'variant' => 'termine' ),
						'meta'        => trim( $date_fmt . ( get_field( 'plateforme', $post->ID ) ? ' · ' . get_field( 'plateforme', $post->ID ) : '' ), ' ·' ),
						'description' => $intervenant ? sprintf( __( 'Avec %s', 'cerer-child' ), $intervenant ) : '',
						'cta_label'   => $replay ? __( 'Voir le replay', 'cerer-child' ) : __( 'Replay bientôt disponible', 'cerer-child' ),
						'cta_url'     => $replay ?: '#',
					)
				);
			endforeach;
			?>
		<?php else : ?>
			<p><?php esc_html_e( 'Aucun replay disponible pour le moment.', 'cerer-child' ); ?></p>
		<?php endif; ?>
	</div>
</div>

<?php get_footer(); ?>
