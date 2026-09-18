<?php
/**
 * Macro Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'macro-calculator',
		'title' => 'Macro Calculator',
		'category' => 'health',
		'description' => 'Split a calorie target into protein, carbohydrate and fat.',
		'keyword' => 'Macro Calculator',
		'h1' => 'Macro Calculator',
		'meta_title' => 'Macro Calculator - Protein, Carbs and Fat in Grams',
		'meta_description' => 'Free macro calculator. Turn a daily calorie target into grams of protein, carbohydrate and fat across balanced, low carb, high carb or keto splits.',
		'fields' => array(
			array(
				'id' => 'calories',
				'label' => 'Daily calories',
				'type' => 'number',
				'suffix' => 'kcal',
				'default' => 2000,
			),
			array(
				'id' => 'split',
				'label' => 'Split',
				'type' => 'segmented',
				'options' => array(
					'balanced' => 'Balanced',
					'lowcarb' => 'Low carb',
					'highcarb' => 'High carb',
					'keto' => 'Keto',
				),
				'default' => 'balanced',
			),
		),
		'default_result' => array(
			'label' => 'Balanced macros',
			'value' => '150P / 200C / 67F',
			'rows' => array(
				array(
					'label' => 'Protein',
					'value' => '150 g  (30%)',
				),
				array(
					'label' => 'Carbohydrate',
					'value' => '200 g  (40%)',
				),
				array(
					'label' => 'Fat',
					'value' => '67 g  (30%)',
				),
				array(
					'label' => 'Total calories',
					'value' => '2,000 kcal',
				),
			),
			'note' => 'Protein is the macro worth hitting accurately, because it protects muscle in a deficit and is the most filling of the three. The carbohydrate and fat split is far more a matter of preference and adherence than most advice admits.',
		),
		'explainer' => array(
			array(
				'heading' => 'Why grams and percentages differ so much',
				'body' => 'Protein and carbohydrate supply four calories per gram, fat supplies nine. That is why a high fat split looks like far fewer grams of food even though the calories are identical, and why swapping fat for carbohydrate changes the volume on your plate more than the number on the label suggests.',
				'formula' => 'Grams = (calories × share) ÷ (4 for P and C, 9 for F)',
			),
			array(
				'heading' => 'Protein is the one worth hitting',
				'body' => 'Protein preserves muscle in a calorie deficit and is the most satiating of the three, so it is the macro to get right. The split between carbohydrate and fat matters far less than most advice implies, and the best ratio is usually whichever one you can actually stick to.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How many grams of protein do I need?',
				'a' => 'Commonly 1.6 to 2.2 grams per kilogram of body weight for anyone training, which is well above the minimum needed to avoid deficiency.',
			),
			array(
				'q' => 'Do I have to hit my macros exactly?',
				'a' => 'No. Getting close to the protein target and staying near the calorie total captures nearly all the benefit. Chasing the last few grams of fat and carbohydrate adds effort without adding results.',
			),
		),
		'related' => array(
			'tdee-calculator',
			'body-fat-calculator',
			'water-intake-calculator',
		),
		'disclaimer' => 'General population estimate, not medical advice. It cannot account for your history, medication or physiology, so treat it as a starting point for a conversation with a clinician.',
		'sources' => array(),
	);
