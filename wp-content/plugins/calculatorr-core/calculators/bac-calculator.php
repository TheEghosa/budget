<?php
/**
 * BAC Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'bac-calculator',
		'title' => 'BAC Calculator',
		'category' => 'health',
		'description' => 'Estimate blood alcohol content from drinks, weight and time.',
		'keyword' => 'BAC Calculator',
		'h1' => 'BAC Calculator',
		'meta_title' => 'BAC Calculator - Estimate Blood Alcohol Content',
		'meta_description' => 'Free BAC calculator using the Widmark formula. Estimate blood alcohol from standard drinks, body weight and hours elapsed, with the legal limits marked.',
		'fields' => array(
			array(
				'id' => 'drinks',
				'label' => 'Standard drinks',
				'type' => 'number',
				'default' => 3,
				'hint' => 'One standard drink is 0.6 fl oz of pure alcohol: a 12oz beer, 5oz wine or 1.5oz spirit.',
			),
			array(
				'id' => 'units',
				'label' => 'Units',
				'type' => 'segmented',
				'options' => array(
					'metric' => 'Metric',
					'imperial' => 'Imperial',
				),
				'default' => 'imperial',
			),
			array(
				'id' => 'pounds',
				'label' => 'Body weight',
				'type' => 'number',
				'suffix' => 'lb',
				'default' => 180,
				'show_when' => array(
					'units' => 'imperial',
				),
			),
			array(
				'id' => 'weight',
				'label' => 'Body weight',
				'type' => 'number',
				'suffix' => 'kg',
				'default' => 82,
				'show_when' => array(
					'units' => 'metric',
				),
			),
			array(
				'id' => 'sex',
				'label' => 'Sex assigned at birth',
				'type' => 'segmented',
				'options' => array(
					'male' => 'Male',
					'female' => 'Female',
				),
				'default' => 'male',
			),
			array(
				'id' => 'hours',
				'label' => 'Hours since your first drink',
				'type' => 'number',
				'default' => 2,
			),
		),
		'default_result' => array(
			'label' => 'Estimated BAC',
			'value' => '0.040%',
			'rows' => array(
				array(
					'label' => 'Status',
					'value' => 'Under 0.05',
				),
				array(
					'label' => 'Peak before metabolism',
					'value' => '0.070%',
				),
				array(
					'label' => 'Pure alcohol consumed',
					'value' => '1.8 fl oz',
				),
				array(
					'label' => 'Hours until zero',
					'value' => '2.7',
				),
			),
			'note' => 'A rough population estimate from the Widmark formula, not a measurement. Food, medication, body composition, liver function and how fast you drank all move the real figure, often by a lot. Never use this to decide whether to drive. If you have been drinking, do not drive.',
		),
		'explainer' => array(
			array(
				'heading' => 'The Widmark formula',
				'body' => 'Alcohol distributes through body water, so the same drink produces a higher concentration in a smaller person. The formula divides the alcohol consumed by body weight times a distribution factor, then subtracts metabolism at roughly 0.015 per hour, which is remarkably constant and cannot be sped up by coffee, food or a cold shower.',
				'formula' => 'BAC = (alcohol oz × 5.14) ÷ (weight lb × r) − 0.015 × hours',
			),
			array(
				'heading' => 'Why this cannot tell you whether to drive',
				'body' => 'It is a population average with wide individual variation. Food in the stomach, medication, liver function, body composition, drinking speed and simple genetics all move the real figure, sometimes substantially. There is no safe way to calculate your way to the limit. If you have been drinking, do not drive.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How long does alcohol take to leave your system?',
				'a' => 'Roughly one standard drink per hour, though that is an average rather than a rule. The calculator shows an estimated time to zero based on the same 0.015 per hour rate.',
			),
			array(
				'q' => 'Does eating lower your BAC?',
				'a' => 'Food slows absorption, which lowers the peak, but it does not reduce the total alcohol your body has to process. The area under the curve is the same.',
			),
			array(
				'q' => 'Is 0.08 the limit everywhere?',
				'a' => 'No. It is the standard in most US states for drivers over 21, but many countries use 0.05 or lower, commercial drivers face tighter limits, and under-21 drivers in the US are subject to zero tolerance laws.',
			),
		),
		'related' => array(
			'water-intake-calculator',
			'tdee-calculator',
		),
		'disclaimer' => 'An estimate from a population average, never a measurement. Do not use it to decide whether to drive. If you have been drinking, do not drive.',
	);
