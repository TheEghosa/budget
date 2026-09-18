<?php
/**
 * Work Hours Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'work-hours-calculator',
		'title' => 'Work Hours Calculator',
		'category' => 'time',
		'description' => 'Work out weekly hours and pay from a daily schedule.',
		'keyword' => 'Work Hours Calculator',
		'h1' => 'Work Hours Calculator',
		'meta_title' => 'Work Hours Calculator - Weekly Hours and Annual Pay',
		'meta_description' => 'Free work hours calculator. Turn a daily schedule into weekly hours, decimal hours, weekly pay and an annual figure, with overtime flagged.',
		'fields' => array(
			array(
				'id' => 'start',
				'label' => 'Start time',
				'type' => 'text',
				'default' => '9:00 AM',
				'hint' => 'Such as 9:00 AM, 5:30 PM or 14:45.',
			),
			array(
				'id' => 'end',
				'label' => 'End time',
				'type' => 'text',
				'default' => '5:00 PM',
				'hint' => 'Such as 9:00 AM, 5:30 PM or 14:45.',
			),
			array(
				'id' => 'breakMins',
				'label' => 'Unpaid break',
				'type' => 'number',
				'suffix' => 'minutes',
				'default' => 60,
			),
			array(
				'id' => 'days',
				'label' => 'Days per week',
				'type' => 'number',
				'default' => 5,
			),
			array(
				'id' => 'rate',
				'label' => 'Hourly rate',
				'type' => 'number',
				'prefix' => '$',
				'default' => 25,
			),
		),
		'default_result' => array(
			'label' => 'Weekly hours',
			'value' => '35h 0m',
			'rows' => array(
				array(
					'label' => 'Per day',
					'value' => '7h 0m',
				),
				array(
					'label' => 'Decimal weekly hours',
					'value' => '35',
				),
				array(
					'label' => 'Over 40 hours',
					'value' => 'none',
				),
				array(
					'label' => 'Weekly pay at $25.00',
					'value' => '$875.00',
				),
				array(
					'label' => 'Annual, 52 weeks',
					'value' => '$45,500.00',
				),
			),
			'note' => '',
		),
		'explainer' => array(
			array(
				'heading' => 'The gap between contract hours and paid hours',
				'body' => 'A nine to five with an hour for lunch is thirty-five paid hours a week, not forty. Over a year that difference is more than two hundred and fifty hours, which is why the distinction matters when comparing two jobs whose headline hours look identical.',
				'formula' => 'Weekly paid hours = (shift length − break) × days',
			),
			array(
				'heading' => 'Annualising honestly',
				'body' => 'The annual figure multiplies by fifty-two, which assumes you are paid for every week including holidays. For an hourly role with unpaid leave, subtract your unpaid weeks before comparing against a salary.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How many work hours are in a year?',
				'a' => '2,080 for a full time 40 hour week across 52 weeks, which is the figure used to convert between hourly rates and salaries.',
			),
			array(
				'q' => 'What counts as full time?',
				'a' => 'Commonly 40 hours in the US, though the ACA uses 30 hours for benefit eligibility and many employers set their own threshold. There is no single legal definition.',
			),
		),
		'related' => array(
			'overtime-calculator',
			'take-home-pay-calculator',
			'time-card-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
