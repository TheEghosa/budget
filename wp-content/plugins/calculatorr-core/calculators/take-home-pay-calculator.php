<?php
/**
 * Take Home Pay Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'take-home-pay-calculator',
		'title' => 'Take Home Pay Calculator',
		'category' => 'finance',
		'description' => 'Work out net pay after tax, FICA and pre-tax deductions.',
		'keyword' => 'Take Home Pay Calculator',
		'h1' => 'Take Home Pay Calculator',
		'meta_title' => 'Take Home Pay Calculator - Net Pay After Tax',
		'meta_description' => 'Free take home pay calculator. Work out net pay after federal and state tax, Social Security, Medicare and pre-tax deductions, for any pay frequency.',
		'fields' => array(
			array(
				'id' => 'gross',
				'label' => 'Gross pay',
				'type' => 'number',
				'prefix' => '$',
				'default' => 75000,
			),
			array(
				'id' => 'frequency',
				'label' => 'Paid',
				'type' => 'segmented',
				'options' => array(
					'week' => 'Weekly',
					'biweek' => 'Fortnightly',
					'semimonth' => 'Twice monthly',
					'month' => 'Monthly',
					'year' => 'Annually',
				),
				'default' => 'year',
			),
			array(
				'id' => 'federalRate',
				'label' => 'Federal tax rate',
				'type' => 'number',
				'suffix' => '%',
				'default' => 12,
				'hint' => 'Your effective rate, not your bracket. Last year’s return is the best guide.',
			),
			array(
				'id' => 'stateRate',
				'label' => 'State tax rate',
				'type' => 'number',
				'suffix' => '%',
				'default' => 4,
			),
			array(
				'id' => 'retirement',
				'label' => '401(k) contribution',
				'type' => 'number',
				'suffix' => '%',
				'default' => 6,
			),
			array(
				'id' => 'healthAnnual',
				'label' => 'Health premiums per year',
				'type' => 'number',
				'prefix' => '$',
				'default' => 2400,
			),
			array(
				'id' => 'wageBase',
				'label' => 'Social Security wage base',
				'type' => 'number',
				'prefix' => '$',
				'default' => 168600,
				'hint' => 'Updated annually by the SSA.',
			),
		),
		'default_result' => array(
			'label' => 'Take-home per year',
			'value' => '$51,650.10',
			'rows' => array(
				array(
					'label' => 'Annual take-home',
					'value' => '$51,650',
				),
				array(
					'label' => 'Income tax',
					'value' => '$10,896',
				),
				array(
					'label' => 'Social Security & Medicare',
					'value' => '$5,554',
				),
				array(
					'label' => 'Pre-tax deductions',
					'value' => '$6,900',
				),
				array(
					'label' => 'Gross annual',
					'value' => '$75,000',
				),
				array(
					'label' => 'Effective total rate',
					'value' => '31.13%',
				),
			),
			'note' => 'Income tax rates are entered rather than looked up, because brackets change yearly and depend on filing status and credits. For a precise figure use the IRS withholding estimator; for planning, your last return’s effective rate is the number to put in.',
		),
		'explainer' => array(
			array(
				'heading' => 'Why the rates are asked for rather than assumed',
				'body' => 'Federal brackets change every year and depend on filing status, dependants and credits, so a calculator that hard-codes them is wrong within twelve months and confidently wrong in the meantime. Entering your own effective rate from last year’s return gives a better answer than any generic table.',
				'formula' => 'Net = gross − pre-tax − FICA − income tax',
			),
			array(
				'heading' => 'The part that is fixed',
				'body' => 'FICA is the stable component: 6.2 per cent Social Security up to the annual wage base plus 1.45 per cent Medicare with no cap at all. Those rates rarely move, which is why they are calculated for you rather than asked for.',
			),
			array(
				'heading' => 'Pre-tax deductions do two jobs',
				'body' => 'A 401(k) contribution and health premiums both reduce your taxable income, so their real cost is less than their face value. Contributing six per cent of a 75,000 salary costs noticeably less than 4,500 in take-home pay because it lowers the tax owed as well.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What is the difference between my bracket and my effective rate?',
				'a' => 'Your bracket is the rate on your last dollar. Your effective rate is total tax divided by total income, and it is always lower because earlier income is taxed at lower rates. Use the effective rate here.',
			),
			array(
				'q' => 'Why is my first paycheck of the year smaller?',
				'a' => 'Social Security stops once you pass the wage base, so high earners see take-home pay rise later in the year when that deduction ends.',
			),
			array(
				'q' => 'Does this include local taxes?',
				'a' => 'No. Some cities levy their own income tax, and if yours does, fold it into the state rate field.',
			),
		),
		'related' => array(
			'pay-raise-calculator',
			'overtime-calculator',
			'401k-calculator',
		),
		'disclaimer' => 'For planning only, not tax or financial advice. Rates and thresholds change, so confirm current figures before acting on a projection.',
	);
