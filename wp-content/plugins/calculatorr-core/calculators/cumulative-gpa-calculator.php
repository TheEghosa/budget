<?php
/**
 * Cumulative GPA Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'cumulative-gpa-calculator',
		'title' => 'Cumulative GPA Calculator',
		'category' => 'education',
		'description' => 'Combine a prior GPA with this term to get your cumulative average.',
		'keyword' => 'Cumulative GPA Calculator',
		'h1' => 'Cumulative GPA Calculator',
		'meta_title' => 'Cumulative GPA Calculator - Combine Terms',
		'meta_description' => 'Free cumulative GPA calculator. Combine your existing GPA and credits with this term’s results to see your new overall average and how far it moved.',
		'fields' => array(
			array(
				'id' => 'priorGpa',
				'label' => 'Previous cumulative GPA',
				'type' => 'number',
				'default' => 3.4,
				'step' => 'any',
			),
			array(
				'id' => 'priorCredits',
				'label' => 'Credits earned so far',
				'type' => 'number',
				'default' => 60,
			),
			array(
				'id' => 'termGpa',
				'label' => 'This term’s GPA',
				'type' => 'number',
				'default' => 3.8,
				'step' => 'any',
			),
			array(
				'id' => 'termCredits',
				'label' => 'Credits this term',
				'type' => 'number',
				'default' => 15,
			),
		),
		'default_result' => array(
			'label' => 'Cumulative GPA',
			'value' => '3.48',
			'rows' => array(
				array(
					'label' => 'Change this term',
					'value' => '+0.08',
				),
				array(
					'label' => 'Total credits',
					'value' => '75',
				),
				array(
					'label' => 'Total quality points',
					'value' => '261',
				),
				array(
					'label' => 'To reach 3.5 you would need',
					'value' => '3.6 next term over 15 credits',
				),
			),
			'note' => 'The more credits already banked, the less any single term moves the average. That is why a weak first year is recoverable and a weak final year usually is not.',
		),
		'explainer' => array(
			array(
				'heading' => 'Weighted by credits, always',
				'body' => 'A cumulative GPA is not the average of your term GPAs. It is the total quality points divided by the total credits, so a term with twelve credits counts less than one with eighteen. Averaging term GPAs directly gives a different and wrong answer whenever credit loads differ.',
				'formula' => 'Cumulative = Σ(GPA × credits) ÷ Σcredits',
			),
			array(
				'heading' => 'Why later terms move it less',
				'body' => 'With sixty credits already banked, fifteen new ones are a fifth of the total and can shift the average meaningfully. With a hundred and twenty banked, the same fifteen credits move it half as much. That is why a weak first year is recoverable and a weak final year usually is not.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How do I raise my cumulative GPA?',
				'a' => 'Prioritise the high credit courses, since they carry the most weight in both directions, and act early while each term is still a large share of the total.',
			),
			array(
				'q' => 'Do transfer credits count?',
				'a' => 'Usually the credits transfer but the grades do not, so they enter the denominator without adding quality points. Policies vary, so check with your registrar.',
			),
		),
		'related' => array(
			'gpa-calculator',
			'ap-score-calculator',
		),
		'disclaimer' => '',
	);
