<?php
/**
 * Period Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'period-calculator',
		'title' => 'Period Calculator',
		'category' => 'health',
		'description' => 'Predict your next five periods from your cycle length.',
		'keyword' => 'Period Calculator',
		'h1' => 'Period Calculator',
		'meta_title' => 'Period Calculator - Predict Your Next Five Cycles',
		'meta_description' => 'Free period calculator. Enter your last period, cycle length and how long bleeding lasts to see the dates of your next five periods.',
		'fields' => array(
			array(
				'id' => 'lmp',
				'label' => 'First day of your last period',
				'type' => 'date',
				'default' => '2026-09-01',
			),
			array(
				'id' => 'cycle',
				'label' => 'Cycle length',
				'type' => 'number',
				'suffix' => 'days',
				'default' => 28,
			),
			array(
				'id' => 'length',
				'label' => 'Period lasts',
				'type' => 'number',
				'suffix' => 'days',
				'default' => 5,
			),
		),
		'default_result' => array(
			'label' => 'Next period expected',
			'value' => 'Tue, Sep 29, 2026',
			'rows' => array(
				array(
					'label' => 'Period 1',
					'value' => 'Tue, Sep 29, 2026 to Sat, Oct 3, 2026',
				),
				array(
					'label' => 'Period 2',
					'value' => 'Tue, Oct 27, 2026 to Sat, Oct 31, 2026',
				),
				array(
					'label' => 'Period 3',
					'value' => 'Tue, Nov 24, 2026 to Sat, Nov 28, 2026',
				),
				array(
					'label' => 'Period 4',
					'value' => 'Tue, Dec 22, 2026 to Sat, Dec 26, 2026',
				),
				array(
					'label' => 'Period 5',
					'value' => 'Tue, Jan 19, 2027 to Sat, Jan 23, 2027',
				),
			),
			'note' => 'Straight cycle arithmetic. Real cycles vary by several days month to month, and stress, illness, travel and training all move them, so the later predictions are looser than the first.',
		),
		'explainer' => array(
			array(
				'heading' => 'Straight arithmetic, and its limits',
				'body' => 'Each prediction simply adds another cycle length to the last. That works well for the next one and gets progressively looser after that, because small variations compound. If your cycles vary by more than a few days, treat anything past the second prediction as a rough window.',
				'formula' => 'Next period = Last period + cycle length',
			),
			array(
				'heading' => 'What moves a cycle',
				'body' => 'Stress, illness, significant weight change, heavy training, travel across time zones and hormonal contraception all shift timing. A cycle between 21 and 35 days is considered normal, and variation within that range is ordinary rather than a problem.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What is a normal cycle length?',
				'a' => 'Anywhere from 21 to 35 days in adults. Consistency matters more than the number, and cycles are often longer and less regular in the first years after periods begin.',
			),
			array(
				'q' => 'Why is my period late?',
				'a' => 'Ovulation happening later than usual is the most common reason, since the luteal phase that follows is fairly fixed. Stress, illness and travel are the usual causes, though a test is worth doing if pregnancy is possible.',
			),
		),
		'related' => array(
			'ovulation-calculator',
			'pregnancy-calculator',
		),
		'disclaimer' => 'General population estimate, not medical advice. It cannot account for your history, medication or physiology, so treat it as a starting point for a conversation with a clinician.',
		'sources' => array(),
	);
