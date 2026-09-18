<?php
/**
 * GCF Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'gcf-calculator',
		'title' => 'GCF Calculator',
		'category' => 'math',
		'description' => 'Find the greatest common factor of two or more numbers.',
		'keyword' => 'GCF Calculator',
		'h1' => 'GCF Calculator',
		'meta_title' => 'GCF Calculator - Greatest Common Factor of Any Numbers',
		'meta_description' => 'Free GCF calculator. Enter two or more whole numbers to find the greatest common factor, the lowest common multiple and every shared factor.',
		'fields' => array(
			array(
				'id' => 'numbers',
				'label' => 'Your numbers',
				'type' => 'text',
				'default' => '48, 180',
				'hint' => 'Separate numbers with spaces or commas.',
			),
		),
		'default_result' => array(
			'label' => 'Greatest common factor',
			'value' => '12',
			'rows' => array(
				array(
					'label' => 'Lowest common multiple',
					'value' => '720',
				),
				array(
					'label' => 'Numbers',
					'value' => '48, 180',
				),
				array(
					'label' => 'Shared factors',
					'value' => '1, 2, 3, 4, 6, 12',
				),
				array(
					'label' => 'Coprime',
					'value' => 'no',
				),
			),
			'note' => '',
		),
		'explainer' => array(
			array(
				'heading' => 'What the GCF is for',
				'body' => 'The greatest common factor is the largest number that divides all your numbers exactly. Its main everyday use is reducing fractions: dividing the top and bottom by their GCF gives the simplest form in one step rather than by repeated halving.',
				'formula' => 'GCF(48, 180) = 12',
			),
			array(
				'heading' => 'Euclid’s algorithm',
				'body' => 'The method used here is over two thousand years old and still the fastest known: divide the larger by the smaller, replace the larger with the remainder, and repeat until the remainder is zero. Whatever is left is the GCF, and it works on numbers of any size.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What if the GCF is 1?',
				'a' => 'The numbers are coprime, meaning they share no factors other than one. It does not mean either is prime, just that they have nothing in common.',
			),
			array(
				'q' => 'How are GCF and LCM related?',
				'a' => 'For two numbers, the GCF multiplied by the LCM always equals the two numbers multiplied together, which is a quick way to find one from the other.',
			),
		),
		'related' => array(
			'lcm-calculator',
			'factor-calculator',
			'fraction-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
