<?php
/**
 * Future Value Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'future-value-calculator',
		'title' => 'Future Value Calculator',
		'category' => 'finance',
		'description' => 'Project the future value of a lump sum and regular payments.',
		'keyword' => 'Future Value Calculator',
		'h1' => 'Future Value Calculator',
		'meta_title' => 'Future Value Calculator - Lump Sum and Contributions',
		'meta_description' => 'Free future value calculator. Project what a starting amount and regular contributions grow to, and what that balance is worth in today’s money.',
		'fields' => array(
			array(
				'id' => 'present',
				'label' => 'Starting amount',
				'type' => 'number',
				'prefix' => '$',
				'default' => 10000,
			),
			array(
				'id' => 'payment',
				'label' => 'Regular payment',
				'type' => 'number',
				'prefix' => '$',
				'default' => 500,
			),
			array(
				'id' => 'frequency',
				'label' => 'Paid',
				'type' => 'segmented',
				'options' => array(
					'12' => 'Monthly',
					'4' => 'Quarterly',
					'1' => 'Yearly',
				),
				'default' => '12',
			),
			array(
				'id' => 'rate',
				'label' => 'Annual return',
				'type' => 'number',
				'suffix' => '%',
				'default' => 6,
			),
			array(
				'id' => 'years',
				'label' => 'Years',
				'type' => 'number',
				'default' => 20,
			),
			array(
				'id' => 'inflation',
				'label' => 'Inflation rate',
				'type' => 'number',
				'suffix' => '%',
				'default' => 3,
			),
		),
		'default_result' => array(
			'label' => 'Future value',
			'value' => '$264,122',
			'rows' => array(
				array(
					'label' => 'Total paid in',
					'value' => '$130,000',
				),
				array(
					'label' => 'Interest earned',
					'value' => '$134,122',
				),
				array(
					'label' => 'Periods',
					'value' => '240',
				),
				array(
					'label' => 'Worth in today’s money',
					'value' => '$146,238',
				),
			),
			'note' => 'The figure in today’s money is the one worth planning against, because a balance that looks large in thirty years buys considerably less than the same number does now.',
		),
		'explainer' => array(
			array(
				'heading' => 'Two sums added together',
				'body' => 'Future value has two parts: what your starting amount grows into on its own, and what the stream of regular payments accumulates to. The second is an annuity calculation, and for most people saving over a long period it ends up much the larger of the two.',
				'formula' => 'FV = PV(1+i)&#8319; + PMT × ((1+i)&#8319; − 1) ÷ i',
			),
			array(
				'heading' => 'The number that actually matters',
				'body' => 'A projected balance of half a million in thirty years sounds transformative and buys roughly what two hundred thousand buys today at three per cent inflation. The real-terms figure is shown for exactly that reason, because planning against the nominal number leads to a shortfall that only becomes visible far too late.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What is the difference between future value and present value?',
				'a' => 'Future value projects forward to what an amount becomes. Present value discounts backwards to what a future amount is worth now. They are the same equation rearranged.',
			),
			array(
				'q' => 'What inflation rate should I use?',
				'a' => 'Long-run averages sit around two to three per cent in developed economies. Using a higher figure makes your plan more conservative, which is rarely a bad thing.',
			),
		),
		'related' => array(
			'compound-interest-calculator',
			'401k-calculator',
			'roth-ira-calculator',
		),
		'disclaimer' => 'For planning only, not tax or financial advice. Rates and thresholds change, so confirm current figures before acting on a projection.',
		'sources' => array(),
	);
