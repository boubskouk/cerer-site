/**
 * Newsletter (AJAX) — README §4.7.
 * Vanilla JS, aucune dépendance jQuery.
 */
( function () {
	'use strict';

	/* -----------------------------------------------------------
	 * Newsletter
	 * --------------------------------------------------------- */
	var form = document.getElementById( 'cerer-newsletter-form' );

	if ( form && window.cererNewsletter ) {
		var feedback = form.parentElement.querySelector( '.cerer-newsletter__feedback' );

		form.addEventListener( 'submit', function ( e ) {
			e.preventDefault();

			var email  = form.querySelector( '#cerer-newsletter-email' );
			var button = form.querySelector( 'button[type="submit"]' );

			if ( ! email.checkValidity() ) {
				showFeedback( email.validationMessage || 'Adresse e-mail invalide.', true );
				email.focus();
				return;
			}

			var data = new FormData( form );
			data.append( 'action', 'cerer_newsletter_subscribe' );
			data.append( 'nonce', window.cererNewsletter.nonce );

			button.disabled = true;

			fetch( window.cererNewsletter.ajaxUrl, {
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
					showFeedback( message, ! ( json && json.success ) );
					if ( json && json.success ) {
						form.reset();
					}
				} )
				.catch( function () {
					showFeedback( 'Une erreur est survenue, réessayez.', true );
				} )
				.finally( function () {
					button.disabled = false;
				} );
		} );

		function showFeedback( message, isError ) {
			if ( ! feedback ) {
				return;
			}
			feedback.textContent = message;
			feedback.hidden = false;
			feedback.classList.toggle( 'is-error', !! isError );
		}
	}

} )();
