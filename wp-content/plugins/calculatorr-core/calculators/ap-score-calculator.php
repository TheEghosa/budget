<?php
/**
 * AP Score Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'ap-score-calculator',
		'title' => 'AP Score Calculator',
		'category' => 'education',
		'description' => 'Estimate an AP exam score from section results.',
		'keyword' => 'AP Score Calculator',
		'h1' => 'AP Score Calculator',
		'meta_title' => 'AP Score Calculator - Estimate Your 1 to 5 Score',
		'meta_description' => 'Free AP score calculator. Estimate your 1 to 5 score from multiple choice and free response results, with the usual credit threshold marked.',
		'fields' => array(
			array(
				'id' => 'mcCorrect',
				'label' => 'Multiple choice correct',
				'type' => 'number',
				'default' => 40,
			),
			array(
				'id' => 'mcTotal',
				'label' => 'Multiple choice questions',
				'type' => 'number',
				'default' => 55,
			),
			array(
				'id' => 'frqEarned',
				'label' => 'Free response points earned',
				'type' => 'number',
				'default' => 18,
			),
			array(
				'id' => 'frqTotal',
				'label' => 'Free response points available',
				'type' => 'number',
				'default' => 27,
			),
			array(
				'id' => 'mcWeight',
				'label' => 'Multiple choice weighting',
				'type' => 'number',
				'suffix' => '%',
				'default' => 50,
				'hint' => 'Usually 50%, though it differs by subject.',
			),
		),
		'default_result' => array(
			'label' => 'Estimated AP score',
			'value' => '4',
			'rows' => array(
				array(
					'label' => 'Composite',
					'value' => '69.7%',
				),
				array(
					'label' => 'Multiple choice',
					'value' => '40 of 55',
				),
				array(
					'label' => 'Free response',
					'value' => '18 of 27',
				),
				array(
					'label' => 'Typically earns credit',
					'value' => 'yes at most colleges',
				),
			),
			'note' => 'An indication rather than a prediction. The College Board sets the cut points separately for every subject every year and does not publish them, so the real boundaries move. Treat a borderline result as genuinely uncertain.',
		),
		'explainer' => array(
			array(
				'heading' => 'How the composite works',
				'body' => 'Each section is scored, weighted and combined into a composite, which is then mapped onto the 1 to 5 scale. Most subjects weight the two halves equally, though several do not, which is why the weighting is adjustable here.',
				'formula' => 'Composite = MC share × weight + FRQ share × (1 − weight)',
			),
			array(
				'heading' => 'Why this is an estimate and always will be',
				'body' => 'The College Board sets the cut points separately for every subject every year and does not publish them. They move with exam difficulty, so a composite that earned a 4 last year may earn a 3 or a 5 this year. Anything near a boundary is genuinely uncertain rather than precisely predictable.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What AP score do I need for college credit?',
				'a' => 'A 3 is the usual minimum and many selective institutions want a 4 or 5. Policies vary by college and by subject, so check the specific institution.',
			),
			array(
				'q' => 'Is a 3 a good AP score?',
				'a' => 'It is officially described as qualified and earns credit at a great many institutions. Whether it helps your application is a separate question from whether it earns credit.',
			),
		),
		'related' => array(
			'gpa-calculator',
			'cumulative-gpa-calculator',
			'percentage-calculator',
		),
		'disclaimer' => '',
	);
