/*
 * One formula per calculator, keyed by the same slug as its config file.
 *
 * Each function receives the field values as strings and returns the shape the
 * runtime paints: a label, a headline value, optional breakdown rows, an
 * optional proportion bar and an optional note. Colours are written as CSS
 * variables rather than hex codes so the bars follow the theme into dark mode.
 */
( function () {
	'use strict';

	var ACCENT = 'var(--calcr-accent)';
	var WARN = 'var(--calcr-warn)';
	var NEUTRAL = 'var(--calcr-neutral-mark)';

	function num( value ) {
		var parsed = parseFloat( String( value === undefined ? '' : value ).replace( /[^0-9.\-]/g, '' ) );
		return isFinite( parsed ) ? parsed : 0;
	}

	function money( value, currency ) {
		if ( ! isFinite( value ) ) {
			return ( currency || '$' ) + '0';
		}
		var sign = value < 0 ? '-' : '';
		return sign + ( currency || '$' ) + Math.abs( Math.round( value ) ).toLocaleString( 'en-US' );
	}

	function money2( value, currency ) {
		if ( ! isFinite( value ) ) {
			return ( currency || '$' ) + '0.00';
		}
		var sign = value < 0 ? '-' : '';
		return sign + ( currency || '$' ) + Math.abs( value ).toLocaleString( 'en-US', {
			minimumFractionDigits: 2,
			maximumFractionDigits: 2
		} );
	}

	function decimals( value, places ) {
		if ( ! isFinite( value ) ) {
			return '0';
		}
		return Number( value.toFixed( places === undefined ? 1 : places ) ).toLocaleString( 'en-US' );
	}

	function share( part, total ) {
		if ( ! isFinite( total ) || total <= 0 ) {
			return 0;
		}
		return Math.max( ( part / total ) * 100, 0 );
	}

	var formulas = {};

	/* ---------- Math ---------- */

	formulas[ 'percentage-calculator' ] = function ( v ) {
		var a = num( v.a );
		var b = num( v.b );
		var mode = v.mode || 'of';

		if ( 'is' === mode ) {
			var pct = b === 0 ? 0 : ( a / b ) * 100;
			return {
				label: a + ' as a percentage of ' + b,
				value: decimals( pct, 2 ) + '%',
				rows: [
					{ label: 'Part', value: decimals( a, 2 ) },
					{ label: 'Whole', value: decimals( b, 2 ) }
				],
				note: b === 0 ? 'A whole of zero has no percentage, since dividing by zero is undefined.' : ''
			};
		}

		if ( 'change' === mode ) {
			var delta = b - a;
			var change = a === 0 ? 0 : ( delta / Math.abs( a ) ) * 100;
			return {
				label: change >= 0 ? 'Percentage increase' : 'Percentage decrease',
				value: decimals( Math.abs( change ), 2 ) + '%',
				rows: [
					{ label: 'Absolute change', value: decimals( delta, 2 ) },
					{ label: 'From', value: decimals( a, 2 ) },
					{ label: 'To', value: decimals( b, 2 ) }
				],
				note: a === 0 ? 'A change measured from zero has no percentage, because every increase would be infinite.' : ''
			};
		}

		var result = ( a / 100 ) * b;
		return {
			label: a + '% of ' + b,
			value: decimals( result, 2 ),
			rows: [
				{ label: 'Percentage', value: decimals( a, 2 ) + '%' },
				{ label: 'Of', value: decimals( b, 2 ) },
				{ label: 'Remainder', value: decimals( b - result, 2 ), divide: true }
			]
		};
	};

	/* ---------- Health ---------- */

	formulas[ 'bmi-calculator' ] = function ( v ) {
		var metric = 'metric' === ( v.units || 'metric' );
		var bmi;

		if ( metric ) {
			var metres = num( v.height ) / 100;
			bmi = metres > 0 ? num( v.weight ) / ( metres * metres ) : 0;
		} else {
			var inches = ( num( v.feet ) * 12 ) + num( v.inches );
			bmi = inches > 0 ? ( 703 * num( v.pounds ) ) / ( inches * inches ) : 0;
		}

		var band = 'Underweight';
		if ( bmi >= 30 ) {
			band = 'Obese';
		} else if ( bmi >= 25 ) {
			band = 'Overweight';
		} else if ( bmi >= 18.5 ) {
			band = 'Healthy weight';
		}

		return {
			label: 'Body mass index',
			value: bmi > 0 ? decimals( bmi, 1 ) : '—',
			rows: [
				{ label: 'Category', value: bmi > 0 ? band : '—', emphasis: bmi >= 25 || ( bmi > 0 && bmi < 18.5 ) },
				{ label: 'Healthy range', value: '18.5 to 24.9' }
			],
			note: 'BMI cannot tell muscle from fat, so it overstates risk for athletes and understates it for anyone sedentary with a light frame.'
		};
	};

	formulas[ 'tdee-calculator' ] = function ( v ) {
		var metric = 'metric' === ( v.units || 'metric' );
		var kg = metric ? num( v.weight ) : num( v.pounds ) * 0.45359237;
		var cm = metric ? num( v.height ) : ( ( num( v.feet ) * 12 ) + num( v.inches ) ) * 2.54;
		var age = num( v.age );

		/* Mifflin-St Jeor, which predicts resting energy more accurately than
		   Harris-Benedict for most modern populations. */
		var bmr = ( 10 * kg ) + ( 6.25 * cm ) - ( 5 * age ) + ( 'female' === v.sex ? -161 : 5 );
		var factor = num( v.activity ) || 1.2;
		var tdee = bmr * factor;

		return {
			label: 'Daily energy needs',
			value: tdee > 0 ? decimals( tdee, 0 ) + ' kcal' : '—',
			rows: [
				{ label: 'Resting rate (BMR)', value: decimals( bmr, 0 ) + ' kcal', color: NEUTRAL },
				{ label: 'Activity on top', value: decimals( tdee - bmr, 0 ) + ' kcal', color: ACCENT },
				{ label: 'Lose about 0.5kg a week', value: decimals( tdee - 500, 0 ) + ' kcal', divide: true },
				{ label: 'Gain about 0.5kg a week', value: decimals( tdee + 500, 0 ) + ' kcal' }
			],
			bar: [
				{ pct: share( bmr, tdee ), color: NEUTRAL },
				{ pct: share( tdee - bmr, tdee ), color: ACCENT }
			],
			note: 'Predicted rather than measured, so track your weight for a fortnight and adjust the figure to what actually happens.'
		};
	};

	/* ---------- Loans ---------- */

	formulas[ 'mortgage-payment-calculator' ] = function ( v ) {
		var price = num( v.price );
		var principal = Math.max( price - ( price * ( num( v.downPct ) / 100 ) ), 0 );
		var monthlyRate = ( num( v.rate ) / 100 ) / 12;
		var payments = num( v.term ) * 12;
		var pi;

		if ( monthlyRate > 0 && payments > 0 ) {
			var growth = Math.pow( 1 + monthlyRate, payments );
			pi = principal * ( monthlyRate * growth ) / ( growth - 1 );
		} else {
			pi = payments > 0 ? principal / payments : 0;
		}

		if ( ! isFinite( pi ) ) {
			pi = 0;
		}

		var taxMonthly = num( v.tax ) / 12;
		var insMonthly = num( v.insurance ) / 12;
		var total = pi + taxMonthly + insMonthly;
		var interest = Math.max( ( pi * payments ) - principal, 0 );

		return {
			label: 'Monthly payment',
			value: money( total ),
			bar: [
				{ pct: share( pi, total ), color: ACCENT },
				{ pct: share( taxMonthly, total ), color: WARN },
				{ pct: share( insMonthly, total ), color: NEUTRAL }
			],
			rows: [
				{ label: 'Principal & interest', value: money( pi ), color: ACCENT },
				{ label: 'Property tax', value: money( taxMonthly ), color: WARN },
				{ label: 'Home insurance', value: money( insMonthly ), color: NEUTRAL },
				{ label: 'Loan amount', value: money( principal ), divide: true },
				{ label: 'Total interest paid', value: money( interest ), emphasis: true },
				{ label: 'Total cost of loan', value: money( ( pi * payments ) + ( price - principal ) ) }
			],
			note: 'Excludes mortgage insurance, HOA dues and closing costs, which vary too much by lender and property to estimate honestly.'
		};
	};

	/* ---------- Finance ---------- */

	formulas[ 'compound-interest-calculator' ] = function ( v ) {
		var principal = num( v.principal );
		var monthly = num( v.monthly );
		var annualRate = num( v.rate ) / 100;
		var years = num( v.years );
		var perYear = num( v.frequency ) || 12;

		/* Contributions land monthly, so the compounding rate is converted to an
		   equivalent monthly rate rather than assumed to be monthly already. */
		var monthlyRate = annualRate > 0
			? Math.pow( 1 + ( annualRate / perYear ), perYear / 12 ) - 1
			: 0;
		var months = years * 12;

		var future;
		if ( monthlyRate > 0 ) {
			var growth = Math.pow( 1 + monthlyRate, months );
			future = ( principal * growth ) + ( monthly * ( ( growth - 1 ) / monthlyRate ) );
		} else {
			future = principal + ( monthly * months );
		}

		var contributed = principal + ( monthly * months );
		var earned = future - contributed;

		return {
			label: 'Balance after ' + decimals( years, 0 ) + ' years',
			value: money( future ),
			bar: [
				{ pct: share( contributed, future ), color: NEUTRAL },
				{ pct: share( earned, future ), color: ACCENT }
			],
			rows: [
				{ label: 'You put in', value: money( contributed ), color: NEUTRAL },
				{ label: 'Interest earned', value: money( earned ), color: ACCENT },
				{ label: 'Starting amount', value: money( principal ), divide: true },
				{ label: 'Monthly contribution', value: money( monthly ) }
			],
			note: 'Assumes the rate holds for the whole period and ignores tax, inflation and fees, all of which reduce what the balance is actually worth.'
		};
	};

	/* ---------- Business ---------- */

	formulas[ 'tip-calculator' ] = function ( v ) {
		var bill = num( v.bill );
		var pct = num( v.tip );
		var people = Math.max( Math.round( num( v.people ) ), 1 );
		var tip = bill * ( pct / 100 );
		var total = bill + tip;

		return {
			label: 'Total to pay',
			value: money2( total ),
			bar: [
				{ pct: share( bill, total ), color: NEUTRAL },
				{ pct: share( tip, total ), color: ACCENT }
			],
			rows: [
				{ label: 'Bill', value: money2( bill ), color: NEUTRAL },
				{ label: 'Tip at ' + decimals( pct, 0 ) + '%', value: money2( tip ), color: ACCENT },
				{ label: 'Each person pays', value: money2( total / people ), divide: true },
				{ label: 'Tip per person', value: money2( tip / people ) }
			]
		};
	};

	formulas[ 'discount-calculator' ] = function ( v ) {
		var price = num( v.price );
		var first = num( v.discount );
		var second = num( v.extra );

		/* Stacked discounts apply one after the other rather than adding up,
		   which is why 20% and then 10% is 28% off rather than 30%. */
		var afterFirst = price * ( 1 - ( first / 100 ) );
		var final = afterFirst * ( 1 - ( second / 100 ) );
		var saved = price - final;
		var effective = price > 0 ? ( saved / price ) * 100 : 0;

		return {
			label: 'You pay',
			value: money2( final ),
			bar: [
				{ pct: share( final, price ), color: ACCENT },
				{ pct: share( saved, price ), color: WARN }
			],
			rows: [
				{ label: 'Original price', value: money2( price ) },
				{ label: 'You save', value: money2( saved ), color: WARN, emphasis: true },
				{ label: 'Effective discount', value: decimals( effective, 1 ) + '%', divide: true }
			],
			note: second > 0 ? 'Stacked discounts multiply rather than add, so the combined saving is always less than the two percentages summed.' : ''
		};
	};

	formulas[ 'sales-tax-calculator' ] = function ( v ) {
		var amount = num( v.amount );
		var rate = num( v.rate ) / 100;
		var gross;
		var net;

		if ( 'remove' === ( v.mode || 'add' ) ) {
			gross = amount;
			net = rate > -1 ? amount / ( 1 + rate ) : amount;
		} else {
			net = amount;
			gross = amount * ( 1 + rate );
		}

		var tax = gross - net;

		return {
			label: 'remove' === v.mode ? 'Price before tax' : 'Total with tax',
			value: money2( 'remove' === v.mode ? net : gross ),
			bar: [
				{ pct: share( net, gross ), color: NEUTRAL },
				{ pct: share( tax, gross ), color: ACCENT }
			],
			rows: [
				{ label: 'Before tax', value: money2( net ), color: NEUTRAL },
				{ label: 'Sales tax', value: money2( tax ), color: ACCENT },
				{ label: 'After tax', value: money2( gross ), divide: true }
			]
		};
	};

	/* ---------- Time ---------- */

	formulas[ 'age-calculator' ] = function ( v ) {
		if ( ! v.dob ) {
			return { label: 'Age', value: '—', rows: [], note: 'Enter a date of birth to see an age.' };
		}

		var born = new Date( v.dob + 'T00:00:00' );
		var upto = v.upto ? new Date( v.upto + 'T00:00:00' ) : new Date();

		if ( isNaN( born.getTime() ) || born > upto ) {
			return { label: 'Age', value: '—', rows: [], note: 'That date is in the future, so there is no age to calculate yet.' };
		}

		var years = upto.getFullYear() - born.getFullYear();
		var months = upto.getMonth() - born.getMonth();
		var days = upto.getDate() - born.getDate();

		if ( days < 0 ) {
			months -= 1;
			/* Day zero of the current month is the last day of the previous
			   one, which borrows the right number of days for any month. */
			days += new Date( upto.getFullYear(), upto.getMonth(), 0 ).getDate();
		}

		if ( months < 0 ) {
			years -= 1;
			months += 12;
		}

		var totalDays = Math.floor( ( upto - born ) / 86400000 );

		return {
			label: 'Age',
			value: years + ' years',
			rows: [
				{ label: 'Exactly', value: years + 'y ' + months + 'm ' + days + 'd' },
				{ label: 'Total days', value: totalDays.toLocaleString( 'en-US' ), divide: true },
				{ label: 'Total weeks', value: Math.floor( totalDays / 7 ).toLocaleString( 'en-US' ) },
				{ label: 'Total months', value: ( ( years * 12 ) + months ).toLocaleString( 'en-US' ) }
			]
		};
	};

	/* ---------- Education ---------- */

	formulas[ 'gpa-calculator' ] = function ( v ) {
		var points = {
			'4.0': 4, '3.7': 3.7, '3.3': 3.3, '3.0': 3, '2.7': 2.7,
			'2.3': 2.3, '2.0': 2, '1.7': 1.7, '1.3': 1.3, '1.0': 1, '0.0': 0
		};

		var courses = v.courses || [];
		var totalCredits = 0;
		var totalPoints = 0;
		var counted = 0;

		courses.forEach( function ( course ) {
			var credits = num( course.credits );

			if ( credits <= 0 || ! points.hasOwnProperty( course.grade ) ) {
				return;
			}

			totalCredits += credits;
			totalPoints += points[ course.grade ] * credits;
			counted += 1;
		} );

		var gpa = totalCredits > 0 ? totalPoints / totalCredits : 0;

		return {
			label: 'Grade point average',
			value: totalCredits > 0 ? decimals( gpa, 2 ) : '—',
			rows: [
				{ label: 'Courses counted', value: String( counted ) },
				{ label: 'Total credits', value: decimals( totalCredits, 1 ) },
				{ label: 'Quality points', value: decimals( totalPoints, 1 ), divide: true }
			],
			note: counted === 0 ? 'Add at least one course with a credit value above zero.' : 'Uses the unweighted four point scale, so honours and AP courses are not given extra weight here.'
		};
	};

	/* ---------- Construction & DIY ----------
	 *
	 * Constants used across this group, all standard US trade figures:
	 *   1 cubic yard        = 27 cubic feet
	 *   80 / 60 / 40 lb bag = 0.60 / 0.45 / 0.30 cubic feet of mixed concrete
	 *   crushed stone       = about 1.4 US tons per cubic yard
	 *   screened topsoil    = about 1.1 US tons per cubic yard
	 *   1 cubic foot        = 7.48052 US gallons
	 * The material weights are averages. Wet or unusually dense material runs
	 * heavier, which is why the pages say to confirm with the supplier before
	 * ordering by weight rather than by volume.
	 */

	var CUFT_PER_YARD = 27;
	var GAL_PER_CUFT = 7.48052;

	function withWaste( value, wastePct ) {
		return value * ( 1 + ( num( wastePct ) / 100 ) );
	}

	function metricVolume( cuft ) {
		return decimals( cuft * 0.0283168, 2 ) + ' m3';
	}

	formulas[ 'concrete-calculator' ] = function ( v ) {
		var shape = v.shape || 'slab';
		var qty = Math.max( num( v.quantity ) || 1, 1 );
		var cuft;

		if ( 'column' === shape ) {
			var radiusFt = ( num( v.diameter ) / 12 ) / 2;
			cuft = Math.PI * radiusFt * radiusFt * num( v.height );
		} else if ( 'footing' === shape ) {
			cuft = num( v.length ) * ( num( v.footWidth ) / 12 ) * ( num( v.footDepth ) / 12 );
		} else {
			cuft = num( v.length ) * num( v.width ) * ( num( v.thickness ) / 12 );
		}

		cuft = withWaste( cuft * qty, v.waste );

		var yards = cuft / CUFT_PER_YARD;

		return {
			label: 'Concrete to order',
			value: decimals( yards, 2 ) + ' cubic yards',
			rows: [
				{ label: 'Volume', value: decimals( cuft, 1 ) + ' cu ft' },
				{ label: 'Metric', value: metricVolume( cuft ) },
				{ label: '80 lb bags', value: Math.ceil( cuft / 0.6 ).toLocaleString( 'en-US' ), divide: true },
				{ label: '60 lb bags', value: Math.ceil( cuft / 0.45 ).toLocaleString( 'en-US' ) },
				{ label: '40 lb bags', value: Math.ceil( cuft / 0.3 ).toLocaleString( 'en-US' ) }
			],
			note: yards >= 1
				? 'Above roughly one cubic yard, ready-mix delivered by truck is usually cheaper and far less work than bags.'
				: 'Under a cubic yard, bags are normally the practical choice. Most suppliers will not deliver ready-mix below a one yard minimum.'
		};
	};

	formulas[ 'square-footage-calculator' ] = function ( v ) {
		var shape = v.shape || 'rectangle';
		var sqft;

		if ( 'circle' === shape ) {
			var r = num( v.diameter ) / 2;
			sqft = Math.PI * r * r;
		} else if ( 'triangle' === shape ) {
			sqft = 0.5 * num( v.base ) * num( v.heightFt );
		} else {
			sqft = num( v.length ) * num( v.width );
		}

		var rooms = Math.max( num( v.rooms ) || 1, 1 );
		sqft = sqft * rooms;

		return {
			label: 'Total area',
			value: decimals( sqft, 1 ) + ' sq ft',
			rows: [
				{ label: 'Square metres', value: decimals( sqft * 0.092903, 2 ) + ' m2' },
				{ label: 'Square yards', value: decimals( sqft / 9, 2 ) + ' sq yd' },
				{ label: 'Acres', value: decimals( sqft / 43560, 4 ) + ' ac', divide: true }
			],
			note: 'Measure at the widest points and treat alcoves as separate rectangles, because rounding a room to one rectangle is the usual reason an order comes up short.'
		};
	};

	function aggregate( v, tonsPerYard, material, bagCuft, bagLabel ) {
		var cuft = withWaste(
			num( v.length ) * num( v.width ) * ( num( v.depth ) / 12 ),
			v.waste
		);
		var yards = cuft / CUFT_PER_YARD;
		var rows = [
			{ label: 'Volume', value: decimals( cuft, 1 ) + ' cu ft' },
			{ label: 'Metric', value: metricVolume( cuft ) },
			{ label: 'Approx. weight', value: decimals( yards * tonsPerYard, 2 ) + ' US tons', divide: true }
		];

		if ( bagCuft ) {
			rows.push( { label: bagLabel, value: Math.ceil( cuft / bagCuft ).toLocaleString( 'en-US' ) } );
		}

		return {
			label: material + ' to order',
			value: decimals( yards, 2 ) + ' cubic yards',
			rows: rows,
			note: 'Weight is an average and varies with moisture and grade, so confirm the supplier’s own figure before ordering by the ton rather than by volume.'
		};
	}

	formulas[ 'gravel-calculator' ] = function ( v ) {
		return aggregate( v, 1.4, 'Gravel', 0, '' );
	};

	formulas[ 'topsoil-calculator' ] = function ( v ) {
		return aggregate( v, 1.1, 'Topsoil', 0, '' );
	};

	formulas[ 'mulch-calculator' ] = function ( v ) {
		return aggregate( v, 0.5, 'Mulch', 2, '2 cu ft bags' );
	};

	formulas[ 'cubic-yard-calculator' ] = function ( v ) {
		var depthFt = 'feet' === ( v.depthUnit || 'inches' ) ? num( v.depth ) : num( v.depth ) / 12;
		var cuft = withWaste( num( v.length ) * num( v.width ) * depthFt, v.waste );
		var yards = cuft / CUFT_PER_YARD;

		return {
			label: 'Volume',
			value: decimals( yards, 2 ) + ' cubic yards',
			rows: [
				{ label: 'Cubic feet', value: decimals( cuft, 1 ) },
				{ label: 'Cubic metres', value: decimals( cuft * 0.0283168, 2 ) },
				{ label: 'Cubic inches', value: decimals( cuft * 1728, 0 ), divide: true }
			],
			note: 'One cubic yard is 27 cubic feet, which is the conversion most order mistakes come down to.'
		};
	};

	formulas[ 'pool-volume-calculator' ] = function ( v ) {
		var shape = v.shape || 'rectangle';
		var depth = ( num( v.shallow ) + num( v.deep ) ) / 2;
		var cuft;

		if ( 'round' === shape ) {
			var r = num( v.diameter ) / 2;
			cuft = Math.PI * r * r * depth;
		} else if ( 'oval' === shape ) {
			cuft = Math.PI * ( num( v.length ) / 2 ) * ( num( v.width ) / 2 ) * depth;
		} else {
			cuft = num( v.length ) * num( v.width ) * depth;
		}

		var gallons = cuft * GAL_PER_CUFT;

		return {
			label: 'Pool volume',
			value: decimals( gallons, 0 ) + ' gallons',
			rows: [
				{ label: 'Litres', value: decimals( gallons * 3.78541, 0 ) },
				{ label: 'Cubic feet', value: decimals( cuft, 1 ) },
				{ label: 'Average depth', value: decimals( depth, 2 ) + ' ft', divide: true }
			],
			note: 'Averaging the shallow and deep ends is accurate for a pool with a steady slope. A pool with a sharp drop-off or a spa step holds less than this suggests.'
		};
	};

	formulas[ 'stair-calculator' ] = function ( v ) {
		var totalRise = num( v.totalRise );
		var target = num( v.targetRiser ) || 7;
		var tread = num( v.tread ) || 10;

		var steps = Math.max( Math.round( totalRise / target ), 1 );
		var riser = totalRise / steps;
		/* The top tread is the landing, so there is always one fewer tread
		   than there are risers. */
		var totalRun = ( steps - 1 ) * tread;
		var stringer = Math.sqrt( ( totalRise * totalRise ) + ( totalRun * totalRun ) );
		var rule = ( 2 * riser ) + tread;

		var issues = [];
		if ( riser > 7.75 ) { issues.push( 'riser over 7.75in' ); }
		if ( riser < 4 ) { issues.push( 'riser under 4in' ); }
		if ( tread < 10 ) { issues.push( 'tread under 10in' ); }

		return {
			label: 'Steps needed',
			value: steps + ' risers',
			rows: [
				{ label: 'Riser height', value: decimals( riser, 3 ) + ' in' },
				{ label: 'Tread depth', value: decimals( tread, 2 ) + ' in' },
				{ label: 'Total run', value: decimals( totalRun, 2 ) + ' in' },
				{ label: 'Stringer length', value: decimals( stringer, 2 ) + ' in', divide: true },
				{ label: 'Rule of 25 check', value: decimals( rule, 2 ) + ' in', emphasis: rule < 24 || rule > 25 }
			],
			note: issues.length
				? 'Outside typical residential code: ' + issues.join( ', ' ) + '. Adjust the number of steps or the tread and check your local code before cutting.'
				: 'Within typical residential limits, though local code always wins over any rule of thumb. Two risers plus one tread should land between 24 and 25 inches.'
		};
	};

	formulas[ 'board-foot-calculator' ] = function ( v ) {
		var qty = Math.max( num( v.quantity ) || 1, 1 );
		/* A board foot is 144 cubic inches, so length in feet needs the 12. */
		var perPiece = ( num( v.thickness ) * num( v.width ) * num( v.length ) ) / 12;
		var total = perPiece * qty;
		var price = num( v.price );

		return {
			label: 'Board feet',
			value: decimals( total, 2 ) + ' bd ft',
			rows: [
				{ label: 'Per piece', value: decimals( perPiece, 2 ) + ' bd ft' },
				{ label: 'Pieces', value: String( qty ) },
				{ label: 'Total cost', value: money2( total * price ), divide: true, emphasis: price > 0 }
			],
			note: 'Board feet are measured on nominal rough thickness, so a board sold as one inch is counted as one inch even after it is planed down to three quarters.'
		};
	};

	formulas[ 'deck-calculator' ] = function ( v ) {
		var lengthFt = num( v.length );
		var widthFt = num( v.width );
		var area = lengthFt * widthFt;
		var boardWidth = num( v.boardWidth ) || 5.5;
		var gap = num( v.gap );
		var spacing = num( v.joistSpacing ) || 16;

		var rows = Math.ceil( ( widthFt * 12 ) / ( boardWidth + gap ) );
		var linearFt = withWaste( rows * lengthFt, v.waste );
		var joists = Math.floor( ( lengthFt * 12 ) / spacing ) + 1;

		return {
			label: 'Decking to buy',
			value: decimals( linearFt, 0 ) + ' linear ft',
			rows: [
				{ label: 'Deck area', value: decimals( area, 1 ) + ' sq ft' },
				{ label: 'Board rows', value: String( rows ) },
				{ label: 'Joists at ' + decimals( spacing, 0 ) + 'in centres', value: String( joists ), divide: true },
				{ label: 'If buying 16ft boards', value: Math.ceil( linearFt / 16 ) + ' boards' }
			],
			note: 'Joist count assumes a simple rectangular frame and excludes rim joists, blocking and stairs, which are worth adding before you order.'
		};
	};

	formulas[ 'tile-calculator' ] = function ( v ) {
		var area = num( v.length ) * num( v.width );
		var tileSqft = ( num( v.tileWidth ) * num( v.tileHeight ) ) / 144;
		var perBox = Math.max( num( v.perBox ) || 1, 1 );

		if ( tileSqft <= 0 || area <= 0 ) {
			return { label: 'Tiles needed', value: '—', rows: [], note: 'Enter the room size and the tile size to see how many tiles to buy.' };
		}

		var needed = Math.ceil( withWaste( area, v.waste ) / tileSqft );

		return {
			label: 'Tiles to buy',
			value: needed.toLocaleString( 'en-US' ) + ' tiles',
			rows: [
				{ label: 'Area to cover', value: decimals( area, 1 ) + ' sq ft' },
				{ label: 'Each tile covers', value: decimals( tileSqft, 3 ) + ' sq ft' },
				{ label: 'Boxes to buy', value: Math.ceil( needed / perBox ).toLocaleString( 'en-US' ), divide: true, emphasis: true },
				{ label: 'Spare tiles in the last box', value: String( ( Math.ceil( needed / perBox ) * perBox ) - needed ) }
			],
			note: 'Ten per cent waste covers ordinary cuts. Go to fifteen for a diagonal or herringbone layout, and buy the whole job in one batch because dye lots shift between production runs.'
		};
	};

	formulas[ 'paint-calculator' ] = function ( v ) {
		var perimeter = 2 * ( num( v.length ) + num( v.width ) );
		var wallArea = perimeter * num( v.height );
		/* Standard openings: a door is about 21 sq ft, a window about 15. */
		var openings = ( num( v.doors ) * 21 ) + ( num( v.windows ) * 15 );
		var paintable = Math.max( wallArea - openings, 0 );

		if ( 'yes' === v.ceiling ) {
			paintable += num( v.length ) * num( v.width );
		}

		var coats = Math.max( num( v.coats ) || 1, 1 );
		var coverage = num( v.coverage ) || 350;
		var gallons = ( paintable * coats ) / coverage;

		return {
			label: 'Paint to buy',
			value: Math.ceil( gallons ) + ( 1 === Math.ceil( gallons ) ? ' gallon' : ' gallons' ),
			rows: [
				{ label: 'Exact requirement', value: decimals( gallons, 2 ) + ' gal' },
				{ label: 'Paintable area', value: decimals( paintable, 0 ) + ' sq ft' },
				{ label: 'Wall area before openings', value: decimals( wallArea, 0 ) + ' sq ft' },
				{ label: 'Openings deducted', value: decimals( openings, 0 ) + ' sq ft', divide: true },
				{ label: 'Litres', value: decimals( gallons * 3.78541, 1 ) }
			],
			note: 'Coverage is rounded up to whole cans because paint is not sold by the fraction. A bare, porous or sharply darker wall drinks more, so budget an extra coat on a colour change.'
		};
	};

	formulas[ 'voltage-drop-calculator' ] = function ( v ) {
		/* Circular mils by AWG, from the standard conductor tables. */
		var CM = {
			'14': 4107, '12': 6530, '10': 10380, '8': 16510, '6': 26240,
			'4': 41740, '3': 52620, '2': 66360, '1': 83690, '1/0': 105600,
			'2/0': 133100, '3/0': 167800, '4/0': 211600
		};

		var mils = CM[ v.gauge ] || CM['12'];
		/* K is the resistivity constant in ohm-circular-mils per foot. */
		var K = 'aluminum' === v.material ? 21.2 : 12.9;
		var amps = num( v.amps );
		var feet = num( v.distance );
		var volts = num( v.volts ) || 120;
		var phase = v.phase || 'single';

		var multiplier = 'three' === phase ? 1.732 : 2;
		var drop = ( multiplier * K * amps * feet ) / mils;
		var pct = volts > 0 ? ( drop / volts ) * 100 : 0;

		return {
			label: 'Voltage drop',
			value: decimals( drop, 2 ) + ' V',
			rows: [
				{ label: 'Percentage drop', value: decimals( pct, 2 ) + '%', emphasis: pct > 3 },
				{ label: 'Voltage at the load', value: decimals( volts - drop, 1 ) + ' V' },
				{ label: 'Conductor', value: 'AWG ' + ( v.gauge || '12' ) + ', ' + ( 'aluminum' === v.material ? 'aluminium' : 'copper' ) },
				{ label: '3% branch circuit limit', value: pct <= 3 ? 'Within limit' : 'Exceeded', divide: true, emphasis: pct > 3 }
			],
			note: pct > 3
				? 'Over the three per cent the NEC recommends for a branch circuit. Go up a wire size, shorten the run, or raise the supply voltage.'
				: 'Within the three per cent the NEC recommends for a branch circuit, and within the five per cent recommended for feeder and branch combined.'
		};
	};

	window.CalculatorrFormulas = formulas;
}() );
