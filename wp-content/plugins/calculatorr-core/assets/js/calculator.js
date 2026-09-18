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

		if ( note ) {
			note.textContent = result.note || '';
			note.hidden = ! result.note;
		}
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

	function recalculate( root ) {
		applyVisibility( root );

		var slug = root.getAttribute( 'data-calcr-slug' );
		var formula = FORMULAS[ slug ];

		if ( typeof formula !== 'function' ) {
			return;
		}

		try {
			paint( root, formula( gather( root ) ) );
		} catch ( error ) {
			/* A broken formula should degrade to the server-rendered default
			   rather than take the page down with it, so the error is logged
			   and the existing result is left on screen. */
			if ( window.console && window.console.error ) {
				window.console.error( 'calculatorr: ' + slug, error );
			}
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

		bindSegmented( root );
		bindRepeaters( root );

		var reset = root.querySelector( '[data-calcr-reset]' );

		if ( reset ) {
			reset.addEventListener( 'click', function () {
				root.querySelectorAll( '[data-calcr-input]' ).forEach( function ( el ) {
					if ( defaults.hasOwnProperty( el.getAttribute( 'data-calcr-input' ) ) ) {
						el.value = defaults[ el.getAttribute( 'data-calcr-input' ) ];
					}
				} );

				root.querySelectorAll( '[data-calcr-seg]' ).forEach( function ( group ) {
					var name = group.getAttribute( 'data-calcr-seg' );
					var current = root.querySelector( '[data-calcr-input="' + name + '"]' );

					group.querySelectorAll( '.calcr-seg__btn' ).forEach( function ( button ) {
						var active = current && button.getAttribute( 'data-value' ) === current.value;
						button.classList.toggle( 'is-active', active );
						button.setAttribute( 'aria-pressed', active ? 'true' : 'false' );
					} );
				} );

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
