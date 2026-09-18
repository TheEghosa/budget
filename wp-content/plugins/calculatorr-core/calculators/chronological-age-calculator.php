<?php
/**
 * Chronological Age Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'chronological-age-calculator',
		'title' => 'Chronological Age Calculator',
		'category' => 'time',
		'description' => 'Find an exact age in years, months and days at any date.',
		'keyword' => 'Chronological Age Calculator',
		'h1' => 'Chronological Age Calculator',
		'meta_title' => 'Chronological Age Calculator - Exact Age on Any Date',
		'meta_description' => 'Free chronological age calculator. Get an exact age in years, months and days at today or at any chosen date, plus totals in days, weeks and months.',
		'fields' => array(
			array(
				'id' => 'dob',
				'label' => 'Date of birth',
				'type' => 'date',
				'default' => '1990-01-01',
			),
			array(
				'id' => 'upto',
				'label' => 'Age at this date',
				'type' => 'date',
				'default' => '',
				'hint' => 'Leave empty to use today.',
			),
		),
		'default_result' => array(
			'label' => 'Age',
			'value' => '36 years',
			'rows' => array(
				array(
					'label' => 'Exactly',
					'value' => '36y 8m 17d',
				),
				array(
					'label' => 'Total days',
					'value' => '13,409',
				),
				array(
					'label' => 'Total weeks',
					'value' => '1,915',
				),
				array(
					'label' => 'Total months',
					'value' => '440',
				),
			),
			'note' => '',
		),
		'explainer' => array(
			array(
				'heading' => 'Why schools and clinics need the exact figure',
				'body' => 'Chronological age to the day decides school year placement, developmental assessment scoring and eligibility cut-offs. A child born a day either side of a cut-off date lands in a different year group, so "about six" is not good enough and the months and days matter.',
				'formula' => 'Age = years, then months, then borrowed days',
			),
			array(
				'heading' => 'Borrowing days correctly',
				'body' => 'When the day of the month is earlier than the birth day, a month has to be borrowed, and the number of days borrowed is the length of the previous month rather than a flat thirty. Getting that wrong shifts the answer by up to three days, which is exactly the margin that decides a cut-off.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How is chronological age different from developmental age?',
				'a' => 'Chronological age is time since birth. Developmental age describes the level a child is functioning at, which may be ahead of or behind it. Assessments compare the two.',
			),
			array(
				'q' => 'Can I find an age on a past date?',
				'a' => 'Yes, put that date in the second field. This is how eligibility on a specific cut-off day is checked.',
			),
		),
		'related' => array(
			'age-calculator',
			'date-calculator',
			'dog-age-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
