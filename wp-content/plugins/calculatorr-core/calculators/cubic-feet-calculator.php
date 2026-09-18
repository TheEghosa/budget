<?php
/**
 * Cubic Feet Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'cubic-feet-calculator',
		'title' => 'Cubic Feet Calculator',
		'category' => 'geometry',
		'description' => 'Convert any dimensions into cubic feet.',
		'keyword' => 'Cubic Feet Calculator',
		'h1' => 'Cubic Feet Calculator',
		'meta_title' => 'Cubic Feet Calculator - Volume for Shipping & Storage',
		'meta_description' => 'Free cubic feet calculator. Enter length, width and height in any unit for the volume in cubic feet, cubic yards, cubic metres, gallons and litres.',
		'fields' => array(
			array(
				'id' => 'length',
				'label' => 'Length',
				'type' => 'number',
				'default' => 3,
			),
			array(
				'id' => 'width',
				'label' => 'Width',
				'type' => 'number',
				'default' => 3,
			),
			array(
				'id' => 'heightVal',
				'label' => 'Height',
				'type' => 'number',
				'default' => 3,
			),
			array(
				'id' => 'unit',
				'label' => 'Measured in',
				'type' => 'segmented',
				'options' => array(
					'inches' => 'Inches',
					'feet' => 'Feet',
					'yards' => 'Yards',
					'cm' => 'Centimetres',
					'metres' => 'Metres',
				),
				'default' => 'feet',
			),
			array(
				'id' => 'quantity',
				'label' => 'How many',
				'type' => 'number',
				'default' => 1,
			),
		),
		'default_result' => array(
			'label' => 'Volume',
			'value' => '27 cu ft',
			'rows' => array(
				array(
					'label' => 'Cubic yards',
					'value' => '1',
				),
				array(
					'label' => 'Cubic metres',
					'value' => '0.7646',
				),
				array(
					'label' => 'Cubic inches',
					'value' => '46,656',
				),
				array(
					'label' => 'US gallons',
					'value' => '201.97',
				),
				array(
					'label' => 'Litres',
					'value' => '764.6',
				),
			),
			'note' => 'Shipping and storage are quoted in cubic feet, and a standard moving box is about 1.5. Freight is normally billed on whichever is greater, the volume or the weight.',
		),
		'explainer' => array(
			array(
				'heading' => 'Why shipping is priced this way',
				'body' => 'Freight and storage are charged on whichever is greater, the space something takes or its weight, because a lorry runs out of room long before it runs out of payload with light bulky goods. Cubic feet is the volume half of that calculation, and dimensional weight is how carriers convert it into a billable figure.',
				'formula' => 'Cubic feet = L × W × H, all in feet',
			),
			array(
				'heading' => 'Converting from inches correctly',
				'body' => 'A cubic foot is 1,728 cubic inches, not 144. All three dimensions are being converted at once, so the factor is twelve cubed. Dividing by twelve or by 144 is the usual slip and it is off by a wide margin.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How many cubic feet is a standard moving box?',
				'a' => 'Roughly 1.5 for a medium box and 3 to 4.5 for a large one, which is why movers estimate in box counts rather than measurements.',
			),
			array(
				'q' => 'How do I work out the cubic feet of a fridge?',
				'a' => 'Measure the internal height, width and depth in inches, multiply them together and divide by 1,728.',
			),
		),
		'related' => array(
			'cubic-yard-calculator',
			'volume-calculator',
			'unit-converter',
		),
		'disclaimer' => '',
	);
