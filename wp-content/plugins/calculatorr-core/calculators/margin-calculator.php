<?php
/**
 * Margin Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'margin-calculator',
		'title' => 'Margin Calculator',
		'category' => 'business',
		'description' => 'Work out the price needed for a target profit margin.',
		'keyword' => 'Margin Calculator',
		'h1' => 'Margin Calculator',
		'meta_title' => 'Margin Calculator - Price for a Target Margin',
		'meta_description' => 'Free profit margin calculator. Enter your cost and target margin to get the selling price, the gross profit and the equivalent markup percentage.',
		'fields' => array(
			array(
				'id' => 'cost',
				'label' => 'Unit cost',
				'type' => 'number',
				'prefix' => '$',
				'default' => 40,
				'step' => 'any',
			),
			array(
				'id' => 'margin',
				'label' => 'Target margin',
				'type' => 'number',
				'suffix' => '%',
				'default' => 40,
			),
		),
		'default_result' => array(
			'label' => 'Price for a 40% margin',
			'value' => '$66.67',
			'rows' => array(
				array(
					'label' => 'Cost',
					'value' => '$40.00',
				),
				array(
					'label' => 'Gross profit',
					'value' => '$26.67',
				),
				array(
					'label' => 'Equivalent markup',
					'value' => '66.67%',
				),
				array(
					'label' => 'Cost as a share of price',
					'value' => '60%',
				),
			),
			'note' => 'Margin is profit as a share of the selling price, which is the figure that matters for a profit and loss account. Markup is the same profit measured against cost.',
		),
		'explainer' => array(
			array(
				'heading' => 'Dividing, not multiplying',
				'body' => 'To reach a forty per cent margin you divide the cost by 0.6, not multiply by 1.4. Multiplying gives a fifty-six dollar price and a twenty-nine per cent margin, which is eleven points short of the target. This single error quietly underprices a great many products.',
				'formula' => 'Price = cost ÷ (1 − margin ÷ 100)',
			),
			array(
				'heading' => 'Why margin is the number to run a business on',
				'body' => 'Margin tells you what share of every sale you keep before overheads, which is what determines whether volume translates into profit. It is also directly comparable between products and between competitors in a way markup is not.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'Can margin be over 100%?',
				'a' => 'No. That would require a cost of zero or less, since margin is profit as a share of the price and the price includes the cost.',
			),
			array(
				'q' => 'What is the difference between gross and net margin?',
				'a' => 'Gross margin counts only the direct cost of the goods. Net margin subtracts everything else too: rent, wages, marketing and tax. This calculator works in gross margin.',
			),
		),
		'related' => array(
			'markup-calculator',
			'ebay-fee-calculator',
			'cpm-calculator',
		),
		'disclaimer' => '',
	);
