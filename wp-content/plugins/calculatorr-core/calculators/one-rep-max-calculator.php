<?php
/**
 * One Rep Max Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'one-rep-max-calculator',
		'title' => 'One Rep Max Calculator',
		'category' => 'health',
		'description' => 'Estimate your one rep max and training percentages.',
		'keyword' => 'One Rep Max Calculator',
		'h1' => 'One Rep Max Calculator',
		'meta_title' => 'One Rep Max Calculator - Estimate Your 1RM Safely',
		'meta_description' => 'Free one rep max calculator using the Epley and Brzycki formulas. Estimate your 1RM from a set you have already done, plus a full percentage table.',
		'fields' => array(
			array(
				'id' => 'weight',
				'label' => 'Weight lifted',
				'type' => 'number',
				'default' => 100,
				'step' => 'any',
			),
			array(
				'id' => 'unit',
				'label' => 'Unit',
				'type' => 'segmented',
				'options' => array(
					'lb' => 'lb',
					'kg' => 'kg',
				),
				'default' => 'kg',
			),
			array(
				'id' => 'reps',
				'label' => 'Reps completed',
				'type' => 'number',
				'default' => 5,
				'hint' => 'Reps to genuine failure give the best estimate.',
			),
		),
		'default_result' => array(
			'label' => 'Estimated one rep max',
			'value' => '114.6 kg',
			'rows' => array(
				array(
					'label' => 'Epley formula',
					'value' => '116.7 kg',
				),
				array(
					'label' => 'Brzycki formula',
					'value' => '112.5 kg',
				),
				array(
					'label' => '95% for 2 reps',
					'value' => '108.9 kg',
				),
				array(
					'label' => '90% for 4 reps',
					'value' => '103.1 kg',
				),
				array(
					'label' => '80% for 8 reps',
					'value' => '91.7 kg',
				),
				array(
					'label' => '70% for 12 reps',
					'value' => '80.2 kg',
				),
			),
			'note' => 'Averaged from the two most widely used formulas. They agree closely under about six reps and diverge above it.',
		),
		'explainer' => array(
			array(
				'heading' => 'Why estimate rather than test',
				'body' => 'A true one rep max attempt is the most technically demanding and highest risk lift in training, and it needs a spotter, a warm-up protocol and a fresh nervous system. Estimating from a set of three to six reps gives a figure accurate enough to program with and costs nothing.',
				'formula' => 'Epley: 1RM = w × (1 + reps ÷ 30)',
			),
			array(
				'heading' => 'Where the formulas diverge',
				'body' => 'Epley and Brzycki agree closely under about six reps and separate above it, which is why both are shown and the headline is their average. Above ten reps neither is reliable, because the set becomes limited by endurance rather than by strength.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How accurate is an estimated 1RM?',
				'a' => 'Within a few per cent for sets under six reps. It drifts badly above ten, and it varies by lift, since deadlifts and squats behave differently from bench press.',
			),
			array(
				'q' => 'Should I train at my one rep max?',
				'a' => 'Rarely. Most strength work sits between 70 and 90 per cent, which is why the percentage table is the useful part of this calculator rather than the headline number.',
			),
		),
		'related' => array(
			'tdee-calculator',
			'macro-calculator',
		),
		'disclaimer' => 'General population estimate, not medical advice. It cannot account for your history, medication or physiology, so treat it as a starting point for a conversation with a clinician.',
		'sources' => array(),
	);
