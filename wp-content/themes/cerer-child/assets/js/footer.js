/**
 * Newsletter (AJAX) + chatbot FAQ — README §4.7 et §4.9.
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

	/* -----------------------------------------------------------
	 * Chatbot FAQ
	 * --------------------------------------------------------- */
	var chatbot = document.getElementById( 'cerer-chatbot' );
	if ( ! chatbot ) {
		return;
	}

	var toggle = document.getElementById( 'cerer-chatbot-toggle' );
	var panel  = document.getElementById( 'cerer-chatbot-panel' );
	var close  = document.getElementById( 'cerer-chatbot-close' );

	function getFocusable() {
		return Array.prototype.slice.call(
			panel.querySelectorAll( 'button, a[href], input, [tabindex]:not([tabindex="-1"])' )
		).filter( function ( el ) {
			return ! el.hidden && el.offsetParent !== null;
		} );
	}

	function openPanel() {
		panel.hidden = false;
		toggle.setAttribute( 'aria-expanded', 'true' );
		var focusable = getFocusable();
		if ( focusable.length ) {
			focusable[ 0 ].focus();
		}
		document.addEventListener( 'keydown', onPanelKeydown );
		document.addEventListener( 'click', onOutsideClick );
	}

	function closePanel( returnFocus ) {
		panel.hidden = true;
		toggle.setAttribute( 'aria-expanded', 'false' );
		document.removeEventListener( 'keydown', onPanelKeydown );
		document.removeEventListener( 'click', onOutsideClick );
		if ( returnFocus ) {
			toggle.focus();
		}
	}

	function onOutsideClick( e ) {
		if ( ! chatbot.contains( e.target ) ) {
			closePanel( false );
		}
	}

	// Piégeage du focus + Échap — README §4.9 / §6.2.
	function onPanelKeydown( e ) {
		if ( 'Escape' === e.key || 'Esc' === e.key ) {
			closePanel( true );
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

	toggle.addEventListener( 'click', function () {
		if ( panel.hidden ) {
			openPanel();
		} else {
			closePanel( false );
		}
	} );

	close.addEventListener( 'click', function () {
		closePanel( true );
	} );

	/**
	 * Accordéon des questions. Phase 1 : réponse lue depuis le DOM (générée
	 * par cerer_get_chatbot_faqs() côté PHP). Phase 2 : remplacer ce
	 * gestionnaire par un appel à l'API Claude côté serveur — le contrat
	 * (un bouton .cerer-chatbot__question qui révèle une réponse) ne change
	 * pas, seule la source de la réponse change.
	 */
	var questions = chatbot.querySelectorAll( '.cerer-chatbot__question' );
	questions.forEach( function ( btn ) {
		btn.addEventListener( 'click', function () {
			var answer = document.getElementById( btn.getAttribute( 'aria-controls' ) );
			if ( ! answer ) {
				return;
			}
			var open = answer.hidden;
			answer.hidden = ! open;
			btn.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		} );
	} );
} )();
