<?php
/**
 * Inches to Feet Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'inches-to-feet-calculator',
		'title' => 'Inches to Feet Calculator',
		'category' => 'convert',
		'description' => 'Convert inches to feet, as a decimal and as feet and inches.',
		'keyword' => 'Inches to Feet Calculator',
		'h1' => 'Inches to Feet Calculator',
		'meta_title' => 'Inches to Feet Calculator - Decimal and Feet-Inches',
		'meta_description' => 'Free inches to feet converter. Get both the decimal figure and the proper feet and inches form, which are not the same and are often confused.',
		'fields' => array(
			array(
				'id' => 'value',
				'label' => 'Inches',
				'type' => 'number',
				'default' => 78,
				'step' => 'any',
			),
		),
		'default_result' => array(
			'label' => '78 in in ft',
			'value' => '6.5 ft',
			'rows' => array(
				array(
					'label' => 'Feet and inches',
					'value' => '6 ft 6 in',
				),
				array(
					'label' => 'Centimetres',
					'value' => '198.12',
				),
				array(
					'label' => 'Metres',
					'value' => '1.9812',
				),
				array(
					'label' => 'Inches',
					'value' => '78',
				),
				array(
					'label' => 'Feet',
					'value' => '6.5',
				),
			),
			'note' => 'Twelve inches to the foot, so the decimal part of the answer is not inches: 6.5 feet is six feet and six inches, not six feet five.',
		),
		'explainer' => array(
			array(
				'heading' => 'The decimal trap',
				'body' => 'Seventy-eight inches is 6.5 feet, and 6.5 feet is six feet six inches, not six feet five. The decimal part is a fraction of twelve rather than a count of inches, and mixing the two up is behind a great many mismeasured cuts.',
				'formula' => '6.5 ft = 6 ft + 0.5 × 12 in = 6 ft 6 in',
			),
			array(
				'heading' => 'Which form to use where',
				'body' => 'Construction drawings and lumber use feet and inches. Spreadsheets, CAD and anything you need to do arithmetic on want the decimal. Both are shown so you can carry the right one into the next step.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How many feet is 72 inches?',
				'a' => 'Exactly 6 feet, since twelve inches make a foot.',
			),
			array(
				'q' => 'How do I convert 5 foot 9 into inches?',
				'a' => 'Multiply the feet by twelve and add the inches, giving 69.',
			),
		),
		'related' => array(
			'mm-to-inches-calculator',
			'feet-to-meters-calculator',
			'unit-converter',
		),
		'disclaimer' => '',
	);
