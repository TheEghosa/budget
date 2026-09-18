<?php
/**
 * Factor Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'factor-calculator',
		'title' => 'Factor Calculator',
		'category' => 'math',
		'description' => 'List every factor of a number and its prime factorisation.',
		'keyword' => 'Factor Calculator',
		'h1' => 'Factor Calculator',
		'meta_title' => 'Factor Calculator - All Factors and Prime Factors',
		'meta_description' => 'Free factor calculator. Enter a whole number to list every factor, the prime factorisation in exponent form, and whether the number is prime.',
		'fields' => array(
			array(
				'id' => 'number',
				'label' => 'Number',
				'type' => 'number',
				'default' => 360,
			),
		),
		'default_result' => array(
			'label' => 'Factors of 360',
			'value' => '24 factors',
			'rows' => array(
				array(
					'label' => 'All factors',
					'value' => '1, 2, 3, 4, 5, 6, 8, 9, 10, 12, 15, 18, 20, 24, 30, 36, 40, 45, 60, 72, 90, 120, 180, 360',
				),
				array(
					'label' => 'Prime factorisation',
					'value' => '2^3 × 3^2 × 5',
				),
				array(
					'label' => 'Prime number',
					'value' => 'no',
				),
				array(
					'label' => 'Sum of factors',
					'value' => '1170',
				),
			),
			'note' => '',
		),
		'explainer' => array(
			array(
				'heading' => 'Factors and prime factors are different lists',
				'body' => 'The factors of 360 are every number that divides it exactly, and there are twenty-four of them. The prime factorisation is the unique set of primes that multiply to make it, which for 360 is two cubed times three squared times five. Every whole number above one has exactly one prime factorisation, which is why it is called the fundamental theorem of arithmetic.',
				'formula' => '360 = 2&sup3; &times; 3&sup2; &times; 5',
			),
			array(
				'heading' => 'Why factors come in pairs',
				'body' => 'Every factor below the square root has a partner above it, which is why the list is always symmetric around the root and why checking up to the square root is enough to find them all. A perfect square is the only case where one factor is its own partner.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How do I know if a number is prime?',
				'a' => 'It has exactly two factors, one and itself. The calculator says so directly.',
			),
			array(
				'q' => 'What is prime factorisation used for?',
				'a' => 'Finding greatest common factors and lowest common multiples, simplifying radicals, and in cryptography, where the difficulty of factorising very large numbers is what keeps encryption secure.',
			),
		),
		'related' => array(
			'gcf-calculator',
			'lcm-calculator',
			'square-root-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
