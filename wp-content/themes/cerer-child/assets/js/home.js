/**
 * Compteurs animés de la section "Chiffres clés" — README §6.4 : déclenché
 * par IntersectionObserver, une seule fois, désactivé si
 * prefers-reduced-motion. Vanilla JS (voir home.css pour la note sur
 * CountUp.js).
 */
( function () {
	'use strict';

	var counters = document.querySelectorAll( '[data-count-to]' );
	if ( ! counters.length ) {
		return;
	}

	var reduceMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	function animate( el ) {
		var target = parseInt( el.getAttribute( 'data-count-to' ), 10 ) || 0;

		if ( reduceMotion ) {
			el.textContent = target;
			return;
		}

		var duration = 1800; // PROMPT-11 : 1.8s.
		var start    = null;

		function step( timestamp ) {
			if ( null === start ) {
				start = timestamp;
			}
			var progress = Math.min( ( timestamp - start ) / duration, 1 );
			el.textContent = Math.floor( progress * target );
			if ( progress < 1 ) {
				window.requestAnimationFrame( step );
			} else {
				el.textContent = target;
			}
		}

		window.requestAnimationFrame( step );
	}

	var observer = new IntersectionObserver(
		function ( entries, obs ) {
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting ) {
					animate( entry.target );
					obs.unobserve( entry.target );
				}
			} );
		},
		{ threshold: 0.4 }
	);

	counters.forEach( function ( el ) {
		observer.observe( el );
	} );
} )();
