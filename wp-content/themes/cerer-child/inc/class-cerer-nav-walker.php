<?php
/**
 * Walker de menu pour la navbar CERER.
 *
 * Rend les items de profondeur 0 comme onglets de la navbar (lien simple, ou
 * bouton déclencheur de mega-menu si l'item a des enfants dans l'admin).
 * Rend les items de profondeur 1 comme contenu de mega-menu, avec deux types
 * spéciaux pilotés par les classes CSS posées sur l'item depuis l'admin
 * (Apparence → Menus → Options de l'écran → Classes CSS) :
 *
 *   - "menu-separator"   → séparateur horizontal (README §4.3)
 *   - "menu-group-title" → intertitre de groupe non cliquable (Départements)
 *   - "dept-dot-green" / "dept-dot-blue2" / "dept-dot-blue" / "dept-dot-violet"
 *                        → pastille de couleur devant le libellé
 *   - "badge-upcoming"   → badge orange "à venir" après le libellé
 *                          (ex. Master en Énergie Renouvelable)
 *
 * @package cerer-child
 */

defined( 'ABSPATH' ) || exit;

class Cerer_Nav_Walker extends Walker_Nav_Menu {

	/**
	 * Couleurs de pastille par classe CSS — README §4.3 "Déroulant Départements".
	 *
	 * @var array<string,string>
	 */
	private $dot_colors = array(
		'dept-dot-green'  => '#4CB848',
		'dept-dot-blue2'  => '#0072BC',
		'dept-dot-blue'   => '#005B9A',
		'dept-dot-violet' => '#7B57C8',
	);

	/**
	 * Ouvre le conteneur du mega-menu (appelé une fois par item parent ayant
	 * des enfants, avant de descendre dans ses items de profondeur 1).
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '<div class="cerer-mega" role="menu">' . "\n" . '<ul class="cerer-mega__list">' . "\n";
	}

	/**
	 * Ferme le conteneur du mega-menu.
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$output .= "</ul></div>\n";
	}

	/**
	 * Ouvre le <li> et son contenu. Ne ferme jamais le <li> : c'est le
	 * end_el() par défaut du parent qui s'en charge, pour ne jamais produire
	 * de balise fermante en double avec les cas spéciaux ci-dessous.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;

		// --- Profondeur 0 : onglet de la navbar principale ---
		if ( 0 === $depth ) {
			$has_children = in_array( 'menu-item-has-children', $classes, true );
			$is_active    = in_array( 'current-menu-item', $classes, true )
				|| in_array( 'current-menu-ancestor', $classes, true )
				|| in_array( 'current_page_item', $classes, true );

			$li_class = 'cerer-nav__item' . ( $has_children ? ' has-children' : '' );
			$output  .= '<li class="' . esc_attr( $li_class ) . '">';

			if ( $has_children ) {
				$output .= '<button type="button" class="cerer-nav__link cerer-nav__trigger'
					. ( $is_active ? ' is-active' : '' ) . '" aria-expanded="false" aria-haspopup="true">'
					. esc_html( $item->title )
					. ' <span class="cerer-nav__chevron" aria-hidden="true">&#9662;</span></button>';
			} else {
				$output .= '<a href="' . esc_url( $item->url ) . '" class="cerer-nav__link'
					. ( $is_active ? ' is-active' : '' ) . '">' . esc_html( $item->title ) . '</a>';
			}
			return;
		}

		// --- Profondeur 1 : contenu du mega-menu ---

		if ( in_array( 'menu-separator', $classes, true ) ) {
			$output .= '<li class="cerer-mega__sep" role="separator"><hr aria-hidden="true">';
			return;
		}

		if ( in_array( 'menu-group-title', $classes, true ) ) {
			$output .= '<li class="cerer-mega__group-title" role="presentation"><span>'
				. esc_html( $item->title ) . '</span>';
			return;
		}

		$dot = '';
		foreach ( $this->dot_colors as $class_name => $color ) {
			if ( in_array( $class_name, $classes, true ) ) {
				$dot = '<span class="cerer-mega__dot" style="background-color:' . esc_attr( $color ) . '" aria-hidden="true"></span>';
				break;
			}
		}

		$is_upcoming  = in_array( 'badge-upcoming', $classes, true );
		$label_class  = $is_upcoming ? ' class="is-upcoming"' : '';
		$badge        = $is_upcoming ? ' <span class="cerer-mega__badge">à venir</span>' : '';

		$output .= '<li class="cerer-mega__item"><a href="' . esc_url( $item->url ) . '" class="cerer-mega__link">'
			. $dot . '<span' . $label_class . '>' . esc_html( $item->title ) . '</span>' . $badge . '</a>';
	}
}
