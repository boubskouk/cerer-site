<?php
/**
 * Archive du CPT "webinaire" (/webinaires/), séparée en deux sections
 * (À venir / Passés) plutôt qu'une simple pagination chronologique : c'est
 * ce distinguo qui intéresse le visiteur (s'inscrire vs revoir un replay).
 *
 * Réorganisée : liste (à venir + replays) à gauche, formulaire d'inscription
 * au prochain webinaire à droite (sticky en desktop) — même formulaire natif
 * AJAX que la carte de l'accueil (template-parts/webinar-registration-form),
 * ici en variante claire. En-tête et rythme de sections repris de
 * page-templates/qui-sommes-nous.php (assets/css/about.css) plutôt que du
 * simple cerer-page-hero utilisé par les autres archives.
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

// Formulaire d'inscription : celui du prochain webinaire (le premier de
// $a_venir, trié ASC), même logique que carte-webinaire-vedette.php.
$prochain      = $a_venir ? $a_venir[0] : null;
$prochain_meta = '';
if ( $prochain ) {
	$prochain_date_brute = get_field( 'date', $prochain->ID );
	$prochain_timestamp  = $prochain_date_brute ? strtotime( $prochain_date_brute ) : 0;
	$prochain_plateforme = get_field( 'plateforme', $prochain->ID );
	$prochain_meta       = $prochain_timestamp
		? trim( date_i18n( 'j F Y — H\hi', $prochain_timestamp ) . ( $prochain_plateforme ? ' · ' . $prochain_plateforme : '' ) )
		: $prochain_plateforme;
}
?>

<header class="cerer-about-hero">
	<div class="cerer-about-hero__blob" aria-hidden="true"></div>
	<div class="cerer-container cerer-about-hero__inner">
		<nav class="cerer-about-hero__breadcrumb" aria-label="<?php esc_attr_e( 'Fil d’Ariane', 'cerer-child' ); ?>">
			<a href="<?php echo esc_url( cerer_home_url() ); ?>"><?php esc_html_e( 'Accueil', 'cerer-child' ); ?></a>
			<span class="cerer-about-hero__sep" aria-hidden="true">/</span>
			<span class="cerer-about-hero__current"><?php post_type_archive_title(); ?></span>
		</nav>
		<h1><?php post_type_archive_title(); ?></h1>
		<p class="cerer-about-hero__chapo"><?php esc_html_e( 'Bienvenue sur la page des Webinaires du C.E.R.E.R.', 'cerer-child' ); ?></p>
		<p class="cerer-about-hero__chapo"><?php esc_html_e( 'Vous trouverez ici les enregistrements vidéo de nos webinaires scientifiques et académiques, accessibles librement pour tous les passionnés des énergies renouvelables.', 'cerer-child' ); ?></p>
		<p class="cerer-about-hero__chapo"><?php esc_html_e( 'Pour consulter les Termes de Référence (TDR) de chaque webinaire, veuillez cliquer sur le lien correspondant disponible sur chaque vidéo.', 'cerer-child' ); ?></p>
		<p class="cerer-about-hero__chapo"><?php esc_html_e( 'Le C.E.R.E.R vous souhaite un excellent visionnage !', 'cerer-child' ); ?></p>
	</div>
</header>

<section class="cerer-about-section">
	<div class="cerer-container">
		<div class="cerer-webinaires-layout">
			<div class="cerer-webinaires-layout__main">
				<div>
					<p class="cerer-about__eyebrow"><span class="cerer-about__eyebrow-rule" aria-hidden="true"></span><?php esc_html_e( 'INSCRIPTIONS OUVERTES', 'cerer-child' ); ?></p>
					<h2><?php esc_html_e( 'À venir', 'cerer-child' ); ?></h2>
					<div class="cerer-stacked-list">
						<?php if ( $a_venir ) : ?>
							<?php
							foreach ( $a_venir as $post ) :
								$date_brute  = get_field( 'date', $post->ID );
								$date_fmt    = $date_brute ? date_i18n( 'j F Y — H\hi', strtotime( $date_brute ) ) : '';
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
							<p class="cerer-about__empty"><?php esc_html_e( 'Aucun webinaire programmé pour le moment.', 'cerer-child' ); ?></p>
						<?php endif; ?>
					</div>
				</div>

				<div>
					<p class="cerer-about__eyebrow"><span class="cerer-about__eyebrow-rule" aria-hidden="true"></span><?php esc_html_e( 'À REVOIR', 'cerer-child' ); ?></p>
					<h2><?php esc_html_e( 'Replays', 'cerer-child' ); ?></h2>
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
							<p class="cerer-about__empty"><?php esc_html_e( 'Aucun replay disponible pour le moment.', 'cerer-child' ); ?></p>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<aside class="cerer-webinar-signup">
				<?php
				get_template_part(
					'template-parts/webinar-registration-form',
					null,
					array(
						'webinaire_id' => $prochain ? $prochain->ID : 0,
						'variant'      => 'light',
						'meta'         => $prochain ? $prochain_meta : __( "Aucun webinaire n'est programmé pour le moment — inscrivez-vous pour être informé du prochain.", 'cerer-child' ),
					)
				);
				?>
			</aside>
		</div>
	</div>
</section>

<?php get_footer(); ?>
