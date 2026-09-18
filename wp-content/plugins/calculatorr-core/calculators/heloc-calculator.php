<?php
/**
 * HELOC Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'heloc-calculator',
		'title' => 'HELOC Calculator',
		'category' => 'loans',
		'description' => 'Work out your available home equity line and its payments.',
		'keyword' => 'HELOC Calculator',
		'h1' => 'HELOC Calculator',
		'meta_title' => 'HELOC Calculator - Available Credit and Payment Shock',
		'meta_description' => 'Free HELOC calculator. Work out how much you could borrow against your home equity, the interest-only payment and the jump when repayment begins.',
		'fields' => array(
			array(
				'id' => 'value',
				'label' => 'Home value',
				'type' => 'number',
				'prefix' => '$',
				'default' => 450000,
			),
			array(
				'id' => 'owed',
				'label' => 'Mortgage balance',
				'type' => 'number',
				'prefix' => '$',
				'default' => 250000,
			),
			array(
				'id' => 'ltv',
				'label' => 'Maximum combined LTV',
				'type' => 'number',
				'suffix' => '%',
				'default' => 85,
			),
			array(
				'id' => 'rate',
				'label' => 'Interest rate',
				'type' => 'number',
				'suffix' => '%',
				'default' => 8.5,
				'step' => 'any',
			),
			array(
				'id' => 'draw',
				'label' => 'Amount you would draw',
				'type' => 'number',
				'prefix' => '$',
				'default' => 50000,
			),
		),
		'default_result' => array(
			'label' => 'Available credit line',
			'value' => '$132,500',
			'rows' => array(
				array(
					'label' => 'Home equity',
					'value' => '$200,000',
				),
				array(
					'label' => 'Current loan-to-value',
					'value' => '55.6%',
				),
				array(
					'label' => 'Interest-only payment on $50,000',
					'value' => '$354.17',
				),
				array(
					'label' => 'Repayment phase, 20 years',
					'value' => '$433.91',
				),
				array(
					'label' => 'Payment shock',
					'value' => '$79.74',
				),
			),
			'note' => 'The draw period is usually interest-only, and the jump when repayment starts catches people out badly. The payment shock row is the size of that jump. A HELOC is also secured on your home, so the downside of not paying is losing it.',
		),
		'explainer' => array(
			array(
				'heading' => 'How the limit is set',
				'body' => 'Lenders cap the combined loan to value, commonly at eighty to ninety per cent. Your available line is that percentage of the home’s value minus what you already owe, so a rising market increases it and a falling one can see a line frozen or reduced without warning.',
				'formula' => 'Line = (value × max LTV) − current mortgage',
			),
			array(
				'heading' => 'The payment shock is the part people miss',
				'body' => 'A HELOC usually runs interest-only for a ten year draw period, then converts to full repayment. The payment can double or worse overnight, and it arrives exactly when people have grown used to the low one. That jump is shown explicitly here because it is the risk that matters.',
			),
			array(
				'heading' => 'It is secured on your home',
				'body' => 'A HELOC is a second charge on your house. The rate is lower than unsecured borrowing precisely because the consequence of not paying is losing the property, which is a very different risk from a credit card.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'HELOC or home equity loan?',
				'a' => 'A HELOC is a revolving line with a variable rate, useful when you need flexibility. A home equity loan is a lump sum at a fixed rate, better when the amount and the timing are known.',
			),
			array(
				'q' => 'Can my lender reduce my line?',
				'a' => 'Yes. Lenders can freeze or cut a line if property values fall or your circumstances change, which happened widely in 2008. A line is not the same as money in the bank.',
			),
		),
		'related' => array(
			'mortgage-payoff-calculator',
			'personal-loan-calculator',
			'amortization-calculator',
		),
		'disclaimer' => 'For planning only, not tax or financial advice. Rates and thresholds change, so confirm current figures before acting on a projection.',
	);
