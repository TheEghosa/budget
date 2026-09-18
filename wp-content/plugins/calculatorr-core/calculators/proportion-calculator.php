<?php
/**
 * Proportion Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'proportion-calculator',
		'title' => 'Proportion Calculator',
		'category' => 'math',
		'description' => 'Solve a proportion for any one of its four values.',
		'keyword' => 'Proportion Calculator',
		'h1' => 'Proportion Calculator',
		'meta_title' => 'Proportion Calculator - Solve for Any Missing Value',
		'meta_description' => 'Free proportion calculator. Enter three values in a proportion and solve for the fourth, with the cross products shown so you can check the answer by hand.',
		'fields' => array(
			array(
				'id' => 'solve',
				'label' => 'Solve for',
				'type' => 'segmented',
				'options' => array(
					'a' => 'a',
					'b' => 'b',
					'c' => 'c',
					'd' => 'd',
				),
				'default' => 'd',
			),
			array(
				'id' => 'a',
				'label' => 'a',
				'type' => 'number',
				'default' => 2,
			),
			array(
				'id' => 'b',
				'label' => 'b',
				'type' => 'number',
				'default' => 3,
			),
			array(
				'id' => 'c',
				'label' => 'c',
				'type' => 'number',
				'default' => 8,
			),
			array(
				'id' => 'd',
				'label' => 'd',
				'type' => 'number',
				'default' => 12,
			),
		),
		'default_result' => array(
			'label' => 'Solving for d',
			'value' => '12',
			'rows' => array(
				array(
					'label' => 'Method',
					'value' => '(b × c) ÷ a',
				),
				array(
					'label' => 'Cross product',
					'value' => '24 and 24',
				),
			),
			'note' => 'A proportion holds when the two cross products match, which is the quickest way to check any answer here by hand.',
		),
		'explainer' => array(
			array(
				'heading' => 'Cross multiplication',
				'body' => 'A proportion says two ratios are equal. Multiplying diagonally across the equals sign gives two products that must match, and rearranging that equality isolates whichever value is missing. It is the single most reusable piece of arithmetic in everyday life, behind unit pricing, map scales, recipe scaling and currency conversion.',
				'formula' => 'a/b = c/d means a &times; d = b &times; c',
			),
			array(
				'heading' => 'Checking your own working',
				'body' => 'Because the two cross products must be equal, any proportion can be checked in one step without redoing the algebra. If they differ, the answer is wrong.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How do I know a proportion is correct?',
				'a' => 'Multiply diagonally in both directions. If the two products match, the proportion holds.',
			),
			array(
				'q' => 'Where are proportions used in real life?',
				'a' => 'Unit pricing at the supermarket, scaling recipes, reading map scales, converting currency and mixing anything by ratio. It is the arithmetic most worth being fluent in.',
			),
		),
		'related' => array(
			'ratio-calculator',
			'percentage-calculator',
			'fraction-calculator',
		),
		'disclaimer' => '',
	);
