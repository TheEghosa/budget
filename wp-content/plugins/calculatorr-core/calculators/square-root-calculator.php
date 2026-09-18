<?php
/**
 * Square Root Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'square-root-calculator',
		'title' => 'Square Root Calculator',
		'category' => 'math',
		'description' => 'Find the square root of any number, including the simplified radical.',
		'keyword' => 'Square Root Calculator',
		'h1' => 'Square Root Calculator',
		'meta_title' => 'Square Root Calculator - Exact and Simplified Radical',
		'meta_description' => 'Free square root calculator. Get the decimal root to eight places plus the simplified radical form, the cube root and whether the number is a perfect square.',
		'fields' => array(
			array(
				'id' => 'number',
				'label' => 'Number',
				'type' => 'number',
				'default' => 72,
				'step' => 'any',
			),
		),
		'default_result' => array(
			'label' => 'Square root of 72',
			'value' => '8.48528137',
			'rows' => array(
				array(
					'label' => 'Simplified radical',
					'value' => '6√2',
				),
				array(
					'label' => 'Cube root',
					'value' => '4.16016765',
				),
				array(
					'label' => 'Squared back',
					'value' => '72',
				),
				array(
					'label' => 'Perfect square',
					'value' => 'no',
				),
			),
			'note' => '',
		),
		'explainer' => array(
			array(
				'heading' => 'The simplified radical',
				'body' => 'School work usually wants the root of 72 written as six root two rather than 8.485. That is found by pulling out the largest perfect square factor, here thirty-six, leaving two behind under the radical. It is exact where a decimal is only an approximation.',
				'formula' => '&radic;72 = &radic;(36 &times; 2) = 6&radic;2',
			),
			array(
				'heading' => 'Negative numbers',
				'body' => 'No real number squares to a negative, so the root of a negative is imaginary and written with an i. That is not a failure of the calculator but a genuine feature of the number system, and it is why complex numbers exist at all.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What is a perfect square?',
				'a' => 'A number whose square root is a whole number, such as 16, 25 or 144. The calculator says whether yours is one.',
			),
			array(
				'q' => 'How do I simplify a square root?',
				'a' => 'Find the largest perfect square that divides your number, take its root outside the radical and leave the rest inside.',
			),
		),
		'related' => array(
			'quadratic-formula-calculator',
			'factor-calculator',
			'pythagorean-theorem-calculator',
		),
		'disclaimer' => '',
	);
