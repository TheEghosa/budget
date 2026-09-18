<?php
/**
 * Fraction Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'fraction-calculator',
		'title' => 'Fraction Calculator',
		'category' => 'math',
		'description' => 'Add, subtract, multiply or divide two fractions and simplify the result.',
		'keyword' => 'Fraction Calculator',
		'h1' => 'Fraction Calculator',
		'meta_title' => 'Fraction Calculator - Add, Subtract, Multiply, Divide',
		'meta_description' => 'Free fraction calculator. Add, subtract, multiply or divide two fractions and get the answer fully simplified, as a decimal and as a percentage.',
		'fields' => array(
			array(
				'id' => 'n1',
				'label' => 'First numerator',
				'type' => 'number',
				'default' => 1,
			),
			array(
				'id' => 'd1',
				'label' => 'First denominator',
				'type' => 'number',
				'default' => 2,
			),
			array(
				'id' => 'op',
				'label' => 'Operation',
				'type' => 'segmented',
				'options' => array(
					'add' => '+',
					'sub' => '−',
					'mul' => '×',
					'div' => '÷',
				),
				'default' => 'add',
			),
			array(
				'id' => 'n2',
				'label' => 'Second numerator',
				'type' => 'number',
				'default' => 1,
			),
			array(
				'id' => 'd2',
				'label' => 'Second denominator',
				'type' => 'number',
				'default' => 3,
			),
		),
		'default_result' => array(
			'label' => '1/2 + 1/3',
			'value' => '5/6',
			'rows' => array(
				array(
					'label' => 'As a decimal',
					'value' => '0.833333',
				),
				array(
					'label' => 'As a percentage',
					'value' => '83.3333%',
				),
				array(
					'label' => 'Improper form',
					'value' => '5/6',
				),
			),
			'note' => '',
		),
		'explainer' => array(
			array(
				'heading' => 'Why addition needs a common denominator',
				'body' => 'Halves and thirds are different sized pieces, so they cannot be counted together until both are expressed in the same size. Multiplying the denominators always gives a workable common one, which is what happens here, and the result is then reduced to its simplest form.',
				'formula' => 'a/b + c/d = (ad + cb) / bd',
			),
			array(
				'heading' => 'Multiplication is the easy one',
				'body' => 'Multiplying fractions needs no common denominator at all: numerators across the top, denominators across the bottom. Division is the same operation after flipping the second fraction, which is why it is sometimes taught as multiplying by the reciprocal.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How do I simplify a fraction?',
				'a' => 'Divide the top and bottom by their greatest common factor. This calculator does it automatically, and the GCF calculator will show you the factor itself.',
			),
			array(
				'q' => 'What is an improper fraction?',
				'a' => 'One where the numerator is larger than the denominator, such as 7/4. It is perfectly valid and often easier to calculate with than the mixed number 1 3/4, which is why both forms are shown.',
			),
		),
		'related' => array(
			'decimal-to-fraction-calculator',
			'gcf-calculator',
			'ratio-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
