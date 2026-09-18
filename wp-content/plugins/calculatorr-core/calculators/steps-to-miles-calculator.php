<?php
/**
 * Steps to Miles Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'steps-to-miles-calculator',
		'title' => 'Steps to Miles Calculator',
		'category' => 'health',
		'description' => 'Convert a step count into miles and kilometres.',
		'keyword' => 'Steps to Miles Calculator',
		'h1' => 'Steps to Miles Calculator',
		'meta_title' => 'Steps to Miles Calculator - Convert Your Step Count',
		'meta_description' => 'Free steps to miles calculator. Convert any step count into miles and kilometres using a stride estimated from your height and walking or running pace.',
		'fields' => array(
			array(
				'id' => 'steps',
				'label' => 'Steps',
				'type' => 'number',
				'default' => 10000,
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
				'id' => 'feet',
				'label' => 'Height (feet)',
				'type' => 'number',
				'suffix' => 'ft',
				'default' => 5,
				'show_when' => array(
					'units' => 'imperial',
				),
			),
			array(
				'id' => 'inches',
				'label' => 'Height (inches)',
				'type' => 'number',
				'suffix' => 'in',
				'default' => 10,
				'show_when' => array(
					'units' => 'imperial',
				),
			),
			array(
				'id' => 'height',
				'label' => 'Height',
				'type' => 'number',
				'suffix' => 'cm',
				'default' => 178,
				'show_when' => array(
					'units' => 'metric',
				),
			),
			array(
				'id' => 'pace',
				'label' => 'Pace',
				'type' => 'segmented',
				'options' => array(
					'walk' => 'Walking',
					'run' => 'Running',
				),
				'default' => 'walk',
			),
		),
		'default_result' => array(
			'label' => 'Distance covered',
			'value' => '4.56 miles',
			'rows' => array(
				array(
					'label' => 'Kilometres',
					'value' => '7.34',
				),
				array(
					'label' => 'Estimated stride',
					'value' => '28.9 in',
				),
				array(
					'label' => 'Steps per mile',
					'value' => '2,192',
				),
				array(
					'label' => 'Rough calories burned',
					'value' => '456 kcal',
				),
			),
			'note' => 'Stride is estimated from height rather than measured, so this is an approximation. Pace, terrain and footwear all change it. To get an accurate figure, walk a measured distance and divide by your step count.',
		),
		'explainer' => array(
			array(
				'heading' => 'Stride comes from height, not from the step count',
				'body' => 'Nobody has measured their own stride, so it is estimated at about 41 per cent of height when walking and 48 per cent when running, since running strides are longer. That is why two people logging ten thousand steps can cover noticeably different distances.',
				'formula' => 'Miles = (steps × stride inches) ÷ 63,360',
			),
			array(
				'heading' => 'Getting a real figure',
				'body' => 'Walk a measured distance, such as a running track lap at 400 metres, count your steps and divide. That takes five minutes and replaces the estimate with your actual stride, which is worth doing once if you track distance seriously.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How many steps are in a mile?',
				'a' => 'Between roughly 1,900 and 2,500 depending on height and pace. The often-quoted 2,000 is a reasonable average and wrong for most individuals.',
			),
			array(
				'q' => 'Where did 10,000 steps come from?',
				'a' => 'A 1960s Japanese pedometer marketing campaign, not from research. Later studies suggest meaningful health benefits appear well below that, with the curve flattening somewhere around 7,500.',
			),
		),
		'related' => array(
			'tdee-calculator',
			'unit-converter',
		),
		'disclaimer' => 'General population estimate, not medical advice. It cannot account for your history, medication or physiology, so treat it as a starting point for a conversation with a clinician.',
		'sources' => array(),
	);
