<?php
/**
 * Long Division Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'long-division-calculator',
		'title' => 'Long Division Calculator',
		'category' => 'math',
		'description' => 'Divide two numbers and see the quotient, remainder and decimal.',
		'keyword' => 'Long Division Calculator',
		'h1' => 'Long Division Calculator',
		'meta_title' => 'Long Division Calculator - Quotient and Remainder',
		'meta_description' => 'Free long division calculator. Divide any two numbers to get the whole quotient, the remainder, the decimal answer and the result as a mixed number.',
		'fields' => array(
			array(
				'id' => 'dividend',
				'label' => 'Dividend',
				'type' => 'number',
				'default' => 847,
			),
			array(
				'id' => 'divisor',
				'label' => 'Divisor',
				'type' => 'number',
				'default' => 23,
			),
		),
		'default_result' => array(
			'label' => '847 ÷ 23',
			'value' => '36 remainder 19',
			'rows' => array(
				array(
					'label' => 'Quotient',
					'value' => '36',
				),
				array(
					'label' => 'Remainder',
					'value' => '19',
				),
				array(
					'label' => 'As a decimal',
					'value' => '36.82608696',
				),
				array(
					'label' => 'As a mixed number',
					'value' => '36 19/23',
				),
			),
			'note' => '',
		),
		'explainer' => array(
			array(
				'heading' => 'Quotient and remainder',
				'body' => 'Long division answers a different question from a calculator division. 847 divided by 23 is 36.826 on a calculator, and 36 remainder 19 in long division. The second form is what you want when the things being divided cannot be split, such as people into teams or items into boxes.',
				'formula' => 'Dividend = Quotient &times; Divisor + Remainder',
			),
			array(
				'heading' => 'Checking the answer',
				'body' => 'Multiply the quotient by the divisor and add the remainder. If it does not return the dividend exactly, something has gone wrong, and this works for any division at all.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What is the remainder in division?',
				'a' => 'What is left over when the divisor will not fit into the dividend a whole number of times. It is always smaller than the divisor.',
			),
			array(
				'q' => 'How do I turn a remainder into a decimal?',
				'a' => 'Divide the remainder by the divisor. A remainder of 19 over a divisor of 23 gives 0.826, which is the decimal part of the answer.',
			),
		),
		'related' => array(
			'average-calculator',
			'fraction-calculator',
			'factor-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
