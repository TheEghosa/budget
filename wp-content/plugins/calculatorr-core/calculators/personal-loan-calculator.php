<?php
/**
 * Personal Loan Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'personal-loan-calculator',
		'title' => 'Personal Loan Calculator',
		'category' => 'loans',
		'description' => 'Work out payments and total cost on a personal loan.',
		'keyword' => 'Personal Loan Calculator',
		'h1' => 'Personal Loan Calculator',
		'meta_title' => 'Personal Loan Calculator - Payment and Total Cost',
		'meta_description' => 'Free personal loan calculator. Work out the monthly payment, total interest and true cost of a personal loan, including any origination fee.',
		'fields' => array(
			array(
				'id' => 'amount',
				'label' => 'Loan amount',
				'type' => 'number',
				'prefix' => '$',
				'default' => 15000,
			),
			array(
				'id' => 'down',
				'label' => 'Deposit',
				'type' => 'number',
				'prefix' => '$',
				'default' => 0,
			),
			array(
				'id' => 'rate',
				'label' => 'Interest rate (APR)',
				'type' => 'number',
				'suffix' => '%',
				'default' => 11.5,
				'step' => 'any',
			),
			array(
				'id' => 'years',
				'label' => 'Term',
				'type' => 'number',
				'suffix' => 'years',
				'default' => 5,
			),
			array(
				'id' => 'fees',
				'label' => 'Origination fee',
				'type' => 'number',
				'prefix' => '$',
				'default' => 300,
			),
		),
		'default_result' => array(
			'label' => 'Monthly payment',
			'value' => '$336.49',
			'rows' => array(
				array(
					'label' => 'Amount financed',
					'value' => '$15,300',
				),
				array(
					'label' => 'Total interest',
					'value' => '$4,889',
				),
				array(
					'label' => 'Total of payments',
					'value' => '$20,189',
				),
				array(
					'label' => 'Number of payments',
					'value' => '60',
				),
				array(
					'label' => 'Cost per dollar borrowed',
					'value' => '$1.32',
				),
			),
			'note' => 'Personal loans are unsecured, so the rate is driven almost entirely by your credit score. Origination fees are often deducted from the amount you receive rather than added to the balance, so check which way your lender does it.',
		),
		'explainer' => array(
			array(
				'heading' => 'Unsecured means the rate is about you',
				'body' => 'A personal loan has no collateral behind it, so the lender prices it almost entirely on your credit score and income. That is why quoted rates span such a wide range, and why shopping around moves the number far more than it does on a mortgage.',
				'formula' => 'Cost per dollar borrowed is the honest comparison figure',
			),
			array(
				'heading' => 'Watch how the fee is applied',
				'body' => 'Some lenders add the origination fee to the balance and some deduct it from what they send you, meaning a 15,000 loan with a 300 fee arrives as 14,700 while you still repay 15,000. Both are legal, they produce different effective rates, and the APR should capture it, so compare APRs rather than headline rates.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'Does applying hurt my credit score?',
				'a' => 'A prequalification is usually a soft check with no effect. A formal application is a hard check with a small temporary effect, so submit applications close together so they count as rate shopping.',
			),
			array(
				'q' => 'Is a personal loan better than a credit card?',
				'a' => 'Usually yes for a fixed one-off expense, because the rate is lower and the balance is on a schedule that actually ends. A card is better for short-term flexibility you will clear quickly.',
			),
		),
		'related' => array(
			'boat-loan-calculator',
			'amortization-calculator',
			'heloc-calculator',
		),
		'disclaimer' => 'For planning only, not tax or financial advice. Rates and thresholds change, so confirm current figures before acting on a projection.',
	);
