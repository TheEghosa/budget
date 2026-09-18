<?php
/**
 * Unit Converter.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'unit-converter',
		'title' => 'Unit Converter',
		'category' => 'convert',
		'description' => 'Convert between millimetres, inches, feet, metres, yards and miles.',
		'keyword' => 'Unit Converter',
		'h1' => 'Unit Converter',
		'meta_title' => 'Unit Converter - Length in Metric and Imperial',
		'meta_description' => 'Free length unit converter. Change between millimetres, centimetres, metres, kilometres, inches, feet, yards and miles with every unit shown at once.',
		'fields' => array(
			array(
				'id' => 'value',
				'label' => 'Amount',
				'type' => 'number',
				'default' => 1,
				'step' => 'any',
			),
			array(
				'id' => 'from',
				'label' => 'From',
				'type' => 'select',
				'options' => array(
					'mm' => 'Millimetres',
					'cm' => 'Centimetres',
					'm' => 'Metres',
					'km' => 'Kilometres',
					'in' => 'Inches',
					'ft' => 'Feet',
					'yd' => 'Yards',
					'mi' => 'Miles',
				),
				'default' => 'm',
			),
			array(
				'id' => 'to',
				'label' => 'To',
				'type' => 'select',
				'options' => array(
					'mm' => 'Millimetres',
					'cm' => 'Centimetres',
					'm' => 'Metres',
					'km' => 'Kilometres',
					'in' => 'Inches',
					'ft' => 'Feet',
					'yd' => 'Yards',
					'mi' => 'Miles',
				),
				'default' => 'ft',
			),
		),
		'default_result' => array(
			'label' => '1 m converted',
			'value' => '3.28084 ft',
			'rows' => array(
				array(
					'label' => 'Millimetres',
					'value' => '1,000',
				),
				array(
					'label' => 'Centimetres',
					'value' => '100',
				),
				array(
					'label' => 'Metres',
					'value' => '1',
				),
				array(
					'label' => 'Inches',
					'value' => '39.3701',
				),
				array(
					'label' => 'Feet',
					'value' => '3.28084',
				),
				array(
					'label' => 'Miles',
					'value' => '0.0006214',
				),
			),
			'note' => 'An inch has been defined as exactly 25.4 millimetres since 1959, so every conversion between the two systems is exact rather than approximate.',
		),
		'explainer' => array(
			array(
				'heading' => 'Every conversion here is exact',
				'body' => 'Since 1959 an inch has been defined as exactly 25.4 millimetres by international agreement, which makes every metric to imperial length conversion exact rather than approximate. Before that, the US and UK inches differed very slightly, which caused real problems in engineering.',
				'formula' => '1 in = 25.4 mm, 1 ft = 0.3048 m',
			),
			array(
				'heading' => 'Useful approximations',
				'body' => 'A metre is a long stride, a centimetre is about the width of a fingernail and a kilometre is roughly six tenths of a mile. Those are close enough for estimating and worth carrying in your head.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How many feet in a metre?',
				'a' => 'About 3.28. Going the other way, a foot is a little under a third of a metre.',
			),
			array(
				'q' => 'How do I convert square or cubic units?',
				'a' => 'Square the conversion factor for areas and cube it for volumes. A square metre is 10.76 square feet, not 3.28.',
			),
		),
		'related' => array(
			'mm-to-inches-calculator',
			'feet-to-meters-calculator',
			'weight-converter',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
