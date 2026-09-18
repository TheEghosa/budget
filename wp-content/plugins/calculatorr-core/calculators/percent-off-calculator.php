<?php
/**
 * Percent Off Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'percent-off-calculator',
		'title' => 'Percent Off Calculator',
		'category' => 'business',
		'description' => 'Work out a sale price and what you save.',
		'keyword' => 'Percent Off Calculator',
		'h1' => 'Percent Off Calculator',
		'meta_title' => 'Percent Off Calculator - Sale Price and Savings',
		'meta_description' => 'Free percent off calculator. Enter a price and a discount to see what you pay, what you save and the discount as a share of the original price.',
		'fields' => array(
			array(
				'id' => 'price',
				'label' => 'Original price',
				'type' => 'number',
				'prefix' => '$',
				'default' => 80,
				'step' => 'any',
			),
			array(
				'id' => 'percent',
				'label' => 'Percent off',
				'type' => 'number',
				'suffix' => '%',
				'default' => 30,
			),
		),
		'default_result' => array(
			'label' => 'You pay',
			'value' => '$56.00',
			'rows' => array(
				array(
					'label' => 'Original price',
					'value' => '$80.00',
				),
				array(
					'label' => 'You save',
					'value' => '$24.00',
				),
				array(
					'label' => 'Percent off',
					'value' => '30%',
				),
			),
			'note' => 'Half off is the only discount most people can do reliably in their head. For everything else, take ten per cent and scale it.',
		),
		'explainer' => array(
			array(
				'heading' => 'Doing it in your head',
				'body' => 'Find ten per cent by moving the decimal one place left, then scale. Thirty per cent off eighty is three lots of eight, so twenty-four off, leaving fifty-six. Quarters and halves are faster still by dividing directly.',
				'formula' => 'You pay = price × (1 − percent ÷ 100)',
			),
			array(
				'heading' => 'What the sign is not telling you',
				'body' => 'Up to seventy per cent off usually means one item is, and the thing in your hand is not. The only figure that matters is what you actually pay against what the item is worth to you, rather than against a reference price the shop chose.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What is 30% off $80?',
				'a' => 'Twenty-four dollars off, so you pay fifty-six.',
			),
			array(
				'q' => 'How do I work out the original price from a sale price?',
				'a' => 'Divide what you paid by one minus the discount. Fifty-six dollars after thirty per cent off means dividing by 0.7, giving eighty.',
			),
		),
		'related' => array(
			'discount-calculator',
			'sales-tax-calculator',
			'percentage-decrease-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
