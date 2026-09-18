<?php
/**
 * Celsius to Fahrenheit Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'celsius-to-fahrenheit-calculator',
		'title' => 'Celsius to Fahrenheit Calculator',
		'category' => 'convert',
		'description' => 'Convert between Celsius, Fahrenheit and Kelvin.',
		'keyword' => 'Celsius to Fahrenheit Calculator',
		'h1' => 'Celsius to Fahrenheit Calculator',
		'meta_title' => 'Celsius to Fahrenheit Calculator - Plus Kelvin',
		'meta_description' => 'Free Celsius to Fahrenheit converter. Change temperatures between Celsius, Fahrenheit and Kelvin in one step, with freezing and boiling points marked.',
		'fields' => array(
			array(
				'id' => 'temperature',
				'label' => 'Temperature',
				'type' => 'number',
				'default' => 20,
				'step' => 'any',
			),
			array(
				'id' => 'from',
				'label' => 'Measured in',
				'type' => 'segmented',
				'options' => array(
					'c' => 'Celsius',
					'f' => 'Fahrenheit',
					'k' => 'Kelvin',
				),
				'default' => 'c',
			),
		),
		'default_result' => array(
			'label' => '20°C converted',
			'value' => '68°F',
			'rows' => array(
				array(
					'label' => 'Celsius',
					'value' => '20°C',
				),
				array(
					'label' => 'Fahrenheit',
					'value' => '68°F',
				),
				array(
					'label' => 'Kelvin',
					'value' => '293.15 K',
				),
				array(
					'label' => 'Reference',
					'value' => 'liquid water range',
				),
			),
			'note' => 'The two scales meet at minus forty, which is the same temperature in Celsius and Fahrenheit and the only point where they agree.',
		),
		'explainer' => array(
			array(
				'heading' => 'Why the conversion needs both a multiply and an add',
				'body' => 'The two scales disagree on where zero sits and on how big a degree is. Fahrenheit degrees are five ninths the size of Celsius ones, and its zero sits 32 degrees below freezing, so converting needs both a scaling step and an offset.',
				'formula' => '°F = °C × 9⁄₅ + 32',
			),
			array(
				'heading' => 'The one point they agree',
				'body' => 'Minus forty is the same temperature on both scales, which is the only place the two lines cross. It is a genuinely useful thing to remember, because it gives you a fixed anchor to sanity check any conversion against.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What is 100 degrees Fahrenheit in Celsius?',
				'a' => 'About 37.8, which is roughly body temperature. That is why 98.6 Fahrenheit and 37 Celsius describe the same thing.',
			),
			array(
				'q' => 'Why does Kelvin have no degree symbol?',
				'a' => 'Because it is an absolute scale starting at absolute zero rather than at an arbitrary reference point, so a Kelvin is a unit in its own right rather than a degree of something.',
			),
		),
		'related' => array(
			'unit-converter',
			'weight-converter',
		),
		'disclaimer' => '',
	);
