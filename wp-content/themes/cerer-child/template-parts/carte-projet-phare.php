<?php
/**
 * Carte "projet phare" — section Nos projets phares de l'accueil (PROMPT-12).
 *
 * Carte entièrement cliquable (le lien enveloppe tout), bandeau coloré selon
 * le département, piste d'avancement, puces de partenaires, repère "Voir le
 * projet" en pied (pas un bouton : la carte entière porte le lien).
 *
 * Usage :
 *   get_template_part( 'template-parts/carte-projet-phare', null, array(
 *       'url'          => get_permalink(),
 *       'title'        => get_the_title(),
 *       'description'  => get_field( 'description' ),        // texte brut ou HTML wpautop
 *       'statut'       => get_field( 'statut' ),              // en-cours|a-venir|termine
 *       'progression'  => (int) get_field( 'progression' ),
 *       'date_debut'   => get_field( 'date_debut' ),          // Y-m-d ou vide
 *       'date_fin'     => get_field( 'date_fin' ),            // Y-m-d ou vide
 *       'partenaires'  => get_field( 'partenaires' ),         // "GIZ, ASER"
 *       'dept_label'   => 'Photovoltaïque',                   // nom du département affiché (déjà dans la langue courante)
 *       'dept_class'   => 'dept-photovoltaique',               // classe couleur — voir $dept_class_map dans front-page.php
 *   ) );
 *
 * @package cerer-child
 * @var array $args
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args(
	$args ?? array(),
	array(
		'url'         => '#',
		'title'       => '',
		'description' => '',
		'statut'      => 'en-cours',
		'progression' => 0,
		'date_debut'  => '',
		'date_fin'    => '',
		'partenaires' => '',
		'dept_label'  => '',
		'dept_class'  => '',
	)
);

$status_labels = array(
	'en-cours' => __( 'En cours', 'cerer-child' ),
	'a-venir'  => __( 'À venir', 'cerer-child' ),
	'termine'  => __( 'Terminé', 'cerer-child' ),
);

$progression = max( 0, min( 100, (int) $args['progression'] ) );
$is_empty    = 0 === $progression;
$bar_width   = $is_empty ? 3 : $progression; // amorce visible, README PROMPT-12.

// "2023 — 2026" si les deux dates existent, sinon "Ouverture 2026" avec la
// seule date de début, sinon rien.
$period = '';
if ( $args['date_debut'] && $args['date_fin'] ) {
	$period = date_i18n( 'Y', strtotime( $args['date_debut'] ) ) . ' — ' . date_i18n( 'Y', strtotime( $args['date_fin'] ) );
} elseif ( $args['date_debut'] ) {
	$period = sprintf( __( 'Ouverture %s', 'cerer-child' ), date_i18n( 'Y', strtotime( $args['date_debut'] ) ) );
}

$partenaires = $args['partenaires'] ? array_filter( array_map( 'trim', explode( ',', $args['partenaires'] ) ) ) : array();

$description = $args['description'] ? wp_trim_words( wp_strip_all_tags( $args['description'] ), 24 ) : '';
?>
<a href="<?php echo esc_url( $args['url'] ); ?>" class="cerer-project-card <?php echo esc_attr( $args['dept_class'] ); ?>">
	<?php if ( $args['dept_label'] ) : ?>
		<div class="cerer-project-card__band">
			<span class="cerer-project-card__dept"><?php echo esc_html( $args['dept_label'] ); ?></span>
			<span class="cerer-project-card__badge is-<?php echo esc_attr( $args['statut'] ); ?>"><?php echo esc_html( $status_labels[ $args['statut'] ] ?? $args['statut'] ); ?></span>
		</div>
	<?php endif; ?>

	<div class="cerer-project-card__body">
		<h3><?php echo esc_html( $args['title'] ); ?></h3>

		<?php if ( $description ) : ?>
			<p class="cerer-project-card__desc"><?php echo esc_html( $description ); ?></p>
		<?php endif; ?>

		<div class="cerer-project-card__progress">
			<div class="cerer-project-card__progress-row">
				<span><?php echo esc_html( $period ); ?></span>
				<span class="cerer-project-card__progress-pct"><?php echo esc_html( $progression . '%' ); ?></span>
			</div>
			<div
				class="cerer-project-card__track"
				role="progressbar"
				aria-valuenow="<?php echo esc_attr( $progression ); ?>"
				aria-valuemin="0"
				aria-valuemax="100"
				aria-label="<?php echo esc_attr( sprintf( __( 'Avancement du projet : %d %%', 'cerer-child' ), $progression ) ); ?>"
			>
				<div class="cerer-project-card__fill<?php echo $is_empty ? ' is-empty' : ''; ?>" style="width:<?php echo esc_attr( $bar_width ); ?>%;"></div>
			</div>
		</div>

		<?php if ( $partenaires ) : ?>
			<div class="cerer-project-card__partners">
				<?php foreach ( $partenaires as $partenaire ) : ?>
					<span class="cerer-project-card__partner"><?php echo esc_html( $partenaire ); ?></span>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<span class="cerer-project-card__link"><?php esc_html_e( 'Voir le projet', 'cerer-child' ); ?> →</span>
	</div>
</a>
