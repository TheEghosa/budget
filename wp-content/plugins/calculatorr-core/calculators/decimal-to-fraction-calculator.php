<?php
/**
 * Decimal to Fraction Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'decimal-to-fraction-calculator',
		'title' => 'Decimal to Fraction Calculator',
		'category' => 'math',
		'description' => 'Turn any decimal into its simplest fraction.',
		'keyword' => 'Decimal to Fraction Calculator',
		'h1' => 'Decimal to Fraction Calculator',
		'meta_title' => 'Decimal to Fraction Calculator - Simplest Form',
		'meta_description' => 'Free decimal to fraction converter. Enter any decimal to get the simplest equivalent fraction, the improper form and the percentage it represents.',
		'fields' => array(
			array(
				'id' => 'decimal',
				'label' => 'Decimal',
				'type' => 'number',
				'default' => 0.375,
				'step' => 'any',
			),
		),
		'default_result' => array(
			'label' => '0.375 as a fraction',
			'value' => '3/8',
			'rows' => array(
				array(
					'label' => 'Improper form',
					'value' => '3/8',
				),
				array(
					'label' => 'Back to decimal',
					'value' => '0.375',
				),
				array(
					'label' => 'As a percentage',
					'value' => '37.5%',
				),
			),
			'note' => '',
		),
		'explainer' => array(
			array(
				'heading' => 'Finding the simplest fraction rather than the literal one',
				'body' => '0.375 could be written as 375/1000, which is correct and useless. What people want is 3/8. This calculator uses continued fractions to find the simplest fraction within a very tight tolerance, which handles repeating decimals like 0.333 landing on 1/3 rather than 333/1000.',
				'formula' => '0.375 = 375/1000 = 3/8',
			),
			array(
				'heading' => 'Terminating and repeating decimals',
				'body' => 'A decimal terminates only when the fraction behind it has a denominator made solely of twos and fives, which is why thirds and sevenths repeat forever in base ten and halves and quarters do not.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What is 0.625 as a fraction?',
				'a' => 'Five eighths. The calculator finds it by reducing 625/1000 by its greatest common factor of 125.',
			),
			array(
				'q' => 'How do I convert a repeating decimal?',
				'a' => 'Enter as many digits as you can and the calculator converges on the simplest fraction, so 0.6666667 returns 2/3 rather than a long ugly fraction.',
			),
		),
		'related' => array(
			'fraction-calculator',
			'percentage-calculator',
			'gcf-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
