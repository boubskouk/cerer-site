<?php
/**
 * Carte publication — archive du CPT "publication".
 *
 * Pas de visuel (une référence bibliographique n'en a pas) : étiquette de
 * type, titre, auteurs/revue/année, résumé, puis les liens externes (PDF,
 * DOI) via cerer_external_link() — README §6.1.
 *
 * Utilise la classe .cerer-card--document (archives.css), partagée avec
 * carte-soutenance.php : même forme de carte pour toute référence
 * bibliographique (étiquette + titre + métadonnées + liens externes).
 *
 * Usage :
 *   get_template_part( 'template-parts/carte-publication', null, array(
 *       'title'        => get_the_title(),
 *       'url'          => get_permalink(),
 *       'type'         => get_field( 'type' ),   // article|rapport|actes|these
 *       'auteurs'      => get_field( 'auteurs' ),
 *       'type_auteur'  => get_field( 'type_auteur' ),  // chercheur|etudiant|externe
 *       'chercheur'    => array( 'nom' => ..., 'url' => ..., 'photo' => ... ) ou null,
 *       'departement'  => array( 'nom' => ..., 'url' => ..., 'couleur' => ... ) ou null,
 *       'revue'        => get_field( 'revue' ),
 *       'annee'        => get_field( 'annee' ),
 *       'resume'       => get_field( 'resume' ),
 *       'lien_pdf'     => get_field( 'lien_pdf' ),
 *       'doi'          => get_field( 'doi' ),
 *   ) );
 *
 * @package cerer-child
 * @var array $args
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args(
	$args ?? array(),
	array(
		'title'       => '',
		'url'         => '#',
		'type'        => 'article',
		'auteurs'     => '',
		'type_auteur' => 'chercheur',
		'chercheur'   => null,
		'departement' => null,
		'revue'       => '',
		'annee'       => '',
		'resume'      => '',
		'lien_pdf'    => '',
		'doi'         => '',
	)
);

// Mêmes libellés que le champ ACF "type" (group_cerer_publication).
$type_labels = array(
	'article' => __( 'Article scientifique', 'cerer-child' ),
	'rapport' => __( 'Rapport', 'cerer-child' ),
	'actes'   => __( 'Actes de conférence', 'cerer-child' ),
	'these'   => __( 'Thèse', 'cerer-child' ),
);

// "informations sur celui qui publie" toujours visibles : badge du profil
// même quand aucune fiche chercheur n'est liée (étudiant, auteur externe).
$auteur_labels = array(
	'chercheur' => __( 'Chercheur CERER', 'cerer-child' ),
	'etudiant'  => __( 'Étudiant', 'cerer-child' ),
	'externe'   => __( 'Auteur externe', 'cerer-child' ),
);

$meta_parts = array_filter( array( $args['revue'], $args['annee'] ? (string) $args['annee'] : '' ) );
?>
<article class="cerer-card cerer-card--document">
	<div class="cerer-card__tags">
		<p class="cerer-label cerer-eyebrow"><?php echo esc_html( $type_labels[ $args['type'] ] ?? $args['type'] ); ?></p>
		<?php if ( $args['departement'] ) : ?>
			<a href="<?php echo esc_url( $args['departement']['url'] ); ?>" class="cerer-pub-dept-tag" style="--cerer-pub-dept-color: <?php echo esc_attr( $args['departement']['couleur'] ); ?>"><?php echo esc_html( $args['departement']['nom'] ); ?></a>
		<?php endif; ?>
	</div>
	<h3><a href="<?php echo esc_url( $args['url'] ); ?>"><?php echo esc_html( $args['title'] ); ?></a></h3>
	<?php if ( $meta_parts ) : ?>
		<p class="cerer-meta cerer-card__meta"><?php echo esc_html( implode( ', ', $meta_parts ) ); ?></p>
	<?php endif; ?>
	<?php if ( $args['resume'] ) : ?>
		<p class="cerer-card__description"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( $args['resume'] ), 28 ) ); ?></p>
	<?php endif; ?>

	<?php if ( $args['auteurs'] || $args['chercheur'] ) : ?>
		<div class="cerer-pub-author">
			<?php if ( $args['chercheur'] ) : ?>
				<a href="<?php echo esc_url( $args['chercheur']['url'] ); ?>" class="cerer-pub-author__photo<?php echo $args['chercheur']['photo'] ? '' : ' is-placeholder'; ?>">
					<?php if ( $args['chercheur']['photo'] ) : ?>
						<img src="<?php echo esc_url( $args['chercheur']['photo'] ); ?>" alt="">
					<?php endif; ?>
				</a>
			<?php endif; ?>
			<div class="cerer-pub-author__body">
				<?php if ( $args['chercheur'] ) : ?>
					<a href="<?php echo esc_url( $args['chercheur']['url'] ); ?>" class="cerer-pub-author__name"><?php echo esc_html( $args['chercheur']['nom'] ); ?></a>
				<?php elseif ( $args['auteurs'] ) : ?>
					<span class="cerer-pub-author__name"><?php echo esc_html( $args['auteurs'] ); ?></span>
				<?php endif; ?>
				<span class="cerer-pub-author__role"><?php echo esc_html( $auteur_labels[ $args['type_auteur'] ] ?? $args['type_auteur'] ); ?></span>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( $args['lien_pdf'] || $args['doi'] ) : ?>
		<div class="cerer-card__actions">
			<?php if ( $args['lien_pdf'] ) : ?>
				<?php cerer_external_link( $args['lien_pdf'], __( 'Télécharger le PDF', 'cerer-child' ), array( 'class' => 'cerer-btn cerer-btn--tertiary cerer-btn--s' ) ); ?>
			<?php endif; ?>
			<?php if ( $args['doi'] ) : ?>
				<span class="cerer-meta">DOI : <?php echo esc_html( $args['doi'] ); ?></span>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</article>
