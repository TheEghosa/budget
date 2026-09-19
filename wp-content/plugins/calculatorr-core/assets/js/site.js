/*
 * Light and dark switching for calculatorr.org.
 *
 * The palette itself lives in tokens.css, which already answers to the
 * visitor's system preference. This adds the part a stylesheet cannot: an
 * explicit choice that overrides the system and survives a reload.
 *
 * The attribute is applied by a tiny inline script in the head, before
 * anything is painted, so nothing here needs to run early. What runs here is
 * the button: labelling it correctly on load, flipping it on click, and
 * following the system again if the visitor clears their choice.
 */
( function () {
	'use strict';

	var KEY = 'calcr-theme';
	var root = document.documentElement;

	/* Storage is unavailable in some privacy modes and throws rather than
	   returning null, so every access is guarded. A visitor who cannot store a
	   preference still gets a working button for the length of the visit. */
	function readStored() {
		try {
			var value = window.localStorage.getItem( KEY );
			return ( 'dark' === value || 'light' === value ) ? value : null;
		} catch ( error ) {
			return null;
		}
	}

	function writeStored( value ) {
		try {
			if ( null === value ) {
				window.localStorage.removeItem( KEY );
			} else {
				window.localStorage.setItem( KEY, value );
			}
		} catch ( error ) {
			/* Nothing to do: the choice still applies to this page. */
		}
	}

	function systemPrefersDark() {
		return !! ( window.matchMedia && window.matchMedia( '(prefers-color-scheme: dark)' ).matches );
	}

	/* What the visitor is actually looking at, which is the stored choice when
	   there is one and the system preference when there is not. */
	function activeTheme() {
		var stored = readStored();

		if ( stored ) {
			return stored;
		}

		return systemPrefersDark() ? 'dark' : 'light';
	}

	function apply( theme ) {
		/* Both names are written: data-theme is what the design system
		   specifies and what the token stylesheet keys off first, and
		   data-calcr-theme is what the site shipped with, so a preference
		   already sitting in somebody's browser still lands somewhere the
		   stylesheet is listening. */
		root.setAttribute( 'data-theme', theme );
		root.setAttribute( 'data-calcr-theme', theme );
		label( theme );
	}

	function label( theme ) {
		var dark = 'dark' === theme;

		buttons().forEach( function ( button ) {
			button.setAttribute( 'aria-pressed', dark ? 'true' : 'false' );
			/* The accessible name describes the destination rather than the
			   current state, because that is what a button does. */
			button.setAttribute( 'aria-label', dark ? 'Switch to light mode' : 'Switch to dark mode' );

			var text = button.querySelector( '[data-calcr-theme-label]' );

			if ( text ) {
				text.textContent = dark ? 'Light' : 'Dark';
			}
		} );
	}

	function buttons() {
		return Array.prototype.slice.call( document.querySelectorAll( '[data-calcr-theme-toggle]' ) );
	}

	/*
	 * The button is normally printed into the header menu by PHP. On an install
	 * with no menu assigned to the header there is nothing to print it into, so
	 * one is built here instead. It is a fallback rather than the usual path,
	 * because a control added after load can shift the header as it appears.
	 */
	function ensureButton() {
		if ( buttons().length ) {
			return;
		}

		var header = document.querySelector( '.site-header .header-inner' );

		if ( ! header ) {
			return;
		}

		var wrap = document.createElement( 'div' );
		wrap.className = 'calcr-theme-switch';
		wrap.innerHTML = '<button type="button" class="calcr-theme-switch__btn" data-calcr-theme-toggle aria-pressed="false">' +
			'<span class="calcr-theme-switch__icons">' +
			'<svg class="calcr-icon calcr-icon--moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"></path></svg>' +
			'<svg class="calcr-icon calcr-icon--sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="4.2"></circle><path d="M12 2.5v2M12 19.5v2M4.2 4.2l1.4 1.4M18.4 18.4l1.4 1.4M2.5 12h2M19.5 12h2M4.2 19.8l1.4-1.4M18.4 5.6l1.4-1.4"></path></svg>' +
			'</span><span class="calcr-theme-switch__label" data-calcr-theme-label>Dark</span></button>';

		header.appendChild( wrap );
	}

	function start() {
		ensureButton();

		var current = activeTheme();

		/* Applied explicitly even when it matches the system, so the attribute
		   and the button never disagree about which mode is showing. */
		apply( current );

		document.addEventListener( 'click', function ( event ) {
			var button = event.target.closest ? event.target.closest( '[data-calcr-theme-toggle]' ) : null;

			if ( ! button ) {
				return;
			}

			var next = 'dark' === activeTheme() ? 'light' : 'dark';
			writeStored( next );
			apply( next );
		} );

		/* Someone who has never pressed the button should keep following their
		   system, including when it switches at sunset. */
		if ( window.matchMedia ) {
			var query = window.matchMedia( '(prefers-color-scheme: dark)' );
			var onChange = function () {
				if ( ! readStored() ) {
					apply( systemPrefersDark() ? 'dark' : 'light' );
				}
			};

			if ( query.addEventListener ) {
				query.addEventListener( 'change', onChange );
			} else if ( query.addListener ) {
				query.addListener( onChange );
			}
		}
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', start );
	} else {
		start();
	}
}() );
