/**
 * Filtre des actualités de l'accueil — AJAX, sans rechargement de page
 * (PROMPT-14). Chaque pill est un <button aria-pressed>, pas un <a>.
 */
( function () {
	'use strict';

	var pills = document.querySelectorAll( '.cerer-news__pill' );
	var grid  = document.getElementById( 'cerer-news-grid' );

	if ( ! pills.length || ! grid || ! window.cererActualites ) {
		return;
	}

	function setActive( pill ) {
		pills.forEach( function ( p ) {
			var isActive = p === pill;
			p.classList.toggle( 'is-active', isActive );
			p.setAttribute( 'aria-pressed', isActive ? 'true' : 'false' );
		} );
	}

	pills.forEach( function ( pill ) {
		pill.addEventListener( 'click', function () {
			if ( pill.classList.contains( 'is-active' ) ) {
				return;
			}

			setActive( pill );
			grid.setAttribute( 'aria-busy', 'true' );

			var data = new FormData();
			data.append( 'action', 'cerer_actualites_filter' );
			data.append( 'nonce', window.cererActualites.nonce );
			data.append( 'categorie', pill.getAttribute( 'data-categorie' ) || '' );

			fetch( window.cererActualites.ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				body: data,
			} )
				.then( function ( res ) {
					return res.json();
				} )
				.then( function ( json ) {
					if ( json && json.success && json.data && 'string' === typeof json.data.html ) {
						grid.innerHTML = json.data.html;
					}
				} )
				.finally( function () {
					grid.removeAttribute( 'aria-busy' );
				} );
		} );
	} );
} )();
