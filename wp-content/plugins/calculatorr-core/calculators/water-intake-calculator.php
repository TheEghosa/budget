<?php
/**
 * Water Intake Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'water-intake-calculator',
		'title' => 'Water Intake Calculator',
		'category' => 'health',
		'description' => 'Work out a daily fluid target from your weight, activity and climate.',
		'keyword' => 'Water Intake Calculator',
		'h1' => 'Water Intake Calculator',
		'meta_title' => 'Water Intake Calculator - Daily Fluid Target',
		'meta_description' => 'Free water intake calculator. Get a daily fluid target from your body weight, exercise time and climate, in litres, ounces and cups.',
		'fields' => array(
			array(
				'id' => 'units',
				'label' => 'Units',
				'type' => 'segmented',
				'options' => array(
					'metric' => 'Metric',
					'imperial' => 'Imperial',
				),
				'default' => 'metric',
			),
			array(
				'id' => 'weight',
				'label' => 'Body weight',
				'type' => 'number',
				'suffix' => 'kg',
				'default' => 75,
				'show_when' => array(
					'units' => 'metric',
				),
			),
			array(
				'id' => 'pounds',
				'label' => 'Body weight',
				'type' => 'number',
				'suffix' => 'lb',
				'default' => 165,
				'show_when' => array(
					'units' => 'imperial',
				),
			),
			array(
				'id' => 'exercise',
				'label' => 'Exercise today',
				'type' => 'number',
				'suffix' => 'minutes',
				'default' => 30,
			),
			array(
				'id' => 'climate',
				'label' => 'Climate',
				'type' => 'segmented',
				'options' => array(
					'cold' => 'Cold',
					'temperate' => 'Temperate',
					'hot' => 'Hot',
				),
				'default' => 'temperate',
			),
		),
		'default_result' => array(
			'label' => 'Daily water target',
			'value' => '2.98 litres',
			'rows' => array(
				array(
					'label' => 'US fluid ounces',
					'value' => '101',
				),
				array(
					'label' => 'Cups (8 oz)',
					'value' => '12.6',
				),
				array(
					'label' => 'Baseline for your weight',
					'value' => '2.63 L',
				),
				array(
					'label' => 'Added for exercise',
					'value' => '0.35 L',
				),
			),
			'note' => 'Food supplies roughly a fifth of daily fluid, and so do tea, coffee and other drinks, so this is total intake rather than plain water you must drink. Thirst and pale urine are better day to day guides than any number.',
		),
		'explainer' => array(
			array(
				'heading' => 'Where the number comes from',
				'body' => 'Roughly 35 millilitres per kilogram covers baseline needs for a sedentary adult, with about 350 millilitres added for every half hour of exercise and an adjustment for heat. That is total fluid intake, not plain water you must drink on top of everything else.',
				'formula' => 'Baseline ≈ 35 ml × body weight in kg',
			),
			array(
				'heading' => 'Food and other drinks count',
				'body' => 'About a fifth of daily fluid comes from food, and tea, coffee, juice and milk all count towards the rest. The idea that caffeine dehydrates you is largely a myth at normal intakes: the fluid in a cup of coffee more than offsets its mild diuretic effect.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How much water should I drink a day?',
				'a' => 'There is no single right answer, which is why this is calculated from your weight and activity. Thirst and pale straw coloured urine are better daily guides than any target.',
			),
			array(
				'q' => 'Can you drink too much water?',
				'a' => 'Yes. Drinking far more than you lose can dilute blood sodium dangerously, which is a real risk in endurance events. Very high intakes are not safer than adequate ones.',
			),
		),
		'related' => array(
			'tdee-calculator',
			'macro-calculator',
		),
		'disclaimer' => 'General population estimate, not medical advice. It cannot account for your history, medication or physiology, so treat it as a starting point for a conversation with a clinician.',
	);
