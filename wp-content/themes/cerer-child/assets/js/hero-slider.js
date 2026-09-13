/**
 * Diaporama du hero de l'accueil — PROMPT-8. Vanilla JS, aucune dépendance
 * (pas de Swiper/slick/jQuery). Chargé uniquement sur l'accueil
 * (wp_enqueue_script conditionné par is_front_page en functions.php), et
 * seulement si le hero contient au moins deux photos (galerie ACF
 * "hero_slides").
 */
( function () {
	'use strict';

	var hero = document.querySelector( '.cerer-hero--slider' );
	if ( ! hero ) {
		return;
	}

	var photos = hero.querySelectorAll( '.cerer-hero__photo' );
	var dots   = hero.querySelectorAll( '.cerer-hero__dot' );

	if ( photos.length < 2 ) {
		return; // Rien à faire tourner avec 0 ou 1 photo.
	}

	// prefers-reduced-motion : aucune rotation, aucun fondu, aucun zoom — la
	// première image (déjà .is-active côté PHP) reste affichée. Non négociable.
	if ( window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
		return;
	}

	var current    = 0;
	var intervalId = null;
	var paused     = false;

	function activate( index ) {
		photos.forEach( function ( img, i ) {
			if ( i === index ) {
				// Force le redémarrage de l'animation Ken Burns : sans ce
				// retrait/reflow/ajout, la classe reste posée en continu et
				// l'animation ne joue qu'une fois au chargement.
				img.classList.remove( 'is-active' );
				void img.offsetWidth; // eslint-disable-line no-void -- force le reflow.
				img.classList.add( 'is-active' );
			} else {
				img.classList.remove( 'is-active' );
			}
		} );

		dots.forEach( function ( dot, i ) {
			var isActive = i === index;
			dot.classList.toggle( 'is-active', isActive );
			if ( isActive ) {
				dot.setAttribute( 'aria-current', 'true' );
			} else {
				dot.removeAttribute( 'aria-current' );
			}
		} );

		current = index;
	}

	function next() {
		activate( ( current + 1 ) % photos.length );
	}

	function start() {
		if ( intervalId || paused ) {
			return;
		}
		intervalId = window.setInterval( next, 6000 );
	}

	function stop() {
		if ( intervalId ) {
			window.clearInterval( intervalId );
			intervalId = null;
		}
	}

	dots.forEach( function ( dot, i ) {
		dot.addEventListener( 'click', function () {
			activate( i );
			stop();
			if ( ! paused ) {
				start();
			}
		} );
	} );

	hero.addEventListener( 'mouseenter', function () {
		paused = true;
		stop();
	} );

	hero.addEventListener( 'mouseleave', function () {
		paused = false;
		start();
	} );

	document.addEventListener( 'visibilitychange', function () {
		if ( document.hidden ) {
			stop();
		} else if ( ! paused ) {
			start();
		}
	} );

	if ( ! document.hidden ) {
		start();
	}
} )();
