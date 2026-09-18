<?php
/**
 * Weight Converter.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'weight-converter',
		'title' => 'Weight Converter',
		'category' => 'convert',
		'description' => 'Convert between kilograms, pounds, ounces, stone and tonnes.',
		'keyword' => 'Weight Converter',
		'h1' => 'Weight Converter',
		'meta_title' => 'Weight Converter - Kg, Lb, Oz, Stone and Tonnes',
		'meta_description' => 'Free weight converter. Change between kilograms, pounds, ounces, grams, stone, tonnes and US tons, with every unit shown at once.',
		'fields' => array(
			array(
				'id' => 'value',
				'label' => 'Amount',
				'type' => 'number',
				'default' => 70,
				'step' => 'any',
			),
			array(
				'id' => 'from',
				'label' => 'From',
				'type' => 'select',
				'options' => array(
					'mg' => 'Milligrams',
					'g' => 'Grams',
					'kg' => 'Kilograms',
					't' => 'Tonnes',
					'oz' => 'Ounces',
					'lb' => 'Pounds',
					'st' => 'Stone',
					'ton' => 'US tons',
				),
				'default' => 'kg',
			),
			array(
				'id' => 'to',
				'label' => 'To',
				'type' => 'select',
				'options' => array(
					'mg' => 'Milligrams',
					'g' => 'Grams',
					'kg' => 'Kilograms',
					't' => 'Tonnes',
					'oz' => 'Ounces',
					'lb' => 'Pounds',
					'st' => 'Stone',
					'ton' => 'US tons',
				),
				'default' => 'lb',
			),
		),
		'default_result' => array(
			'label' => '70 kg converted',
			'value' => '154.3237 lb',
			'rows' => array(
				array(
					'label' => 'Kilograms',
					'value' => '70',
				),
				array(
					'label' => 'Pounds',
					'value' => '154.3237',
				),
				array(
					'label' => 'Ounces',
					'value' => '2,469.179',
				),
				array(
					'label' => 'Grams',
					'value' => '70,000',
				),
				array(
					'label' => 'Stone',
					'value' => '11.0231',
				),
				array(
					'label' => 'US tons',
					'value' => '0.07716',
				),
			),
			'note' => 'A US ton is 2,000 pounds and a metric tonne is 1,000 kilograms, which is about 2,205 pounds. They differ by roughly ten per cent, so the spelling matters on an invoice.',
		),
		'explainer' => array(
			array(
				'heading' => 'Tons are three different things',
				'body' => 'A US short ton is 2,000 pounds, a metric tonne is 1,000 kilograms or about 2,205 pounds, and a UK long ton is 2,240 pounds. They differ by up to twelve per cent, which on a freight invoice is a real amount of money, so the spelling and the origin of the quote both matter.',
				'formula' => '1 kg = 2.20462 lb exactly',
			),
			array(
				'heading' => 'Mass and weight',
				'body' => 'Strictly these units measure mass, and weight is the force gravity exerts on it. The distinction only bites in physics problems and in space, which is why everyday use treats them as the same thing without causing any harm.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How many pounds is 70 kg?',
				'a' => 'About 154.3 pounds, which is 11 stone in British usage.',
			),
			array(
				'q' => 'What is a stone in pounds?',
				'a' => 'Fourteen pounds. It is still the common way to talk about body weight in the UK and Ireland and is essentially unused elsewhere.',
			),
		),
		'related' => array(
			'unit-converter',
			'celsius-to-fahrenheit-calculator',
			'bmi-calculator',
		),
		'disclaimer' => '',
	);
