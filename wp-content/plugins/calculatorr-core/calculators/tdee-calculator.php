<?php
/**
 * Calorie (TDEE) Calculator.
 *
 * Eighth in the build order because it is the most complex input pattern the
 * template has to carry: a multi-field form with a unit switch and a dropdown.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'slug'        => 'tdee-calculator',
	'title'       => 'Calorie (TDEE) Calculator',
	'category'    => 'health',
	'description' => 'Estimate how many calories you burn in a normal day from your height, weight, age and activity level.',
	'keyword'     => 'TDEE Calculator',
	'h1'          => 'TDEE Calculator',
	'meta_title'  => 'TDEE Calculator - Daily Calorie Needs, Mifflin-St Jeor',
	'meta_description' => 'Free TDEE calculator using the Mifflin-St Jeor equation. Estimate daily calories from height, weight, age and activity, with targets to lose or gain weight.',
	'fields'      => array(
		array(
			'id'      => 'units',
			'label'   => 'Units',
			'type'    => 'segmented',
			'options' => array( 'metric' => 'Metric', 'imperial' => 'Imperial' ),
			'default' => 'metric',
		),
		array(
			'id'      => 'sex',
			'label'   => 'Sex assigned at birth',
			'type'    => 'segmented',
			'options' => array( 'male' => 'Male', 'female' => 'Female' ),
			'default' => 'male',
			'hint'    => 'The formula was derived separately for each group, which is why it is asked for here.',
		),
		array( 'id' => 'age', 'label' => 'Age', 'type' => 'number', 'suffix' => 'years', 'default' => 30, 'min' => 0 ),
		array( 'id' => 'height', 'label' => 'Height', 'type' => 'number', 'suffix' => 'cm', 'default' => 180, 'min' => 0, 'show_when' => array( 'units' => 'metric' ) ),
		array( 'id' => 'weight', 'label' => 'Weight', 'type' => 'number', 'suffix' => 'kg', 'default' => 80, 'min' => 0, 'show_when' => array( 'units' => 'metric' ) ),
		array( 'id' => 'feet', 'label' => 'Height (feet)', 'type' => 'number', 'suffix' => 'ft', 'default' => 5, 'min' => 0, 'show_when' => array( 'units' => 'imperial' ) ),
		array( 'id' => 'inches', 'label' => 'Height (inches)', 'type' => 'number', 'suffix' => 'in', 'default' => 11, 'min' => 0, 'show_when' => array( 'units' => 'imperial' ) ),
		array( 'id' => 'pounds', 'label' => 'Weight', 'type' => 'number', 'suffix' => 'lb', 'default' => 176, 'min' => 0, 'show_when' => array( 'units' => 'imperial' ) ),
		array(
			'id'      => 'activity',
			'label'   => 'Activity level',
			'type'    => 'select',
			'options' => array(
				'1.2'   => 'Sedentary, desk job and no exercise',
				'1.375' => 'Light, exercise one to three days a week',
				'1.55'  => 'Moderate, exercise three to five days a week',
				'1.725' => 'Hard, exercise six or seven days a week',
				'1.9'   => 'Very hard, physical job or twice daily training',
			),
			'default' => '1.55',
		),
	),
	'default_result' => array(
		'label' => 'Daily energy needs',
		'value' => '2,759 kcal',
		'rows'  => array(
			array( 'label' => 'Resting rate (BMR)', 'value' => '1,780 kcal' ),
			array( 'label' => 'Activity on top', 'value' => '979 kcal' ),
			array( 'label' => 'Lose about 0.5kg a week', 'value' => '2,259 kcal' ),
			array( 'label' => 'Gain about 0.5kg a week', 'value' => '3,259 kcal' ),
		),
	),
	'explainer'   => array(
		array(
			'heading' => 'What TDEE actually measures',
			'body'    => 'Total daily energy expenditure is everything your body burns in twenty-four hours: the resting metabolism that keeps you alive, the energy spent digesting food, and the movement you do on top. The resting portion is the majority of it for most people, which is why an hour at the gym changes the total less than the number on the treadmill suggests.',
			'formula' => 'BMR = (10 &times; kg) + (6.25 &times; cm) &minus; (5 &times; age) + s',
		),
		array(
			'heading' => 'Why the activity multiplier is the weak link',
			'body'    => 'The resting rate comes from the Mifflin-St Jeor equation, which predicts reasonably well across most adults. The activity multiplier is far cruder, since it compresses everything from a sedentary week to a physically demanding job into five brackets, and people routinely overestimate which bracket they belong in. If the number here does not match what happens to your weight over a fortnight, trust the scale and adjust the multiplier down rather than assuming the formula is broken.',
		),
	),
	'faqs'        => array(
		array(
			'q' => 'How accurate is this?',
			'a' => 'The resting portion is typically within about ten per cent for most adults, which is close enough to plan from. The full TDEE figure is less reliable because the activity multiplier is a broad estimate. Use it as a starting point, track your weight for two weeks, and adjust the target by two hundred calories in whichever direction the trend calls for.',
		),
		array(
			'q' => 'Why does a 500 calorie deficit mean half a kilo a week?',
			'a' => 'A kilogram of body fat stores roughly 7,700 calories, so a daily deficit of 500 comes to 3,500 a week, or close to half a kilo. The relationship is approximate rather than exact, since metabolism adapts downward as you lose weight, which is the main reason a deficit that worked in month one stalls by month three.',
		),
		array(
			'q' => 'Should I eat back the calories I burn exercising?',
			'a' => 'Not if you already selected an activity level above sedentary, because those calories are counted in the multiplier and eating them again double counts them. Fitness trackers are also generous with exercise estimates, so treating their numbers as spendable is one of the most common reasons a deficit quietly disappears.',
		),
	),
	'disclaimer'  => 'A general population estimate rather than medical advice. It cannot account for medical conditions, medication or body composition, so speak to a clinician before making significant changes.',
	'related'     => array( 'bmi-calculator', 'percentage-calculator' ),
);
