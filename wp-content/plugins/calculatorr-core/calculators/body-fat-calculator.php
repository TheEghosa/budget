<?php
/**
 * Body Fat Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'body-fat-calculator',
		'title' => 'Body Fat Calculator',
		'category' => 'health',
		'description' => 'Estimate body fat percentage with the US Navy tape method.',
		'keyword' => 'Body Fat Calculator',
		'h1' => 'Body Fat Calculator',
		'meta_title' => 'Body Fat Calculator - US Navy Tape Method',
		'meta_description' => 'Free body fat calculator using the US Navy method. Enter tape measurements for a body fat percentage, your category, and fat and lean mass.',
		'fields' => array(
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
				'id' => 'heightVal',
				'label' => 'Height',
				'type' => 'number',
				'suffix' => 'in',
				'default' => 70,
				'show_when' => array(
					'units' => 'imperial',
				),
			),
			array(
				'id' => 'neck',
				'label' => 'Neck',
				'type' => 'number',
				'suffix' => 'in',
				'default' => 15,
				'show_when' => array(
					'units' => 'imperial',
				),
			),
			array(
				'id' => 'waist',
				'label' => 'Waist',
				'type' => 'number',
				'suffix' => 'in',
				'default' => 34,
				'show_when' => array(
					'units' => 'imperial',
				),
				'hint' => 'At the navel for men, at the narrowest point for women.',
			),
			array(
				'id' => 'hip',
				'label' => 'Hip',
				'type' => 'number',
				'suffix' => 'in',
				'default' => 38,
				'show_when' => array(
					'units' => 'imperial',
					'sex' => 'female',
				),
			),
			array(
				'id' => 'pounds',
				'label' => 'Weight',
				'type' => 'number',
				'suffix' => 'lb',
				'default' => 180,
				'show_when' => array(
					'units' => 'imperial',
				),
			),
			array(
				'id' => 'heightVal',
				'label' => 'Height',
				'type' => 'number',
				'suffix' => 'cm',
				'default' => 178,
				'show_when' => array(
					'units' => 'metric',
				),
			),
			array(
				'id' => 'neck',
				'label' => 'Neck',
				'type' => 'number',
				'suffix' => 'cm',
				'default' => 38,
				'show_when' => array(
					'units' => 'metric',
				),
			),
			array(
				'id' => 'waist',
				'label' => 'Waist',
				'type' => 'number',
				'suffix' => 'cm',
				'default' => 86,
				'show_when' => array(
					'units' => 'metric',
				),
			),
			array(
				'id' => 'hip',
				'label' => 'Hip',
				'type' => 'number',
				'suffix' => 'cm',
				'default' => 97,
				'show_when' => array(
					'units' => 'metric',
					'sex' => 'female',
				),
			),
			array(
				'id' => 'weight',
				'label' => 'Weight',
				'type' => 'number',
				'suffix' => 'kg',
				'default' => 82,
				'show_when' => array(
					'units' => 'metric',
				),
			),
		),
		'default_result' => array(
			'label' => 'Body fat',
			'value' => '17.2%',
			'rows' => array(
				array(
					'label' => 'Category',
					'value' => 'Fitness',
				),
				array(
					'label' => 'Fat mass',
					'value' => '31 lb',
				),
				array(
					'label' => 'Lean mass',
					'value' => '149 lb',
				),
			),
			'note' => 'The US Navy tape method, which is typically within about three or four percentage points of a DEXA scan. It is far better than BMI at telling body composition, and far worse than a scan. Measure at the same time of day for comparable readings.',
		),
		'explainer' => array(
			array(
				'heading' => 'Why circumferences predict body fat at all',
				'body' => 'Fat is not distributed randomly. Waist circumference relative to neck and height tracks total body fat closely enough across populations that the US Navy adopted it as a screening tool, and it needs nothing but a tape measure.',
				'formula' => '495 ÷ (1.0324 − 0.19077 log(waist−neck) + 0.15456 log(height)) − 450',
			),
			array(
				'heading' => 'How good is it',
				'body' => 'Typically within three or four percentage points of a DEXA scan, which makes it far better than BMI at describing body composition and considerably worse than an actual scan. Measure at the same time of day and in the same state, because a heavy meal or a hard session moves the tape.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What is a healthy body fat percentage?',
				'a' => 'Broadly 14 to 24 per cent for men and 21 to 31 for women, with athletes lower. Essential fat, below which health suffers, is around 3 per cent for men and 12 for women.',
			),
			array(
				'q' => 'Why does the female formula need a hip measurement?',
				'a' => 'Because fat distribution differs, and the hip measurement captures a pattern the waist alone misses, which improves the estimate materially.',
			),
		),
		'related' => array(
			'bmi-calculator',
			'tdee-calculator',
			'macro-calculator',
		),
		'disclaimer' => 'General population estimate, not medical advice. It cannot account for your history, medication or physiology, so treat it as a starting point for a conversation with a clinician.',
		'sources' => array(),
	);
