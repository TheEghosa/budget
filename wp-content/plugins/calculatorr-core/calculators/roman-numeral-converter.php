<?php
/**
 * Roman Numeral Converter.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'roman-numeral-converter',
		'title' => 'Roman Numeral Converter',
		'category' => 'math',
		'description' => 'Convert numbers to Roman numerals and back again.',
		'keyword' => 'Roman Numeral Converter',
		'h1' => 'Roman Numeral Converter',
		'meta_title' => 'Roman Numeral Converter - Numbers Both Ways',
		'meta_description' => 'Free Roman numeral converter. Turn any number from 1 to 3999 into Roman numerals, or read numerals back into numbers, with the standard form shown.',
		'fields' => array(
			array(
				'id' => 'direction',
				'label' => 'Direction',
				'type' => 'segmented',
				'options' => array(
					'toRoman' => 'Number to Roman',
					'toNumber' => 'Roman to number',
				),
				'default' => 'toRoman',
			),
			array(
				'id' => 'number',
				'label' => 'Number',
				'type' => 'number',
				'default' => 1994,
				'show_when' => array(
					'direction' => 'toRoman',
				),
			),
			array(
				'id' => 'roman',
				'label' => 'Roman numeral',
				'type' => 'text',
				'default' => 'MCMXCIV',
				'show_when' => array(
					'direction' => 'toNumber',
				),
			),
		),
		'default_result' => array(
			'label' => '1994 in Roman numerals',
			'value' => 'MCMXCIV',
			'rows' => array(
				array(
					'label' => 'Characters',
					'value' => '7',
				),
				array(
					'label' => 'Broken down',
					'value' => 'M C M X C I V',
				),
			),
			'note' => '',
		),
		'explainer' => array(
			array(
				'heading' => 'Subtractive notation',
				'body' => 'Roman numerals put a smaller symbol before a larger one to mean subtraction, so IV is four and IX is nine. Only six such pairs are standard: IV, IX, XL, XC, CD and CM. Anything else, such as IIX for eight, is readable but not correct.',
				'formula' => '1994 = MCMXCIV = M + CM + XC + IV',
			),
			array(
				'heading' => 'Where the system stops',
				'body' => 'There is no zero and no standard single character above M, so the system runs out at 3999. The overline notation for thousands exists but was never consistently used, which is part of why Roman numerals survive only on clock faces, film credits and monarchs.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What is 2026 in Roman numerals?',
				'a' => 'MMXXVI. Two thousands, two tens, a five and a one.',
			),
			array(
				'q' => 'Why is there no zero in Roman numerals?',
				'a' => 'The system counts things rather than marking place value, so a symbol for nothing was never needed. Positional notation with a zero arrived from India through Arabic mathematics much later.',
			),
		),
		'related' => array(
			'factor-calculator',
			'average-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
