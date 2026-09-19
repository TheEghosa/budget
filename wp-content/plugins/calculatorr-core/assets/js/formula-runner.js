/*
 * Turning a JSON calculator's formula into an answer.
 *
 * This is deliberately not part of sandbox.js. The worker runs a formula for a
 * visitor, and the authoring tool runs the same formula on a workstation to
 * compute the worked answer the server will render before any script has
 * loaded. Those two answers appear one after the other on the same page, a
 * fraction of a second apart, so if they were produced by two copies of this
 * code the page would eventually flicker from one number to a different one
 * and nobody would be able to say which was right.
 *
 * So there is one copy, both sides load it, and neither owns it.
 */
( function ( scope ) {
	'use strict';

	var runner = {};

	/**
	 * Compiles a formula body into a callable.
	 *
	 * The helpers are declared as locals in front of the body, so a formula
	 * written in JSON reads exactly like one of the hundred and five that ship
	 * as code: num( v.height ) rather than K.num( v.height ). That is what lets
	 * a formula move between the two forms without being rewritten, which is
	 * the whole promise being made here.
	 *
	 * Strict mode applies inside the body too, so an undeclared variable is an
	 * error at the point it is written rather than a global left lying around
	 * for whatever runs next.
	 */
	runner.compile = function ( source, kit ) {
		var head = kit.NAMES.map( function ( name ) {
			return 'var ' + name + ' = K.' + name + ';';
		} ).join( '\n' );

		return new Function( 'v', 'K', '"use strict";\n' + head + '\n' + String( source ) + '\n' );
	};

	/* Anything a formula offers as a colour has to look like one. The shipped
	   formulas only ever use the three theme variables, so this is wide enough
	   for every real case and narrow enough that nothing else gets through to
	   an inline style. */
	var COLOUR = /^(#[0-9a-f]{3,8}|var\( *--[a-z0-9-]+ *\)|[a-z]+|rgba?\([0-9.,%\s]+\)|hsla?\([0-9.,%\sdegra]+\))$/i;

	function colour( value ) {
		var text = String( value === undefined || value === null ? '' : value ).trim();
		return ( text.length <= 64 && COLOUR.test( text ) ) ? text : '';
	}

	function text( value ) {
		if ( value === undefined || value === null ) {
			return '';
		}

		/* Capped, because a formula that builds a string inside a loop could
		   otherwise hand the page a megabyte to put in one table cell. */
		return String( value ).slice( 0, 400 );
	}

	/**
	 * Reduces whatever the formula returned to the shape the panel reads.
	 *
	 * Two things follow from doing it here. The result is plain strings and
	 * numbers, so posting it out of the worker can never fail on a Date or a
	 * function the formula happened to include, and the page receives nothing
	 * it did not ask for even if the formula tried to send it something else.
	 */
	runner.clean = function ( raw ) {
		if ( ! raw || typeof raw !== 'object' ) {
			return null;
		}

		var out = {
			label: text( raw.label ),
			value: text( raw.value ),
			sub: text( raw.sub ),
			note: text( raw.note ),
			rows: [],
			bar: []
		};

		if ( Array.isArray( raw.rows ) ) {
			out.rows = raw.rows.slice( 0, 24 ).map( function ( row ) {
				row = row || {};
				return {
					label: text( row.label ),
					value: text( row.value ),
					color: colour( row.color ),
					emphasis: !! row.emphasis,
					divide: !! row.divide
				};
			} );
		}

		if ( Array.isArray( raw.bar ) ) {
			out.bar = raw.bar.slice( 0, 8 ).map( function ( segment ) {
				segment = segment || {};
				var pct = Number( segment.pct );
				return {
					pct: isFinite( pct ) ? Math.min( Math.max( pct, 0 ), 100 ) : 0,
					color: colour( segment.color )
				};
			} );
		}

		return out;
	};

	scope.CalculatorrRunner = runner;
}( typeof globalThis !== 'undefined' ? globalThis : self ) );
