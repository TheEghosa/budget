<?php
/**
 * Percent Change Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'percent-change-calculator',
		'title' => 'Percent Change Calculator',
		'category' => 'math',
		'description' => 'Find the percentage change between two figures, in either direction.',
		'keyword' => 'Percent Change Calculator',
		'h1' => 'Percent Change Calculator',
		'meta_title' => 'Percent Change Calculator - Between Two Numbers',
		'meta_description' => 'Free percent change calculator. Enter a before and after value to get the percentage increase or decrease, plus the absolute change between them.',
		'fields' => array(
			array(
				'id' => 'from',
				'label' => 'From',
				'type' => 'number',
				'default' => 50,
			),
			array(
				'id' => 'to',
				'label' => 'To',
				'type' => 'number',
				'default' => 75,
			),
		),
		'default_result' => array(
			'label' => 'Percentage increase',
			'value' => '50%',
			'rows' => array(
				array(
					'label' => 'From',
					'value' => '50',
				),
				array(
					'label' => 'To',
					'value' => '75',
				),
				array(
					'label' => 'Absolute change',
					'value' => '25',
				),
			),
			'note' => '',
		),
		'explainer' => array(
			array(
				'heading' => 'Direction matters',
				'body' => 'Going from 50 to 75 is a fifty per cent increase. Going from 75 back to 50 is a thirty-three per cent decrease. The same absolute movement produces different percentages because each is measured against a different starting point, which is why percentage changes should never be averaged.',
				'formula' => 'Change = (New &minus; Old) &divide; |Old| &times; 100',
			),
			array(
				'heading' => 'The zero problem',
				'body' => 'Change measured from zero has no percentage. Any increase from nothing is infinite, which is why analytics dashboards show a dash rather than a number when last month was zero.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What is the formula for percent change?',
				'a' => 'Subtract the old value from the new one, divide by the absolute value of the old one, and multiply by a hundred.',
			),
			array(
				'q' => 'Why do two sources report different percentage changes for the same thing?',
				'a' => 'Usually because they picked different baselines. Year on year, month on month and versus forecast all describe the same underlying number and produce very different percentages.',
			),
		),
		'related' => array(
			'percentage-increase-calculator',
			'percentage-decrease-calculator',
			'percentage-calculator',
		),
		'disclaimer' => '',
	);
