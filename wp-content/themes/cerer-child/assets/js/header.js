/**
 * Comportement de la topbar + navbar CERER — README §4.2/4.3 et §6.2.
 * Vanilla JS, aucune dépendance jQuery.
 */
( function () {
	'use strict';

	var SCROLL_THRESHOLD = 80;
	var html = document.documentElement;

	/* -----------------------------------------------------------
	 * 1. Rétractation de la topbar au scroll — écouteur passif +
	 *    requestAnimationFrame pour ne jamais bloquer le scroll.
	 * --------------------------------------------------------- */
	( function initScrollRetract() {
		var ticking = false;

		function update() {
			var scrolled = window.scrollY > SCROLL_THRESHOLD;
			html.classList.toggle( 'is-scrolled', scrolled );
			ticking = false;
		}

		window.addEventListener(
			'scroll',
			function () {
				if ( ! ticking ) {
					window.requestAnimationFrame( update );
					ticking = true;
				}
			},
			{ passive: true }
		);

		update();
	} )();

	/* -----------------------------------------------------------
	 * 2. Mega-menus Formations / Départements.
	 * --------------------------------------------------------- */
	var navItems = Array.prototype.slice.call(
		document.querySelectorAll( '.cerer-nav__item.has-children' )
	);

	function closeItem( item ) {
		item.classList.remove( 'is-open' );
		var trigger = item.querySelector( '.cerer-nav__trigger' );
		if ( trigger ) {
			trigger.setAttribute( 'aria-expanded', 'false' );
		}
	}

	function closeAllItems( except ) {
		navItems.forEach( function ( item ) {
			if ( item !== except ) {
				closeItem( item );
			}
		} );
	}

	navItems.forEach( function ( item ) {
		var trigger = item.querySelector( '.cerer-nav__trigger' );
		if ( ! trigger ) {
			return;
		}

		trigger.addEventListener( 'click', function () {
			var willOpen = ! item.classList.contains( 'is-open' );
			closeAllItems( item );
			item.classList.toggle( 'is-open', willOpen );
			trigger.setAttribute( 'aria-expanded', willOpen ? 'true' : 'false' );
		} );
	} );

	// Clic en dehors : referme tout mega-menu ouvert.
	document.addEventListener( 'click', function ( e ) {
		var insideOpenMenu = e.target.closest( '.cerer-nav__item' );
		if ( ! insideOpenMenu ) {
			closeAllItems();
		}
	} );

	/**
	 * Sortie du focus au clavier (Tab) : sans ça, un mega-menu ouvert au
	 * clavier restait visuellement ouvert quand on tabulait plus loin dans
	 * la page (seuls le clic extérieur et Échap le refermaient) — rapport
	 * de recette Prompt 6, point 4. setTimeout(…, 0) attend que le nouvel
	 * élément ait effectivement reçu le focus avant de vérifier.
	 */
	document.addEventListener( 'focusout', function () {
		var openItem = navItems.filter( function ( item ) {
			return item.classList.contains( 'is-open' );
		} )[ 0 ];
		if ( ! openItem ) {
			return;
		}
		setTimeout( function () {
			if ( ! openItem.contains( document.activeElement ) ) {
				closeItem( openItem );
			}
		}, 0 );
	} );

	/* -----------------------------------------------------------
	 * 3. Burger mobile.
	 * --------------------------------------------------------- */
	var burger = document.getElementById( 'cerer-burger-toggle' );
	if ( burger ) {
		burger.addEventListener( 'click', function () {
			var open = ! html.classList.contains( 'cerer-menu-open' );
			html.classList.toggle( 'cerer-menu-open', open );
			burger.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			if ( ! open ) {
				closeAllItems();
			}
		} );
	}

	/* -----------------------------------------------------------
	 * 4. Recherche — bouton dupliqué en desktop (topbar) et mobile
	 *    (menu burger) : ciblés par classe, un seul panneau partagé.
	 * --------------------------------------------------------- */
	var searchPanel   = document.getElementById( 'cerer-search-panel' );
	var searchToggles = Array.prototype.slice.call(
		document.querySelectorAll( '.cerer-search-toggle' )
	);

	function setSearchOpen( open ) {
		if ( ! searchPanel ) {
			return;
		}
		searchPanel.hidden = ! open;
		searchToggles.forEach( function ( btn ) {
			btn.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		} );
		if ( open ) {
			var input = document.getElementById( 'cerer-search-input' );
			if ( input ) {
				input.focus();
			}
		}
	}

	searchToggles.forEach( function ( btn ) {
		btn.addEventListener( 'click', function () {
			setSearchOpen( searchPanel && searchPanel.hidden );
		} );
	} );

	/* -----------------------------------------------------------
	 * 5. Contraste élevé — persisté en localStorage, README §6.2.
	 * --------------------------------------------------------- */
	var CONTRAST_KEY     = 'cerer-high-contrast';
	var contrastToggles  = Array.prototype.slice.call(
		document.querySelectorAll( '.cerer-contrast-toggle' )
	);

	function setContrast( on ) {
		document.body.classList.toggle( 'high-contrast', on );
		contrastToggles.forEach( function ( btn ) {
			btn.setAttribute( 'aria-pressed', on ? 'true' : 'false' );
		} );
		try {
			localStorage.setItem( CONTRAST_KEY, on ? '1' : '0' );
		} catch ( e ) {
			/* stockage indisponible (navigation privée…) : on continue sans persister */
		}
	}

	contrastToggles.forEach( function ( btn ) {
		btn.addEventListener( 'click', function () {
			setContrast( ! document.body.classList.contains( 'high-contrast' ) );
		} );
	} );

	try {
		if ( '1' === localStorage.getItem( CONTRAST_KEY ) ) {
			setContrast( true );
		}
	} catch ( e ) {
		/* ignore */
	}

	/* -----------------------------------------------------------
	 * 5bis. Mode sombre — même patron que le contraste élevé ci-dessus
	 *       (classe sur <body>, persistée en localStorage sous une clé
	 *       distincte pour que les deux réglages restent indépendants).
	 * --------------------------------------------------------- */
	var DARK_KEY    = 'cerer-dark-mode';
	var darkToggles = Array.prototype.slice.call(
		document.querySelectorAll( '.cerer-dark-toggle' )
	);

	function setDark( on ) {
		document.body.classList.toggle( 'dark-mode', on );
		darkToggles.forEach( function ( btn ) {
			btn.setAttribute( 'aria-pressed', on ? 'true' : 'false' );
		} );
		try {
			localStorage.setItem( DARK_KEY, on ? '1' : '0' );
		} catch ( e ) {
			/* stockage indisponible (navigation privée…) : on continue sans persister */
		}
	}

	darkToggles.forEach( function ( btn ) {
		btn.addEventListener( 'click', function () {
			setDark( ! document.body.classList.contains( 'dark-mode' ) );
		} );
	} );

	try {
		if ( '1' === localStorage.getItem( DARK_KEY ) ) {
			setDark( true );
		}
	} catch ( e ) {
		/* ignore */
	}

	/* -----------------------------------------------------------
	 * 6. Échap referme tout (mega-menu, recherche, menu mobile) et
	 *    rend le focus au déclencheur — README §6.2 navigation clavier.
	 * --------------------------------------------------------- */
	document.addEventListener( 'keydown', function ( e ) {
		if ( 'Escape' !== e.key && 'Esc' !== e.key ) {
			return;
		}

		var openItem = navItems.filter( function ( item ) {
			return item.classList.contains( 'is-open' );
		} )[ 0 ];

		if ( openItem ) {
			closeItem( openItem );
			var trigger = openItem.querySelector( '.cerer-nav__trigger' );
			if ( trigger ) {
				trigger.focus();
			}
			return;
		}

		if ( searchPanel && ! searchPanel.hidden ) {
			setSearchOpen( false );
			return;
		}

		if ( html.classList.contains( 'cerer-menu-open' ) && burger ) {
			html.classList.remove( 'cerer-menu-open' );
			burger.setAttribute( 'aria-expanded', 'false' );
			burger.focus();
		}
	} );
} )();
