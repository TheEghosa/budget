<?php
/**
 * Markup Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'markup-calculator',
		'title' => 'Markup Calculator',
		'category' => 'business',
		'description' => 'Work out a selling price from cost and markup.',
		'keyword' => 'Markup Calculator',
		'h1' => 'Markup Calculator',
		'meta_title' => 'Markup Calculator - Selling Price From Cost',
		'meta_description' => 'Free markup calculator. Turn a cost and a markup percentage into a selling price, with the resulting profit margin shown so the two are never confused.',
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
				'id' => 'markup',
				'label' => 'Markup',
				'type' => 'number',
				'suffix' => '%',
				'default' => 50,
			),
		),
		'default_result' => array(
			'label' => 'Selling price',
			'value' => '$60.00',
			'rows' => array(
				array(
					'label' => 'Cost',
					'value' => '$40.00',
				),
				array(
					'label' => 'Profit per unit',
					'value' => '$20.00',
				),
				array(
					'label' => 'Markup',
					'value' => '50%',
				),
				array(
					'label' => 'Resulting margin',
					'value' => '33.33%',
				),
			),
			'note' => 'Markup and margin are not the same number and confusing them is how businesses quietly underprice. A 50% markup is only a 33% margin, because markup is measured against cost and margin against the selling price.',
		),
		'explainer' => array(
			array(
				'heading' => 'Markup and margin are different numbers',
				'body' => 'Markup is profit measured against cost. Margin is the same profit measured against the selling price. A fifty per cent markup on a forty dollar item gives a sixty dollar price and a thirty-three per cent margin. Treating them as interchangeable is one of the most common and most expensive pricing errors in small business.',
				'formula' => 'Price = cost × (1 + markup ÷ 100)',
			),
			array(
				'heading' => 'Why markup is the working number',
				'body' => 'Markup is easier to apply because you start from cost, which is the number you know. Margin is what you report, because it shows what share of revenue you keep. Both are shown here so you can price in one and report in the other without converting by hand.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What markup gives a 50% margin?',
				'a' => 'One hundred per cent. Doubling the cost is the only way to keep half the selling price, which surprises people every time.',
			),
			array(
				'q' => 'What is a typical retail markup?',
				'a' => 'Keystone pricing, meaning a hundred per cent markup, is a long-standing retail convention. Actual markups vary enormously by category, from single digits in groceries to several hundred per cent in fashion.',
			),
		),
		'related' => array(
			'margin-calculator',
			'discount-calculator',
			'sales-tax-calculator',
		),
		'disclaimer' => '',
	);
