<?php
/**
 * Pregnancy Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'pregnancy-calculator',
		'title' => 'Pregnancy Calculator',
		'category' => 'health',
		'description' => 'Work out your due date and how far along you are.',
		'keyword' => 'Pregnancy Calculator',
		'h1' => 'Pregnancy Calculator',
		'meta_title' => 'Pregnancy Calculator - Due Date and Weeks Along',
		'meta_description' => 'Free pregnancy calculator. Enter your last period for an estimated due date, how many weeks along you are today, and which trimester you are in.',
		'fields' => array(
			array(
				'id' => 'lmp',
				'label' => 'First day of your last period',
				'type' => 'date',
				'default' => '2026-04-01',
			),
			array(
				'id' => 'cycle',
				'label' => 'Cycle length',
				'type' => 'number',
				'suffix' => 'days',
				'default' => 28,
			),
		),
		'default_result' => array(
			'label' => 'Estimated due date',
			'value' => 'Wed, Jan 6, 2027',
			'rows' => array(
				array(
					'label' => 'How far along today',
					'value' => '24 weeks 2 days',
				),
				array(
					'label' => 'Trimester',
					'value' => 'Second',
				),
				array(
					'label' => 'Days remaining',
					'value' => '110',
				),
				array(
					'label' => 'Conception, approximately',
					'value' => 'Wed, Apr 15, 2026',
				),
				array(
					'label' => 'Full term from',
					'value' => 'Wed, Dec 16, 2026',
				),
			),
			'note' => 'Only about one birth in twenty happens on the due date itself. A dating scan in the first trimester is considerably more accurate than any calculation from a period date.',
		),
		'explainer' => array(
			array(
				'heading' => 'Naegele’s rule and its adjustment',
				'body' => 'The standard due date is 280 days from the first day of the last period, which assumes a 28 day cycle with ovulation on day 14. A longer cycle means later ovulation and a later due date, so the calculation is shifted by the difference. Pregnancy is dated from the last period rather than conception, which is why week one contains no pregnancy at all.',
				'formula' => 'Due date = Last period + 280 days + (cycle − 28)',
			),
			array(
				'heading' => 'How much a due date really means',
				'body' => 'Only around one birth in twenty falls on the due date. Term runs from 37 to 42 weeks, and a first trimester dating scan measures the fetus directly and is considerably more accurate than any calculation from a remembered date.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'Why is pregnancy dated from my last period?',
				'a' => 'Because that date is usually known and the date of conception usually is not. It adds about two weeks at the front, which is why you are considered four weeks pregnant roughly when a test first turns positive.',
			),
			array(
				'q' => 'What if my cycle is not 28 days?',
				'a' => 'Enter your actual length and the calculator adjusts. A 35 day cycle typically pushes the due date about a week later than the standard rule suggests.',
			),
		),
		'related' => array(
			'ovulation-calculator',
			'period-calculator',
			'chronological-age-calculator',
		),
		'disclaimer' => 'General population estimate, not medical advice. It cannot account for your history, medication or physiology, so treat it as a starting point for a conversation with a clinician.',
		'sources' => array(),
	);
