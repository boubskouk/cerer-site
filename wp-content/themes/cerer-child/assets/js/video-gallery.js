/**
 * Galerie vidéo "Qui sommes-nous" : filtre par catégorie + modale.
 * Vanilla JS, même patron de piégeage du focus / Échap que le chatbot
 * (assets/js/footer.js) pour rester cohérent.
 */
( function () {
	'use strict';

	var gallery = document.querySelector( '.cerer-video-gallery' );
	if ( ! gallery ) {
		return;
	}

	/* -----------------------------------------------------------
	 * Filtre par catégorie
	 * --------------------------------------------------------- */
	var pills = gallery.querySelectorAll( '[data-video-filter]' );
	var cards = gallery.querySelectorAll( '.cerer-video-card' );

	pills.forEach( function ( pill ) {
		pill.addEventListener( 'click', function () {
			pills.forEach( function ( p ) {
				p.classList.toggle( 'is-active', p === pill );
			} );

			var filter = pill.getAttribute( 'data-video-filter' );
			cards.forEach( function ( card ) {
				var match = 'all' === filter || card.getAttribute( 'data-video-categorie' ) === filter;
				card.hidden = ! match;
			} );
		} );
	} );

	/* -----------------------------------------------------------
	 * Modale
	 * --------------------------------------------------------- */
	var modal       = document.getElementById( 'cerer-video-modal' );
	var player      = modal ? modal.querySelector( '.cerer-video-modal__player' ) : null;
	var titleEl     = modal ? modal.querySelector( '.cerer-video-modal__title' ) : null;
	var descEl      = modal ? modal.querySelector( '.cerer-video-modal__description' ) : null;
	var lastTrigger = null;

	function getFocusable() {
		if ( ! modal ) {
			return [];
		}
		return Array.prototype.slice.call(
			modal.querySelectorAll( 'button, a[href], [tabindex]:not([tabindex="-1"])' )
		);
	}

	function openModal( card ) {
		if ( ! modal || ! player ) {
			return;
		}
		var embed = card.getAttribute( 'data-video-embed' );
		if ( ! embed ) {
			return;
		}

		lastTrigger = card;

		var iframe = document.createElement( 'iframe' );
		iframe.src = embed;
		iframe.title = card.getAttribute( 'data-video-titre' ) || '';
		iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
		iframe.allowFullscreen = true;
		player.innerHTML = '';
		player.appendChild( iframe );

		titleEl.textContent = card.getAttribute( 'data-video-titre' ) || '';
		descEl.textContent  = card.getAttribute( 'data-video-description' ) || '';

		modal.hidden = false;
		document.addEventListener( 'keydown', onModalKeydown );

		var focusable = getFocusable();
		if ( focusable.length ) {
			focusable[ 0 ].focus();
		}
	}

	function closeModal() {
		if ( ! modal || ! player ) {
			return;
		}
		modal.hidden = true;
		player.innerHTML = ''; // stoppe la lecture en retirant l'iframe
		document.removeEventListener( 'keydown', onModalKeydown );
		if ( lastTrigger ) {
			lastTrigger.focus();
			lastTrigger = null;
		}
	}

	function onModalKeydown( e ) {
		if ( 'Escape' === e.key || 'Esc' === e.key ) {
			closeModal();
			return;
		}
		if ( 'Tab' !== e.key ) {
			return;
		}
		var focusable = getFocusable();
		if ( ! focusable.length ) {
			return;
		}
		var first = focusable[ 0 ];
		var last  = focusable[ focusable.length - 1 ];
		if ( e.shiftKey && document.activeElement === first ) {
			e.preventDefault();
			last.focus();
		} else if ( ! e.shiftKey && document.activeElement === last ) {
			e.preventDefault();
			first.focus();
		}
	}

	cards.forEach( function ( card ) {
		if ( card.hasAttribute( 'data-video-embed' ) ) {
			card.addEventListener( 'click', function () {
				openModal( card );
			} );
			card.addEventListener( 'keydown', function ( e ) {
				if ( 'Enter' === e.key || ' ' === e.key ) {
					e.preventDefault();
					openModal( card );
				}
			} );
		}
	} );

	if ( modal ) {
		modal.querySelectorAll( '[data-video-modal-close]' ).forEach( function ( el ) {
			el.addEventListener( 'click', closeModal );
		} );
	}
} )();
