<?php
/**
 * Amortization Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'amortization-calculator',
		'title' => 'Amortization Calculator',
		'category' => 'loans',
		'description' => 'See how a loan splits between principal and interest over time.',
		'keyword' => 'Amortization Calculator',
		'h1' => 'Amortization Calculator',
		'meta_title' => 'Amortization Calculator - Principal and Interest Split',
		'meta_description' => 'Free amortization calculator. See the monthly payment, total interest, and how much of your first payment and first year actually reduces the balance.',
		'fields' => array(
			array(
				'id' => 'principal',
				'label' => 'Loan amount',
				'type' => 'number',
				'prefix' => '$',
				'default' => 300000,
			),
			array(
				'id' => 'rate',
				'label' => 'Interest rate',
				'type' => 'number',
				'suffix' => '%',
				'default' => 6.5,
				'step' => 'any',
			),
			array(
				'id' => 'years',
				'label' => 'Term',
				'type' => 'number',
				'suffix' => 'years',
				'default' => 30,
			),
		),
		'default_result' => array(
			'label' => 'Monthly payment',
			'value' => '$1,896.20',
			'rows' => array(
				array(
					'label' => 'Total interest',
					'value' => '$382,633',
				),
				array(
					'label' => 'Total paid',
					'value' => '$682,633',
				),
				array(
					'label' => 'First payment interest',
					'value' => '$1,625.00',
				),
				array(
					'label' => 'First payment principal',
					'value' => '$271.20',
				),
				array(
					'label' => 'Year one interest',
					'value' => '$19,401',
				),
				array(
					'label' => 'Balance after a year',
					'value' => '$296,647',
				),
			),
			'note' => 'The payment never changes but its split does. Early on almost all of it is interest, which is why the balance seems to barely move for the first few years.',
		),
		'explainer' => array(
			array(
				'heading' => 'The payment is level but the split is not',
				'body' => 'Every payment is the same size, yet the first one is almost entirely interest and the last is almost entirely principal. Interest is charged on the outstanding balance, which barely moves at first, so early payments have very little left over to reduce it. This is why the balance seems stuck for years.',
				'formula' => 'M = P × r(1+r)&#8319; ÷ ((1+r)&#8319; − 1)',
			),
			array(
				'heading' => 'Why overpaying early is worth so much more',
				'body' => 'An extra payment made in year two removes that amount of principal from every remaining month, so it saves interest hundreds of times over. The same payment in year twenty-eight has almost nothing left to save, which is why the value of overpaying decays sharply with time.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'Why is so much of my payment interest?',
				'a' => 'Because interest is calculated on the balance, and at the start the balance is almost the full loan. The proportion shifts steadily as the balance falls.',
			),
			array(
				'q' => 'What is negative amortization?',
				'a' => 'When the payment does not even cover the interest, so the balance grows rather than shrinks. It appears in some adjustable and interest-only products and is worth avoiding.',
			),
		),
		'related' => array(
			'mortgage-payoff-calculator',
			'mortgage-payment-calculator',
			'personal-loan-calculator',
		),
		'disclaimer' => 'For planning only, not tax or financial advice. Rates and thresholds change, so confirm current figures before acting on a projection.',
		'sources' => array(),
	);
