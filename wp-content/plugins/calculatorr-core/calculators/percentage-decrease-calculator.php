<?php
/**
 * Percentage Decrease Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'percentage-decrease-calculator',
		'title' => 'Percentage Decrease Calculator',
		'category' => 'math',
		'description' => 'Reduce any number by a percentage and see what is left.',
		'keyword' => 'Percentage Decrease Calculator',
		'h1' => 'Percentage Decrease Calculator',
		'meta_title' => 'Percentage Decrease Calculator - Subtract a Percent',
		'meta_description' => 'Free percentage decrease calculator. Enter a value and a percentage to see the reduced figure, how much came off and the multiplier that produced it.',
		'fields' => array(
			array(
				'id' => 'value',
				'label' => 'Starting value',
				'type' => 'number',
				'default' => 200,
			),
			array(
				'id' => 'percent',
				'label' => 'Decrease by',
				'type' => 'number',
				'suffix' => '%',
				'default' => 15,
			),
		),
		'default_result' => array(
			'label' => 'Decreased value',
			'value' => '170',
			'rows' => array(
				array(
					'label' => 'Original value',
					'value' => '200',
				),
				array(
					'label' => 'Decrease of 15%',
					'value' => '30',
				),
				array(
					'label' => 'As a multiplier',
					'value' => '× 0.85',
				),
			),
			'note' => '',
		),
		'explainer' => array(
			array(
				'heading' => 'How a decrease is applied',
				'body' => 'Taking fifteen per cent off means multiplying by 0.85. Working out the fifteen per cent and subtracting it separately gives the same answer but takes two steps and offers two chances to go wrong.',
				'formula' => 'New value = Original &times; (1 &minus; rate &divide; 100)',
			),
			array(
				'heading' => 'Stacked reductions are smaller than they look',
				'body' => 'Two successive twenty per cent reductions do not remove forty per cent, they remove thirty-six, because the second applies to what is left after the first. Retail sales rely on this being counterintuitive.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How do I subtract a percentage?',
				'a' => 'Multiply by one minus the percentage as a decimal. To take 15% off, multiply by 0.85.',
			),
			array(
				'q' => 'Can a percentage decrease go past 100%?',
				'a' => 'Not meaningfully for a quantity, since removing more than everything leaves a negative, which usually signals the wrong figures rather than a real result.',
			),
		),
		'related' => array(
			'percentage-calculator',
			'percent-off-calculator',
			'percent-change-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
