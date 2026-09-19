/*
 * The shared calculator runtime.
 *
 * One copy of this file drives every calculator on the site. It never knows
 * what any individual tool computes: it gathers the field values, hands them to
 * the formula registered under the same slug, and paints whatever comes back.
 * That is what keeps adding a calculator down to one config file and one
 * formula rather than a new script each time.
 */
( function () {
	'use strict';

	var FORMULAS = window.CalculatorrFormulas || {};
	var reported = {};

	/**
	 * Sends a formula error back to the site so it appears in the admin log.
	 *
	 * A formula that throws on somebody's phone leaves no trace on the server
	 * at all: they see a stale answer and leave, and nobody ever finds out.
	 * This is the only way that failure becomes visible.
	 */
	function report( slug, error ) {
		var config = window.CalculatorrConfig;

		if ( ! config || ! config.logUrl || reported[ slug ] ) {
			return;
		}

		reported[ slug ] = true;

		try {
			var body = JSON.stringify( {
				slug: slug,
				message: ( error && error.message ) ? error.message : String( error ),
				context: navigator.userAgent.slice( 0, 200 )
			} );

			if ( navigator.sendBeacon ) {
				navigator.sendBeacon( config.logUrl, new Blob( [ body ], { type: 'application/json' } ) );
				return;
			}

			var xhr = new XMLHttpRequest();
			xhr.open( 'POST', config.logUrl, true );
			xhr.setRequestHeader( 'Content-Type', 'application/json' );
			xhr.send( body );
		} catch ( ignored ) {
			/* Reporting must never become the thing that breaks the page. */
		}
	}

	/**
	 * Reads every value out of one calculator, including repeater rows, which
	 * arrive as an array of objects so a formula can iterate them naturally.
	 */
	function gather( root ) {
		var values = {};

		root.querySelectorAll( '[data-calcr-input]' ).forEach( function ( el ) {
			values[ el.getAttribute( 'data-calcr-input' ) ] = el.value;
		} );

		root.querySelectorAll( '[data-calcr-repeater]' ).forEach( function ( rep ) {
			var name = rep.getAttribute( 'data-calcr-repeater' );
			var rows = [];

			rep.querySelectorAll( '[data-calcr-rep-row]' ).forEach( function ( row ) {
				var entry = {};
				row.querySelectorAll( '[data-calcr-rep-cell]' ).forEach( function ( cell ) {
					entry[ cell.getAttribute( 'data-calcr-rep-cell' ) ] = cell.value;
				} );
				rows.push( entry );
			} );

			values[ name ] = rows;
		} );

		return values;
	}

	function paint( root, result ) {
		if ( ! result ) {
			return;
		}

		var label = root.querySelector( '[data-calcr-primary-label]' );
		var value = root.querySelector( '[data-calcr-primary-value]' );
		var bar = root.querySelector( '[data-calcr-bar]' );
		var rows = root.querySelector( '[data-calcr-rows]' );
		var note = root.querySelector( '[data-calcr-note]' );

		if ( label && result.label ) {
			label.textContent = result.label;
		}

		if ( value ) {
			value.textContent = ( result.value === undefined || result.value === null ) ? '—' : result.value;
		}

		if ( bar ) {
			if ( result.bar && result.bar.length ) {
				bar.innerHTML = '';
				result.bar.forEach( function ( segment ) {
					var piece = document.createElement( 'span' );
					piece.style.width = segment.pct + '%';
					piece.style.background = segment.color;
					bar.appendChild( piece );
				} );
				bar.hidden = false;
			} else {
				bar.hidden = true;
			}
		}

		if ( rows ) {
			rows.innerHTML = '';
			( result.rows || [] ).forEach( function ( row ) {
				var li = document.createElement( 'li' );
				li.className = 'calcr__row' + ( row.divide ? ' calcr__row--divide' : '' );

				if ( row.color ) {
					var swatch = document.createElement( 'span' );
					swatch.className = 'calcr__row-swatch';
					swatch.style.background = row.color;
					li.appendChild( swatch );
				}

				var name = document.createElement( 'span' );
				name.className = 'calcr__row-label';
				name.textContent = row.label;
				li.appendChild( name );

				var amount = document.createElement( 'span' );
				amount.className = 'calcr__row-value';
				amount.textContent = row.value;
				if ( row.emphasis ) {
					amount.style.color = 'var(--calcr-warn)';
				}
				li.appendChild( amount );

				rows.appendChild( li );
			} );
		}

		var sub = root.querySelector( '[data-calcr-primary-sub]' );

		if ( sub ) {
			sub.textContent = result.sub || '';
			sub.hidden = ! result.sub;
		}

		if ( note ) {
			note.textContent = result.note || '';
			note.hidden = ! result.note;
		}

		var stickyLabel = root.querySelector( '[data-calcr-sticky-label]' );
		var stickyValue = root.querySelector( '[data-calcr-sticky-value]' );

		if ( stickyLabel && result.label ) {
			stickyLabel.textContent = result.label;
		}

		if ( stickyValue ) {
			stickyValue.textContent = ( result.value === undefined || result.value === null ) ? '' : result.value;
		}

		root.__calcrResult = result;
	}

	/**
	 * Builds the plain-text version of the current answer for the clipboard.
	 * People paste these into messages and spreadsheets, so it is written as
	 * readable lines rather than as the JSON the runtime works in.
	 */
	function resultAsText( root ) {
		var result = root.__calcrResult;

		if ( ! result ) {
			return '';
		}

		var lines = [ ( result.label || 'Result' ) + ': ' + ( result.value || '' ) ];

		( result.rows || [] ).forEach( function ( row ) {
			lines.push( row.label + ': ' + row.value );
		} );

		lines.push( '' );
		lines.push( document.title );
		lines.push( window.location.href );

		return lines.join( '\n' );
	}

	function bindCopy( root ) {
		var button = root.querySelector( '[data-calcr-copy]' );

		if ( ! button ) {
			return;
		}

		var label = button.querySelector( '[data-calcr-copy-label]' );
		var original = label ? label.textContent : '';
		var timer = null;

		button.addEventListener( 'click', function () {
			var text = resultAsText( root );

			if ( ! text ) {
				return;
			}

			var done = function ( message ) {
				if ( ! label ) {
					return;
				}
				label.textContent = message;
				window.clearTimeout( timer );
				timer = window.setTimeout( function () {
					label.textContent = original;
				}, 2000 );
			};

			if ( navigator.clipboard && navigator.clipboard.writeText ) {
				navigator.clipboard.writeText( text ).then(
					function () { done( 'Copied' ); },
					function () { done( 'Press Ctrl+C' ); }
				);
				return;
			}

			/* Older browsers and insecure origins have no clipboard API, so
			   fall back to selecting the text and letting the person copy it
			   rather than failing silently with no explanation. */
			var scratch = document.createElement( 'textarea' );
			scratch.value = text;
			scratch.setAttribute( 'readonly', '' );
			scratch.style.cssText = 'position:absolute;left:-9999px';
			document.body.appendChild( scratch );
			scratch.select();

			try {
				done( document.execCommand( 'copy' ) ? 'Copied' : 'Press Ctrl+C' );
			} catch ( error ) {
				done( 'Press Ctrl+C' );
			}

			document.body.removeChild( scratch );
		} );
	}

	/**
	 * The sticky answer bar appears only once the result panel has scrolled off
	 * screen, because showing it while the real panel is visible would cover
	 * content to repeat something already in view.
	 */
	/**
	 * The sticky answer bar on a phone.
	 *
	 * On a 390 by 844 screen the inputs push the number below the fold, so it
	 * follows the visitor up the page while they type. Everything here exists
	 * to stop it appearing when it is not useful, because a bar that is always
	 * there is just a smaller screen.
	 */
	function bindSticky( root ) {
		var bar = root.querySelector( '[data-calcr-sticky]' );
		var panel = root.querySelector( '[data-calcr-result]' );
		var number = root.querySelector( '[data-calcr-primary-value]' );

		if ( ! bar || ! panel || ! number ) {
			return;
		}

		var wide = window.matchMedia ? window.matchMedia( '(min-width: 768px)' ) : null;
		var numberVisible = false;
		var typing = false;

		/* The bar owns the live region while it is showing and the panel gives
		   its up, because two regions announcing the same number means a
		   screen reader reads every keystroke twice. */
		function announceFrom( element ) {
			bar.setAttribute( 'aria-live', element === bar ? 'polite' : 'off' );
			panel.setAttribute( 'aria-live', element === panel ? 'polite' : 'off' );
		}

		function shouldShow() {
			if ( wide && wide.matches ) {
				return false;
			}

			/* Live feedback while typing is the bar's main job, so a focused
			   field keeps it up even if the number happens to be on screen. */
			return typing || ! numberVisible;
		}

		function sync() {
			var show = shouldShow();

			if ( show === ! bar.hidden ) {
				announceFrom( show ? bar : panel );
				return;
			}

			if ( show ) {
				bar.hidden = false;
				/* Read back before adding the class so the browser has a frame
				   to paint the hidden state from, otherwise the fade is
				   skipped and the bar simply appears. */
				void bar.offsetHeight;
				bar.classList.add( 'is-visible' );
			} else {
				bar.classList.remove( 'is-visible' );
				window.setTimeout( function () {
					if ( ! shouldShow() ) {
						bar.hidden = true;
					}
				}, 150 );
			}

			announceFrom( show ? bar : panel );
		}

		if ( 'IntersectionObserver' in window ) {
			/* The number rather than the whole panel: on a tall result card the
			   rows can be in view while the figure itself is still above the
			   fold, and the figure is what the bar is standing in for. */
			var observer = new IntersectionObserver( function ( entries ) {
				entries.forEach( function ( entry ) {
					numberVisible = entry.isIntersecting;
				} );
				sync();
			}, { threshold: 1 } );

			observer.observe( number );
		}

		root.addEventListener( 'focusin', function ( event ) {
			if ( event.target.closest( '.calcr__form' ) ) {
				typing = true;
				sync();
			}
		} );

		root.addEventListener( 'focusout', function () {
			/* Deferred, because moving between two fields fires focusout
			   before the next focusin and the bar would flicker. */
			window.setTimeout( function () {
				typing = !! ( document.activeElement && document.activeElement.closest
					&& document.activeElement.closest( '.calcr__form' ) );
				sync();
			}, 0 );
		} );

		/* An on-screen keyboard shrinks the visual viewport without moving the
		   layout viewport, so a bar fixed to the bottom of the page ends up
		   behind the keyboard on iOS. Riding the visual viewport keeps it in
		   sight, which is the whole point of it while somebody is typing. */
		if ( window.visualViewport ) {
			var ride = function () {
				var vv = window.visualViewport;
				var gap = ( window.innerHeight - vv.height - vv.offsetTop );
				bar.style.transform = gap > 1 ? 'translateY(-' + Math.round( gap ) + 'px)' : '';
			};

			window.visualViewport.addEventListener( 'resize', ride );
			window.visualViewport.addEventListener( 'scroll', ride );
			ride();
		}

		if ( wide ) {
			var onWide = function () { sync(); };

			if ( wide.addEventListener ) {
				wide.addEventListener( 'change', onWide );
			} else if ( wide.addListener ) {
				wide.addListener( onWide );
			}
		}

		var breakdown = root.querySelector( '[data-calcr-breakdown]' );

		if ( breakdown ) {
			breakdown.addEventListener( 'click', function () {
				panel.scrollIntoView( { behavior: 'smooth', block: 'center' } );
			} );
		}

		sync();
	}

	/**
	 * Shows or hides fields that declared a dependency, so a unit switch never
	 * leaves the visitor looking at two sets of height boxes at once.
	 */
	function applyVisibility( root ) {
		var current = {};

		root.querySelectorAll( '[data-calcr-input]' ).forEach( function ( el ) {
			current[ el.getAttribute( 'data-calcr-input' ) ] = el.value;
		} );

		root.querySelectorAll( '[data-calcr-when]' ).forEach( function ( field ) {
			var conditions;

			try {
				conditions = JSON.parse( field.getAttribute( 'data-calcr-when' ) );
			} catch ( error ) {
				return;
			}

			var visible = Object.keys( conditions ).every( function ( key ) {
				var expected = conditions[ key ];
				return Array.isArray( expected )
					? expected.indexOf( current[ key ] ) !== -1
					: String( current[ key ] ) === String( expected );
			} );

			field.hidden = ! visible;
		} );
	}

	/**
	 * Whether enough has been typed for an answer to mean anything.
	 *
	 * Only fields marked required on the server count, and only while they are
	 * on screen, because a metric height is not missing when the imperial
	 * inputs are the ones showing. Optional fields stay out of it, so a waste
	 * allowance left blank does not hold the whole result back.
	 */
	function isReady( root ) {
		if ( ! root.hasAttribute( 'data-calcr-empty-start' ) ) {
			return true;
		}

		var ready = true;

		root.querySelectorAll( '[data-calcr-required]' ).forEach( function ( el ) {
			var field = el.closest ? el.closest( '[data-calcr-when]' ) : null;

			if ( field && field.hidden ) {
				return;
			}

			if ( '' === String( el.value ).trim() ) {
				ready = false;
			}
		} );

		return ready;
	}

	/**
	 * The worked example the panel shows before anything has been entered, read
	 * off the element the server put it on. It is the answer to the numbers the
	 * fields carry as placeholders, so the two always agree.
	 */
	function exampleResult( root ) {
		var raw = root.getAttribute( 'data-calcr-example' );

		if ( ! raw ) {
			return null;
		}

		try {
			return JSON.parse( raw );
		} catch ( error ) {
			/* Malformed for any reason, and the dash below is the fallback. */
			return null;
		}
	}

	/**
	 * The panel before anything has been entered, and the panel it returns to
	 * after a reset: the worked example, muted and captioned as one. Copying and
	 * sharing stay switched off, because an example is not the visitor's result
	 * to send anyone.
	 *
	 * The server already painted this, so the first call here is a no-op in
	 * practice. It still has to exist, because a reset has to be able to put it
	 * back after real figures have overwritten it.
	 */
	function awaitInput( root ) {
		var label = root.querySelector( '[data-calcr-primary-label]' );
		var example = exampleResult( root );
		var note = root.getAttribute( 'data-calcr-prompt' ) || '';

		if ( example ) {
			paint( root, {
				label: example.label,
				value: example.value,
				sub: example.sub,
				rows: example.rows || [],
				bar: example.bar || [],
				note: note
			} );
		} else {
			paint( root, {
				label: label ? label.textContent : 'Result',
				value: '—',
				rows: [],
				bar: [],
				note: note
			} );
		}

		root.__calcrResult = null;
		root.classList.add( 'calcr--awaiting' );
		setResultActionsEnabled( root, false );
	}

	function setResultActionsEnabled( root, enabled ) {
		root.querySelectorAll( '[data-calcr-copy], [data-calcr-share-toggle]' ).forEach( function ( button ) {
			button.disabled = ! enabled;
		} );

		if ( ! enabled ) {
			var panel = root.querySelector( '[data-calcr-share-panel]' );
			var toggle = root.querySelector( '[data-calcr-share-toggle]' );

			if ( panel ) {
				panel.hidden = true;
			}

			if ( toggle ) {
				toggle.setAttribute( 'aria-expanded', 'false' );
			}
		}
	}

	/**
	 * Whether the form still holds exactly what it was rendered with.
	 *
	 * Reset has nothing to do in that state, which matters more than it sounds:
	 * since the fields now open blank, a freshly loaded page has nothing to
	 * clear, and a button that looks live and does nothing reads as broken
	 * rather than as finished.
	 */
	function isPristine( root ) {
		var defaults = root.__calcrDefaults;

		if ( ! defaults ) {
			return false;
		}

		var pristine = true;

		root.querySelectorAll( '[data-calcr-input]' ).forEach( function ( el ) {
			var key = el.getAttribute( 'data-calcr-input' );

			if ( defaults.hasOwnProperty( key ) && String( el.value ) !== String( defaults[ key ] ) ) {
				pristine = false;
			}
		} );

		/* A row added or removed is a change even when every box in it is
		   still empty. */
		root.querySelectorAll( '[data-calcr-repeater]' ).forEach( function ( rep ) {
			var name = rep.getAttribute( 'data-calcr-repeater' );
			var rows = rep.querySelectorAll( '[data-calcr-rep-row]' ).length;
			var was = root.__calcrRows && root.__calcrRows[ name ];

			if ( was !== undefined && was !== rows ) {
				pristine = false;
			}
		} );

		root.querySelectorAll( '[data-calcr-rep-cell]' ).forEach( function ( cell ) {
			var seeded = cell.getAttribute( 'data-calcr-rep-seeded' );
			var value = String( cell.value ).trim();

			if ( null === seeded ) {
				if ( '' !== value ) {
					pristine = false;
				}
				return;
			}

			if ( '' !== seeded && value !== seeded ) {
				pristine = false;
			}
		} );

		return pristine;
	}

	function updateResetState( root ) {
		var reset = root.querySelector( '[data-calcr-reset]' );

		if ( reset ) {
			reset.disabled = isPristine( root );
		}
	}

	/* One calculator, one page load, one count. Sent on the first calculation
	   that actually produces an answer, because a page view is not usage:
	   somebody can arrive from a search, read the explainer and leave without
	   touching a field. sendBeacon so it never delays anything, and it is
	   simply skipped where the browser does not have it rather than falling
	   back to a request that could hold the page open. */
	var counted = {};

	function countUse( slug ) {
		var config = window.CalculatorrConfig;

		if ( ! config || ! config.usageUrl || counted[ slug ] ) {
			return;
		}

		counted[ slug ] = true;

		if ( ! navigator.sendBeacon ) {
			return;
		}

		try {
			navigator.sendBeacon(
				config.usageUrl,
				new Blob( [ JSON.stringify( { slug: slug } ) ], { type: 'application/json' } )
			);
		} catch ( error ) {
			/* Counting is never worth an error in front of a visitor. */
		}
	}

	function recalculate( root ) {
		applyVisibility( root );
		updateResetState( root );

		var slug = root.getAttribute( 'data-calcr-slug' );
		var formula = FORMULAS[ slug ];

		if ( typeof formula !== 'function' ) {
			return;
		}

		if ( ! isReady( root ) ) {
			awaitInput( root );
			return;
		}

		root.classList.remove( 'calcr--awaiting' );
		setResultActionsEnabled( root, true );
		countUse( slug );

		try {
			paint( root, formula( gather( root ) ) );
		} catch ( error ) {
			/* A broken formula degrades to the server-rendered default rather
			   than taking the page down, so the visitor still sees an answer.
			   The error is reported once, because otherwise it fires on every
			   keystroke and buries the log. */
			if ( window.console && window.console.error ) {
				window.console.error( 'calculatorr: ' + slug, error );
			}

			report( slug, error );
		}
	}

	function bindSegmented( root ) {
		root.querySelectorAll( '[data-calcr-seg]' ).forEach( function ( group ) {
			var name = group.getAttribute( 'data-calcr-seg' );
			var hidden = root.querySelector( '[data-calcr-input="' + name + '"]' );

			group.querySelectorAll( '.calcr-seg__btn' ).forEach( function ( button ) {
				button.addEventListener( 'click', function () {
					group.querySelectorAll( '.calcr-seg__btn' ).forEach( function ( sibling ) {
						sibling.classList.remove( 'is-active' );
						sibling.setAttribute( 'aria-pressed', 'false' );
					} );

					button.classList.add( 'is-active' );
					button.setAttribute( 'aria-pressed', 'true' );

					if ( hidden ) {
						hidden.value = button.getAttribute( 'data-value' );
					}

					recalculate( root );
				} );
			} );
		} );
	}

	function bindRepeaters( root ) {
		root.querySelectorAll( '[data-calcr-repeater]' ).forEach( function ( rep ) {
			var list = rep.querySelector( '[data-calcr-rep-list]' );
			var add = rep.querySelector( '[data-calcr-rep-add]' );

			if ( ! list ) {
				return;
			}

			var template = list.querySelector( '[data-calcr-rep-row]' );
			var blank = template ? template.cloneNode( true ) : null;

			if ( blank ) {
				blank.querySelectorAll( 'input' ).forEach( function ( input ) {
					input.value = '';
				} );
			}

			if ( add && blank ) {
				add.addEventListener( 'click', function () {
					list.appendChild( blank.cloneNode( true ) );
					recalculate( root );
				} );
			}

			/* Delegated so rows added later are covered without rebinding. */
			rep.addEventListener( 'click', function ( event ) {
				var remove = event.target.closest( '[data-calcr-rep-remove]' );

				if ( ! remove ) {
					return;
				}

				var rows = list.querySelectorAll( '[data-calcr-rep-row]' );

				/* The last row is emptied instead of deleted, because a
				   repeater with nothing in it gives the visitor no way back. */
				if ( rows.length <= 1 ) {
					rows[ 0 ].querySelectorAll( 'input' ).forEach( function ( input ) {
						input.value = '';
					} );
				} else {
					remove.closest( '[data-calcr-rep-row]' ).remove();
				}

				recalculate( root );
			} );
		} );
	}

	/**
	 * Restores the figures a shared link carries. The state lives in the URL
	 * fragment, so it never reaches the server and never turns one page into
	 * thousands of crawlable near-duplicates.
	 */
	function restoreFromHash( root ) {
		var hash = window.location.hash.replace( /^#/, '' );

		if ( ! hash || hash.indexOf( '=' ) === -1 ) {
			return false;
		}

		var restored = false;

		hash.split( '&' ).forEach( function ( pair ) {
			var parts = pair.split( '=' );

			if ( parts.length !== 2 ) {
				return;
			}

			var key = decodeURIComponent( parts[ 0 ] );
			var value = decodeURIComponent( parts[ 1 ] );
			var field = root.querySelector( '[data-calcr-input="' + key.replace( /"/g, '' ) + '"]' );

			if ( field ) {
				field.value = value;
				restored = true;
			}
		} );

		return restored;
	}

	function syncSegmented( root ) {
		root.querySelectorAll( '[data-calcr-seg]' ).forEach( function ( group ) {
			var current = root.querySelector( '[data-calcr-input="' + group.getAttribute( 'data-calcr-seg' ) + '"]' );

			group.querySelectorAll( '.calcr-seg__btn' ).forEach( function ( button ) {
				var active = current && button.getAttribute( 'data-value' ) === current.value;
				button.classList.toggle( 'is-active', active );
				button.setAttribute( 'aria-pressed', active ? 'true' : 'false' );
			} );
		} );
	}

	/**
	 * Keeps the address bar in step with the form, debounced so a person
	 * typing a number does not generate one history entry per keystroke.
	 * replaceState is used rather than pushState for the same reason: the back
	 * button should leave the page, not walk back through every digit.
	 */
	function bindUrlState( root ) {
		if ( ! window.history || ! window.history.replaceState || ! window.CalculatorrShare ) {
			return;
		}

		var timer = null;

		var update = function () {
			window.clearTimeout( timer );
			timer = window.setTimeout( function () {
				try {
					window.history.replaceState( null, '', window.CalculatorrShare.url( root ) );
				} catch ( error ) {
					/* Some embedded contexts forbid history writes. The
					   calculator still works; only the address bar is stale. */
				}
			}, 600 );
		};

		root.addEventListener( 'input', update );
		root.addEventListener( 'change', update );
		root.addEventListener( 'click', function ( event ) {
			if ( event.target.closest( '.calcr-seg__btn, [data-calcr-reset]' ) ) {
				update();
			}
		} );
	}

	function init( root ) {
		if ( root.hasAttribute( 'data-calcr-ready' ) ) {
			return;
		}

		root.setAttribute( 'data-calcr-ready', '' );

		/* Defaults are read from the markup rather than declared twice, so the
		   server-rendered page and the reset button can never drift apart. */
		var defaults = {};
		root.querySelectorAll( '[data-calcr-input]' ).forEach( function ( el ) {
			defaults[ el.getAttribute( 'data-calcr-input' ) ] = el.value;
		} );

		/* Stashed on the element because the reset button's enabled state is
		   decided during recalculation, which happens outside this scope. */
		root.__calcrDefaults = defaults;
		root.__calcrRows = {};

		/* Cells that arrived with something in them, a dropdown included, are
		   part of the starting state rather than something the visitor typed.
		   They are marked first so the snapshot taken next carries the marks,
		   otherwise a restored row looks like fresh input and leaves the reset
		   button enabled straight after a reset. */
		root.querySelectorAll( '[data-calcr-rep-cell]' ).forEach( function ( cell ) {
			if ( '' !== String( cell.value ).trim() ) {
				cell.setAttribute( 'data-calcr-rep-seeded', String( cell.value ) );
			}
		} );

		root.querySelectorAll( '[data-calcr-repeater]' ).forEach( function ( rep ) {
			root.__calcrRows[ rep.getAttribute( 'data-calcr-repeater' ) ] = rep.querySelectorAll( '[data-calcr-rep-row]' ).length;

			/* The row markup as it was served, so reset can put back the
			   original number of rows rather than only blanking the ones that
			   happen to still be there. */
			var list = rep.querySelector( '[data-calcr-rep-list]' );

			if ( list ) {
				list.setAttribute( 'data-calcr-rep-initial', list.innerHTML );
			}
		} );

		root.addEventListener( 'input', function ( event ) {
			if ( event.target.matches( '[data-calcr-input], [data-calcr-rep-cell]' ) ) {
				recalculate( root );
			}
		} );

		root.addEventListener( 'change', function ( event ) {
			if ( event.target.matches( '[data-calcr-input], [data-calcr-rep-cell]' ) ) {
				recalculate( root );
			}
		} );

		if ( restoreFromHash( root ) ) {
			syncSegmented( root );
		}

		bindSegmented( root );
		bindRepeaters( root );
		bindCopy( root );
		bindSticky( root );
		bindUrlState( root );

		if ( window.CalculatorrShare && window.CalculatorrShare.bind ) {
			window.CalculatorrShare.bind( root );
		}

		var reset = root.querySelector( '[data-calcr-reset]' );

		if ( reset ) {
			reset.addEventListener( 'click', function () {
				root.querySelectorAll( '[data-calcr-input]' ).forEach( function ( el ) {
					if ( defaults.hasOwnProperty( el.getAttribute( 'data-calcr-input' ) ) ) {
						el.value = defaults[ el.getAttribute( 'data-calcr-input' ) ];
					}
				} );

				/* Repeaters were missed entirely: reset cleared the ordinary
				   fields and left every added row sitting there with its
				   figures in it, which on a GPA or a timesheet is most of what
				   the visitor wanted cleared. */
				root.querySelectorAll( '[data-calcr-rep-list]' ).forEach( function ( list ) {
					var original = list.getAttribute( 'data-calcr-rep-initial' );

					if ( null !== original ) {
						list.innerHTML = original;
					}
				} );

				syncSegmented( root );
				recalculate( root );
			} );
		}

		recalculate( root );
	}

	function boot() {
		document.querySelectorAll( '.calcr[data-calcr-slug]' ).forEach( init );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}

	/* Elementor swaps widget markup in without reloading the page, so the
	   editor preview needs the same boot run again after each render. */
	if ( window.jQuery ) {
		window.jQuery( window ).on( 'elementor/frontend/init', function () {
			if ( window.elementorFrontend && window.elementorFrontend.hooks ) {
				window.elementorFrontend.hooks.addAction( 'frontend/element_ready/calculatorr.default', function ( $scope ) {
					$scope.find( '.calcr[data-calcr-slug]' ).each( function () {
						init( this );
					} );
				} );
			}
		} );
	}
}() );
