<?php
/**
 * Circumference Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'circumference-calculator',
		'title' => 'Circumference Calculator',
		'category' => 'geometry',
		'description' => 'Find the circumference of a circle from any known measurement.',
		'keyword' => 'Circumference Calculator',
		'h1' => 'Circumference Calculator',
		'meta_title' => 'Circumference Calculator - From Radius, Diameter or Area',
		'meta_description' => 'Free circumference calculator. Start from the radius, diameter, circumference or area of a circle and get all four, plus the quarter arc length.',
		'fields' => array(
			array(
				'id' => 'from',
				'label' => 'I know the',
				'type' => 'segmented',
				'options' => array(
					'radius' => 'Radius',
					'diameter' => 'Diameter',
					'circumference' => 'Circumference',
					'area' => 'Area',
				),
				'default' => 'radius',
			),
			array(
				'id' => 'value',
				'label' => 'Value',
				'type' => 'number',
				'default' => 5,
				'step' => 'any',
			),
		),
		'default_result' => array(
			'label' => 'Circumference',
			'value' => '31.415927',
			'rows' => array(
				array(
					'label' => 'Radius',
					'value' => '5',
				),
				array(
					'label' => 'Diameter',
					'value' => '10',
				),
				array(
					'label' => 'Area',
					'value' => '78.539816',
				),
				array(
					'label' => 'Quarter turn along the edge',
					'value' => '7.853982',
				),
			),
			'note' => 'Every circle has the same ratio of circumference to diameter, and that ratio is pi. It is why one measurement of a circle gives you all the others.',
		),
		'explainer' => array(
			array(
				'heading' => 'One measurement gives you all of them',
				'body' => 'Every circle has the same ratio of circumference to diameter, and that ratio is pi. Because of that single constant, knowing any one measurement of a circle determines every other one, which is not true of most shapes.',
				'formula' => 'C = 2πr = πd',
			),
			array(
				'heading' => 'Working backwards',
				'body' => 'If you can measure around something but not across it, divide the circumference by pi to get the diameter. That is how you size a pipe, a tree trunk or anything else you cannot cut in half.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What is the circumference of a 12 inch circle?',
				'a' => 'If 12 is the diameter, about 37.7 inches. If it is the radius, about 75.4. Check which measurement you have before starting.',
			),
			array(
				'q' => 'How do I find the radius from the circumference?',
				'a' => 'Divide by two pi, roughly 6.283. The calculator does this if you select circumference as your starting measurement.',
			),
		),
		'related' => array(
			'area-calculator',
			'cylinder-volume-calculator',
			'tire-size-calculator',
		),
		'disclaimer' => '',
	);
