<?php
/**
 * LCM Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'lcm-calculator',
		'title' => 'LCM Calculator',
		'category' => 'math',
		'description' => 'Find the lowest common multiple of two or more numbers.',
		'keyword' => 'LCM Calculator',
		'h1' => 'LCM Calculator',
		'meta_title' => 'LCM Calculator - Lowest Common Multiple',
		'meta_description' => 'Free LCM calculator. Enter two or more whole numbers to find the lowest common multiple, the greatest common factor and the prime factors behind it.',
		'fields' => array(
			array(
				'id' => 'numbers',
				'label' => 'Your numbers',
				'type' => 'text',
				'default' => '4, 6',
				'hint' => 'Separate numbers with spaces or commas.',
			),
		),
		'default_result' => array(
			'label' => 'Lowest common multiple',
			'value' => '12',
			'rows' => array(
				array(
					'label' => 'Greatest common factor',
					'value' => '2',
				),
				array(
					'label' => 'Numbers',
					'value' => '4, 6',
				),
				array(
					'label' => 'Prime factors of the LCM',
					'value' => '2 × 2 × 3',
				),
			),
			'note' => 'For two numbers the LCM times the GCF always equals the two numbers multiplied together, which is a quick way to check the answer.',
		),
		'explainer' => array(
			array(
				'heading' => 'What the LCM is for',
				'body' => 'The lowest common multiple is the smallest number all of yours divide into. It is what you need to add fractions with different denominators, and it answers scheduling questions: two buses leaving every 4 and 6 minutes next depart together after 12.',
				'formula' => 'LCM(4, 6) = 12',
			),
			array(
				'heading' => 'Finding it without listing multiples',
				'body' => 'Listing multiples works for small numbers and becomes hopeless quickly. Multiplying the numbers and dividing by their greatest common factor gives the same answer instantly, which is the method used here.',
				'formula' => 'LCM(a, b) = (a &times; b) &divide; GCF(a, b)',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What is the LCM of two prime numbers?',
				'a' => 'Their product, because primes share no factors, so nothing cancels.',
			),
			array(
				'q' => 'Why do I need the LCM to add fractions?',
				'a' => 'Because fractions can only be added when the pieces are the same size. The LCM of the denominators is the smallest common size that works.',
			),
		),
		'related' => array(
			'gcf-calculator',
			'fraction-calculator',
			'factor-calculator',
		),
		'disclaimer' => '',
	);
