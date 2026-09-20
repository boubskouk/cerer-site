/**
 * Formulaire de demande de stage — AJAX, même patron que assets/js/contact-form.js.
 */
( function () {
	'use strict';

	var form = document.getElementById( 'cerer-stage-form' );
	if ( ! form || ! window.cererStage ) {
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
			showFeedback( window.cererStage.i18nRequired, 'error' );
			return;
		}

		var button = form.querySelector( 'button[type="submit"]' );
		var data   = new FormData( form );
		data.append( 'action', 'cerer_stage_request' );
		data.append( 'nonce', window.cererStage.nonce );

		button.disabled = true;

		fetch( window.cererStage.ajaxUrl, {
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
					: window.cererStage.i18nError;
				showFeedback( message, json && json.success ? 'success' : 'error' );
				if ( json && json.success ) {
					form.reset();
				}
			} )
			.catch( function () {
				showFeedback( window.cererStage.i18nError, 'error' );
			} )
			.finally( function () {
				button.disabled = false;
			} );
	} );
} )();
