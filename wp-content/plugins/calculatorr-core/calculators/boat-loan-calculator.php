<?php
/**
 * Boat Loan Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'boat-loan-calculator',
		'title' => 'Boat Loan Calculator',
		'category' => 'loans',
		'description' => 'Work out payments on a boat loan.',
		'keyword' => 'Boat Loan Calculator',
		'h1' => 'Boat Loan Calculator',
		'meta_title' => 'Boat Loan Calculator - Monthly Payment and Interest',
		'meta_description' => 'Free boat loan calculator. Work out monthly payments, total interest and the real cost of financing a boat over a long term.',
		'fields' => array(
			array(
				'id' => 'amount',
				'label' => 'Boat price',
				'type' => 'number',
				'prefix' => '$',
				'default' => 60000,
			),
			array(
				'id' => 'down',
				'label' => 'Deposit',
				'type' => 'number',
				'prefix' => '$',
				'default' => 12000,
			),
			array(
				'id' => 'rate',
				'label' => 'Interest rate',
				'type' => 'number',
				'suffix' => '%',
				'default' => 7.5,
				'step' => 'any',
			),
			array(
				'id' => 'years',
				'label' => 'Term',
				'type' => 'number',
				'suffix' => 'years',
				'default' => 15,
			),
			array(
				'id' => 'fees',
				'label' => 'Fees and documentation',
				'type' => 'number',
				'prefix' => '$',
				'default' => 500,
			),
		),
		'default_result' => array(
			'label' => 'Monthly payment',
			'value' => '$449.60',
			'rows' => array(
				array(
					'label' => 'Amount financed',
					'value' => '$48,500',
				),
				array(
					'label' => 'Total interest',
					'value' => '$32,428',
				),
				array(
					'label' => 'Total of payments',
					'value' => '$80,928',
				),
				array(
					'label' => 'Number of payments',
					'value' => '180',
				),
				array(
					'label' => 'Cost per dollar borrowed',
					'value' => '$1.67',
				),
			),
			'note' => 'Boat loans run longer than car loans, commonly fifteen to twenty years, which keeps the payment low and the total interest high. Budget separately for mooring, insurance, winterising and maintenance, which together often exceed the loan payment.',
		),
		'explainer' => array(
			array(
				'heading' => 'Long terms hide the real cost',
				'body' => 'Boat loans commonly run fifteen to twenty years, far longer than a car loan, which keeps the monthly payment comfortable and pushes the total interest very high. Look at the total of payments rather than the monthly figure before deciding what you can afford.',
				'formula' => 'Total cost = payment × months + deposit',
			),
			array(
				'heading' => 'The loan is the smaller expense',
				'body' => 'Mooring or storage, insurance, winterising, hauling, bottom paint and routine maintenance together often exceed the loan payment over a year. Boats also depreciate steadily, so being underwater on a long loan is common. Budget for the running costs before the financing.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How long can you finance a boat?',
				'a' => 'Commonly fifteen to twenty years on larger vessels. Longer terms mean lower payments and considerably more interest.',
			),
			array(
				'q' => 'What deposit do boat lenders want?',
				'a' => 'Typically ten to twenty per cent. A larger deposit improves the rate and reduces the time spent owing more than the boat is worth.',
			),
		),
		'related' => array(
			'personal-loan-calculator',
			'amortization-calculator',
		),
		'disclaimer' => 'For planning only, not tax or financial advice. Rates and thresholds change, so confirm current figures before acting on a projection.',
		'sources' => array(),
	);
