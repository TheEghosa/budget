<?php
/**
 * Time Duration Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'time-duration-calculator',
		'title' => 'Time Duration Calculator',
		'category' => 'time',
		'description' => 'Find the time between two clock times, including overnight.',
		'keyword' => 'Time Duration Calculator',
		'h1' => 'Time Duration Calculator',
		'meta_title' => 'Time Duration Calculator - Hours Between Two Times',
		'meta_description' => 'Free time duration calculator. Find the hours and minutes between any two times, including shifts that run past midnight, plus decimal hours for payroll.',
		'fields' => array(
			array(
				'id' => 'start',
				'label' => 'Start time',
				'type' => 'text',
				'default' => '10:00 PM',
				'hint' => 'Such as 9:00 AM, 5:30 PM or 14:45.',
			),
			array(
				'id' => 'end',
				'label' => 'End time',
				'type' => 'text',
				'default' => '6:30 AM',
				'hint' => 'Such as 9:00 AM, 5:30 PM or 14:45.',
			),
		),
		'default_result' => array(
			'label' => 'Duration',
			'value' => '8h 30m',
			'rows' => array(
				array(
					'label' => 'Decimal hours',
					'value' => '8.5',
				),
				array(
					'label' => 'Total minutes',
					'value' => '510',
				),
				array(
					'label' => 'Crosses midnight',
					'value' => 'yes',
				),
				array(
					'label' => 'From',
					'value' => '10:00 PM to 6:30 AM',
				),
			),
			'note' => '',
		),
		'explainer' => array(
			array(
				'heading' => 'Overnight is normal, not an error',
				'body' => 'When the end time is earlier than the start, the period ran through midnight. Rather than returning a negative number, the calculator adds a day, because a shift from 10pm to 6am is eight hours and not minus sixteen. That case is flagged so you can tell it apart from a typo.',
				'formula' => 'Duration = end − start, plus 24h if negative',
			),
			array(
				'heading' => 'Decimal hours for payroll',
				'body' => 'Payroll systems work in decimal hours, where eight hours thirty minutes is 8.5 rather than 8.30. Confusing the two costs eighteen minutes of pay per shift, which is why the decimal figure is shown alongside.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How many hours is 9am to 5:30pm?',
				'a' => 'Eight and a half hours, or 8.5 in decimal. Deduct any unpaid break from that.',
			),
			array(
				'q' => 'How do I calculate a night shift?',
				'a' => 'Enter the times as they are. The calculator recognises that an end time before the start means the shift crossed midnight and handles it.',
			),
		),
		'related' => array(
			'hours-calculator',
			'time-card-calculator',
			'work-hours-calculator',
		),
		'disclaimer' => '',
	);
