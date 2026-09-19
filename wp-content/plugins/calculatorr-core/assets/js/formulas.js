/*
 * One formula per calculator, keyed by the same slug as its config file.
 *
 * Each function receives the field values as strings and returns the shape the
 * runtime paints: a label, a headline value, optional breakdown rows, an
 * optional proportion bar and an optional note. The helpers they are written
 * against come from formula-kit.js, which the sandbox worker loads too.
 */
( function () {
	'use strict';

	/*
	 * Every helper these formulas are written against now lives in
	 * formula-kit.js, because the sandbox worker that runs a JSON-defined
	 * calculator loads the same file. Two copies of decimals() would have
	 * drifted apart the first time one of them was fixed, and the answers on
	 * the page would have quietly stopped matching each other.
	 */
	var KIT = globalThis.CalculatorrKit;

	var ACCENT = KIT.ACCENT, WARN = KIT.WARN, NEUTRAL = KIT.NEUTRAL;
	var num = KIT.num, money = KIT.money, money2 = KIT.money2, decimals = KIT.decimals;
	var years = KIT.years, share = KIT.share;
	var toKg = KIT.toKg, toCm = KIT.toCm;
	var addDays = KIT.addDays, fmtDate = KIT.fmtDate, parseDate = KIT.parseDate;
	var parseClock = KIT.parseClock, clockText = KIT.clockText, hhmm = KIT.hhmm;
	var monthlyPayment = KIT.monthlyPayment;
	var listOf = KIT.listOf, gcd = KIT.gcd, simplify = KIT.simplify, fractionText = KIT.fractionText;

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
		var termYears = years( v.years );
		var perYear = num( v.frequency ) || 12;

		/* Contributions land monthly, so the compounding rate is converted to an
		   equivalent monthly rate rather than assumed to be monthly already. */
		var monthlyRate = annualRate > 0
			? Math.pow( 1 + ( annualRate / perYear ), perYear / 12 ) - 1
			: 0;
		var months = termYears * 12;

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
			label: 'Balance after ' + decimals( termYears, 0 ) + ' years',
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

	/* ---------- Math & Numbers ---------- */

	formulas[ 'percentage-increase-calculator' ] = function ( v ) {
		var start = num( v.value );
		var pct = num( v.percent );
		var result = start * ( 1 + pct / 100 );
		return {
			label: 'Increased value',
			value: decimals( result, 2 ),
			rows: [
				{ label: 'Original value', value: decimals( start, 2 ) },
				{ label: 'Increase of ' + decimals( pct, 2 ) + '%', value: decimals( result - start, 2 ), color: ACCENT },
				{ label: 'As a multiplier', value: '× ' + decimals( 1 + pct / 100, 4 ), divide: true }
			],
			note: 'Adding a percentage and then removing the same percentage does not return you to the start, because the second calculation runs against the larger number.'
		};
	};

	formulas[ 'percentage-decrease-calculator' ] = function ( v ) {
		var start = num( v.value );
		var pct = num( v.percent );
		var result = start * ( 1 - pct / 100 );
		return {
			label: 'Decreased value',
			value: decimals( result, 2 ),
			rows: [
				{ label: 'Original value', value: decimals( start, 2 ) },
				{ label: 'Decrease of ' + decimals( pct, 2 ) + '%', value: decimals( start - result, 2 ), color: WARN, emphasis: true },
				{ label: 'As a multiplier', value: '× ' + decimals( 1 - pct / 100, 4 ), divide: true }
			],
			note: pct >= 100 ? 'A decrease of 100% or more leaves nothing, or a negative value, which is rarely what a real problem means.' : ''
		};
	};

	formulas[ 'percent-change-calculator' ] = function ( v ) {
		var from = num( v.from );
		var to = num( v.to );
		var diff = to - from;
		var pct = from === 0 ? 0 : ( diff / Math.abs( from ) ) * 100;
		return {
			label: pct >= 0 ? 'Percentage increase' : 'Percentage decrease',
			value: decimals( Math.abs( pct ), 2 ) + '%',
			rows: [
				{ label: 'From', value: decimals( from, 2 ) },
				{ label: 'To', value: decimals( to, 2 ) },
				{ label: 'Absolute change', value: decimals( diff, 2 ), divide: true, emphasis: diff < 0 }
			],
			note: from === 0 ? 'Change measured from zero has no percentage, because every increase from nothing is infinite.' : ''
		};
	};

	formulas[ 'fraction-calculator' ] = function ( v ) {
		var n1 = num( v.n1 ), d1 = num( v.d1 ), n2 = num( v.n2 ), d2 = num( v.d2 );
		var op = v.op || 'add';
		var r;

		if ( d1 === 0 || d2 === 0 ) {
			return { label: 'Result', value: '—', rows: [], note: 'A denominator of zero has no value, since nothing can be divided into zero parts.' };
		}

		if ( 'add' === op )      { r = simplify( n1 * d2 + n2 * d1, d1 * d2 ); }
		else if ( 'sub' === op ) { r = simplify( n1 * d2 - n2 * d1, d1 * d2 ); }
		else if ( 'mul' === op ) { r = simplify( n1 * n2, d1 * d2 ); }
		else                     { r = n2 === 0 ? null : simplify( n1 * d2, d1 * n2 ); }

		var symbol = { add: '+', sub: '−', mul: '×', div: '÷' }[ op ] || '+';

		return {
			label: n1 + '/' + d1 + ' ' + symbol + ' ' + n2 + '/' + d2,
			value: fractionText( r ),
			rows: [
				{ label: 'As a decimal', value: r ? decimals( r.n / r.d, 6 ) : '—' },
				{ label: 'As a percentage', value: r ? decimals( ( r.n / r.d ) * 100, 4 ) + '%' : '—' },
				{ label: 'Improper form', value: r ? r.n + '/' + r.d : '—', divide: true }
			],
			note: ! r ? 'Dividing by a fraction with a numerator of zero has no result.' : ''
		};
	};

	formulas[ 'decimal-to-fraction-calculator' ] = function ( v ) {
		var dec = num( v.decimal );
		var sign = dec < 0 ? -1 : 1;
		var x = Math.abs( dec );

		/* Continued fractions converge on the simplest fraction within
		   tolerance, which is what people mean by "as a fraction" rather than
		   the literal power-of-ten expansion. */
		var h1 = 1, h2 = 0, k1 = 0, k2 = 1, b = x;
		var limit = 0;
		do {
			var a = Math.floor( b );
			var t = h1; h1 = a * h1 + h2; h2 = t;
			t = k1; k1 = a * k1 + k2; k2 = t;
			b = 1 / ( b - a );
			limit++;
		} while ( Math.abs( x - h1 / k1 ) > 1e-10 && limit < 40 && isFinite( b ) );

		var f = simplify( sign * h1, k1 );
		var exact = Math.abs( ( f ? f.n / f.d : 0 ) - dec ) < 1e-10;

		return {
			label: dec + ' as a fraction',
			value: fractionText( f ),
			rows: [
				{ label: 'Improper form', value: f ? f.n + '/' + f.d : '—' },
				{ label: 'Back to decimal', value: f ? decimals( f.n / f.d, 10 ) : '—' },
				{ label: 'As a percentage', value: decimals( dec * 100, 4 ) + '%', divide: true }
			],
			note: exact ? '' : 'This is the closest simple fraction rather than an exact one, because the decimal does not terminate.'
		};
	};

	formulas[ 'ratio-calculator' ] = function ( v ) {
		var a = num( v.a ), b = num( v.b ), c = num( v.c );
		/* a : b = c : d, solving for d, which is the form nearly every real
		   ratio question arrives in. */
		var d = a === 0 ? 0 : ( b * c ) / a;
		var s = simplify( a, b );

		return {
			label: 'Missing value',
			value: decimals( d, 4 ),
			rows: [
				{ label: 'Your ratio', value: decimals( a, 2 ) + ' : ' + decimals( b, 2 ) },
				{ label: 'Simplified', value: s ? s.n + ' : ' + s.d : '—' },
				{ label: 'As a decimal', value: b === 0 ? '—' : decimals( a / b, 4 ), divide: true },
				{ label: 'Complete proportion', value: decimals( a, 2 ) + ' : ' + decimals( b, 2 ) + ' = ' + decimals( c, 2 ) + ' : ' + decimals( d, 2 ) }
			],
			note: a === 0 ? 'The first term cannot be zero, because the proportion would have nothing to scale from.' : ''
		};
	};

	formulas[ 'proportion-calculator' ] = function ( v ) {
		var a = num( v.a ), b = num( v.b ), c = num( v.c ), d = num( v.d );
		var solve = v.solve || 'd';
		var answer, working;

		if ( 'a' === solve )      { answer = d === 0 ? 0 : ( b * c ) / d; working = '(b × c) ÷ d'; }
		else if ( 'b' === solve ) { answer = c === 0 ? 0 : ( a * d ) / c; working = '(a × d) ÷ c'; }
		else if ( 'c' === solve ) { answer = b === 0 ? 0 : ( a * d ) / b; working = '(a × d) ÷ b'; }
		else                      { answer = a === 0 ? 0 : ( b * c ) / a; working = '(b × c) ÷ a'; }

		return {
			label: 'Solving for ' + solve,
			value: decimals( answer, 4 ),
			rows: [
				{ label: 'Method', value: working },
				{ label: 'Cross product', value: decimals( a * d, 4 ) + ' and ' + decimals( b * c, 4 ), divide: true }
			],
			note: 'A proportion holds when the two cross products match, which is the quickest way to check any answer here by hand.'
		};
	};

	formulas[ 'average-calculator' ] = function ( v ) {
		var xs = listOf( v.numbers );

		if ( ! xs.length ) {
			return { label: 'Mean', value: '—', rows: [], note: 'Enter some numbers, separated by spaces or commas.' };
		}

		var sum = xs.reduce( function ( t, x ) { return t + x; }, 0 );
		var mean = sum / xs.length;
		var sorted = xs.slice().sort( function ( a, b ) { return a - b; } );
		var mid = Math.floor( sorted.length / 2 );
		var median = sorted.length % 2 ? sorted[ mid ] : ( sorted[ mid - 1 ] + sorted[ mid ] ) / 2;

		var counts = {}, best = 0, modes = [];
		xs.forEach( function ( x ) { counts[ x ] = ( counts[ x ] || 0 ) + 1; best = Math.max( best, counts[ x ] ); } );
		Object.keys( counts ).forEach( function ( k ) { if ( counts[ k ] === best ) { modes.push( k ); } } );

		return {
			label: 'Mean average',
			value: decimals( mean, 4 ),
			rows: [
				{ label: 'Median', value: decimals( median, 4 ) },
				{ label: 'Mode', value: best > 1 ? modes.join( ', ' ) : 'none repeats' },
				{ label: 'Count', value: String( xs.length ), divide: true },
				{ label: 'Sum', value: decimals( sum, 4 ) },
				{ label: 'Range', value: decimals( sorted[ sorted.length - 1 ] - sorted[ 0 ], 4 ) }
			],
			note: 'The mean is pulled by outliers and the median is not, so when the two disagree sharply the median usually describes the data better.'
		};
	};

	formulas[ 'long-division-calculator' ] = function ( v ) {
		var a = num( v.dividend ), b = num( v.divisor );

		if ( b === 0 ) {
			return { label: 'Quotient', value: '—', rows: [], note: 'Nothing can be divided by zero.' };
		}

		var q = Math.trunc( a / b );
		var r = a - q * b;

		return {
			label: decimals( a, 0 ) + ' ÷ ' + decimals( b, 0 ),
			value: decimals( q, 0 ) + ( r ? ' remainder ' + decimals( Math.abs( r ), 0 ) : '' ),
			rows: [
				{ label: 'Quotient', value: decimals( q, 0 ) },
				{ label: 'Remainder', value: decimals( Math.abs( r ), 0 ) },
				{ label: 'As a decimal', value: decimals( a / b, 8 ), divide: true },
				{ label: 'As a mixed number', value: r ? q + ' ' + Math.abs( r ) + '/' + Math.abs( b ) : String( q ) }
			]
		};
	};

	formulas[ 'square-root-calculator' ] = function ( v ) {
		var n = num( v.number );

		if ( n < 0 ) {
			return {
				label: 'Square root of ' + n,
				value: decimals( Math.sqrt( -n ), 6 ) + 'i',
				rows: [ { label: 'Form', value: 'imaginary' } ],
				note: 'Negative numbers have no real square root, so the answer is given in imaginary form.'
			};
		}

		/* Pull out the largest square factor to give the simplified radical,
		   which is the form school work actually asks for. */
		var outside = 1, inside = Math.round( n );
		var exact = Math.abs( inside - n ) < 1e-9;

		if ( exact && inside > 0 ) {
			for ( var i = Math.floor( Math.sqrt( inside ) ); i >= 2; i-- ) {
				if ( inside % ( i * i ) === 0 ) { outside = i; inside = inside / ( i * i ); break; }
			}
		}

		var radical = ! exact ? '—'
			: inside === 1 ? String( outside )
			: ( outside === 1 ? '' : outside ) + '√' + inside;

		return {
			label: 'Square root of ' + decimals( n, 4 ),
			value: decimals( Math.sqrt( n ), 8 ),
			rows: [
				{ label: 'Simplified radical', value: radical },
				{ label: 'Cube root', value: decimals( Math.cbrt( n ), 8 ) },
				{ label: 'Squared back', value: decimals( n, 6 ), divide: true },
				{ label: 'Perfect square', value: exact && Number.isInteger( Math.sqrt( n ) ) ? 'yes' : 'no' }
			]
		};
	};

	formulas[ 'roman-numeral-converter' ] = function ( v ) {
		var MAP = [ [1000,'M'],[900,'CM'],[500,'D'],[400,'CD'],[100,'C'],[90,'XC'],
					[50,'L'],[40,'XL'],[10,'X'],[9,'IX'],[5,'V'],[4,'IV'],[1,'I'] ];
		var VALUES = { I:1, V:5, X:10, L:50, C:100, D:500, M:1000 };

		if ( 'toNumber' === ( v.direction || 'toRoman' ) ) {
			var text = String( v.roman || '' ).toUpperCase().replace( /[^IVXLCDM]/g, '' );
			var total = 0, valid = text.length > 0;

			for ( var i = 0; i < text.length; i++ ) {
				var here = VALUES[ text[ i ] ];
				var next = VALUES[ text[ i + 1 ] ] || 0;
				total += next > here ? -here : here;
			}

			var roundTrip = total > 0 && total < 4000;
			var back = '';
			if ( roundTrip ) {
				var left = total;
				MAP.forEach( function ( pair ) { while ( left >= pair[0] ) { back += pair[1]; left -= pair[0]; } } );
			}

			return {
				label: ( text || '—' ) + ' in numbers',
				value: valid ? String( total ) : '—',
				rows: [
					{ label: 'Standard spelling', value: back || '—' },
					{ label: 'Written correctly', value: back === text ? 'yes' : 'no', emphasis: back !== text }
				],
				note: back && back !== text ? 'That is readable but not the standard form, which would be ' + back + '.' : ''
			};
		}

		var n = Math.round( num( v.number ) );

		if ( n < 1 || n > 3999 ) {
			return {
				label: 'Roman numeral',
				value: '—',
				rows: [],
				note: 'Standard Roman numerals run from 1 to 3999. There is no zero and no accepted single-character form above M.'
			};
		}

		var out = '', rest = n;
		MAP.forEach( function ( pair ) { while ( rest >= pair[0] ) { out += pair[1]; rest -= pair[0]; } } );

		return {
			label: n + ' in Roman numerals',
			value: out,
			rows: [
				{ label: 'Characters', value: String( out.length ) },
				{ label: 'Broken down', value: out.split( '' ).join( ' ' ), divide: true }
			]
		};
	};

	formulas[ 'quadratic-formula-calculator' ] = function ( v ) {
		var a = num( v.a ), b = num( v.b ), c = num( v.c );

		if ( a === 0 ) {
			var linear = b === 0 ? null : -c / b;
			return {
				label: 'Root',
				value: linear === null ? '—' : decimals( linear, 6 ),
				rows: [ { label: 'Equation type', value: 'linear, not quadratic' } ],
				note: 'With a of zero the x squared term disappears, so this is a straight line rather than a parabola.'
			};
		}

		var disc = b * b - 4 * a * c;
		var vertexX = -b / ( 2 * a );
		var vertexY = a * vertexX * vertexX + b * vertexX + c;
		var rows = [
			{ label: 'Discriminant', value: decimals( disc, 6 ) },
			{ label: 'Vertex', value: '(' + decimals( vertexX, 4 ) + ', ' + decimals( vertexY, 4 ) + ')' },
			{ label: 'Axis of symmetry', value: 'x = ' + decimals( vertexX, 4 ), divide: true }
		];

		if ( disc > 0 ) {
			var r1 = ( -b + Math.sqrt( disc ) ) / ( 2 * a );
			var r2 = ( -b - Math.sqrt( disc ) ) / ( 2 * a );
			return {
				label: 'Two real roots',
				value: 'x = ' + decimals( r1, 4 ) + ' or ' + decimals( r2, 4 ),
				rows: rows,
				note: 'A positive discriminant means the parabola crosses the x axis twice.'
			};
		}

		if ( disc === 0 ) {
			return {
				label: 'One repeated root',
				value: 'x = ' + decimals( -b / ( 2 * a ), 6 ),
				rows: rows,
				note: 'A discriminant of zero means the parabola touches the x axis at exactly one point.'
			};
		}

		var re = -b / ( 2 * a );
		var im = Math.sqrt( -disc ) / ( 2 * a );
		return {
			label: 'Two complex roots',
			value: decimals( re, 4 ) + ' ± ' + decimals( Math.abs( im ), 4 ) + 'i',
			rows: rows,
			note: 'A negative discriminant means the parabola never crosses the x axis, so the roots are complex.'
		};
	};

	formulas[ 'system-of-equations-calculator' ] = function ( v ) {
		var a1 = num( v.a1 ), b1 = num( v.b1 ), c1 = num( v.c1 );
		var a2 = num( v.a2 ), b2 = num( v.b2 ), c2 = num( v.c2 );
		var det = a1 * b2 - a2 * b1;

		if ( det === 0 ) {
			var consistent = ( a1 * c2 - a2 * c1 ) === 0 && ( b1 * c2 - b2 * c1 ) === 0;
			return {
				label: 'No unique solution',
				value: consistent ? 'infinitely many' : 'none',
				rows: [ { label: 'Determinant', value: '0' } ],
				note: consistent
					? 'The two equations describe the same line, so every point on it is a solution.'
					: 'The two lines are parallel and never meet, so there is no solution.'
			};
		}

		var x = ( c1 * b2 - c2 * b1 ) / det;
		var y = ( a1 * c2 - a2 * c1 ) / det;

		return {
			label: 'Solution',
			value: 'x = ' + decimals( x, 4 ) + ', y = ' + decimals( y, 4 ),
			rows: [
				{ label: 'x', value: decimals( x, 6 ) },
				{ label: 'y', value: decimals( y, 6 ) },
				{ label: 'Determinant', value: decimals( det, 6 ), divide: true }
			],
			note: 'Solved by Cramer’s rule. The determinant is non-zero, so the two lines cross at exactly one point.'
		};
	};

	formulas[ 'slope-calculator' ] = function ( v ) {
		var x1 = num( v.x1 ), y1 = num( v.y1 ), x2 = num( v.x2 ), y2 = num( v.y2 );
		var dx = x2 - x1, dy = y2 - y1;

		if ( dx === 0 ) {
			return {
				label: 'Slope',
				value: 'undefined',
				rows: [ { label: 'Line', value: 'vertical, x = ' + decimals( x1, 4 ) } ],
				note: 'A vertical line has no slope, because the run is zero and nothing can be divided by zero.'
			};
		}

		var m = dy / dx;
		var b = y1 - m * x1;

		return {
			label: 'Slope',
			value: decimals( m, 6 ),
			rows: [
				{ label: 'Equation', value: 'y = ' + decimals( m, 4 ) + 'x ' + ( b < 0 ? '− ' : '+ ' ) + decimals( Math.abs( b ), 4 ) },
				{ label: 'Rise over run', value: decimals( dy, 4 ) + ' / ' + decimals( dx, 4 ) },
				{ label: 'Angle', value: decimals( Math.atan( m ) * 180 / Math.PI, 3 ) + '°', divide: true },
				{ label: 'Distance between points', value: decimals( Math.sqrt( dx * dx + dy * dy ), 6 ) }
			]
		};
	};

	formulas[ 'standard-deviation-calculator' ] = function ( v ) {
		var xs = listOf( v.numbers );
		var population = 'population' === ( v.type || 'sample' );

		if ( xs.length < 2 ) {
			return { label: 'Standard deviation', value: '—', rows: [], note: 'At least two numbers are needed, because deviation describes spread and one value has none.' };
		}

		var mean = xs.reduce( function ( t, x ) { return t + x; }, 0 ) / xs.length;
		var sq = xs.reduce( function ( t, x ) { return t + Math.pow( x - mean, 2 ); }, 0 );
		var divisor = population ? xs.length : xs.length - 1;
		var variance = sq / divisor;
		var sd = Math.sqrt( variance );

		return {
			label: ( population ? 'Population' : 'Sample' ) + ' standard deviation',
			value: decimals( sd, 6 ),
			rows: [
				{ label: 'Variance', value: decimals( variance, 6 ) },
				{ label: 'Mean', value: decimals( mean, 6 ) },
				{ label: 'Count', value: String( xs.length ), divide: true },
				{ label: 'Sum of squared deviations', value: decimals( sq, 6 ) },
				{ label: 'Coefficient of variation', value: mean === 0 ? '—' : decimals( ( sd / mean ) * 100, 3 ) + '%' }
			],
			note: population
				? 'Dividing by n is correct only when these numbers are the entire population rather than a sample drawn from one.'
				: 'Dividing by n minus one corrects for the fact that a sample underestimates the spread of the population it came from.'
		};
	};

	function factorsOf( n ) {
		var out = [];
		for ( var i = 1; i * i <= n; i++ ) {
			if ( n % i === 0 ) {
				out.push( i );
				if ( i !== n / i ) { out.push( n / i ); }
			}
		}
		return out.sort( function ( a, b ) { return a - b; } );
	}

	function primeFactors( n ) {
		var out = [];
		for ( var d = 2; d * d <= n; d++ ) {
			while ( n % d === 0 ) { out.push( d ); n /= d; }
		}
		if ( n > 1 ) { out.push( n ); }
		return out;
	}

	formulas[ 'gcf-calculator' ] = function ( v ) {
		var xs = listOf( v.numbers ).map( function ( x ) { return Math.abs( Math.round( x ) ); } ).filter( function ( x ) { return x > 0; } );

		if ( xs.length < 2 ) {
			return { label: 'Greatest common factor', value: '—', rows: [], note: 'Enter at least two whole numbers above zero.' };
		}

		var g = xs.reduce( gcd );
		var l = xs.reduce( function ( a, b ) { return ( a * b ) / gcd( a, b ); } );

		return {
			label: 'Greatest common factor',
			value: String( g ),
			rows: [
				{ label: 'Lowest common multiple', value: String( l ) },
				{ label: 'Numbers', value: xs.join( ', ' ) },
				{ label: 'Shared factors', value: factorsOf( g ).join( ', ' ), divide: true },
				{ label: 'Coprime', value: g === 1 ? 'yes' : 'no' }
			]
		};
	};

	formulas[ 'lcm-calculator' ] = function ( v ) {
		var xs = listOf( v.numbers ).map( function ( x ) { return Math.abs( Math.round( x ) ); } ).filter( function ( x ) { return x > 0; } );

		if ( xs.length < 2 ) {
			return { label: 'Lowest common multiple', value: '—', rows: [], note: 'Enter at least two whole numbers above zero.' };
		}

		var l = xs.reduce( function ( a, b ) { return ( a * b ) / gcd( a, b ); } );
		var g = xs.reduce( gcd );

		return {
			label: 'Lowest common multiple',
			value: String( l ),
			rows: [
				{ label: 'Greatest common factor', value: String( g ) },
				{ label: 'Numbers', value: xs.join( ', ' ) },
				{ label: 'Prime factors of the LCM', value: primeFactors( l ).join( ' × ' ) || String( l ), divide: true }
			],
			note: 'For two numbers the LCM times the GCF always equals the two numbers multiplied together, which is a quick way to check the answer.'
		};
	};

	formulas[ 'factor-calculator' ] = function ( v ) {
		var n = Math.abs( Math.round( num( v.number ) ) );

		if ( n < 1 || n > 100000000 ) {
			return { label: 'Factors', value: '—', rows: [], note: 'Enter a whole number between 1 and 100,000,000.' };
		}

		var fs = factorsOf( n );
		var ps = primeFactors( n );
		var counts = {};
		ps.forEach( function ( p ) { counts[ p ] = ( counts[ p ] || 0 ) + 1; } );
		var exp = Object.keys( counts ).map( function ( p ) {
			return counts[ p ] > 1 ? p + '^' + counts[ p ] : p;
		} ).join( ' × ' );

		return {
			label: 'Factors of ' + n,
			value: String( fs.length ) + ( fs.length === 1 ? ' factor' : ' factors' ),
			rows: [
				{ label: 'All factors', value: fs.length <= 24 ? fs.join( ', ' ) : fs.slice( 0, 24 ).join( ', ' ) + '…' },
				{ label: 'Prime factorisation', value: exp || String( n ) },
				{ label: 'Prime number', value: fs.length === 2 ? 'yes' : 'no', divide: true, emphasis: fs.length === 2 },
				{ label: 'Sum of factors', value: String( fs.reduce( function ( t, x ) { return t + x; }, 0 ) ) }
			]
		};
	};

	/* ---------- Calculus ----------
	 *
	 * A deliberately small symbolic engine. It handles sums of terms in x:
	 * powers, sine, cosine, e^x, ln and roots, which covers the overwhelming
	 * majority of what people type into a derivative box. It does NOT do the
	 * product, quotient or chain rules, and it says so on the page rather than
	 * returning a confident wrong answer, because a calculus tool that is
	 * silently wrong is worse than one that admits its limits.
	 */

	function parseTerms( input ) {
		var text = String( input || '' ).replace( /\s+/g, '' ).replace( /\*\*/g, '^' );

		if ( ! text ) { return null; }

		/* Split on + and - that start a term, keeping the sign with the term. */
		var chunks = text.replace( /([+-])/g, '\u0000$1' ).split( '\u0000' ).filter( Boolean );
		var terms = [];

		for ( var i = 0; i < chunks.length; i++ ) {
			var t = chunks[ i ];
			var sign = 1;

			if ( t[ 0 ] === '+' ) { t = t.slice( 1 ); }
			else if ( t[ 0 ] === '-' ) { sign = -1; t = t.slice( 1 ); }

			if ( ! t ) { return null; }

			var m;

			if ( ( m = t.match( /^(\d*\.?\d*)\*?sin\(x\)$/i ) ) ) {
				terms.push( { c: sign * ( m[1] === '' ? 1 : parseFloat( m[1] ) ), kind: 'sin' } );
			} else if ( ( m = t.match( /^(\d*\.?\d*)\*?cos\(x\)$/i ) ) ) {
				terms.push( { c: sign * ( m[1] === '' ? 1 : parseFloat( m[1] ) ), kind: 'cos' } );
			} else if ( ( m = t.match( /^(\d*\.?\d*)\*?e\^x$/i ) ) ) {
				terms.push( { c: sign * ( m[1] === '' ? 1 : parseFloat( m[1] ) ), kind: 'exp' } );
			} else if ( ( m = t.match( /^(\d*\.?\d*)\*?ln\(x\)$/i ) ) ) {
				terms.push( { c: sign * ( m[1] === '' ? 1 : parseFloat( m[1] ) ), kind: 'ln' } );
			} else if ( ( m = t.match( /^(\d*\.?\d*)\*?sqrt\(x\)$/i ) ) ) {
				terms.push( { c: sign * ( m[1] === '' ? 1 : parseFloat( m[1] ) ), kind: 'poly', n: 0.5 } );
			} else if ( ( m = t.match( /^(\d*\.?\d*)\*?x\^\(?(-?\d*\.?\d+)\)?$/i ) ) ) {
				terms.push( { c: sign * ( m[1] === '' ? 1 : parseFloat( m[1] ) ), kind: 'poly', n: parseFloat( m[2] ) } );
			} else if ( ( m = t.match( /^(\d*\.?\d*)\*?x$/i ) ) ) {
				terms.push( { c: sign * ( m[1] === '' ? 1 : parseFloat( m[1] ) ), kind: 'poly', n: 1 } );
			} else if ( ( m = t.match( /^(\d+\.?\d*)$/ ) ) ) {
				terms.push( { c: sign * parseFloat( m[1] ), kind: 'poly', n: 0 } );
			} else {
				return null;
			}
		}

		return terms;
	}

	function coefText( c, needsOne ) {
		if ( c === 1 && needsOne ) { return ''; }
		if ( c === -1 && needsOne ) { return '-'; }
		return String( Number( c.toFixed( 6 ) ) );
	}

	function termText( t ) {
		if ( 'sin' === t.kind ) { return coefText( t.c, true ) + 'sin(x)'; }
		if ( 'cos' === t.kind ) { return coefText( t.c, true ) + 'cos(x)'; }
		if ( 'exp' === t.kind ) { return coefText( t.c, true ) + 'e^x'; }
		if ( 'ln'  === t.kind ) { return coefText( t.c, true ) + 'ln|x|'; }
		if ( t.n === 0 ) { return coefText( t.c, false ); }
		if ( t.n === 1 ) { return coefText( t.c, true ) + 'x'; }
		return coefText( t.c, true ) + 'x^' + Number( t.n.toFixed( 6 ) );
	}

	function joinTerms( terms ) {
		var kept = terms.filter( function ( t ) { return t.c !== 0; } );

		if ( ! kept.length ) { return '0'; }

		return kept.map( function ( t, i ) {
			var text = termText( t );
			if ( i === 0 ) { return text; }
			return text[ 0 ] === '-' ? ' - ' + text.slice( 1 ) : ' + ' + text;
		} ).join( '' );
	}

	function evaluateTerms( terms, x ) {
		return terms.reduce( function ( total, t ) {
			if ( 'sin' === t.kind ) { return total + t.c * Math.sin( x ); }
			if ( 'cos' === t.kind ) { return total + t.c * Math.cos( x ); }
			if ( 'exp' === t.kind ) { return total + t.c * Math.exp( x ); }
			if ( 'ln'  === t.kind ) { return total + t.c * Math.log( Math.abs( x ) ); }
			return total + t.c * Math.pow( x, t.n );
		}, 0 );
	}

	var PARSE_HELP = 'Understood: powers such as 3x^2, plain terms such as 5x or 7, sin(x), cos(x), e^x, ln(x) and sqrt(x), added or subtracted. Products, quotients and nested functions are not supported yet.';

	formulas[ 'derivative-calculator' ] = function ( v ) {
		var terms = parseTerms( v.expression );

		if ( ! terms ) {
			return { label: 'Derivative', value: '—', rows: [], note: 'That expression could not be read. ' + PARSE_HELP };
		}

		var d = terms.map( function ( t ) {
			if ( 'sin' === t.kind ) { return { c: t.c, kind: 'cos' }; }
			if ( 'cos' === t.kind ) { return { c: -t.c, kind: 'sin' }; }
			if ( 'exp' === t.kind ) { return { c: t.c, kind: 'exp' }; }
			if ( 'ln'  === t.kind ) { return { c: t.c, kind: 'poly', n: -1 }; }
			if ( t.n === 0 ) { return { c: 0, kind: 'poly', n: 0 }; }
			return { c: t.c * t.n, kind: 'poly', n: t.n - 1 };
		} );

		var at = num( v.at );

		return {
			label: 'Derivative',
			value: "f'(x) = " + joinTerms( d ),
			rows: [
				{ label: 'Original', value: 'f(x) = ' + joinTerms( terms ) },
				{ label: 'Slope at x = ' + decimals( at, 3 ), value: decimals( evaluateTerms( d, at ), 6 ), divide: true },
				{ label: 'f(x) at that point', value: decimals( evaluateTerms( terms, at ), 6 ) }
			],
			note: 'Differentiated term by term with the power rule. ' + PARSE_HELP
		};
	};

	formulas[ 'integral-calculator' ] = function ( v ) {
		var terms = parseTerms( v.expression );

		if ( ! terms ) {
			return { label: 'Integral', value: '—', rows: [], note: 'That expression could not be read. ' + PARSE_HELP };
		}

		var special = null;

		var integrated = terms.map( function ( t ) {
			if ( 'sin' === t.kind ) { return { c: -t.c, kind: 'cos' }; }
			if ( 'cos' === t.kind ) { return { c: t.c, kind: 'sin' }; }
			if ( 'exp' === t.kind ) { return { c: t.c, kind: 'exp' }; }
			if ( 'ln'  === t.kind ) { special = 'ln'; return { c: t.c, kind: 'poly', n: 1 }; }
			if ( t.n === -1 ) { return { c: t.c, kind: 'ln' }; }
			return { c: t.c / ( t.n + 1 ), kind: 'poly', n: t.n + 1 };
		} );

		var antiderivative = joinTerms( integrated ) + ( special ? ' (with the ln term integrated by parts)' : '' ) + ' + C';

		var a = num( v.from ), b = num( v.to );
		var definite = evaluateTerms( integrated, b ) - evaluateTerms( integrated, a );

		return {
			label: 'Indefinite integral',
			value: '∫ f(x) dx = ' + antiderivative,
			rows: [
				{ label: 'Original', value: 'f(x) = ' + joinTerms( terms ) },
				{ label: 'Definite from ' + decimals( a, 3 ) + ' to ' + decimals( b, 3 ), value: isFinite( definite ) ? decimals( definite, 6 ) : '—', divide: true, emphasis: true }
			],
			note: 'Integrated term by term with the reverse power rule. The constant of integration is written as C because an indefinite integral describes a family of curves rather than one. ' + PARSE_HELP
		};
	};

	/* ---------- Health & Body ---------- */

	formulas[ 'ovulation-calculator' ] = function ( v ) {
		var lmp = parseDate( v.lmp );
		var cycle = Math.max( num( v.cycle ) || 28, 20 );

		if ( ! lmp ) {
			return { label: 'Fertile window', value: '—', rows: [], note: 'Enter the first day of your last period.' };
		}

		/* The luteal phase is the stable part of a cycle at roughly 14 days,
		   so ovulation is counted back from the next period rather than
		   forward from the last one. That is why a long cycle moves ovulation
		   later without moving it by the same amount. */
		var nextPeriod = addDays( lmp, cycle );
		var ovulation = addDays( nextPeriod, -14 );

		return {
			label: 'Estimated ovulation',
			value: fmtDate( ovulation ),
			rows: [
				{ label: 'Fertile window opens', value: fmtDate( addDays( ovulation, -5 ) ), color: ACCENT },
				{ label: 'Most fertile', value: fmtDate( addDays( ovulation, -1 ) ) + ' to ' + fmtDate( ovulation ), emphasis: true },
				{ label: 'Fertile window closes', value: fmtDate( addDays( ovulation, 1 ) ) },
				{ label: 'Next period expected', value: fmtDate( nextPeriod ), divide: true },
				{ label: 'Cycle length used', value: cycle + ' days' }
			],
			note: 'An estimate from cycle arithmetic, not an observation. Real ovulation shifts by several days between cycles even in regular ones, so treat the window as wider than the dates suggest and use tracking rather than a calendar if timing matters.'
		};
	};

	formulas[ 'pregnancy-calculator' ] = function ( v ) {
		var lmp = parseDate( v.lmp );
		var cycle = Math.max( num( v.cycle ) || 28, 20 );

		if ( ! lmp ) {
			return { label: 'Due date', value: '—', rows: [], note: 'Enter the first day of your last period.' };
		}

		/* Naegele's rule is 280 days from the last period, adjusted for a
		   cycle that is not 28 days, since the rule assumes ovulation on day
		   14 and a longer cycle pushes conception later. */
		var due = addDays( lmp, 280 + ( cycle - 28 ) );
		var today = new Date();
		today.setHours( 0, 0, 0, 0 );
		var daysAlong = Math.floor( ( today - lmp ) / 86400000 );
		var weeks = Math.floor( daysAlong / 7 );
		var days = daysAlong % 7;
		var trimester = weeks < 13 ? 'First' : ( weeks < 28 ? 'Second' : 'Third' );

		return {
			label: 'Estimated due date',
			value: fmtDate( due ),
			rows: [
				{ label: 'How far along today', value: daysAlong >= 0 ? weeks + ' weeks ' + days + ' days' : '—' },
				{ label: 'Trimester', value: daysAlong >= 0 && weeks <= 42 ? trimester : '—' },
				{ label: 'Days remaining', value: String( Math.max( Math.floor( ( due - today ) / 86400000 ), 0 ) ), divide: true },
				{ label: 'Conception, approximately', value: fmtDate( addDays( lmp, 14 + ( cycle - 28 ) ) ) },
				{ label: 'Full term from', value: fmtDate( addDays( due, -21 ) ) }
			],
			note: 'Only about one birth in twenty happens on the due date itself. A dating scan in the first trimester is considerably more accurate than any calculation from a period date.'
		};
	};

	formulas[ 'period-calculator' ] = function ( v ) {
		var lmp = parseDate( v.lmp );
		var cycle = Math.max( num( v.cycle ) || 28, 20 );
		var length = Math.max( num( v.length ) || 5, 1 );

		if ( ! lmp ) {
			return { label: 'Next period', value: '—', rows: [], note: 'Enter the first day of your last period.' };
		}

		var rows = [];
		for ( var i = 1; i <= 5; i++ ) {
			var start = addDays( lmp, cycle * i );
			rows.push( {
				label: 'Period ' + i,
				value: fmtDate( start ) + ' to ' + fmtDate( addDays( start, length - 1 ) ),
				divide: i === 2
			} );
		}

		return {
			label: 'Next period expected',
			value: fmtDate( addDays( lmp, cycle ) ),
			rows: rows,
			note: 'Straight cycle arithmetic. Real cycles vary by several days month to month, and stress, illness, travel and training all move them, so the later predictions are looser than the first.'
		};
	};

	formulas[ 'bac-calculator' ] = function ( v ) {
		var drinks = num( v.drinks );
		var metric = 'metric' === ( v.units || 'imperial' );
		var lbs = metric ? num( v.weight ) * 2.20462 : num( v.pounds );
		var hours = num( v.hours );
		/* Widmark's r: the fraction of body mass that is water available to
		   dilute alcohol. It differs by sex because body composition does. */
		var r = 'female' === v.sex ? 0.66 : 0.73;

		if ( lbs <= 0 ) {
			return { label: 'Estimated BAC', value: '—', rows: [], note: 'Enter a body weight.' };
		}

		var ounces = drinks * 0.6;
		var peak = ( ounces * 5.14 ) / ( lbs * r );
		var bac = Math.max( peak - ( 0.015 * hours ), 0 );

		var status = bac >= 0.08 ? 'Over the 0.08 limit'
			: bac >= 0.05 ? 'Over the 0.05 limit used in some places'
			: bac > 0 ? 'Under 0.05' : 'No measurable alcohol';

		return {
			label: 'Estimated BAC',
			value: bac.toFixed( 3 ) + '%',
			rows: [
				{ label: 'Status', value: status, emphasis: bac >= 0.05 },
				{ label: 'Peak before metabolism', value: peak.toFixed( 3 ) + '%' },
				{ label: 'Pure alcohol consumed', value: decimals( ounces, 2 ) + ' fl oz', divide: true },
				{ label: 'Hours until zero', value: decimals( bac / 0.015, 1 ) }
			],
			note: 'A rough population estimate from the Widmark formula, not a measurement. Food, medication, body composition, liver function and how fast you drank all move the real figure, often by a lot. Never use this to decide whether to drive. If you have been drinking, do not drive.'
		};
	};

	formulas[ 'steps-to-miles-calculator' ] = function ( v ) {
		var steps = num( v.steps );
		var metric = 'metric' === ( v.units || 'imperial' );
		var heightIn = metric ? num( v.height ) / 2.54 : ( num( v.feet ) * 12 + num( v.inches ) );
		var pace = v.pace || 'walk';

		/* Stride is estimated from height because almost nobody has measured
		   theirs: about 41.3% of height walking, 48% running. */
		var strideIn = heightIn * ( 'run' === pace ? 0.48 : 0.413 );
		var miles = ( steps * strideIn ) / 63360;
		var km = miles * 1.609344;

		return {
			label: 'Distance covered',
			value: decimals( miles, 2 ) + ' miles',
			rows: [
				{ label: 'Kilometres', value: decimals( km, 2 ) },
				{ label: 'Estimated stride', value: decimals( strideIn, 1 ) + ' in' },
				{ label: 'Steps per mile', value: strideIn > 0 ? decimals( 63360 / strideIn, 0 ) : '—', divide: true },
				{ label: 'Rough calories burned', value: decimals( miles * 100, 0 ) + ' kcal' }
			],
			note: 'Stride is estimated from height rather than measured, so this is an approximation. Pace, terrain and footwear all change it. To get an accurate figure, walk a measured distance and divide by your step count.'
		};
	};

	formulas[ 'one-rep-max-calculator' ] = function ( v ) {
		var w = num( v.weight );
		var reps = Math.max( Math.round( num( v.reps ) ), 1 );
		var unit = 'kg' === ( v.unit || 'lb' ) ? 'kg' : 'lb';

		var epley = reps === 1 ? w : w * ( 1 + reps / 30 );
		var brzycki = reps >= 37 ? 0 : w * ( 36 / ( 37 - reps ) );
		var best = brzycki > 0 ? ( epley + brzycki ) / 2 : epley;

		return {
			label: 'Estimated one rep max',
			value: decimals( best, 1 ) + ' ' + unit,
			rows: [
				{ label: 'Epley formula', value: decimals( epley, 1 ) + ' ' + unit },
				{ label: 'Brzycki formula', value: brzycki > 0 ? decimals( brzycki, 1 ) + ' ' + unit : '—' },
				{ label: '95% for 2 reps', value: decimals( best * 0.95, 1 ) + ' ' + unit, divide: true },
				{ label: '90% for 4 reps', value: decimals( best * 0.90, 1 ) + ' ' + unit },
				{ label: '80% for 8 reps', value: decimals( best * 0.80, 1 ) + ' ' + unit },
				{ label: '70% for 12 reps', value: decimals( best * 0.70, 1 ) + ' ' + unit }
			],
			note: reps > 10
				? 'Above ten reps these formulas drift badly, because endurance starts limiting the set rather than strength. Test with a heavier weight and fewer reps for a usable figure.'
				: 'Averaged from the two most widely used formulas. They agree closely under about six reps and diverge above it.'
		};
	};

	formulas[ 'bra-size-calculator' ] = function ( v ) {
		var metric = 'metric' === ( v.units || 'imperial' );
		var under = metric ? num( v.underbust ) / 2.54 : num( v.underbust );
		var bust = metric ? num( v.bust ) / 2.54 : num( v.bust );

		if ( under <= 0 || bust <= 0 ) {
			return { label: 'Bra size', value: '—', rows: [], note: 'Measure snugly under the bust and then around the fullest part.' };
		}

		/* Modern method: band is the underbust rounded to the nearest even
		   inch, with no plus-four addition, which is what fitters now use. */
		var band = Math.round( under / 2 ) * 2;
		var diff = Math.round( bust - band );
		var CUPS = [ 'AA', 'A', 'B', 'C', 'D', 'DD', 'DDD/F', 'G', 'H', 'I', 'J' ];
		var cup = diff < 0 ? 'AA' : ( CUPS[ diff ] || CUPS[ CUPS.length - 1 ] );

		/* Sister sizes hold cup volume while changing the band, which is the
		   usual fix when the cup fits and the band does not. */
		var downCup = CUPS[ Math.max( diff + 1, 0 ) ] || cup;
		var upCup = CUPS[ Math.max( diff - 1, 0 ) ] || cup;

		return {
			label: 'Estimated size',
			value: band + cup,
			rows: [
				{ label: 'Band', value: String( band ) },
				{ label: 'Cup', value: cup + ' (' + diff + ' in difference)' },
				{ label: 'Sister size down', value: ( band - 2 ) + downCup, divide: true },
				{ label: 'Sister size up', value: ( band + 2 ) + upCup }
			],
			note: 'Sizing is not standardised between brands, so treat this as a starting point and expect to try a size either side. If the cup fits but the band rides up, the sister sizes above are the ones to try.'
		};
	};

	formulas[ 'water-intake-calculator' ] = function ( v ) {
		var metric = 'metric' === ( v.units || 'imperial' );
		var kg = metric ? num( v.weight ) : num( v.pounds ) * 0.45359237;
		var minutes = num( v.exercise );
		var climate = v.climate || 'temperate';

		/* About 35 ml per kg for a sedentary adult, plus roughly 350 ml per
		   half hour of exercise, adjusted for heat. */
		var base = kg * 35;
		var exercise = ( minutes / 30 ) * 350;
		var factor = 'hot' === climate ? 1.15 : ( 'cold' === climate ? 0.95 : 1 );
		var ml = ( base + exercise ) * factor;

		return {
			label: 'Daily water target',
			value: decimals( ml / 1000, 2 ) + ' litres',
			rows: [
				{ label: 'US fluid ounces', value: decimals( ml * 0.033814, 0 ) },
				{ label: 'Cups (8 oz)', value: decimals( ( ml * 0.033814 ) / 8, 1 ) },
				{ label: 'Baseline for your weight', value: decimals( base / 1000, 2 ) + ' L', divide: true },
				{ label: 'Added for exercise', value: decimals( exercise / 1000, 2 ) + ' L' }
			],
			note: 'Food supplies roughly a fifth of daily fluid, and so do tea, coffee and other drinks, so this is total intake rather than plain water you must drink. Thirst and pale urine are better day to day guides than any number.'
		};
	};

	formulas[ 'body-fat-calculator' ] = function ( v ) {
		var metric = 'metric' === ( v.units || 'imperial' );
		var toIn = function ( x ) { return metric ? num( x ) / 2.54 : num( x ); };
		var height = toIn( v.heightVal );
		var neck = toIn( v.neck );
		var waist = toIn( v.waist );
		var hip = toIn( v.hip );
		var female = 'female' === v.sex;

		var bf;
		if ( female ) {
			var inner = waist + hip - neck;
			bf = inner > 0 && height > 0
				? 495 / ( 1.29579 - 0.35004 * Math.log10( inner ) + 0.22100 * Math.log10( height ) ) - 450
				: 0;
		} else {
			var d = waist - neck;
			bf = d > 0 && height > 0
				? 495 / ( 1.0324 - 0.19077 * Math.log10( d ) + 0.15456 * Math.log10( height ) ) - 450
				: 0;
		}

		bf = Math.max( Math.min( bf, 75 ), 0 );

		var band = female
			? ( bf < 14 ? 'Essential fat' : bf < 21 ? 'Athletic' : bf < 25 ? 'Fitness' : bf < 32 ? 'Average' : 'Above average' )
			: ( bf < 6 ? 'Essential fat' : bf < 14 ? 'Athletic' : bf < 18 ? 'Fitness' : bf < 25 ? 'Average' : 'Above average' );

		var kg = toKg( v, metric );
		var fatMass = kg * ( bf / 100 );

		return {
			label: 'Body fat',
			value: decimals( bf, 1 ) + '%',
			rows: [
				{ label: 'Category', value: band, emphasis: bf > ( female ? 32 : 25 ) },
				{ label: 'Fat mass', value: metric ? decimals( fatMass, 1 ) + ' kg' : decimals( fatMass * 2.20462, 1 ) + ' lb' },
				{ label: 'Lean mass', value: metric ? decimals( kg - fatMass, 1 ) + ' kg' : decimals( ( kg - fatMass ) * 2.20462, 1 ) + ' lb', divide: true }
			],
			note: 'The US Navy tape method, which is typically within about three or four percentage points of a DEXA scan. It is far better than BMI at telling body composition, and far worse than a scan. Measure at the same time of day for comparable readings.'
		};
	};

	formulas[ 'macro-calculator' ] = function ( v ) {
		var calories = num( v.calories );
		var split = v.split || 'balanced';
		var SPLITS = {
			balanced: { p: 30, c: 40, f: 30, name: 'Balanced' },
			lowcarb:  { p: 35, c: 25, f: 40, name: 'Low carb' },
			highcarb: { p: 25, c: 55, f: 20, name: 'High carb' },
			keto:     { p: 25, c: 5,  f: 70, name: 'Ketogenic' }
		};
		var s = SPLITS[ split ] || SPLITS.balanced;

		/* Four calories per gram of protein and carbohydrate, nine per gram
		   of fat, which is why a high-fat split has so few grams in it. */
		var protein = ( calories * s.p / 100 ) / 4;
		var carbs = ( calories * s.c / 100 ) / 4;
		var fat = ( calories * s.f / 100 ) / 9;

		return {
			label: s.name + ' macros',
			value: decimals( protein, 0 ) + 'P / ' + decimals( carbs, 0 ) + 'C / ' + decimals( fat, 0 ) + 'F',
			bar: [
				{ pct: s.p, color: ACCENT },
				{ pct: s.c, color: WARN },
				{ pct: s.f, color: NEUTRAL }
			],
			rows: [
				{ label: 'Protein', value: decimals( protein, 0 ) + ' g  (' + s.p + '%)', color: ACCENT },
				{ label: 'Carbohydrate', value: decimals( carbs, 0 ) + ' g  (' + s.c + '%)', color: WARN },
				{ label: 'Fat', value: decimals( fat, 0 ) + ' g  (' + s.f + '%)', color: NEUTRAL },
				{ label: 'Total calories', value: decimals( calories, 0 ) + ' kcal', divide: true }
			],
			note: 'Protein is the macro worth hitting accurately, because it protects muscle in a deficit and is the most filling of the three. The carbohydrate and fat split is far more a matter of preference and adherence than most advice admits.'
		};
	};

	formulas[ 'body-surface-area-calculator' ] = function ( v ) {
		var metric = 'metric' === ( v.units || 'metric' );
		var kg = toKg( v, metric );
		var cm = toCm( v, metric );

		if ( kg <= 0 || cm <= 0 ) {
			return { label: 'Body surface area', value: '—', rows: [], note: 'Enter a height and weight.' };
		}

		var mosteller = Math.sqrt( ( cm * kg ) / 3600 );
		var dubois = 0.007184 * Math.pow( cm, 0.725 ) * Math.pow( kg, 0.425 );
		var haycock = 0.024265 * Math.pow( cm, 0.3964 ) * Math.pow( kg, 0.5378 );

		return {
			label: 'Body surface area',
			value: decimals( mosteller, 3 ) + ' m2',
			rows: [
				{ label: 'Mosteller', value: decimals( mosteller, 4 ) + ' m2' },
				{ label: 'Du Bois', value: decimals( dubois, 4 ) + ' m2' },
				{ label: 'Haycock', value: decimals( haycock, 4 ) + ' m2', divide: true },
				{ label: 'Square feet', value: decimals( mosteller * 10.7639, 2 ) }
			],
			note: 'Mosteller is shown as the headline because it is the formula most commonly used for drug dosing, being both simple and about as accurate as the longer ones. Where a dose depends on this, the prescriber’s own formula is the one that counts.'
		};
	};

	/* ---------- Date, Time & Work ---------- */

	formulas[ 'time-calculator' ] = function ( v ) {
		var base = parseClock( v.start );
		var op = v.op || 'add';
		var delta = num( v.hours ) * 60 + num( v.minutes );

		if ( base === null ) {
			return { label: 'Result', value: '—', rows: [], note: 'Enter a start time such as 9:30 AM or 14:45.' };
		}

		var total = base + ( 'sub' === op ? -delta : delta );
		var dayShift = Math.floor( total / 1440 );

		return {
			label: ( 'sub' === op ? 'Time minus ' : 'Time plus ' ) + hhmm( delta ),
			value: clockText( total ),
			rows: [
				{ label: '24 hour clock', value: ( function () {
					var m = ( ( total % 1440 ) + 1440 ) % 1440;
					var h = Math.floor( m / 60 ), mi = Math.round( m % 60 );
					return ( h < 10 ? '0' : '' ) + h + ':' + ( mi < 10 ? '0' : '' ) + mi;
				} )() },
				{ label: 'Day change', value: dayShift === 0 ? 'same day' : ( dayShift > 0 ? '+' + dayShift + ' day' : dayShift + ' day' ), emphasis: dayShift !== 0 },
				{ label: 'Started at', value: clockText( base ), divide: true }
			]
		};
	};

	formulas[ 'time-duration-calculator' ] = function ( v ) {
		var a = parseClock( v.start );
		var b = parseClock( v.end );

		if ( a === null || b === null ) {
			return { label: 'Duration', value: '—', rows: [], note: 'Enter both times, such as 9:00 AM and 5:30 PM.' };
		}

		/* An end earlier than the start means the period ran past midnight,
		   which is the normal case for night shifts rather than an error. */
		var mins = b - a;
		var overnight = mins < 0;
		if ( overnight ) { mins += 1440; }

		return {
			label: 'Duration',
			value: hhmm( mins ),
			rows: [
				{ label: 'Decimal hours', value: decimals( mins / 60, 4 ) },
				{ label: 'Total minutes', value: decimals( mins, 0 ) },
				{ label: 'Crosses midnight', value: overnight ? 'yes' : 'no', divide: true, emphasis: overnight },
				{ label: 'From', value: clockText( a ) + ' to ' + clockText( b ) }
			]
		};
	};

	formulas[ 'hours-calculator' ] = function ( v ) {
		var a = parseClock( v.start );
		var b = parseClock( v.end );
		var lunch = num( v.breakMins );
		var rate = num( v.rate );

		if ( a === null || b === null ) {
			return { label: 'Hours worked', value: '—', rows: [], note: 'Enter a start and end time.' };
		}

		var mins = b - a;
		if ( mins < 0 ) { mins += 1440; }
		var worked = Math.max( mins - lunch, 0 );

		return {
			label: 'Hours worked',
			value: hhmm( worked ),
			rows: [
				{ label: 'Decimal hours', value: decimals( worked / 60, 2 ) },
				{ label: 'Break deducted', value: lunch + ' min' },
				{ label: 'Gross time on site', value: hhmm( mins ), divide: true },
				{ label: 'Pay at ' + money2( rate ) + '/hr', value: money2( ( worked / 60 ) * rate ), emphasis: rate > 0 }
			],
			note: 'Payroll systems usually work in decimal hours rather than hours and minutes, so 8h 30m is entered as 8.5.'
		};
	};

	formulas[ 'military-time-converter' ] = function ( v ) {
		if ( 'toStandard' === ( v.direction || 'toMilitary' ) ) {
			var raw = String( v.military || '' ).replace( /[^0-9]/g, '' );
			if ( raw.length < 3 || raw.length > 4 ) {
				return { label: 'Standard time', value: '—', rows: [], note: 'Enter a four digit time such as 1430 or 0900.' };
			}
			var h = parseInt( raw.slice( 0, raw.length - 2 ), 10 );
			var mi = parseInt( raw.slice( -2 ), 10 );
			if ( h > 23 || mi > 59 ) {
				return { label: 'Standard time', value: '—', rows: [], note: 'That is not a valid time. Hours run 00 to 23 and minutes 00 to 59.' };
			}
			return {
				label: ( h < 10 ? '0' : '' ) + h + ( mi < 10 ? '0' : '' ) + mi + ' in standard time',
				value: clockText( h * 60 + mi ),
				rows: [
					{ label: 'Spoken as', value: ( h < 10 ? 'zero ' : '' ) + h + ' ' + ( mi === 0 ? 'hundred' : ( mi < 10 ? 'zero ' : '' ) + mi ) },
					{ label: 'Minutes past midnight', value: String( h * 60 + mi ), divide: true }
				]
			};
		}

		var mins = parseClock( v.standard );
		if ( mins === null ) {
			return { label: 'Military time', value: '—', rows: [], note: 'Enter a time such as 2:30 PM.' };
		}
		var hh = Math.floor( mins / 60 ), mm = mins % 60;
		return {
			label: clockText( mins ) + ' in military time',
			value: ( hh < 10 ? '0' : '' ) + hh + ( mm < 10 ? '0' : '' ) + mm,
			rows: [
				{ label: 'With a colon', value: ( hh < 10 ? '0' : '' ) + hh + ':' + ( mm < 10 ? '0' : '' ) + mm },
				{ label: 'Minutes past midnight', value: String( mins ), divide: true }
			],
			note: 'Midnight is 0000 and noon is 1200. There is no 2400 in normal use, since the day rolls over to 0000.'
		};
	};

	formulas[ 'date-calculator' ] = function ( v ) {
		var start = parseDate( v.start );
		var op = v.op || 'add';
		var days = num( v.days ) + num( v.weeks ) * 7;

		if ( ! start ) {
			return { label: 'Resulting date', value: '—', rows: [], note: 'Pick a starting date.' };
		}

		var months = num( v.months );
		var result = new Date( start.getTime() );
		var sign = 'sub' === op ? -1 : 1;

		if ( months ) {
			var targetDay = result.getDate();
			result.setDate( 1 );
			result.setMonth( result.getMonth() + sign * months );
			/* Clamp to the last day of the month, so 31 January plus one
			   month lands on 28 February rather than spilling into March. */
			var lastDay = new Date( result.getFullYear(), result.getMonth() + 1, 0 ).getDate();
			result.setDate( Math.min( targetDay, lastDay ) );
		}

		result = addDays( result, sign * days );

		return {
			label: 'Resulting date',
			value: fmtDate( result ),
			rows: [
				{ label: 'Day of the week', value: result.toLocaleDateString( 'en-US', { weekday: 'long' } ) },
				{ label: 'Days from the start', value: String( Math.round( ( result - start ) / 86400000 ) ) },
				{ label: 'ISO format', value: result.toISOString().slice( 0, 10 ), divide: true },
				{ label: 'Day of the year', value: String( Math.floor( ( result - new Date( result.getFullYear(), 0, 0 ) ) / 86400000 ) ) }
			]
		};
	};

	formulas[ 'chronological-age-calculator' ] = function ( v ) {
		return formulas[ 'age-calculator' ]( { dob: v.dob, upto: v.upto } );
	};

	formulas[ 'dog-age-calculator' ] = function ( v ) {
		/* Named dogYears so it cannot shadow the years() clamp helper. */
		var dogYears = Math.min( num( v.years ) + num( v.months ) / 12, 40 );
		var size = v.size || 'medium';

		if ( dogYears <= 0 ) {
			return { label: 'In human years', value: '—', rows: [], note: 'Enter your dog’s age.' };
		}

		/* The 2020 epigenetic clock study: 16 x ln(age) + 31. It fits young
		   dogs far better than the old times-seven rule, which badly
		   underestimates the first two years. */
		var epigenetic = 16 * Math.log( dogYears ) + 31;

		/* The veterinary size table, because a Great Dane and a chihuahua of
		   the same age are nothing like the same age. */
		var first = 15, second = 9;
		var perYear = { small: 4, medium: 5, large: 6, giant: 7 }[ size ] || 5;
		var table;
		if ( dogYears <= 1 ) { table = dogYears * first; }
		else if ( dogYears <= 2 ) { table = first + ( dogYears - 1 ) * second; }
		else { table = first + second + ( dogYears - 2 ) * perYear; }

		return {
			label: 'In human years',
			value: decimals( table, 0 ) + ' years',
			rows: [
				{ label: 'By the size table', value: decimals( table, 1 ) + ' years' },
				{ label: 'By the epigenetic formula', value: dogYears >= 1 ? decimals( epigenetic, 1 ) + ' years' : 'not applicable under 1' },
				{ label: 'Old times-seven rule', value: decimals( dogYears * 7, 1 ) + ' years', divide: true },
				{ label: 'Dog age entered', value: decimals( dogYears, 2 ) + ' years' }
			],
			note: 'The size table is shown as the headline because breed size affects ageing more than anything else: small dogs commonly reach sixteen while giant breeds rarely pass ten. The times-seven rule is included only to show how far off it is early on.'
		};
	};

	formulas[ 'business-days-calculator' ] = function ( v ) {
		var start = parseDate( v.start );
		var end = parseDate( v.end );
		var holidays = Math.max( Math.round( num( v.holidays ) ), 0 );

		if ( ! start || ! end ) {
			return { label: 'Business days', value: '—', rows: [], note: 'Pick both dates.' };
		}

		var from = start <= end ? start : end;
		var to = start <= end ? end : start;
		var days = Math.round( ( to - from ) / 86400000 ) + 1;

		/* Roughly two centuries. Beyond that the loop is the problem rather
		   than the answer, and no real question needs it. */
		if ( days > 73000 ) {
			return {
				label: 'Business days',
				value: '\u2014',
				rows: [ { label: 'Calendar days', value: decimals( days, 0 ) } ],
				note: 'That range spans more than two centuries, which is beyond what this calculator will count.'
			};
		}

		var business = 0, weekend = 0;

		for ( var i = 0; i < days; i++ ) {
			var d = addDays( from, i ).getDay();
			if ( d === 0 || d === 6 ) { weekend++; } else { business++; }
		}

		var net = Math.max( business - holidays, 0 );

		return {
			label: 'Business days',
			value: String( net ),
			rows: [
				{ label: 'Calendar days', value: String( days ) },
				{ label: 'Weekend days', value: String( weekend ) },
				{ label: 'Weekdays before holidays', value: String( business ), divide: true },
				{ label: 'Holidays deducted', value: String( holidays ) },
				{ label: 'Working weeks', value: decimals( net / 5, 2 ) }
			],
			note: 'Both the start and end dates are counted, which is the convention for contractual notice periods. Public holidays are entered by hand because they differ by country and by state.'
		};
	};

	formulas[ 'work-hours-calculator' ] = function ( v ) {
		var a = parseClock( v.start );
		var b = parseClock( v.end );
		var lunch = num( v.breakMins );
		var days = Math.max( num( v.days ) || 5, 1 );
		var rate = num( v.rate );

		if ( a === null || b === null ) {
			return { label: 'Weekly hours', value: '—', rows: [], note: 'Enter a start and end time.' };
		}

		var mins = b - a;
		if ( mins < 0 ) { mins += 1440; }
		var daily = Math.max( mins - lunch, 0 );
		var weekly = daily * days;

		return {
			label: 'Weekly hours',
			value: hhmm( weekly ),
			rows: [
				{ label: 'Per day', value: hhmm( daily ) },
				{ label: 'Decimal weekly hours', value: decimals( weekly / 60, 2 ) },
				{ label: 'Over 40 hours', value: weekly / 60 > 40 ? decimals( weekly / 60 - 40, 2 ) + ' h' : 'none', divide: true, emphasis: weekly / 60 > 40 },
				{ label: 'Weekly pay at ' + money2( rate ), value: money2( ( weekly / 60 ) * rate ) },
				{ label: 'Annual, 52 weeks', value: money2( ( weekly / 60 ) * rate * 52 ) }
			],
			note: weekly / 60 > 40 ? 'Above forty hours, the excess is usually overtime in the United States. Use the overtime calculator for the premium rate.' : ''
		};
	};

	formulas[ 'time-card-calculator' ] = function ( v ) {
		var rows = v.shifts || [];
		var rate = num( v.rate );
		var totalMins = 0;
		var detail = [];

		rows.forEach( function ( shift, i ) {
			var a = parseClock( shift.start );
			var b = parseClock( shift.end );

			if ( a === null || b === null ) { return; }

			var mins = b - a;
			if ( mins < 0 ) { mins += 1440; }
			mins = Math.max( mins - num( shift.brk ), 0 );
			totalMins += mins;

			if ( detail.length < 4 ) {
				detail.push( { label: 'Day ' + ( i + 1 ), value: hhmm( mins ) } );
			}
		} );

		var hours = totalMins / 60;
		var regular = Math.min( hours, 40 );
		var overtime = Math.max( hours - 40, 0 );

		return {
			label: 'Total for the week',
			value: hhmm( totalMins ),
			rows: detail.concat( [
				{ label: 'Decimal hours', value: decimals( hours, 2 ), divide: true },
				{ label: 'Regular hours', value: decimals( regular, 2 ) },
				{ label: 'Overtime hours', value: decimals( overtime, 2 ), emphasis: overtime > 0 },
				{ label: 'Gross pay', value: money2( regular * rate + overtime * rate * 1.5 ) }
			] ),
			note: 'Overtime is calculated at time and a half above forty hours in a week, which is the federal FLSA rule. Some states, California among them, also pay overtime above eight hours in a single day.'
		};
	};

	formulas[ 'overtime-calculator' ] = function ( v ) {
		var rate = num( v.rate );
		var regularHours = num( v.regular );
		var otHours = num( v.overtime );
		var doubleHours = num( v.doubleTime );
		var multiplier = num( v.multiplier ) || 1.5;

		var regularPay = regularHours * rate;
		var otPay = otHours * rate * multiplier;
		var doublePay = doubleHours * rate * 2;
		var total = regularPay + otPay + doublePay;

		return {
			label: 'Gross pay',
			value: money2( total ),
			bar: [
				{ pct: share( regularPay, total ), color: NEUTRAL },
				{ pct: share( otPay, total ), color: ACCENT },
				{ pct: share( doublePay, total ), color: WARN }
			],
			rows: [
				{ label: 'Regular, ' + decimals( regularHours, 2 ) + ' h', value: money2( regularPay ), color: NEUTRAL },
				{ label: 'Overtime at ' + decimals( multiplier, 2 ) + 'x', value: money2( otPay ), color: ACCENT },
				{ label: 'Double time', value: money2( doublePay ), color: WARN },
				{ label: 'Effective hourly rate', value: money2( total / Math.max( regularHours + otHours + doubleHours, 1 ) ), divide: true },
				{ label: 'Total hours', value: decimals( regularHours + otHours + doubleHours, 2 ) }
			],
			note: 'Under the federal FLSA, overtime is time and a half above forty hours in a week for non-exempt employees. Several states are more generous, so check your own before assuming forty is the threshold.'
		};
	};

	/* ---------- Geometry & Shapes ---------- */

	formulas[ 'area-calculator' ] = function ( v ) {
		var shape = v.shape || 'rectangle';
		var a, label;

		if ( 'circle' === shape )        { var r = num( v.radius ); a = Math.PI * r * r; label = 'Circle'; }
		else if ( 'triangle' === shape ) { a = 0.5 * num( v.base ) * num( v.heightVal ); label = 'Triangle'; }
		else if ( 'trapezoid' === shape ){ a = 0.5 * ( num( v.base ) + num( v.base2 ) ) * num( v.heightVal ); label = 'Trapezoid'; }
		else if ( 'ellipse' === shape )  { a = Math.PI * num( v.radius ) * num( v.radius2 ); label = 'Ellipse'; }
		else                             { a = num( v.length ) * num( v.width ); label = 'Rectangle'; }

		return {
			label: label + ' area',
			value: decimals( a, 4 ) + ' sq units',
			rows: [
				{ label: 'If units are feet', value: decimals( a, 2 ) + ' sq ft' },
				{ label: 'If units are metres', value: decimals( a, 2 ) + ' m2' },
				{ label: 'Square inches to square feet', value: decimals( a / 144, 4 ), divide: true }
			],
			note: 'Area is unitless here: whatever unit you measure in, the answer is in that unit squared. Mixing feet and inches in the same calculation is the usual source of a wrong answer.'
		};
	};

	formulas[ 'volume-calculator' ] = function ( v ) {
		var shape = v.shape || 'box';
		var vol, label;

		if ( 'cylinder' === shape )   { var r = num( v.radius ); vol = Math.PI * r * r * num( v.heightVal ); label = 'Cylinder'; }
		else if ( 'sphere' === shape ){ var rs = num( v.radius ); vol = ( 4 / 3 ) * Math.PI * rs * rs * rs; label = 'Sphere'; }
		else if ( 'cone' === shape )  { var rc = num( v.radius ); vol = ( 1 / 3 ) * Math.PI * rc * rc * num( v.heightVal ); label = 'Cone'; }
		else if ( 'pyramid' === shape ){ vol = ( 1 / 3 ) * num( v.length ) * num( v.width ) * num( v.heightVal ); label = 'Pyramid'; }
		else                          { vol = num( v.length ) * num( v.width ) * num( v.heightVal ); label = 'Box'; }

		return {
			label: label + ' volume',
			value: decimals( vol, 4 ) + ' cubic units',
			rows: [
				{ label: 'If units are feet', value: decimals( vol, 3 ) + ' cu ft' },
				{ label: 'That in cubic yards', value: decimals( vol / 27, 4 ) },
				{ label: 'If units are inches, in cubic feet', value: decimals( vol / 1728, 5 ), divide: true },
				{ label: 'If feet, in US gallons', value: decimals( vol * 7.48052, 2 ) }
			],
			note: 'A cone and a pyramid are each exactly one third of the box or cylinder that would enclose them, which is worth remembering as a sanity check.'
		};
	};

	formulas[ 'cubic-feet-calculator' ] = function ( v ) {
		var unit = v.unit || 'feet';
		var factor = { feet: 1, inches: 1 / 12, yards: 3, cm: 0.0328084, metres: 3.28084 }[ unit ] || 1;
		var cuft = ( num( v.length ) * factor ) * ( num( v.width ) * factor ) * ( num( v.heightVal ) * factor );
		var qty = Math.max( num( v.quantity ) || 1, 1 );
		cuft = cuft * qty;

		return {
			label: 'Volume',
			value: decimals( cuft, 3 ) + ' cu ft',
			rows: [
				{ label: 'Cubic yards', value: decimals( cuft / 27, 4 ) },
				{ label: 'Cubic metres', value: decimals( cuft * 0.0283168, 4 ) },
				{ label: 'Cubic inches', value: decimals( cuft * 1728, 0 ), divide: true },
				{ label: 'US gallons', value: decimals( cuft * 7.48052, 2 ) },
				{ label: 'Litres', value: decimals( cuft * 28.3168, 1 ) }
			],
			note: 'Shipping and storage are quoted in cubic feet, and a standard moving box is about 1.5. Freight is normally billed on whichever is greater, the volume or the weight.'
		};
	};

	formulas[ 'cylinder-volume-calculator' ] = function ( v ) {
		var useDia = 'diameter' === ( v.measure || 'radius' );
		var r = useDia ? num( v.diameter ) / 2 : num( v.radius );
		var h = num( v.heightVal );
		var vol = Math.PI * r * r * h;
		var side = 2 * Math.PI * r * h;
		var ends = 2 * Math.PI * r * r;

		return {
			label: 'Cylinder volume',
			value: decimals( vol, 4 ) + ' cubic units',
			rows: [
				{ label: 'Radius used', value: decimals( r, 4 ) },
				{ label: 'If units are inches, in gallons', value: decimals( vol / 231, 3 ) },
				{ label: 'If units are feet, in gallons', value: decimals( vol * 7.48052, 2 ), divide: true },
				{ label: 'Curved surface area', value: decimals( side, 3 ) },
				{ label: 'Total surface area', value: decimals( side + ends, 3 ) }
			],
			note: 'Doubling the radius quadruples the volume while doubling the height only doubles it, which is why wide tanks hold so much more than tall ones of the same material.'
		};
	};

	formulas[ 'circumference-calculator' ] = function ( v ) {
		var from = v.from || 'radius';
		var r;

		if ( 'diameter' === from )           { r = num( v.value ) / 2; }
		else if ( 'circumference' === from ) { r = num( v.value ) / ( 2 * Math.PI ); }
		else if ( 'area' === from )          { r = Math.sqrt( Math.max( num( v.value ), 0 ) / Math.PI ); }
		else                                 { r = num( v.value ); }

		return {
			label: 'Circumference',
			value: decimals( 2 * Math.PI * r, 6 ),
			rows: [
				{ label: 'Radius', value: decimals( r, 6 ) },
				{ label: 'Diameter', value: decimals( 2 * r, 6 ) },
				{ label: 'Area', value: decimals( Math.PI * r * r, 6 ), divide: true },
				{ label: 'Quarter turn along the edge', value: decimals( ( Math.PI * r ) / 2, 6 ) }
			],
			note: 'Every circle has the same ratio of circumference to diameter, and that ratio is pi. It is why one measurement of a circle gives you all the others.'
		};
	};

	formulas[ 'pythagorean-theorem-calculator' ] = function ( v ) {
		var solve = v.solve || 'c';
		var a = num( v.a ), b = num( v.b ), c = num( v.c );
		var answer, known;

		if ( 'c' === solve ) {
			answer = Math.sqrt( a * a + b * b );
			known = 'legs ' + decimals( a, 4 ) + ' and ' + decimals( b, 4 );
		} else if ( 'a' === solve ) {
			answer = c > b ? Math.sqrt( c * c - b * b ) : 0;
			known = 'hypotenuse ' + decimals( c, 4 ) + ', leg ' + decimals( b, 4 );
		} else {
			answer = c > a ? Math.sqrt( c * c - a * a ) : 0;
			known = 'hypotenuse ' + decimals( c, 4 ) + ', leg ' + decimals( a, 4 );
		}

		var sideA = 'a' === solve ? answer : a;
		var sideB = 'b' === solve ? answer : b;
		var sideC = 'c' === solve ? answer : c;

		return {
			label: 'Side ' + solve,
			value: decimals( answer, 6 ),
			rows: [
				{ label: 'From', value: known },
				{ label: 'Area of the triangle', value: decimals( 0.5 * sideA * sideB, 4 ) },
				{ label: 'Perimeter', value: decimals( sideA + sideB + sideC, 4 ), divide: true },
				{ label: 'Angle opposite a', value: sideC > 0 ? decimals( Math.asin( Math.min( sideA / sideC, 1 ) ) * 180 / Math.PI, 3 ) + '°' : '—' }
			],
			note: ( 'c' !== solve && c <= Math.max( a, b ) )
				? 'The hypotenuse must be the longest side, so those numbers cannot form a right triangle.'
				: 'Works only for right triangles. For any other triangle, the law of cosines is the tool.'
		};
	};

	formulas[ 'right-triangle-calculator' ] = function ( v ) {
		var a = num( v.a ), b = num( v.b );
		var c = Math.sqrt( a * a + b * b );

		if ( a <= 0 || b <= 0 ) {
			return { label: 'Right triangle', value: '—', rows: [], note: 'Enter both legs.' };
		}

		var angleA = Math.atan( a / b ) * 180 / Math.PI;

		return {
			label: 'Hypotenuse',
			value: decimals( c, 6 ),
			rows: [
				{ label: 'Angle A', value: decimals( angleA, 3 ) + '°' },
				{ label: 'Angle B', value: decimals( 90 - angleA, 3 ) + '°' },
				{ label: 'Angle C', value: '90°', divide: true },
				{ label: 'Area', value: decimals( 0.5 * a * b, 4 ) },
				{ label: 'Perimeter', value: decimals( a + b + c, 4 ) },
				{ label: 'Inradius', value: decimals( ( a + b - c ) / 2, 4 ) }
			],
			note: 'The three angles always total 180 degrees, and one of them is fixed at 90, so knowing one of the other two gives you the third for free.'
		};
	};

	formulas[ 'triangle-calculator' ] = function ( v ) {
		var a = num( v.a ), b = num( v.b ), c = num( v.c );

		if ( a <= 0 || b <= 0 || c <= 0 ) {
			return { label: 'Triangle', value: '—', rows: [], note: 'Enter all three sides.' };
		}

		/* The triangle inequality: any two sides must exceed the third, or the
		   shape cannot close. */
		if ( a + b <= c || a + c <= b || b + c <= a ) {
			return {
				label: 'Triangle',
				value: 'impossible',
				rows: [ { label: 'Sides given', value: [ a, b, c ].join( ', ' ) } ],
				note: 'Those three lengths cannot form a triangle, because any two sides must add up to more than the third.'
			};
		}

		var sp = ( a + b + c ) / 2;
		var area = Math.sqrt( sp * ( sp - a ) * ( sp - b ) * ( sp - c ) );
		var A = Math.acos( ( b * b + c * c - a * a ) / ( 2 * b * c ) ) * 180 / Math.PI;
		var B = Math.acos( ( a * a + c * c - b * b ) / ( 2 * a * c ) ) * 180 / Math.PI;

		var kind = Math.abs( A - 90 ) < 0.001 || Math.abs( B - 90 ) < 0.001 || Math.abs( 180 - A - B - 90 ) < 0.001
			? 'Right' : ( a === b && b === c ? 'Equilateral' : ( a === b || b === c || a === c ? 'Isosceles' : 'Scalene' ) );

		return {
			label: kind + ' triangle area',
			value: decimals( area, 6 ),
			rows: [
				{ label: 'Angle A', value: decimals( A, 3 ) + '°' },
				{ label: 'Angle B', value: decimals( B, 3 ) + '°' },
				{ label: 'Angle C', value: decimals( 180 - A - B, 3 ) + '°' },
				{ label: 'Perimeter', value: decimals( a + b + c, 4 ), divide: true },
				{ label: 'Semi-perimeter', value: decimals( sp, 4 ) },
				{ label: 'Height on side a', value: decimals( ( 2 * area ) / a, 4 ) }
			],
			note: 'Area is found with Heron’s formula, which needs only the three sides and no angle at all.'
		};
	};

	formulas[ 'distance-calculator' ] = function ( v ) {
		var mode = v.mode || 'plane';

		if ( 'coordinates' === mode ) {
			/* Haversine on a spherical earth: accurate to about 0.5%, which is
			   well inside the error of most coordinates people type in. */
			var R = 3958.8;
			var toRad = function ( d ) { return d * Math.PI / 180; };
			var lat1 = num( v.lat1 ), lon1 = num( v.lon1 ), lat2 = num( v.lat2 ), lon2 = num( v.lon2 );
			var dLat = toRad( lat2 - lat1 ), dLon = toRad( lon2 - lon1 );
			var h = Math.sin( dLat / 2 ) * Math.sin( dLat / 2 )
				+ Math.cos( toRad( lat1 ) ) * Math.cos( toRad( lat2 ) ) * Math.sin( dLon / 2 ) * Math.sin( dLon / 2 );
			var miles = 2 * R * Math.asin( Math.min( Math.sqrt( h ), 1 ) );

			return {
				label: 'Great circle distance',
				value: decimals( miles, 2 ) + ' miles',
				rows: [
					{ label: 'Kilometres', value: decimals( miles * 1.609344, 2 ) },
					{ label: 'Nautical miles', value: decimals( miles * 0.868976, 2 ) },
					{ label: 'Metres', value: decimals( miles * 1609.344, 0 ), divide: true }
				],
				note: 'This is the straight line over the earth’s surface, not a driving distance. A road route is typically twenty to forty per cent longer.'
			};
		}

		var x1 = num( v.x1 ), y1 = num( v.y1 ), x2 = num( v.x2 ), y2 = num( v.y2 );
		var dx = x2 - x1, dy = y2 - y1;
		var d = Math.sqrt( dx * dx + dy * dy );

		return {
			label: 'Distance between the points',
			value: decimals( d, 6 ),
			rows: [
				{ label: 'Horizontal change', value: decimals( dx, 4 ) },
				{ label: 'Vertical change', value: decimals( dy, 4 ) },
				{ label: 'Midpoint', value: '(' + decimals( ( x1 + x2 ) / 2, 4 ) + ', ' + decimals( ( y1 + y2 ) / 2, 4 ) + ')', divide: true },
				{ label: 'Slope', value: dx === 0 ? 'undefined' : decimals( dy / dx, 4 ) }
			],
			note: 'The distance formula is the Pythagorean theorem with the horizontal and vertical changes as the two legs.'
		};
	};

	/* ---------- Unit conversion ---------- */

	var LENGTH = { mm: 0.001, cm: 0.01, m: 1, km: 1000, in: 0.0254, ft: 0.3048, yd: 0.9144, mi: 1609.344 };
	var MASS = { mg: 0.000001, g: 0.001, kg: 1, t: 1000, oz: 0.0283495, lb: 0.453592, st: 6.35029, ton: 907.185 };

	function convert( value, from, to, table ) {
		if ( ! table[ from ] || ! table[ to ] ) { return 0; }
		return ( value * table[ from ] ) / table[ to ];
	}

	formulas[ 'celsius-to-fahrenheit-calculator' ] = function ( v ) {
		var t = num( v.temperature );
		var from = v.from || 'c';
		var c = 'f' === from ? ( t - 32 ) * 5 / 9 : ( 'k' === from ? t - 273.15 : t );

		return {
			label: decimals( t, 2 ) + '°' + from.toUpperCase() + ' converted',
			value: decimals( ( c * 9 / 5 ) + 32, 2 ) + '°F',
			rows: [
				{ label: 'Celsius', value: decimals( c, 2 ) + '°C' },
				{ label: 'Fahrenheit', value: decimals( ( c * 9 / 5 ) + 32, 2 ) + '°F' },
				{ label: 'Kelvin', value: decimals( c + 273.15, 2 ) + ' K', divide: true },
				{ label: 'Reference', value: c <= 0 ? 'at or below freezing' : ( c >= 100 ? 'at or above boiling' : 'liquid water range' ) }
			],
			note: 'The two scales meet at minus forty, which is the same temperature in Celsius and Fahrenheit and the only point where they agree.'
		};
	};

	formulas[ 'weight-converter' ] = function ( v ) {
		var value = num( v.value );
		var from = v.from || 'kg';
		var kg = ( MASS[ from ] || 1 ) * value;

		return {
			label: decimals( value, 4 ) + ' ' + from + ' converted',
			value: decimals( convert( value, from, v.to || 'lb', MASS ), 4 ) + ' ' + ( v.to || 'lb' ),
			rows: [
				{ label: 'Kilograms', value: decimals( kg, 4 ) },
				{ label: 'Pounds', value: decimals( kg / MASS.lb, 4 ) },
				{ label: 'Ounces', value: decimals( kg / MASS.oz, 3 ) },
				{ label: 'Grams', value: decimals( kg / MASS.g, 2 ), divide: true },
				{ label: 'Stone', value: decimals( kg / MASS.st, 4 ) },
				{ label: 'US tons', value: decimals( kg / MASS.ton, 5 ) }
			],
			note: 'A US ton is 2,000 pounds and a metric tonne is 1,000 kilograms, which is about 2,205 pounds. They differ by roughly ten per cent, so the spelling matters on an invoice.'
		};
	};

	formulas[ 'unit-converter' ] = function ( v ) {
		var value = num( v.value );
		var from = v.from || 'm';
		var metres = ( LENGTH[ from ] || 1 ) * value;

		return {
			label: decimals( value, 4 ) + ' ' + from + ' converted',
			value: decimals( convert( value, from, v.to || 'ft', LENGTH ), 6 ) + ' ' + ( v.to || 'ft' ),
			rows: [
				{ label: 'Millimetres', value: decimals( metres / LENGTH.mm, 2 ) },
				{ label: 'Centimetres', value: decimals( metres / LENGTH.cm, 3 ) },
				{ label: 'Metres', value: decimals( metres, 5 ) },
				{ label: 'Inches', value: decimals( metres / LENGTH.in, 4 ), divide: true },
				{ label: 'Feet', value: decimals( metres / LENGTH.ft, 5 ) },
				{ label: 'Miles', value: decimals( metres / LENGTH.mi, 7 ) }
			],
			note: 'An inch has been defined as exactly 25.4 millimetres since 1959, so every conversion between the two systems is exact rather than approximate.'
		};
	};

	function simpleLength( v, fromUnit, toUnit, places ) {
		var value = num( v.value );
		var out = convert( value, fromUnit, toUnit, LENGTH );
		var metres = value * LENGTH[ fromUnit ];

		return {
			label: decimals( value, 4 ) + ' ' + fromUnit + ' in ' + toUnit,
			value: decimals( out, places ) + ' ' + toUnit,
			rows: [
				{ label: 'Centimetres', value: decimals( metres / LENGTH.cm, 4 ) },
				{ label: 'Metres', value: decimals( metres, 5 ) },
				{ label: 'Inches', value: decimals( metres / LENGTH.in, 5 ), divide: true },
				{ label: 'Feet', value: decimals( metres / LENGTH.ft, 5 ) }
			]
		};
	}

	formulas[ 'mm-to-inches-calculator' ] = function ( v ) {
		var r = simpleLength( v, 'mm', 'in', 5 );
		r.note = 'Divide millimetres by 25.4 to get inches. For a quick mental check, 25 mm is almost exactly an inch.';
		return r;
	};

	formulas[ 'inches-to-feet-calculator' ] = function ( v ) {
		var inches = num( v.value );
		var feet = Math.floor( Math.abs( inches ) / 12 ) * ( inches < 0 ? -1 : 1 );
		var rem = Math.abs( inches ) % 12;
		var r = simpleLength( v, 'in', 'ft', 5 );
		r.value = decimals( inches / 12, 5 ) + ' ft';
		r.rows.unshift( { label: 'Feet and inches', value: feet + ' ft ' + decimals( rem, 3 ) + ' in' } );
		r.note = 'Twelve inches to the foot, so the decimal part of the answer is not inches: 6.5 feet is six feet and six inches, not six feet five.';
		return r;
	};

	formulas[ 'feet-to-meters-calculator' ] = function ( v ) {
		var r = simpleLength( v, 'ft', 'm', 6 );
		r.note = 'A foot is exactly 0.3048 metres by definition, so three feet is a little under a metre and the two are close enough that people often confuse them.';
		return r;
	};

	formulas[ 'tire-size-calculator' ] = function ( v ) {
		var w = num( v.width );
		var ratio = num( v.ratio );
		var rim = num( v.rim );

		if ( w <= 0 || rim <= 0 ) {
			return { label: 'Tyre diameter', value: '—', rows: [], note: 'Enter a size such as 225/45R17.' };
		}

		var sidewallMm = w * ( ratio / 100 );
		var diameterIn = ( sidewallMm * 2 / 25.4 ) + rim;
		var circumference = Math.PI * diameterIn;
		var revsPerMile = circumference > 0 ? 63360 / circumference : 0;

		var rows = [
			{ label: 'Sidewall height', value: decimals( sidewallMm, 1 ) + ' mm' },
			{ label: 'Circumference', value: decimals( circumference, 2 ) + ' in' },
			{ label: 'Revolutions per mile', value: decimals( revsPerMile, 0 ), divide: true }
		];

		var w2 = num( v.width2 ), r2 = num( v.ratio2 ), rim2 = num( v.rim2 );

		if ( w2 > 0 && rim2 > 0 ) {
			var d2 = ( ( w2 * ( r2 / 100 ) ) * 2 / 25.4 ) + rim2;
			var diff = ( ( d2 - diameterIn ) / diameterIn ) * 100;
			rows.push( { label: 'Comparison diameter', value: decimals( d2, 3 ) + ' in' } );
			rows.push( { label: 'Difference', value: decimals( diff, 2 ) + '%', emphasis: Math.abs( diff ) > 3 } );
			rows.push( { label: 'Speedo reads 60, actual', value: decimals( 60 * ( d2 / diameterIn ), 1 ) + ' mph' } );
		}

		return {
			label: 'Overall diameter',
			value: decimals( diameterIn, 3 ) + ' in',
			rows: rows,
			note: 'Stay within about three per cent of the original diameter. Beyond that the speedometer, odometer and in many cars the traction control all read wrong, and the tyre may foul the arch on full lock.'
		};
	};

	/* ---------- Finance ---------- */

	formulas[ 'take-home-pay-calculator' ] = function ( v ) {
		var gross = num( v.gross );
		var per = v.frequency || 'year';
		var PERIODS = { week: 52, biweek: 26, semimonth: 24, month: 12, year: 1 };
		var periods = PERIODS[ per ] || 1;
		var annualGross = gross * periods;

		var preTax = num( v.retirement ) / 100 * annualGross + num( v.healthAnnual );
		var taxable = Math.max( annualGross - preTax, 0 );

		/* FICA is the stable part: 6.2% Social Security to the wage base plus
		   1.45% Medicare with no cap. Income tax rates are asked for rather
		   than assumed, because brackets change every year and depend on
		   filing status, and a stale bracket table is worse than no table. */
		var ssWageBase = num( v.wageBase ) || 168600;
		var socialSecurity = Math.min( annualGross - num( v.healthAnnual ), ssWageBase ) * 0.062;
		var medicare = ( annualGross - num( v.healthAnnual ) ) * 0.0145;
		var federal = taxable * ( num( v.federalRate ) / 100 );
		var state = taxable * ( num( v.stateRate ) / 100 );

		var net = annualGross - preTax - socialSecurity - medicare - federal - state;

		return {
			label: 'Take-home per ' + ( 'year' === per ? 'year' : per.replace( 'biweek', 'two weeks' ).replace( 'semimonth', 'half month' ) ),
			value: money2( net / periods ),
			bar: [
				{ pct: share( net, annualGross ), color: ACCENT },
				{ pct: share( federal + state, annualGross ), color: WARN },
				{ pct: share( socialSecurity + medicare + preTax, annualGross ), color: NEUTRAL }
			],
			rows: [
				{ label: 'Annual take-home', value: money( net ), color: ACCENT },
				{ label: 'Income tax', value: money( federal + state ), color: WARN, emphasis: true },
				{ label: 'Social Security & Medicare', value: money( socialSecurity + medicare ), color: NEUTRAL },
				{ label: 'Pre-tax deductions', value: money( preTax ), divide: true },
				{ label: 'Gross annual', value: money( annualGross ) },
				{ label: 'Effective total rate', value: annualGross > 0 ? decimals( ( 1 - net / annualGross ) * 100, 2 ) + '%' : '—' }
			],
			note: 'Income tax rates are entered rather than looked up, because brackets change yearly and depend on filing status and credits. For a precise figure use the IRS withholding estimator; for planning, your last return’s effective rate is the number to put in.'
		};
	};

	formulas[ 'pay-raise-calculator' ] = function ( v ) {
		var current = num( v.current );
		var mode = v.mode || 'percent';
		var newPay = 'percent' === mode
			? current * ( 1 + num( v.percent ) / 100 )
			: num( v.newSalary );
		var diff = newPay - current;
		var pct = current > 0 ? ( diff / current ) * 100 : 0;
		var inflation = num( v.inflation );
		var real = pct - inflation;

		return {
			label: 'New salary',
			value: money( newPay ),
			rows: [
				{ label: 'Increase', value: money( diff ), color: ACCENT },
				{ label: 'Percentage raise', value: decimals( pct, 2 ) + '%' },
				{ label: 'Extra per month', value: money2( diff / 12 ), divide: true },
				{ label: 'Extra per week', value: money2( diff / 52 ) },
				{ label: 'Real raise after ' + decimals( inflation, 1 ) + '% inflation', value: decimals( real, 2 ) + '%', emphasis: real < 0 }
			],
			note: real < 0
				? 'Below inflation, which means a pay cut in real terms even though the number on the payslip went up.'
				: 'A raise only counts once inflation is taken out, which is why the real figure matters more than the headline one.'
		};
	};

	formulas[ 'military-pay-calculator' ] = function ( v ) {
		var basePay = num( v.basePay );
		var bah = num( v.bah );
		var bas = num( v.bas );
		var special = num( v.special );

		var taxable = basePay + special;
		var untaxed = bah + bas;
		var gross = taxable + untaxed;
		var rate = num( v.taxRate ) / 100;

		/* Allowances are not taxed, so their real value is higher than the
		   same amount of base pay. Grossing them up is how the services and
		   the RMC calculator express it. */
		var grossedUp = rate < 1 ? untaxed / ( 1 - rate ) : untaxed;
		var rmc = taxable + grossedUp;

		return {
			label: 'Monthly gross',
			value: money2( gross ),
			bar: [
				{ pct: share( taxable, gross ), color: ACCENT },
				{ pct: share( untaxed, gross ), color: NEUTRAL }
			],
			rows: [
				{ label: 'Taxable pay', value: money2( taxable ), color: ACCENT },
				{ label: 'Tax-free allowances', value: money2( untaxed ), color: NEUTRAL },
				{ label: 'Annual gross', value: money( gross * 12 ), divide: true },
				{ label: 'Allowances grossed up', value: money2( grossedUp ) },
				{ label: 'Regular Military Compensation', value: money2( rmc ), emphasis: true },
				{ label: 'RMC annualised', value: money( rmc * 12 ) }
			],
			note: 'Base pay, BAH and BAS are entered rather than looked up, because the tables change every January and BAH depends on duty ZIP code and dependant status. Take the current figures from your LES or the DFAS pay tables. Regular Military Compensation is the honest number to compare against a civilian salary, because it reflects that the allowances arrive untaxed.'
		};
	};

	formulas[ '401k-calculator' ] = function ( v ) {
		var salary = num( v.salary );
		var contribPct = num( v.contribution ) / 100;
		var matchPct = num( v.match ) / 100;
		var matchLimit = num( v.matchLimit ) / 100;
		var yearCount = years( v.years );
		var rate = num( v.rate ) / 100;
		var current = num( v.current );
		var raise = num( v.raise ) / 100;

		var balance = current, contributed = current, employer = 0;

		for ( var y = 0; y < yearCount; y++ ) {
			var pay = salary * Math.pow( 1 + raise, y );
			var mine = pay * contribPct;
			var theirs = pay * Math.min( contribPct, matchLimit ) * matchPct;
			/* Contributions are spread through the year, so half a year of
			   growth on them is closer than crediting them on day one. */
			balance = ( balance + mine + theirs ) * ( 1 + rate ) - ( mine + theirs ) * rate / 2;
			contributed += mine;
			employer += theirs;
		}

		return {
			label: 'Balance after ' + decimals( yearCount, 0 ) + ' years',
			value: money( balance ),
			bar: [
				{ pct: share( contributed, balance ), color: NEUTRAL },
				{ pct: share( employer, balance ), color: WARN },
				{ pct: share( balance - contributed - employer, balance ), color: ACCENT }
			],
			rows: [
				{ label: 'Your contributions', value: money( contributed ), color: NEUTRAL },
				{ label: 'Employer match', value: money( employer ), color: WARN },
				{ label: 'Investment growth', value: money( balance - contributed - employer ), color: ACCENT },
				{ label: 'First year contribution', value: money( salary * contribPct ), divide: true },
				{ label: 'First year match', value: money( salary * Math.min( contribPct, matchLimit ) * matchPct ) }
			],
			note: contribPct < matchLimit
				? 'You are contributing less than your employer will match, so you are leaving free money behind. Raising the contribution to the match limit is the highest guaranteed return available anywhere.'
				: 'Contributing at or above the match limit, which is the first thing to get right before anything else in a retirement plan.'
		};
	};

	formulas[ 'roth-ira-calculator' ] = function ( v ) {
		var current = num( v.current );
		var annual = num( v.annual );
		var yearCount = years( v.years );
		var rate = num( v.rate ) / 100;
		var taxRate = num( v.taxRate ) / 100;

		var balance = current, contributed = current;

		for ( var y = 0; y < yearCount; y++ ) {
			balance = ( balance + annual ) * ( 1 + rate ) - annual * rate / 2;
			contributed += annual;
		}

		var growth = balance - contributed;
		/* The Roth's whole point: the growth is never taxed on withdrawal,
		   so the saving is the tax that a traditional account would owe. */
		var taxSaved = growth * taxRate;

		return {
			label: 'Tax-free balance at retirement',
			value: money( balance ),
			bar: [
				{ pct: share( contributed, balance ), color: NEUTRAL },
				{ pct: share( growth, balance ), color: ACCENT }
			],
			rows: [
				{ label: 'You contributed', value: money( contributed ), color: NEUTRAL },
				{ label: 'Tax-free growth', value: money( growth ), color: ACCENT },
				{ label: 'Tax avoided on the growth', value: money( taxSaved ), divide: true, emphasis: true },
				{ label: 'Annual contribution', value: money( annual ) }
			],
			note: 'Roth contributions are made from money already taxed, so nothing is deducted now and nothing is owed on the growth later. That trade favours you when your tax rate in retirement is higher than it is today, which is usually the case early in a career.'
		};
	};

	formulas[ 'cd-calculator' ] = function ( v ) {
		var principal = num( v.principal );
		var apy = num( v.rate ) / 100;
		var months = num( v.months );
		var compounds = num( v.compounds ) || 12;

		var years = months / 12;
		var periodRate = apy / compounds;
		var balance = principal * Math.pow( 1 + periodRate, compounds * years );
		var interest = balance - principal;
		var effectiveApy = years > 0 ? ( Math.pow( balance / principal, 1 / years ) - 1 ) * 100 : 0;

		return {
			label: 'Value at maturity',
			value: money2( balance ),
			rows: [
				{ label: 'Interest earned', value: money2( interest ), color: ACCENT },
				{ label: 'Principal', value: money2( principal ), color: NEUTRAL },
				{ label: 'Effective annual yield', value: decimals( effectiveApy, 3 ) + '%', divide: true },
				{ label: 'Term', value: decimals( months, 0 ) + ' months' },
				{ label: 'Interest per month, averaged', value: months > 0 ? money2( interest / months ) : '—' }
			],
			note: 'The rate is locked for the term, which is the point of a CD and also its cost: withdrawing early usually forfeits several months of interest, and you cannot take advantage if rates rise.'
		};
	};

	formulas[ 'dividend-calculator' ] = function ( v ) {
		var shares = num( v.shares );
		var price = num( v.price );
		var dividend = num( v.dividend );
		var frequency = num( v.frequency ) || 4;
		var growth = num( v.growth ) / 100;
		var yearCount = years( v.years );
		var reinvest = 'yes' === ( v.reinvest || 'yes' );

		var invested = shares * price;
		var annualIncome = shares * dividend * frequency;
		var yieldPct = invested > 0 ? ( annualIncome / invested ) * 100 : 0;

		var s = shares, perShare = dividend * frequency, p = price, totalIncome = 0;

		for ( var y = 0; y < yearCount; y++ ) {
			var income = s * perShare;
			totalIncome += income;
			if ( reinvest && p > 0 ) { s += income / p; }
			perShare = perShare * ( 1 + growth );
			p = p * ( 1 + growth );
		}

		return {
			label: 'Annual dividend income',
			value: money2( annualIncome ),
			rows: [
				{ label: 'Dividend yield', value: decimals( yieldPct, 3 ) + '%' },
				{ label: 'Per payment', value: money2( annualIncome / frequency ) },
				{ label: 'Invested', value: money2( invested ), divide: true },
				{ label: 'Income over ' + decimals( yearCount, 0 ) + ' years', value: money( totalIncome ) },
				{ label: 'Shares after ' + decimals( yearCount, 0 ) + ' years', value: decimals( s, 2 ) },
				{ label: 'Portfolio value then', value: money( s * p ) }
			],
			note: reinvest
				? 'Reinvesting means each payment buys more shares, which then pay their own dividends. That compounding is where most of the long-run return in dividend investing comes from.'
				: 'Taking the income as cash means the share count never grows, so the only increase comes from the company raising its dividend.'
		};
	};

	formulas[ 'future-value-calculator' ] = function ( v ) {
		var present = num( v.present );
		var payment = num( v.payment );
		var rate = num( v.rate ) / 100;
		var termYears = years( v.years );
		var perYear = num( v.frequency ) || 12;

		var i = rate / perYear;
		var n = termYears * perYear;
		var fv;

		if ( i === 0 ) {
			fv = present + payment * n;
		} else {
			var g = Math.pow( 1 + i, n );
			fv = present * g + payment * ( ( g - 1 ) / i );
		}

		var paidIn = present + payment * n;
		var inflation = num( v.inflation ) / 100;
		var realValue = termYears > 0 ? fv / Math.pow( 1 + inflation, termYears ) : fv;

		return {
			label: 'Future value',
			value: money( fv ),
			bar: [
				{ pct: share( paidIn, fv ), color: NEUTRAL },
				{ pct: share( fv - paidIn, fv ), color: ACCENT }
			],
			rows: [
				{ label: 'Total paid in', value: money( paidIn ), color: NEUTRAL },
				{ label: 'Interest earned', value: money( fv - paidIn ), color: ACCENT },
				{ label: 'Periods', value: decimals( n, 0 ), divide: true },
				{ label: 'Worth in today’s money', value: money( realValue ), emphasis: true }
			],
			note: 'The figure in today’s money is the one worth planning against, because a balance that looks large in thirty years buys considerably less than the same number does now.'
		};
	};

	/* ---------- Loans ---------- */

	function loanResult( label, principal, rate, term, extra ) {
		var payment = monthlyPayment( principal, rate, term );
		var monthlyRate = ( rate / 100 ) / 12;
		var balance = principal, months = 0, interest = 0;
		var total = payment + ( extra || 0 );
		/* Twice the nominal term, and never more than a century of months
		   whatever was typed into the term field. */
		var cap = Math.min( term * 12 * 2 + 12, 1200 );

		while ( balance > 0 && months < cap ) {
			var monthInterest = balance * monthlyRate;
			var principalPart = total - monthInterest;
			if ( principalPart <= 0 ) { months = 0; interest = 0; break; }
			balance -= principalPart;
			interest += monthInterest;
			months++;
			if ( balance < 0 ) { interest += balance * 0; balance = 0; }
		}

		return { payment: payment, months: months, interest: interest, total: total };
	}

	formulas[ 'amortization-calculator' ] = function ( v ) {
		var principal = num( v.principal );
		var rate = num( v.rate );
		var term = years( v.years );
		var payment = monthlyPayment( principal, rate, term );
		var n = term * 12;
		var monthlyRate = ( rate / 100 ) / 12;

		var balance = principal, firstInterest = balance * monthlyRate;
		var yearOneInterest = 0, yearOnePrincipal = 0;

		for ( var m = 0; m < Math.min( 12, n ); m++ ) {
			var mi = balance * monthlyRate;
			yearOneInterest += mi;
			yearOnePrincipal += payment - mi;
			balance -= payment - mi;
		}

		var totalPaid = payment * n;

		return {
			label: 'Monthly payment',
			value: money2( payment ),
			bar: [
				{ pct: share( principal, totalPaid ), color: ACCENT },
				{ pct: share( totalPaid - principal, totalPaid ), color: WARN }
			],
			rows: [
				{ label: 'Total interest', value: money( totalPaid - principal ), color: WARN, emphasis: true },
				{ label: 'Total paid', value: money( totalPaid ), color: ACCENT },
				{ label: 'First payment interest', value: money2( firstInterest ), divide: true },
				{ label: 'First payment principal', value: money2( payment - firstInterest ) },
				{ label: 'Year one interest', value: money( yearOneInterest ) },
				{ label: 'Balance after a year', value: money( Math.max( balance, 0 ) ) }
			],
			note: 'The payment never changes but its split does. Early on almost all of it is interest, which is why the balance seems to barely move for the first few years.'
		};
	};

	formulas[ 'mortgage-payoff-calculator' ] = function ( v ) {
		var balance = num( v.balance );
		var rate = num( v.rate );
		var yearsLeft = years( v.yearsLeft );
		var extra = num( v.extra );

		var base = loanResult( 'base', balance, rate, yearsLeft, 0 );
		var faster = loanResult( 'faster', balance, rate, yearsLeft, extra );

		var monthsSaved = Math.max( base.months - faster.months, 0 );
		var interestSaved = Math.max( base.interest - faster.interest, 0 );

		return {
			label: 'Paid off in',
			value: Math.floor( faster.months / 12 ) + ' yr ' + ( faster.months % 12 ) + ' mo',
			rows: [
				{ label: 'Interest saved', value: money( interestSaved ), color: ACCENT, emphasis: true },
				{ label: 'Time saved', value: Math.floor( monthsSaved / 12 ) + ' yr ' + ( monthsSaved % 12 ) + ' mo' },
				{ label: 'New monthly payment', value: money2( faster.total ), divide: true },
				{ label: 'Original payment', value: money2( base.payment ) },
				{ label: 'Interest without overpaying', value: money( base.interest ) },
				{ label: 'Interest when overpaying', value: money( faster.interest ) }
			],
			note: extra > 0
				? 'Every extra dollar goes straight at the principal, which removes its interest from every remaining month. That is why overpaying early saves so much more than overpaying later.'
				: 'Enter an extra monthly amount to see what it saves. Even a small regular overpayment shortens the term noticeably.'
		};
	};

	function plainLoan( v, noteText ) {
		var principal = num( v.amount ) - num( v.down );
		var rate = num( v.rate );
		var term = years( v.years );
		var fees = num( v.fees );
		var payment = monthlyPayment( principal + fees, rate, term );
		var n = term * 12;
		var total = payment * n;

		return {
			label: 'Monthly payment',
			value: money2( payment ),
			bar: [
				{ pct: share( principal + fees, total ), color: ACCENT },
				{ pct: share( total - principal - fees, total ), color: WARN }
			],
			rows: [
				{ label: 'Amount financed', value: money( principal + fees ), color: ACCENT },
				{ label: 'Total interest', value: money( total - principal - fees ), color: WARN, emphasis: true },
				{ label: 'Total of payments', value: money( total ), divide: true },
				{ label: 'Number of payments', value: decimals( n, 0 ) },
				{ label: 'Cost per dollar borrowed', value: principal + fees > 0 ? money2( total / ( principal + fees ) ) : '—' }
			],
			note: noteText
		};
	}

	formulas[ 'personal-loan-calculator' ] = function ( v ) {
		return plainLoan( v, 'Personal loans are unsecured, so the rate is driven almost entirely by your credit score. Origination fees are often deducted from the amount you receive rather than added to the balance, so check which way your lender does it.' );
	};

	formulas[ 'boat-loan-calculator' ] = function ( v ) {
		return plainLoan( v, 'Boat loans run longer than car loans, commonly fifteen to twenty years, which keeps the payment low and the total interest high. Budget separately for mooring, insurance, winterising and maintenance, which together often exceed the loan payment.' );
	};

	formulas[ 'heloc-calculator' ] = function ( v ) {
		var value = num( v.value );
		var owed = num( v.owed );
		var ltv = num( v.ltv ) / 100;
		var rate = num( v.rate );
		var draw = num( v.draw );

		var maxBorrow = Math.max( value * ltv - owed, 0 );
		var equity = Math.max( value - owed, 0 );
		var used = Math.min( draw, maxBorrow );
		var interestOnly = used * ( rate / 100 ) / 12;
		var repayment = monthlyPayment( used, rate, 20 );

		return {
			label: 'Available credit line',
			value: money( maxBorrow ),
			rows: [
				{ label: 'Home equity', value: money( equity ) },
				{ label: 'Current loan-to-value', value: value > 0 ? decimals( ( owed / value ) * 100, 1 ) + '%' : '—' },
				{ label: 'Interest-only payment on ' + money( used ), value: money2( interestOnly ), divide: true, emphasis: true },
				{ label: 'Repayment phase, 20 years', value: money2( repayment ) },
				{ label: 'Payment shock', value: money2( repayment - interestOnly ) }
			],
			note: 'The draw period is usually interest-only, and the jump when repayment starts catches people out badly. The payment shock row is the size of that jump. A HELOC is also secured on your home, so the downside of not paying is losing it.'
		};
	};

	/* ---------- Business & Shopping ---------- */

	formulas[ 'percent-off-calculator' ] = function ( v ) {
		var price = num( v.price );
		var off = num( v.percent );
		var pay = price * ( 1 - off / 100 );

		return {
			label: 'You pay',
			value: money2( pay ),
			bar: [
				{ pct: share( pay, price ), color: ACCENT },
				{ pct: share( price - pay, price ), color: WARN }
			],
			rows: [
				{ label: 'Original price', value: money2( price ) },
				{ label: 'You save', value: money2( price - pay ), color: WARN, emphasis: true },
				{ label: 'Percent off', value: decimals( off, 2 ) + '%', divide: true }
			],
			note: 'Half off is the only discount most people can do reliably in their head. For everything else, take ten per cent and scale it.'
		};
	};

	formulas[ 'markup-calculator' ] = function ( v ) {
		var cost = num( v.cost );
		var markup = num( v.markup );
		var price = cost * ( 1 + markup / 100 );
		var profit = price - cost;
		var margin = price > 0 ? ( profit / price ) * 100 : 0;

		return {
			label: 'Selling price',
			value: money2( price ),
			rows: [
				{ label: 'Cost', value: money2( cost ) },
				{ label: 'Profit per unit', value: money2( profit ), color: ACCENT },
				{ label: 'Markup', value: decimals( markup, 2 ) + '%', divide: true },
				{ label: 'Resulting margin', value: decimals( margin, 2 ) + '%', emphasis: true }
			],
			note: 'Markup and margin are not the same number and confusing them is how businesses quietly underprice. A 50% markup is only a 33% margin, because markup is measured against cost and margin against the selling price.'
		};
	};

	formulas[ 'margin-calculator' ] = function ( v ) {
		var cost = num( v.cost );
		var margin = num( v.margin );
		var price = margin < 100 ? cost / ( 1 - margin / 100 ) : 0;
		var profit = price - cost;
		var markup = cost > 0 ? ( profit / cost ) * 100 : 0;

		return {
			label: 'Price for a ' + decimals( margin, 1 ) + '% margin',
			value: money2( price ),
			rows: [
				{ label: 'Cost', value: money2( cost ) },
				{ label: 'Gross profit', value: money2( profit ), color: ACCENT },
				{ label: 'Equivalent markup', value: decimals( markup, 2 ) + '%', divide: true, emphasis: true },
				{ label: 'Cost as a share of price', value: price > 0 ? decimals( ( cost / price ) * 100, 2 ) + '%' : '—' }
			],
			note: margin >= 100
				? 'A margin of 100% or more is impossible, because the cost would have to be zero or negative.'
				: 'Margin is profit as a share of the selling price, which is the figure that matters for a profit and loss account. Markup is the same profit measured against cost.'
		};
	};

	function taxFormula( name, defaultRate ) {
		return function ( v ) {
			var amount = num( v.amount );
			var rate = num( v.rate ) / 100;
			var adding = 'add' === ( v.mode || 'add' );
			var net = adding ? amount : amount / ( 1 + rate );
			var gross = adding ? amount * ( 1 + rate ) : amount;
			var tax = gross - net;

			return {
				label: adding ? 'Total including ' + name : 'Price before ' + name,
				value: money2( adding ? gross : net ),
				bar: [
					{ pct: share( net, gross ), color: NEUTRAL },
					{ pct: share( tax, gross ), color: ACCENT }
				],
				rows: [
					{ label: 'Excluding ' + name, value: money2( net ), color: NEUTRAL },
					{ label: name + ' amount', value: money2( tax ), color: ACCENT },
					{ label: 'Including ' + name, value: money2( gross ), divide: true },
					{ label: 'Rate applied', value: decimals( num( v.rate ), 2 ) + '%' }
				],
				note: 'Removing ' + name + ' means dividing by one plus the rate, not subtracting the percentage. Subtracting gives a number that is always too low, and the gap grows with the size of the bill.'
			};
		};
	}

	formulas[ 'vat-calculator' ] = taxFormula( 'VAT', 20 );
	formulas[ 'gst-calculator' ] = taxFormula( 'GST', 10 );

	formulas[ 'ebay-fee-calculator' ] = function ( v ) {
		var price = num( v.price );
		var shipping = num( v.shipping );
		var cost = num( v.cost );
		var shipCost = num( v.shipCost );
		var feeRate = num( v.feeRate ) / 100;
		var perOrder = num( v.perOrder );
		var promoted = num( v.promoted ) / 100;

		var total = price + shipping;
		var finalValue = total * feeRate + perOrder;
		var promotedFee = price * promoted;
		var fees = finalValue + promotedFee;
		var net = total - fees - cost - shipCost;

		return {
			label: 'Profit on the sale',
			value: money2( net ),
			bar: [
				{ pct: share( Math.max( net, 0 ), total ), color: ACCENT },
				{ pct: share( fees, total ), color: WARN },
				{ pct: share( cost + shipCost, total ), color: NEUTRAL }
			],
			rows: [
				{ label: 'Buyer pays', value: money2( total ) },
				{ label: 'eBay fees', value: money2( fees ), color: WARN, emphasis: true },
				{ label: 'Your item cost', value: money2( cost ), color: NEUTRAL },
				{ label: 'Your shipping cost', value: money2( shipCost ), divide: true },
				{ label: 'Profit margin', value: total > 0 ? decimals( ( net / total ) * 100, 2 ) + '%' : '—' },
				{ label: 'Break-even sale price', value: money2( ( cost + shipCost + perOrder ) / Math.max( 1 - feeRate - promoted, 0.01 ) - shipping ) }
			],
			note: 'The fee rate is entered rather than fixed, because eBay charges differently by category and by store subscription and changes the rates periodically. Note that the final value fee applies to the shipping the buyer pays as well as to the item price, which is the part sellers most often miss.'
		};
	};

	formulas[ 'cpm-calculator' ] = function ( v ) {
		var solve = v.solve || 'cpm';
		var cost = num( v.cost );
		var impressions = num( v.impressions );
		var cpm = num( v.cpm );
		var answer, label;

		if ( 'cost' === solve )             { answer = ( impressions / 1000 ) * cpm; label = 'Campaign cost'; }
		else if ( 'impressions' === solve ) { answer = cpm > 0 ? ( cost / cpm ) * 1000 : 0; label = 'Impressions bought'; }
		else                                { answer = impressions > 0 ? ( cost / impressions ) * 1000 : 0; label = 'CPM'; }

		var finalCost = 'cost' === solve ? answer : cost;
		var finalImps = 'impressions' === solve ? answer : impressions;
		var clicks = finalImps * ( num( v.ctr ) / 100 );

		return {
			label: label,
			value: 'impressions' === solve ? decimals( answer, 0 ) : money2( answer ),
			rows: [
				{ label: 'Cost', value: money2( finalCost ) },
				{ label: 'Impressions', value: decimals( finalImps, 0 ) },
				{ label: 'CPM', value: finalImps > 0 ? money2( ( finalCost / finalImps ) * 1000 ) : '—', divide: true },
				{ label: 'Clicks at ' + decimals( num( v.ctr ), 2 ) + '% CTR', value: decimals( clicks, 0 ) },
				{ label: 'Effective cost per click', value: clicks > 0 ? money2( finalCost / clicks ) : '—' }
			],
			note: 'CPM is cost per thousand impressions, from the Latin mille. It measures reach rather than response, so a low CPM on an audience that never converts is more expensive than a high one that does.'
		};
	};

	/* ---------- Education ---------- */

	formulas[ 'cumulative-gpa-calculator' ] = function ( v ) {
		var priorGpa = num( v.priorGpa );
		var priorCredits = num( v.priorCredits );
		var termGpa = num( v.termGpa );
		var termCredits = num( v.termCredits );

		var totalCredits = priorCredits + termCredits;
		var points = priorGpa * priorCredits + termGpa * termCredits;
		var cumulative = totalCredits > 0 ? points / totalCredits : 0;
		var change = cumulative - priorGpa;

		return {
			label: 'Cumulative GPA',
			value: decimals( cumulative, 3 ),
			rows: [
				{ label: 'Change this term', value: ( change >= 0 ? '+' : '' ) + decimals( change, 3 ), emphasis: change < 0 },
				{ label: 'Total credits', value: decimals( totalCredits, 1 ) },
				{ label: 'Total quality points', value: decimals( points, 2 ), divide: true },
				{ label: 'To reach 3.5 you would need', value: totalCredits > 0 ? decimals( Math.max( ( 3.5 * ( totalCredits + 15 ) - points ) / 15, 0 ), 2 ) + ' next term over 15 credits' : '—' }
			],
			note: 'The more credits already banked, the less any single term moves the average. That is why a weak first year is recoverable and a weak final year usually is not.'
		};
	};

	formulas[ 'ap-score-calculator' ] = function ( v ) {
		var mcCorrect = num( v.mcCorrect );
		var mcTotal = Math.max( num( v.mcTotal ) || 1, 1 );
		var frqEarned = num( v.frqEarned );
		var frqTotal = Math.max( num( v.frqTotal ) || 1, 1 );
		var mcWeight = num( v.mcWeight ) / 100;

		var composite = ( mcCorrect / mcTotal ) * mcWeight + ( frqEarned / frqTotal ) * ( 1 - mcWeight );
		var pct = composite * 100;

		/* Indicative cut points. The College Board resets these per subject
		   per year and does not publish them, so this is a guide only. */
		var score = pct >= 75 ? 5 : pct >= 62 ? 4 : pct >= 48 ? 3 : pct >= 35 ? 2 : 1;

		return {
			label: 'Estimated AP score',
			value: String( score ),
			rows: [
				{ label: 'Composite', value: decimals( pct, 1 ) + '%' },
				{ label: 'Multiple choice', value: decimals( mcCorrect, 0 ) + ' of ' + decimals( mcTotal, 0 ) },
				{ label: 'Free response', value: decimals( frqEarned, 1 ) + ' of ' + decimals( frqTotal, 0 ), divide: true },
				{ label: 'Typically earns credit', value: score >= 3 ? 'yes at most colleges' : 'usually not', emphasis: score < 3 }
			],
			note: 'An indication rather than a prediction. The College Board sets the cut points separately for every subject every year and does not publish them, so the real boundaries move. Treat a borderline result as genuinely uncertain.'
		};
	};

	formulas[ 'molarity-calculator' ] = function ( v ) {
		var solve = v.solve || 'molarity';
		var mass = num( v.mass );
		var molarMass = num( v.molarMass );
		var volume = num( v.volume );
		var molarity = num( v.molarity );

		var moles = molarMass > 0 ? mass / molarMass : 0;
		var answer, label, unit;

		if ( 'mass' === solve ) {
			answer = molarity * volume * molarMass; label = 'Mass needed'; unit = ' g';
			moles = molarity * volume;
		} else if ( 'volume' === solve ) {
			answer = molarity > 0 ? moles / molarity : 0; label = 'Volume needed'; unit = ' L';
		} else {
			answer = volume > 0 ? moles / volume : 0; label = 'Molarity'; unit = ' M';
		}

		return {
			label: label,
			value: decimals( answer, 5 ) + unit,
			rows: [
				{ label: 'Moles of solute', value: decimals( moles, 5 ) + ' mol' },
				{ label: 'Molar mass', value: decimals( molarMass, 4 ) + ' g/mol' },
				{ label: 'Millimolar', value: decimals( ( 'molarity' === solve ? answer : molarity ) * 1000, 2 ) + ' mM', divide: true }
			],
			note: 'Molarity is moles of solute per litre of solution, not per litre of solvent. Dissolving something changes the volume, so make the solution up to the mark rather than adding solute to a litre of water.'
		};
	};

	/* ---------- Novelty ---------- */

	formulas[ 'love-calculator' ] = function ( v ) {
		var a = String( v.name1 || '' ).toLowerCase().replace( /[^a-z]/g, '' );
		var b = String( v.name2 || '' ).toLowerCase().replace( /[^a-z]/g, '' );

		if ( ! a || ! b ) {
			return { label: 'Compatibility', value: '—', rows: [], note: 'Enter two names.' };
		}

		/* Deterministic so the same pair always gets the same answer, which is
		   the only property that matters in a toy like this. */
		var combined = ( a < b ? a + b : b + a );
		var hash = 0;
		for ( var i = 0; i < combined.length; i++ ) {
			hash = ( ( hash << 5 ) - hash + combined.charCodeAt( i ) ) | 0;
		}

		var score = Math.abs( hash ) % 101;
		var verdict = score >= 85 ? 'Written in the stars'
			: score >= 65 ? 'Very promising'
			: score >= 45 ? 'Worth a try'
			: score >= 25 ? 'It would take work'
			: 'Better as friends';

		return {
			label: 'Compatibility',
			value: score + '%',
			bar: [ { pct: score, color: ACCENT }, { pct: 100 - score, color: NEUTRAL } ],
			rows: [
				{ label: 'Verdict', value: verdict },
				{ label: 'Names', value: ( v.name1 || '' ) + ' and ' + ( v.name2 || '' ), divide: true }
			],
			note: 'For fun only. This is a hash of two names and nothing more, so it has no predictive value whatsoever. It will give the same answer every time for the same pair, which is the only honest claim it can make.'
		};
	};

	window.CalculatorrFormulas = formulas;
}() );
