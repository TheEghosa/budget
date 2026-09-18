<?php
/**
 * Ratio Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'ratio-calculator',
		'title' => 'Ratio Calculator',
		'category' => 'math',
		'description' => 'Solve, scale and simplify ratios.',
		'keyword' => 'Ratio Calculator',
		'h1' => 'Ratio Calculator',
		'meta_title' => 'Ratio Calculator - Solve, Scale and Simplify',
		'meta_description' => 'Free ratio calculator. Solve for a missing value in a proportion, simplify a ratio to its lowest terms and see it as a decimal, all in one place.',
		'fields' => array(
			array(
				'id' => 'a',
				'label' => 'First term',
				'type' => 'number',
				'default' => 3,
			),
			array(
				'id' => 'b',
				'label' => 'Second term',
				'type' => 'number',
				'default' => 4,
			),
			array(
				'id' => 'c',
				'label' => 'Third term',
				'type' => 'number',
				'default' => 9,
			),
		),
		'default_result' => array(
			'label' => 'Missing value',
			'value' => '12',
			'rows' => array(
				array(
					'label' => 'Your ratio',
					'value' => '3 : 4',
				),
				array(
					'label' => 'Simplified',
					'value' => '3 : 4',
				),
				array(
					'label' => 'As a decimal',
					'value' => '0.75',
				),
				array(
					'label' => 'Complete proportion',
					'value' => '3 : 4 = 9 : 12',
				),
			),
			'note' => '',
		),
		'explainer' => array(
			array(
				'heading' => 'Scaling a recipe or a mix',
				'body' => 'A ratio says nothing about quantity, only about relationship. Three parts to four parts is the same mix whether it makes a cup or a bathtub, which is why ratios are used for concrete, paint, fertiliser and cocktails. Solving for the fourth term is how you scale a known ratio to a quantity you actually need.',
				'formula' => 'a : b = c : d, so d = (b &times; c) &divide; a',
			),
			array(
				'heading' => 'Simplifying',
				'body' => 'Dividing both sides by their greatest common factor gives the lowest terms, which is the form to quote. 12:16 and 3:4 describe the identical relationship and the second is easier to hold in your head.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How do I simplify a ratio?',
				'a' => 'Divide both numbers by their greatest common factor. 12:18 divided by 6 gives 2:3.',
			),
			array(
				'q' => 'What is the difference between a ratio and a fraction?',
				'a' => 'A ratio compares two parts to each other and a fraction compares one part to the whole. In a 1:3 mix there are four parts in total, so the first ingredient is one quarter of it, not one third.',
			),
		),
		'related' => array(
			'proportion-calculator',
			'fraction-calculator',
			'percentage-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
