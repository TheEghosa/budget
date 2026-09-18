<?php
/**
 * Tire Size Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'tire-size-calculator',
		'title' => 'Tire Size Calculator',
		'category' => 'convert',
		'description' => 'Work out tyre diameter and compare two sizes.',
		'keyword' => 'Tire Size Calculator',
		'h1' => 'Tire Size Calculator',
		'meta_title' => 'Tire Size Calculator - Diameter and Speedo Error',
		'meta_description' => 'Free tire size calculator. Decode a tyre size into diameter, sidewall and revolutions per mile, and compare two sizes for speedometer error.',
		'fields' => array(
			array(
				'id' => 'width',
				'label' => 'Section width',
				'type' => 'number',
				'suffix' => 'mm',
				'default' => 225,
			),
			array(
				'id' => 'ratio',
				'label' => 'Aspect ratio',
				'type' => 'number',
				'suffix' => '%',
				'default' => 45,
			),
			array(
				'id' => 'rim',
				'label' => 'Rim diameter',
				'type' => 'number',
				'suffix' => 'in',
				'default' => 17,
			),
			array(
				'id' => 'width2',
				'label' => 'Compare width',
				'type' => 'number',
				'suffix' => 'mm',
				'default' => 235,
			),
			array(
				'id' => 'ratio2',
				'label' => 'Compare ratio',
				'type' => 'number',
				'suffix' => '%',
				'default' => 40,
			),
			array(
				'id' => 'rim2',
				'label' => 'Compare rim',
				'type' => 'number',
				'suffix' => 'in',
				'default' => 18,
			),
		),
		'default_result' => array(
			'label' => 'Overall diameter',
			'value' => '24.972 in',
			'rows' => array(
				array(
					'label' => 'Sidewall height',
					'value' => '101.3 mm',
				),
				array(
					'label' => 'Circumference',
					'value' => '78.45 in',
				),
				array(
					'label' => 'Revolutions per mile',
					'value' => '808',
				),
				array(
					'label' => 'Comparison diameter',
					'value' => '25.402 in',
				),
				array(
					'label' => 'Difference',
					'value' => '1.72%',
				),
				array(
					'label' => 'Speedo reads 60, actual',
					'value' => '61 mph',
				),
			),
			'note' => 'Stay within about three per cent of the original diameter. Beyond that the speedometer, odometer and in many cars the traction control all read wrong, and the tyre may foul the arch on full lock.',
		),
		'explainer' => array(
			array(
				'heading' => 'Reading a tyre size',
				'body' => '225/45R17 means a section width of 225 millimetres, a sidewall height that is 45 per cent of that width, and a 17 inch rim. The aspect ratio is a percentage rather than a measurement, so a wider tyre with the same ratio also has a taller sidewall.',
				'formula' => 'Diameter = (width × ratio⁄₁₀₀ × 2 ÷ 25.4) + rim',
			),
			array(
				'heading' => 'The three per cent rule',
				'body' => 'Stay within about three per cent of the original overall diameter. Beyond that the speedometer and odometer read wrong, ABS and traction control receive incorrect wheel speeds, and the tyre may rub the arch or suspension at full lock or full compression.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'Will bigger tyres make my speedometer wrong?',
				'a' => 'Yes. A larger diameter covers more ground per revolution, so the speedometer under-reads and you are travelling faster than it shows. The comparison row quantifies it.',
			),
			array(
				'q' => 'What does plus sizing mean?',
				'a' => 'Fitting a larger rim with a lower profile tyre so the overall diameter stays roughly the same. It sharpens the steering response and costs you ride comfort.',
			),
		),
		'related' => array(
			'circumference-calculator',
			'distance-calculator',
			'unit-converter',
		),
		'disclaimer' => '',
	);
