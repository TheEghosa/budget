<?php
/**
 * Time Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'time-calculator',
		'title' => 'Time Calculator',
		'category' => 'time',
		'description' => 'Add or subtract hours and minutes from a time.',
		'keyword' => 'Time Calculator',
		'h1' => 'Time Calculator',
		'meta_title' => 'Time Calculator - Add or Subtract Hours and Minutes',
		'meta_description' => 'Free time calculator. Add or subtract hours and minutes from any start time, with the 24 hour equivalent and any day change shown clearly.',
		'fields' => array(
			array(
				'id' => 'start',
				'label' => 'Start time',
				'type' => 'text',
				'default' => '9:00 AM',
				'hint' => 'Such as 9:00 AM, 5:30 PM or 14:45.',
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
				'id' => 'hours',
				'label' => 'Hours',
				'type' => 'number',
				'default' => 3,
			),
			array(
				'id' => 'minutes',
				'label' => 'Minutes',
				'type' => 'number',
				'default' => 30,
			),
		),
		'default_result' => array(
			'label' => 'Time plus 3h 30m',
			'value' => '12:30 PM',
			'rows' => array(
				array(
					'label' => '24 hour clock',
					'value' => '12:30',
				),
				array(
					'label' => 'Day change',
					'value' => 'same day',
				),
				array(
					'label' => 'Started at',
					'value' => '9:00 AM',
				),
			),
			'note' => '',
		),
		'explainer' => array(
			array(
				'heading' => 'Why clock arithmetic trips people up',
				'body' => 'Time runs in base sixty for minutes and base twelve or twenty-four for hours, so ordinary decimal addition gives the wrong answer. Adding 45 minutes to 9:30 is not 9:75. The calculator converts everything to minutes past midnight, does the arithmetic there and converts back, which is how every reliable method works.',
				'formula' => 'Result = (start minutes + change) mod 1440',
			),
			array(
				'heading' => 'Crossing midnight',
				'body' => 'Adding enough hours rolls past midnight into the next day, and subtracting can roll into the previous one. The day change is reported separately rather than silently absorbed, because for a shift schedule or a flight arrival that detail is the whole point.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How do I add hours to a time?',
				'a' => 'Convert to minutes past midnight, add, then convert back. The calculator does this and also tells you if the result lands on a different day.',
			),
			array(
				'q' => 'What is 9:45 plus 2 hours 30 minutes?',
				'a' => '12:15 PM. The minutes carry over into an extra hour, which is where hand calculations usually go wrong.',
			),
		),
		'related' => array(
			'time-duration-calculator',
			'hours-calculator',
			'military-time-converter',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
