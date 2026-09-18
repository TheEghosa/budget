<?php
/**
 * Body Surface Area Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'body-surface-area-calculator',
		'title' => 'Body Surface Area Calculator',
		'category' => 'health',
		'description' => 'Calculate body surface area with the Mosteller, Du Bois and Haycock formulas.',
		'keyword' => 'Body Surface Area Calculator',
		'h1' => 'Body Surface Area Calculator',
		'meta_title' => 'Body Surface Area Calculator - BSA for Dosing',
		'meta_description' => 'Free body surface area calculator. Get BSA in square metres from the Mosteller, Du Bois and Haycock formulas, with height and weight in either unit.',
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
				'id' => 'weight',
				'label' => 'Weight',
				'type' => 'number',
				'suffix' => 'kg',
				'default' => 75,
				'show_when' => array(
					'units' => 'metric',
				),
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
				'id' => 'pounds',
				'label' => 'Weight',
				'type' => 'number',
				'suffix' => 'lb',
				'default' => 165,
				'show_when' => array(
					'units' => 'imperial',
				),
			),
		),
		'default_result' => array(
			'label' => 'Body surface area',
			'value' => '1.926 m2',
			'rows' => array(
				array(
					'label' => 'Mosteller',
					'value' => '1.9257 m2',
				),
				array(
					'label' => 'Du Bois',
					'value' => '1.9267 m2',
				),
				array(
					'label' => 'Haycock',
					'value' => '1.9295 m2',
				),
				array(
					'label' => 'Square feet',
					'value' => '20.73',
				),
			),
			'note' => 'Mosteller is shown as the headline because it is the formula most commonly used for drug dosing, being both simple and about as accurate as the longer ones. Where a dose depends on this, the prescriber’s own formula is the one that counts.',
		),
		'explainer' => array(
			array(
				'heading' => 'Why surface area rather than weight',
				'body' => 'Many physiological measures, metabolic rate and drug clearance among them, scale with surface area rather than with mass. That is why chemotherapy and several other drug classes are dosed per square metre, and why getting this figure right matters clinically in a way that most calculators here do not.',
				'formula' => 'Mosteller: BSA = √((height cm × weight kg) ÷ 3600)',
			),
			array(
				'heading' => 'Three formulas, small differences',
				'body' => 'Mosteller is shown first because it is the one most commonly used for dosing, being simple enough to do on paper and about as accurate as the longer alternatives. Du Bois is the oldest and Haycock performs better in children. They typically agree within a few per cent.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'Which formula should I use?',
				'a' => 'Whichever your institution specifies. Where a dose depends on it, the prescriber’s own protocol is the one that counts, not the one a website prefers.',
			),
			array(
				'q' => 'What is a typical BSA?',
				'a' => 'Around 1.7 square metres for an average adult, roughly 1.6 for women and 1.9 for men, though it varies widely with size.',
			),
		),
		'related' => array(
			'bmi-calculator',
			'body-fat-calculator',
		),
		'disclaimer' => 'General population estimate, not medical advice. It cannot account for your history, medication or physiology, so treat it as a starting point for a conversation with a clinician.',
	);
