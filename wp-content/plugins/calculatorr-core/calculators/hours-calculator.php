<?php
/**
 * Hours Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'hours-calculator',
		'title' => 'Hours Calculator',
		'category' => 'time',
		'description' => 'Work out hours worked between two times, less breaks.',
		'keyword' => 'Hours Calculator',
		'h1' => 'Hours Calculator',
		'meta_title' => 'Hours Calculator - Hours Worked With Breaks Deducted',
		'meta_description' => 'Free hours calculator. Enter clock in and clock out times and an unpaid break to get hours worked, decimal hours for payroll and the pay owed.',
		'fields' => array(
			array(
				'id' => 'start',
				'label' => 'Clock in',
				'type' => 'text',
				'default' => '9:00 AM',
				'hint' => 'Such as 9:00 AM, 5:30 PM or 14:45.',
			),
			array(
				'id' => 'end',
				'label' => 'Clock out',
				'type' => 'text',
				'default' => '5:30 PM',
				'hint' => 'Such as 9:00 AM, 5:30 PM or 14:45.',
			),
			array(
				'id' => 'breakMins',
				'label' => 'Unpaid break',
				'type' => 'number',
				'suffix' => 'minutes',
				'default' => 30,
			),
			array(
				'id' => 'rate',
				'label' => 'Hourly rate',
				'type' => 'number',
				'prefix' => '$',
				'default' => 20,
			),
		),
		'default_result' => array(
			'label' => 'Hours worked',
			'value' => '8h 0m',
			'rows' => array(
				array(
					'label' => 'Decimal hours',
					'value' => '8',
				),
				array(
					'label' => 'Break deducted',
					'value' => '30 min',
				),
				array(
					'label' => 'Gross time on site',
					'value' => '8h 30m',
				),
				array(
					'label' => 'Pay at $20.00/hr',
					'value' => '$160.00',
				),
			),
			'note' => 'Payroll systems usually work in decimal hours rather than hours and minutes, so 8h 30m is entered as 8.5.',
		),
		'explainer' => array(
			array(
				'heading' => 'Gross time and paid time are different',
				'body' => 'The hours between clocking in and out are not the hours you are paid for. Unpaid breaks come out, and in most US workplaces a meal break of thirty minutes or more is unpaid while short rest breaks are paid. The calculator shows both figures so the gap is visible.',
				'formula' => 'Paid hours = (clock out − clock in) − unpaid break',
			),
			array(
				'heading' => 'Rounding',
				'body' => 'Many employers round clock times to the nearest quarter hour. That is legal in the US only if it averages out fairly over time and never systematically favours the employer. Rounding that always goes one way is a wage violation rather than an administrative convenience.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How do I convert minutes to decimal hours?',
				'a' => 'Divide by sixty. Fifteen minutes is 0.25, thirty is 0.5 and forty-five is 0.75. The calculator shows the decimal figure directly.',
			),
			array(
				'q' => 'Should my lunch break be paid?',
				'a' => 'Under federal US law, bona fide meal breaks of thirty minutes or more are generally unpaid, while short rest breaks of five to twenty minutes are paid. Some states require more, so check your own.',
			),
		),
		'related' => array(
			'time-duration-calculator',
			'time-card-calculator',
			'overtime-calculator',
		),
		'disclaimer' => '',
	);
