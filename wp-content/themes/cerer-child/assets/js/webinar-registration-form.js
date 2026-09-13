/**
 * Inscription au prochain webinaire — même patron AJAX que
 * assets/js/contact-form.js.
 */
( function () {
	'use strict';

	var form = document.getElementById( 'cerer-webinar-form' );
	if ( ! form || ! window.cererWebinar ) {
		return;
	}

	var feedback = form.querySelector( '.cerer-form__feedback' );

	function showFeedback( message, state ) {
		if ( ! feedback ) {
			return;
		}
		feedback.textContent = message;
		feedback.hidden = false;
		feedback.classList.toggle( 'is-error', 'error' === state );
		feedback.classList.toggle( 'is-success', 'success' === state );
	}

	form.addEventListener( 'submit', function ( e ) {
		e.preventDefault();

		if ( ! form.checkValidity() ) {
			showFeedback( window.cererWebinar.i18nRequired, 'error' );
			return;
		}

		var button = form.querySelector( 'button[type="submit"]' );
		var data   = new FormData( form );
		data.append( 'action', 'cerer_webinar_registration' );
		data.append( 'nonce', window.cererWebinar.nonce );

		button.disabled = true;

		fetch( window.cererWebinar.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			body: data,
		} )
			.then( function ( res ) {
				return res.json();
			} )
			.then( function ( json ) {
				var message = json && json.data && json.data.message
					? json.data.message
					: window.cererWebinar.i18nError;
				showFeedback( message, json && json.success ? 'success' : 'error' );
				if ( json && json.success ) {
					form.reset();
				}
			} )
			.catch( function () {
				showFeedback( window.cererWebinar.i18nError, 'error' );
			} )
			.finally( function () {
				button.disabled = false;
			} );
	} );
} )();
