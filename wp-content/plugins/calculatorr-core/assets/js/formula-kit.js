/*
 * The helpers every formula is written against.
 *
 * These used to live inside formulas.js, which was fine while formulas.js was
 * the only thing that ran a formula. It stopped being fine once a calculator
 * could be defined in JSON and run inside the sandbox worker, because the
 * worker would then have needed its own copy of decimals() and share() and the
 * two copies would have drifted the first time one of them was fixed. One file
 * that both sides load is the only arrangement where that cannot happen.
 *
 * It attaches to globalThis, which the page, the worker and node all agree on,
 * so the same file serves all three without a module wrapper.
 */
( function ( scope ) {
	'use strict';

	var kit = {};

	/* Colours are written as CSS variables rather than hex codes so the bars
	   follow the theme into dark mode. */
	kit.ACCENT = 'var(--calcr-accent)';
	kit.WARN = 'var(--calcr-warn)';
	kit.NEUTRAL = 'var(--calcr-neutral-mark)';

	kit.num = function ( value ) {
		var parsed = parseFloat( String( value === undefined ? '' : value ).replace( /[^0-9.\-]/g, '' ) );
		return isFinite( parsed ) ? parsed : 0;
	};

	kit.money = function ( value, currency ) {
		if ( ! isFinite( value ) ) {
			return ( currency || '$' ) + '0';
		}
		var sign = value < 0 ? '-' : '';
		return sign + ( currency || '$' ) + Math.abs( Math.round( value ) ).toLocaleString( 'en-US' );
	};

	kit.money2 = function ( value, currency ) {
		if ( ! isFinite( value ) ) {
			return ( currency || '$' ) + '0.00';
		}
		var sign = value < 0 ? '-' : '';
		return sign + ( currency || '$' ) + Math.abs( value ).toLocaleString( 'en-US', {
			minimumFractionDigits: 2,
			maximumFractionDigits: 2
		} );
	};

	kit.decimals = function ( value, places ) {
		if ( ! isFinite( value ) ) {
			return '0';
		}

		var p = places === undefined ? 1 : places;

		/* toLocaleString defaults to a maximum of three fraction digits, which
		   silently truncated every figure asked for more than that: acres to
		   three places, square roots to three, standard deviations to three.
		   The maximum has to be stated explicitly. */
		return Number( value.toFixed( p ) ).toLocaleString( 'en-US', {
			minimumFractionDigits: 0,
			maximumFractionDigits: p
		} );
	};

	/**
	 * Clamps a year count before it reaches a loop.
	 *
	 * Several projections iterate once per year, and nothing stopped somebody
	 * typing a twelve digit number into the field and locking up their own
	 * browser tab. A century is past the point where any of these projections
	 * mean anything, so clamping there costs nothing real.
	 */
	kit.years = function ( value, max ) {
		var n = kit.num( value );
		if ( ! isFinite( n ) || n < 0 ) { return 0; }
		return Math.min( n, max === undefined ? 100 : max );
	};

	kit.share = function ( part, total ) {
		if ( ! isFinite( total ) || total <= 0 ) {
			return 0;
		}

		var pct = ( part / total ) * 100;

		/* Clamped at both ends. A loss, or a figure entered far outside any
		   sensible range, could otherwise produce a segment of several
		   thousand per cent, which drew a bar straight out of its track. */
		if ( ! isFinite( pct ) ) {
			return 0;
		}

		return Math.min( Math.max( pct, 0 ), 100 );
	};

	/* The two unit switches that appear on every body calculator, kept here
	   rather than repeated because a wrong conversion factor in one of them
	   would be very hard to spot from the answer alone. */
	kit.toKg = function ( v, metric ) {
		return metric ? kit.num( v.weight ) : kit.num( v.pounds ) * 0.45359237;
	};

	kit.toCm = function ( v, metric ) {
		return metric ? kit.num( v.height ) : ( kit.num( v.feet ) * 12 + kit.num( v.inches ) ) * 2.54;
	};

	kit.addDays = function ( date, days ) {
		var d = new Date( date.getTime() );
		d.setDate( d.getDate() + days );
		return d;
	};

	kit.fmtDate = function ( d ) {
		if ( ! d || isNaN( d.getTime() ) ) { return '—'; }
		return d.toLocaleDateString( 'en-US', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' } );
	};

	kit.parseDate = function ( value ) {
		if ( ! value ) { return null; }
		var d = new Date( value + 'T00:00:00' );
		return isNaN( d.getTime() ) ? null : d;
	};

	kit.parseClock = function ( value ) {
		var m = String( value || '' ).trim().match( /^(\d{1,2}):?(\d{2})?\s*(am|pm)?$/i );
		if ( ! m ) { return null; }
		var h = parseInt( m[1], 10 );
		var mi = m[2] ? parseInt( m[2], 10 ) : 0;
		var mer = m[3] ? m[3].toLowerCase() : null;
		if ( mer === 'pm' && h < 12 ) { h += 12; }
		if ( mer === 'am' && h === 12 ) { h = 0; }
		if ( h > 23 || mi > 59 ) { return null; }
		return h * 60 + mi;
	};

	kit.clockText = function ( minutes ) {
		var m = ( ( minutes % 1440 ) + 1440 ) % 1440;
		var h = Math.floor( m / 60 );
		var mi = Math.round( m % 60 );
		var mer = h >= 12 ? 'PM' : 'AM';
		var h12 = h % 12 === 0 ? 12 : h % 12;
		return h12 + ':' + ( mi < 10 ? '0' : '' ) + mi + ' ' + mer;
	};

	kit.hhmm = function ( minutes ) {
		var sign = minutes < 0 ? '-' : '';
		var m = Math.abs( Math.round( minutes ) );
		return sign + Math.floor( m / 60 ) + 'h ' + ( m % 60 ) + 'm';
	};

	/* The standard amortising payment, which half the loan calculators need
	   and which is easy to get subtly wrong when the rate is zero. */
	kit.monthlyPayment = function ( principal, annualRate, term ) {
		var r = ( annualRate / 100 ) / 12;
		var n = kit.years( term ) * 12;
		if ( n <= 0 ) { return 0; }
		if ( r === 0 ) { return principal / n; }
		var g = Math.pow( 1 + r, n );
		var pay = principal * ( r * g ) / ( g - 1 );
		return isFinite( pay ) ? pay : 0;
	};

	/* Reading a list of numbers out of one field, which every average, median
	   and spread calculator needs and which is easy to get wrong at the edges:
	   a trailing comma, a double space, a stray word. */
	kit.listOf = function ( raw ) {
		return String( raw || '' )
			.split( /[\s,;]+/ )
			.filter( function ( t ) { return t !== '' && isFinite( parseFloat( t ) ); } )
			.map( parseFloat );
	};

	kit.gcd = function ( a, b ) {
		a = Math.abs( a ); b = Math.abs( b );
		while ( b ) { var t = b; b = a % b; a = t; }
		return a;
	};

	/* Reduced to lowest terms with the sign always carried by the numerator,
	   because 1/-2 and -1/2 are the same number and only one of them reads
	   like an answer. */
	kit.simplify = function ( n, d ) {
		if ( d === 0 ) { return null; }
		var g = kit.gcd( n, d ) || 1;
		n = n / g; d = d / g;
		if ( d < 0 ) { n = -n; d = -d; }
		return { n: n, d: d };
	};

	kit.fractionText = function ( f ) {
		if ( ! f ) { return '—'; }
		if ( f.d === 1 ) { return String( f.n ); }
		var whole = Math.trunc( f.n / f.d );
		var rem = Math.abs( f.n % f.d );
		if ( whole !== 0 && rem !== 0 ) {
			return whole + ' ' + rem + '/' + f.d;
		}
		return f.n + '/' + f.d;
	};

	/* The order a formula body's locals are declared in, so the sandbox and
	   the shipped formulas offer exactly the same vocabulary. Listing it here
	   rather than in the worker means adding a helper is one edit. */
	kit.NAMES = [
		'ACCENT', 'WARN', 'NEUTRAL',
		'num', 'money', 'money2', 'decimals', 'years', 'share',
		'toKg', 'toCm',
		'addDays', 'fmtDate', 'parseDate',
		'parseClock', 'clockText', 'hhmm',
		'monthlyPayment',
		'listOf', 'gcd', 'simplify', 'fractionText'
	];

	scope.CalculatorrKit = kit;
}( typeof globalThis !== 'undefined' ? globalThis : self ) );
