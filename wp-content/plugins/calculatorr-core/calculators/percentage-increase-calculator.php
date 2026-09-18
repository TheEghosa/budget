<?php
/**
 * Percentage Increase Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'percentage-increase-calculator',
		'title' => 'Percentage Increase Calculator',
		'category' => 'math',
		'description' => 'Increase any number by a percentage and see the difference it makes.',
		'keyword' => 'Percentage Increase Calculator',
		'h1' => 'Percentage Increase Calculator',
		'meta_title' => 'Percentage Increase Calculator - Add a Percent to Any Number',
		'meta_description' => 'Free percentage increase calculator. Enter a value and a percentage to get the new figure, the size of the increase and the multiplier behind it.',
		'fields' => array(
			array(
				'id' => 'value',
				'label' => 'Starting value',
				'type' => 'number',
				'default' => 200,
			),
			array(
				'id' => 'percent',
				'label' => 'Increase by',
				'type' => 'number',
				'suffix' => '%',
				'default' => 15,
			),
		),
		'default_result' => array(
			'label' => 'Increased value',
			'value' => '230',
			'rows' => array(
				array(
					'label' => 'Original value',
					'value' => '200',
				),
				array(
					'label' => 'Increase of 15%',
					'value' => '30',
				),
				array(
					'label' => 'As a multiplier',
					'value' => '× 1.15',
				),
			),
			'note' => 'Adding a percentage and then removing the same percentage does not return you to the start, because the second calculation runs against the larger number.',
		),
		'explainer' => array(
			array(
				'heading' => 'How an increase is applied',
				'body' => 'Adding fifteen per cent means multiplying by 1.15, not adding fifteen to the number. That distinction sounds obvious and is the source of most errors, because the size of the increase depends entirely on what it is applied to: fifteen per cent of a hundred is fifteen, and fifteen per cent of a thousand is a hundred and fifty.',
				'formula' => 'New value = Original &times; (1 + rate &divide; 100)',
			),
			array(
				'heading' => 'Why increases do not reverse cleanly',
				'body' => 'Add twenty per cent to a hundred and you reach a hundred and twenty. Take twenty per cent off that and you land on ninety-six, not back at a hundred, because the second calculation runs against the larger number. Anyone comparing a price rise with a later sale needs to know this.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How do I add a percentage to a number?',
				'a' => 'Multiply by one plus the percentage as a decimal. To add 15%, multiply by 1.15. To add 7.5%, multiply by 1.075. Adding the percentage as if it were a plain number is the mistake to avoid.',
			),
			array(
				'q' => 'What is the difference between percentage increase and percentage points?',
				'a' => 'If a rate goes from 4% to 6% it has risen by two percentage points but by fifty per cent. Both describe the same movement and headlines routinely pick whichever sounds larger.',
			),
		),
		'related' => array(
			'percentage-calculator',
			'percent-change-calculator',
			'percentage-decrease-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
