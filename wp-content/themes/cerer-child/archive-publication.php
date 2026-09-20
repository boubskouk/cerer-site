<?php
/**
 * Archive du CPT "publication" (/publications/).
 *
 * Les publications viennent de sources variées — chercheurs, étudiants
 * (mémoires, travaux liés à une soutenance), différents départements — donc
 * la carte met toujours en avant QUI publie (photo + lien vers la fiche
 * chercheur si elle existe, sinon nom + profil) et, quand renseigné, LE
 * DÉPARTEMENT d'origine.
 *
 * Filtre par département en liens simples (?departement=slug) plutôt qu'en
 * JS : la taxonomie "departement" est déjà assignée au CPT "publication"
 * (inc/taxonomies.php) avec son query_var par défaut ('departement'), donc
 * WP_Query la respecte nativement sur cette archive — aucun pre_get_posts ni
 * script à écrire, et ça reste compatible avec la pagination et le SEO.
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

get_header();

$departement_terms = get_terms( array( 'taxonomy' => 'departement', 'hide_empty' => true ) );
if ( is_wp_error( $departement_terms ) ) {
	$departement_terms = array();
}

$current_dept_slug = get_query_var( 'departement' );
$archive_base_url  = get_post_type_archive_link( 'publication' );
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
		<p class="cerer-about-hero__chapo"><?php esc_html_e( 'Articles scientifiques, rapports, actes de conférence, thèses et mémoires produits au CERER — par ses chercheurs, ses étudiants et ses quatre départements de recherche.', 'cerer-child' ); ?></p>
	</div>
</header>

<div class="cerer-container cerer-section">
	<?php if ( $departement_terms ) : ?>
		<nav class="cerer-pub-filters" aria-label="<?php esc_attr_e( 'Filtrer les publications par département', 'cerer-child' ); ?>">
			<a href="<?php echo esc_url( $archive_base_url ); ?>" class="cerer-pub-pill<?php echo ! $current_dept_slug ? ' is-active' : ''; ?>"<?php echo ! $current_dept_slug ? ' aria-current="true"' : ''; ?>>
				<?php esc_html_e( 'Tous les départements', 'cerer-child' ); ?>
			</a>
			<?php foreach ( $departement_terms as $dept ) : ?>
				<?php
				$dept_color = get_field( 'couleur', 'departement_' . $dept->term_id ) ?: 'var(--cerer-blue)';
				$is_active  = $current_dept_slug === $dept->slug;
				?>
				<a
					href="<?php echo esc_url( add_query_arg( 'departement', $dept->slug, $archive_base_url ) ); ?>"
					class="cerer-pub-pill<?php echo $is_active ? ' is-active' : ''; ?>"
					style="--cerer-pub-dept-color: <?php echo esc_attr( $dept_color ); ?>"
					<?php echo $is_active ? ' aria-current="true"' : ''; ?>
				>
					<span class="cerer-pub-pill__dot" aria-hidden="true"></span><?php echo esc_html( $dept->name ); ?>
				</a>
			<?php endforeach; ?>
		</nav>
	<?php endif; ?>

	<?php if ( have_posts() ) : ?>
		<div class="cerer-card-grid">
			<?php
			while ( have_posts() ) :
				the_post();

				$pub_id      = get_the_ID();
				$dept_terms  = get_the_terms( $pub_id, 'departement' );
				$dept_term   = ( $dept_terms && ! is_wp_error( $dept_terms ) ) ? $dept_terms[0] : null;
				$departement = null;
				if ( $dept_term ) {
					$departement = array(
						'nom'     => $dept_term->name,
						'url'     => get_term_link( $dept_term ),
						'couleur' => get_field( 'couleur', 'departement_' . $dept_term->term_id ) ?: 'var(--cerer-blue)',
					);
				}

				$chercheur_id = get_field( 'chercheur_lie' );
				$chercheur    = null;
				if ( $chercheur_id ) {
					$photo     = get_field( 'photo', $chercheur_id );
					$photo_url = $photo['sizes']['thumbnail'] ?? ( $photo['url'] ?? get_the_post_thumbnail_url( $chercheur_id, 'thumbnail' ) );
					$chercheur = array(
						'nom'   => get_the_title( $chercheur_id ),
						'url'   => get_permalink( $chercheur_id ),
						'photo' => $photo_url ?: '',
					);
				}

				get_template_part(
					'template-parts/carte-publication',
					null,
					array(
						'title'       => get_the_title(),
						'url'         => get_permalink(),
						'type'        => get_field( 'type' ) ?: 'article',
						'auteurs'     => get_field( 'auteurs' ),
						'type_auteur' => get_field( 'type_auteur' ) ?: 'chercheur',
						'chercheur'   => $chercheur,
						'departement' => $departement,
						'revue'       => get_field( 'revue' ),
						'annee'       => get_field( 'annee' ),
						'resume'      => get_field( 'resume' ),
						'lien_pdf'    => get_field( 'lien_pdf' ),
						'doi'         => get_field( 'doi' ),
					)
				);
			endwhile;
			?>
		</div>

		<div class="cerer-pagination">
			<?php
			the_posts_pagination(
				array(
					'prev_text' => __( '← Précédent', 'cerer-child' ),
					'next_text' => __( 'Suivant →', 'cerer-child' ),
				)
			);
			?>
		</div>
	<?php else : ?>
		<p>
			<?php
			echo $current_dept_slug
				? esc_html__( 'Aucune publication pour ce département pour le moment.', 'cerer-child' )
				: esc_html__( 'Aucune publication pour le moment.', 'cerer-child' );
			?>
		</p>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
