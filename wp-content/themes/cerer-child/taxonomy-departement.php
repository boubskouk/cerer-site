<?php
/**
 * Archive d'un département (taxonomie "departement", ex. /departement/photovoltaique/).
 *
 * Un département n'a pas de contenu propre : c'est une étiquette posée sur
 * plusieurs CPT (publication, webinaire, projet, soutenance,
 * formation_session, chercheur — cf. inc/taxonomies.php). Cette page
 * regroupe donc tout ce qui porte l'étiquette du terme visité, une section
 * par CPT, en réutilisant les cartes déjà construites pour chacun plutôt que
 * d'en inventer de nouvelles.
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

get_header();

$term = get_queried_object();

// Même mapping couleur que les cartes de la page Départements (front-page.php,
// page-templates/departements.php) : un seul département à la fois, pas de
// paramètre à faire suivre depuis ces pages.
$dept_colors = array(
	'Photovoltaïque'        => 'var(--cerer-green)',
	'Biomasse & Bioénergie' => 'var(--cerer-blue-2)',
	'Éolien'                => 'var(--cerer-blue)',
	'Thermique'             => 'var(--cerer-violet)',
);
$accent = $dept_colors[ $term->name ] ?? 'var(--cerer-blue)';

// CPT partageant la taxonomie "departement" (inc/taxonomies.php), avec le
// libellé de section correspondant.
$content_types = array(
	'publication'       => __( 'Publications', 'cerer-child' ),
	'projet'            => __( 'Projets', 'cerer-child' ),
	'webinaire'         => __( 'Webinaires', 'cerer-child' ),
	'soutenance'        => __( 'Soutenances & mémoires', 'cerer-child' ),
	'formation_session' => __( 'Sessions de formation', 'cerer-child' ),
	'chercheur'         => __( 'Chercheurs', 'cerer-child' ),
);

// Mêmes libellés que le champ ACF "programme" (group_cerer_formation_session).
$programme_labels = array(
	'licence_enr'        => __( 'Licence en Énergie Renouvelable', 'cerer-child' ),
	'master_enr'         => __( 'Master en Énergie Renouvelable', 'cerer-child' ),
	'certifications'     => __( 'Certifications professionnelles', 'cerer-child' ),
	'formation_continue' => __( 'Formation continue', 'cerer-child' ),
);

$has_any = false;

// Le contenu existant (publications, webinaires de démo) est parfois tagué
// en "thematique" plutôt qu'en "departement" — les deux taxonomies partagent
// maintenant les mêmes intitulés (cf. inc/taxonomies.php), donc on
// interroge les deux par nom pour ne rien laisser de côté.
$sibling_thematique = get_term_by( 'name', $term->name, 'thematique' );
?>

<div class="cerer-page-hero">
	<div class="cerer-container">
		<p class="cerer-label cerer-eyebrow" style="color:<?php echo esc_attr( $accent ); ?>;"><?php esc_html_e( 'Département', 'cerer-child' ); ?></p>
		<h1><?php echo esc_html( $term->name ); ?></h1>
		<?php if ( $term->description ) : ?>
			<p><?php echo esc_html( $term->description ); ?></p>
		<?php endif; ?>
	</div>
</div>

<div class="cerer-container cerer-section">
	<?php
	foreach ( $content_types as $post_type => $label ) {
		$tax_query = array(
			'relation' => 'OR',
			array(
				'taxonomy' => 'departement',
				'field'    => 'term_id',
				'terms'    => $term->term_id,
			),
		);
		if ( $sibling_thematique ) {
			$tax_query[] = array(
				'taxonomy' => 'thematique',
				'field'    => 'term_id',
				'terms'    => $sibling_thematique->term_id,
			);
		}

		$posts = get_posts(
			array(
				'post_type'      => $post_type,
				'posts_per_page' => -1,
				'tax_query'      => $tax_query,
			)
		);

		if ( ! $posts ) {
			continue;
		}
		$has_any = true;
		?>
		<h2 style="margin-top:48px;"><?php echo esc_html( $label ); ?></h2>

		<?php if ( 'publication' === $post_type ) : ?>
			<div class="cerer-card-grid" style="margin-top:24px;">
				<?php
				foreach ( $posts as $post ) {
					get_template_part(
						'template-parts/carte-publication',
						null,
						array(
							'title'    => get_the_title( $post ),
							'url'      => get_permalink( $post ),
							'type'     => get_field( 'type', $post->ID ) ?: 'article',
							'auteurs'  => get_field( 'auteurs', $post->ID ),
							'revue'    => get_field( 'revue', $post->ID ),
							'annee'    => get_field( 'annee', $post->ID ),
							'resume'   => get_field( 'resume', $post->ID ),
							'lien_pdf' => get_field( 'lien_pdf', $post->ID ),
							'doi'      => get_field( 'doi', $post->ID ),
						)
					);
				}
				?>
			</div>

		<?php elseif ( 'webinaire' === $post_type ) : ?>
			<div class="cerer-stacked-list">
				<?php
				foreach ( $posts as $post ) {
					$statut      = get_field( 'statut', $post->ID ) ?: 'a_venir';
					$a_venir     = 'a_venir' === $statut;
					$date_brute  = get_field( 'date', $post->ID );
					$date_fmt    = $date_brute ? date_i18n( 'j F Y — H\hi', strtotime( $date_brute ) ) : '';
					$intervenant = get_field( 'intervenant', $post->ID );
					$replay      = get_field( 'lien_replay', $post->ID );
					get_template_part(
						'template-parts/carte-formation',
						null,
						array(
							'icon_html'   => '🎥',
							'title'       => get_the_title( $post ),
							'title_url'   => get_permalink( $post ),
							'badge'       => array( 'kind' => 'status', 'variant' => $a_venir ? 'inscriptions-ouvertes' : 'termine' ),
							'meta'        => trim( $date_fmt . ( get_field( 'plateforme', $post->ID ) ? ' · ' . get_field( 'plateforme', $post->ID ) : '' ), ' ·' ),
							'description' => $intervenant ? sprintf( __( 'Avec %s', 'cerer-child' ), $intervenant ) : '',
							'cta_label'   => $a_venir ? __( "S'inscrire", 'cerer-child' ) : ( $replay ? __( 'Voir le replay', 'cerer-child' ) : __( 'Replay bientôt disponible', 'cerer-child' ) ),
							'cta_url'     => $a_venir ? ( get_field( 'lien_inscription', $post->ID ) ?: '#' ) : ( $replay ?: '#' ),
						)
					);
				}
				?>
			</div>

		<?php elseif ( 'projet' === $post_type ) : ?>
			<div class="cerer-stacked-list">
				<?php
				foreach ( $posts as $post ) {
					$statut      = get_field( 'statut', $post->ID ) ?: 'en-cours';
					$partenaires = get_field( 'partenaires', $post->ID );
					$description = wp_strip_all_tags( (string) get_field( 'description', $post->ID ) );
					get_template_part(
						'template-parts/carte-formation',
						null,
						array(
							'icon_html'   => '🔬',
							'title'       => get_the_title( $post ),
							'title_url'   => get_permalink( $post ),
							'badge'       => array( 'kind' => 'status', 'variant' => $statut ),
							'meta'        => $partenaires ? sprintf( __( 'Avec %s', 'cerer-child' ), $partenaires ) : '',
							'description' => $description ? wp_trim_words( $description, 22 ) : '',
							'cta_label'   => __( 'Voir le projet', 'cerer-child' ),
							'cta_url'     => get_permalink( $post ),
						)
					);
				}
				?>
			</div>

		<?php elseif ( 'soutenance' === $post_type ) : ?>
			<div class="cerer-card-grid" style="margin-top:24px;">
				<?php
				foreach ( $posts as $post ) {
					get_template_part(
						'template-parts/carte-soutenance',
						null,
						array(
							'title'        => get_the_title( $post ),
							'url'          => get_permalink( $post ),
							'niveau'       => get_field( 'niveau', $post->ID ) ?: 'licence',
							'auteur'       => get_field( 'auteur', $post->ID ),
							'annee'        => get_field( 'annee', $post->ID ),
							'directeur'    => get_field( 'directeur', $post->ID ),
							'mention'      => get_field( 'mention', $post->ID ),
							'lien_memoire' => get_field( 'lien_memoire', $post->ID ),
							'lien_video'   => get_field( 'lien_video', $post->ID ),
						)
					);
				}
				?>
			</div>

		<?php elseif ( 'chercheur' === $post_type ) : ?>
			<div class="cerer-card-grid" style="margin-top:24px;">
				<?php
				foreach ( $posts as $post ) {
					$photo     = get_field( 'photo', $post->ID );
					$photo_url = $photo['sizes']['medium'] ?? ( $photo['url'] ?? ( get_the_post_thumbnail_url( $post->ID, 'medium' ) ?: '' ) );
					get_template_part(
						'template-parts/carte-chercheur',
						null,
						array(
							'nom'          => get_the_title( $post ),
							'url'          => get_permalink( $post ),
							'photo'        => $photo_url,
							'titre'        => get_field( 'titre', $post->ID ),
							'specialite'   => get_field( 'specialite', $post->ID ),
							'linkedin'     => get_field( 'lien_linkedin', $post->ID ),
							'researchgate' => get_field( 'lien_researchgate', $post->ID ),
						)
					);
				}
				?>
			</div>

		<?php elseif ( 'formation_session' === $post_type ) : ?>
			<div class="cerer-stacked-list">
				<?php
				foreach ( $posts as $post ) {
					$programme  = get_field( 'programme', $post->ID );
					$date_debut = get_field( 'date_debut', $post->ID );
					$date_fin   = get_field( 'date_fin', $post->ID );
					$lieu       = get_field( 'lieu', $post->ID );
					$places     = get_field( 'places_dispo', $post->ID );
					$inscription = get_field( 'lien_inscription', $post->ID );

					$today = current_time( 'Y-m-d' );
					if ( $date_debut && $today < $date_debut ) {
						$statut = 'a-venir';
					} elseif ( $date_fin && $today > $date_fin ) {
						$statut = 'termine';
					} else {
						$statut = 'en-cours';
					}

					$dates = '';
					if ( $date_debut ) {
						$dates = date_i18n( 'j F Y', strtotime( $date_debut ) );
						if ( $date_fin && $date_fin !== $date_debut ) {
							$dates .= ' – ' . date_i18n( 'j F Y', strtotime( $date_fin ) );
						}
					}

					$meta_parts = array_filter(
						array(
							$dates,
							$lieu,
							( null !== $places ) ? sprintf( _n( '%d place disponible', '%d places disponibles', $places, 'cerer-child' ), $places ) : '',
						)
					);

					get_template_part(
						'template-parts/carte-formation',
						null,
						array(
							'icon_html'   => '🎓',
							'title'       => get_the_title( $post ),
							'title_url'   => get_permalink( $post ),
							'badge'       => array( 'kind' => 'status', 'variant' => $statut ),
							'meta'        => implode( ' · ', $meta_parts ),
							'description' => $programme_labels[ $programme ] ?? '',
							'cta_label'   => 'termine' === $statut ? __( 'Session terminée', 'cerer-child' ) : __( "S'inscrire", 'cerer-child' ),
							'cta_url'     => 'termine' === $statut ? '#' : ( $inscription ?: '#' ),
						)
					);
				}
				?>
			</div>

		<?php else : ?>
			<ul class="cerer-services-list">
				<?php foreach ( $posts as $post ) : ?>
					<li><a href="<?php echo esc_url( get_permalink( $post ) ); ?>"><?php echo esc_html( get_the_title( $post ) ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		<?php endif;
	}

	if ( ! $has_any ) :
		?>
		<p><?php esc_html_e( 'Aucun contenu publié pour ce département pour le moment.', 'cerer-child' ); ?></p>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
