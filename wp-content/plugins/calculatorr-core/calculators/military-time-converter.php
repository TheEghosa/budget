<?php
/**
 * Military Time Converter.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'military-time-converter',
		'title' => 'Military Time Converter',
		'category' => 'time',
		'description' => 'Convert between 12 hour and 24 hour time.',
		'keyword' => 'Military Time Converter',
		'h1' => 'Military Time Converter',
		'meta_title' => 'Military Time Converter - 24 Hour Clock Both Ways',
		'meta_description' => 'Free military time converter. Change 24 hour time into standard AM and PM time or the other way round, with the spoken form shown.',
		'fields' => array(
			array(
				'id' => 'direction',
				'label' => 'Direction',
				'type' => 'segmented',
				'options' => array(
					'toMilitary' => 'Standard to military',
					'toStandard' => 'Military to standard',
				),
				'default' => 'toMilitary',
			),
			array(
				'id' => 'standard',
				'label' => 'Standard time',
				'type' => 'text',
				'default' => '2:30 PM',
				'show_when' => array(
					'direction' => 'toMilitary',
				),
				'hint' => 'Such as 9:00 AM, 5:30 PM or 14:45.',
			),
			array(
				'id' => 'military',
				'label' => 'Military time',
				'type' => 'text',
				'default' => '1430',
				'show_when' => array(
					'direction' => 'toStandard',
				),
				'hint' => 'Four digits, such as 0900 or 1745.',
			),
		),
		'default_result' => array(
			'label' => '2:30 PM in military time',
			'value' => '1430',
			'rows' => array(
				array(
					'label' => 'With a colon',
					'value' => '14:30',
				),
				array(
					'label' => 'Minutes past midnight',
					'value' => '870',
				),
			),
			'note' => 'Midnight is 0000 and noon is 1200. There is no 2400 in normal use, since the day rolls over to 0000.',
		),
		'explainer' => array(
			array(
				'heading' => 'The rule in one line',
				'body' => 'Before noon the hours are the same with a leading zero, so 9am is 0900. After noon add twelve, so 2pm is 1400. Midnight is 0000 and noon is 1200, and the confusion people have with 12am and 12pm disappears entirely in the 24 hour system, which is exactly why hospitals, aviation and the military use it.',
				'formula' => '2:30 PM = 14:30 = 1430',
			),
			array(
				'heading' => 'How it is spoken',
				'body' => '1430 is read as fourteen thirty. On the hour, 1400 is fourteen hundred. Times before ten take a spoken zero, so 0700 is zero seven hundred, which is where the habit of writing the leading zero comes from.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What is 1800 in standard time?',
				'a' => '6:00 PM. Subtract twelve from any hour above twelve to get the PM equivalent.',
			),
			array(
				'q' => 'Is there a 2400?',
				'a' => 'Not in normal use. The day rolls over to 0000, so midnight at the end of Tuesday is 0000 on Wednesday. Some military writing uses 2359 to end a day precisely and avoid the ambiguity.',
			),
		),
		'related' => array(
			'time-calculator',
			'time-duration-calculator',
		),
		'disclaimer' => '',
	);
