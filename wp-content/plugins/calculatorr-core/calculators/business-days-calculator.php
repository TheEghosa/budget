<?php
/**
 * Business Days Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'business-days-calculator',
		'title' => 'Business Days Calculator',
		'category' => 'time',
		'description' => 'Count working days between two dates.',
		'keyword' => 'Business Days Calculator',
		'h1' => 'Business Days Calculator',
		'meta_title' => 'Business Days Calculator - Working Days Between Dates',
		'meta_description' => 'Free business days calculator. Count weekdays between two dates, deduct public holidays and see the calendar days, weekend days and working weeks.',
		'fields' => array(
			array(
				'id' => 'start',
				'label' => 'Start date',
				'type' => 'date',
				'default' => '2026-09-01',
			),
			array(
				'id' => 'end',
				'label' => 'End date',
				'type' => 'date',
				'default' => '2026-09-30',
			),
			array(
				'id' => 'holidays',
				'label' => 'Public holidays in the range',
				'type' => 'number',
				'default' => 0,
				'hint' => 'Entered by hand, since holidays differ by country and state.',
			),
		),
		'default_result' => array(
			'label' => 'Business days',
			'value' => '22',
			'rows' => array(
				array(
					'label' => 'Calendar days',
					'value' => '30',
				),
				array(
					'label' => 'Weekend days',
					'value' => '8',
				),
				array(
					'label' => 'Weekdays before holidays',
					'value' => '22',
				),
				array(
					'label' => 'Holidays deducted',
					'value' => '0',
				),
				array(
					'label' => 'Working weeks',
					'value' => '4.4',
				),
			),
			'note' => 'Both the start and end dates are counted, which is the convention for contractual notice periods. Public holidays are entered by hand because they differ by country and by state.',
		),
		'explainer' => array(
			array(
				'heading' => 'Both ends are counted',
				'body' => 'The start and end dates are both included, which is the convention for contractual notice periods and court deadlines. If your agreement counts from the day after, subtract one from the result. Read the clause rather than assuming, because the two conventions differ by a full day and that day sometimes matters.',
				'formula' => 'Business days = weekdays in range − holidays',
			),
			array(
				'heading' => 'Holidays are your input for a reason',
				'body' => 'Public holidays vary by country, by state and by year, and several move annually. Rather than guess at a calendar that would be wrong for most visitors, the calculator asks how many fall in your range and deducts them.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How many business days are in a month?',
				'a' => 'Typically 20 to 23 before holidays, depending on how the weekends fall.',
			),
			array(
				'q' => 'Do business days include Saturday?',
				'a' => 'Not in the standard five day week used here. Some industries, retail and hospitality among them, count differently, and shipping carriers often treat Saturday as a business day for delivery.',
			),
		),
		'related' => array(
			'date-calculator',
			'work-hours-calculator',
			'time-card-calculator',
		),
		'disclaimer' => '',
	);
