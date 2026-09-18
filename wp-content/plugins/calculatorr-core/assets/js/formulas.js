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

	window.CalculatorrFormulas = formulas;
}() );
