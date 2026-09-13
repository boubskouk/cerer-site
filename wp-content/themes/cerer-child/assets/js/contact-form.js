/**
 * Formulaire de contact — AJAX, même patron que la newsletter (assets/js/footer.js).
 */
( function () {
	'use strict';

	var form = document.getElementById( 'cerer-contact-form' );
	if ( ! form || ! window.cererContact ) {
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
			showFeedback( 'Merci de remplir les champs obligatoires (*).', 'error' );
			return;
		}

		var button = form.querySelector( 'button[type="submit"]' );
		var data   = new FormData( form );
		data.append( 'action', 'cerer_contact_form' );
		data.append( 'nonce', window.cererContact.nonce );

		button.disabled = true;

		fetch( window.cererContact.ajaxUrl, {
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
					: 'Une erreur est survenue, réessayez.';
				showFeedback( message, json && json.success ? 'success' : 'error' );
				if ( json && json.success ) {
					form.reset();
				}
			} )
			.catch( function () {
				showFeedback( 'Une erreur est survenue, réessayez.', 'error' );
			} )
			.finally( function () {
				button.disabled = false;
			} );
	} );
} )();
