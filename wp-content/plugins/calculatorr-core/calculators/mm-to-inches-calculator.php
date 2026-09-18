<?php
/**
 * MM to Inches Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'mm-to-inches-calculator',
		'title' => 'MM to Inches Calculator',
		'category' => 'convert',
		'description' => 'Convert millimetres to inches and back.',
		'keyword' => 'MM to Inches Calculator',
		'h1' => 'MM to Inches Calculator',
		'meta_title' => 'MM to Inches Calculator - Millimetres to Inches',
		'meta_description' => 'Free millimetre to inch converter. Change mm to inches with the centimetre, metre and feet equivalents shown alongside for quick reference.',
		'fields' => array(
			array(
				'id' => 'value',
				'label' => 'Millimetres',
				'type' => 'number',
				'default' => 100,
				'step' => 'any',
			),
		),
		'default_result' => array(
			'label' => '100 mm in in',
			'value' => '3.93701 in',
			'rows' => array(
				array(
					'label' => 'Centimetres',
					'value' => '10',
				),
				array(
					'label' => 'Metres',
					'value' => '0.1',
				),
				array(
					'label' => 'Inches',
					'value' => '3.93701',
				),
				array(
					'label' => 'Feet',
					'value' => '0.32808',
				),
			),
			'note' => 'Divide millimetres by 25.4 to get inches. For a quick mental check, 25 mm is almost exactly an inch.',
		),
		'explainer' => array(
			array(
				'heading' => 'The single number to remember',
				'body' => 'Divide millimetres by 25.4 to get inches, and multiply the other way. That factor is exact by definition rather than rounded, so there is no accumulating error however many times you convert.',
				'formula' => 'inches = mm ÷ 25.4',
			),
			array(
				'heading' => 'Rough mental conversion',
				'body' => 'Twenty-five millimetres is almost exactly one inch, so millimetres divided by twenty-five gets you within two per cent. Good enough for judging whether something will fit, not good enough for machining it.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How many mm in an inch?',
				'a' => 'Exactly 25.4. It has been defined that way internationally since 1959.',
			),
			array(
				'q' => 'Why are screen and pipe sizes still in inches?',
				'a' => 'Convention and an installed base. Manufacturing tooling, fittings and standards all assume inches, and changing them would cost far more than the inconvenience of the conversion.',
			),
		),
		'related' => array(
			'inches-to-feet-calculator',
			'unit-converter',
			'feet-to-meters-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
