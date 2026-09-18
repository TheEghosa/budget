<?php
/**
 * Date Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'date-calculator',
		'title' => 'Date Calculator',
		'category' => 'time',
		'description' => 'Add or subtract days, weeks and months from a date.',
		'keyword' => 'Date Calculator',
		'h1' => 'Date Calculator',
		'meta_title' => 'Date Calculator - Add or Subtract Days From a Date',
		'meta_description' => 'Free date calculator. Add or subtract days, weeks and months from any date and get the weekday, the ISO format and the day of the year.',
		'fields' => array(
			array(
				'id' => 'start',
				'label' => 'Start date',
				'type' => 'date',
				'default' => '2026-01-31',
			),
			array(
				'id' => 'op',
				'label' => 'Operation',
				'type' => 'segmented',
				'options' => array(
					'add' => 'Add',
					'sub' => 'Subtract',
				),
				'default' => 'add',
			),
			array(
				'id' => 'days',
				'label' => 'Days',
				'type' => 'number',
				'default' => 0,
			),
			array(
				'id' => 'weeks',
				'label' => 'Weeks',
				'type' => 'number',
				'default' => 0,
			),
			array(
				'id' => 'months',
				'label' => 'Months',
				'type' => 'number',
				'default' => 1,
			),
		),
		'default_result' => array(
			'label' => 'Resulting date',
			'value' => 'Sat, Feb 28, 2026',
			'rows' => array(
				array(
					'label' => 'Day of the week',
					'value' => 'Saturday',
				),
				array(
					'label' => 'Days from the start',
					'value' => '28',
				),
				array(
					'label' => 'ISO format',
					'value' => '2026-02-28',
				),
				array(
					'label' => 'Day of the year',
					'value' => '59',
				),
			),
			'note' => '',
		),
		'explainer' => array(
			array(
				'heading' => 'Months are not a fixed length',
				'body' => 'Adding one month to 31 January cannot land on 31 February, so the result is clamped to the last day of the target month, giving 28 February or 29 in a leap year. Every date library makes a choice here and they do not all agree, which is why contracts usually specify days rather than months.',
				'formula' => '31 Jan + 1 month = 28 Feb, not 3 Mar',
			),
			array(
				'heading' => 'Order of operations',
				'body' => 'Months are applied first and then days, because doing it the other way round produces different answers for the same input. Adding a month then a day to 31 January gives 1 March, while adding a day then a month gives 1 March as well here, but the two orders diverge in other cases.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What date is 90 days from today?',
				'a' => 'Enter today and 90 days. The calculator also tells you the weekday, which is usually the part that matters for a deadline.',
			),
			array(
				'q' => 'How do I count days between two dates?',
				'a' => 'Use the business days calculator for working days, which also gives the plain calendar count.',
			),
		),
		'related' => array(
			'business-days-calculator',
			'chronological-age-calculator',
			'time-duration-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
