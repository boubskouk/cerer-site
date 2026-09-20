/**
 * Page de détail d'un département (PROMPT-22) — section "En images" :
 * lightbox pour la vidéo (iframe chargée seulement au clic, jamais au
 * chargement de la page) et pour les photos (navigation précédent/suivant
 * entre toutes les photos du département, y compris celles derrière
 * « +N voir tout »). Même patron de piégeage du focus / Échap que la
 * galerie vidéo de "Qui sommes-nous" (assets/js/video-gallery.js).
 */
( function () {
	'use strict';

	var media = document.querySelector( '.cerer-deptd-media' );
	if ( ! media ) {
		return;
	}

	var photos = [];
	try {
		photos = JSON.parse( media.getAttribute( 'data-photos' ) || '[]' );
	} catch ( e ) {
		photos = [];
	}

	var lightbox   = document.getElementById( 'cerer-deptd-lightbox' );
	var mediaSlot   = lightbox ? lightbox.querySelector( '.cerer-deptd-lightbox__media' ) : null;
	var prevBtn     = lightbox ? lightbox.querySelector( '.cerer-deptd-lightbox__nav--prev' ) : null;
	var nextBtn     = lightbox ? lightbox.querySelector( '.cerer-deptd-lightbox__nav--next' ) : null;
	var lastTrigger = null;
	var photoIndex  = -1; // -1 : la vidéo est affichée, pas une photo.

	function getFocusable() {
		if ( ! lightbox ) {
			return [];
		}
		return Array.prototype.slice.call(
			lightbox.querySelectorAll( 'button, a[href], [tabindex]:not([tabindex="-1"])' )
		);
	}

	function showPhoto( index ) {
		if ( ! photos.length || ! mediaSlot ) {
			return;
		}
		photoIndex = ( index + photos.length ) % photos.length;
		mediaSlot.innerHTML = '';
		var img = document.createElement( 'img' );
		img.src = photos[ photoIndex ];
		img.alt = '';
		mediaSlot.appendChild( img );
		if ( prevBtn ) {
			prevBtn.hidden = photos.length < 2;
		}
		if ( nextBtn ) {
			nextBtn.hidden = photos.length < 2;
		}
	}

	function showVideo( embedUrl, title ) {
		if ( ! mediaSlot ) {
			return;
		}
		photoIndex = -1;
		mediaSlot.innerHTML = '';
		var iframe = document.createElement( 'iframe' );
		iframe.src = embedUrl;
		iframe.title = title || '';
		iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
		iframe.allowFullscreen = true;
		mediaSlot.appendChild( iframe );
		if ( prevBtn ) {
			prevBtn.hidden = true;
		}
		if ( nextBtn ) {
			nextBtn.hidden = true;
		}
	}

	function openLightbox( trigger ) {
		if ( ! lightbox ) {
			return;
		}
		lastTrigger = trigger;

		var embedUrl = trigger.getAttribute( 'data-lightbox-video' );
		if ( embedUrl ) {
			showVideo( embedUrl, trigger.getAttribute( 'data-lightbox-title' ) );
		} else {
			var idx = parseInt( trigger.getAttribute( 'data-lightbox-index' ), 10 );
			showPhoto( isNaN( idx ) ? 0 : idx );
		}

		lightbox.hidden = false;
		document.addEventListener( 'keydown', onKeydown );

		var focusable = getFocusable();
		if ( focusable.length ) {
			focusable[ 0 ].focus();
		}
	}

	function closeLightbox() {
		if ( ! lightbox || ! mediaSlot ) {
			return;
		}
		lightbox.hidden = true;
		mediaSlot.innerHTML = ''; // stoppe la lecture vidéo en retirant l'iframe
		document.removeEventListener( 'keydown', onKeydown );
		if ( lastTrigger ) {
			lastTrigger.focus();
			lastTrigger = null;
		}
	}

	function onKeydown( e ) {
		if ( 'Escape' === e.key || 'Esc' === e.key ) {
			closeLightbox();
			return;
		}
		if ( photoIndex > -1 && 'ArrowLeft' === e.key ) {
			showPhoto( photoIndex - 1 );
			return;
		}
		if ( photoIndex > -1 && 'ArrowRight' === e.key ) {
			showPhoto( photoIndex + 1 );
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

	media.querySelectorAll( '[data-lightbox-video], [data-lightbox-index]' ).forEach( function ( trigger ) {
		trigger.addEventListener( 'click', function () {
			openLightbox( trigger );
		} );
	} );

	if ( lightbox ) {
		lightbox.querySelectorAll( '[data-lightbox-close]' ).forEach( function ( el ) {
			el.addEventListener( 'click', closeLightbox );
		} );
	}
	if ( prevBtn ) {
		prevBtn.addEventListener( 'click', function () {
			showPhoto( photoIndex - 1 );
		} );
	}
	if ( nextBtn ) {
		nextBtn.addEventListener( 'click', function () {
			showPhoto( photoIndex + 1 );
		} );
	}
} )();
