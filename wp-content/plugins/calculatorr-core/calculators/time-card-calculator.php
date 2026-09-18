<?php
/**
 * Time Card Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'time-card-calculator',
		'title' => 'Time Card Calculator',
		'category' => 'time',
		'description' => 'Add up a week of shifts and work out gross pay.',
		'keyword' => 'Time Card Calculator',
		'h1' => 'Time Card Calculator',
		'meta_title' => 'Time Card Calculator - Weekly Timesheet and Pay',
		'meta_description' => 'Free time card calculator. Enter each day’s shift and break to total the week, split regular from overtime hours and calculate gross pay.',
		'fields' => array(
			array(
				'id' => 'shifts',
				'label' => 'Your shifts',
				'type' => 'repeater',
				'rows' => 5,
				'row' => array(
					array(
						'id' => 'start',
						'label' => 'Start',
						'type' => 'text',
						'default' => '9:00 AM',
					),
					array(
						'id' => 'end',
						'label' => 'End',
						'type' => 'text',
						'default' => '5:00 PM',
					),
					array(
						'id' => 'brk',
						'label' => 'Break (min)',
						'type' => 'number',
						'default' => 30,
					),
				),
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
			'label' => 'Total for the week',
			'value' => '0h 0m',
			'rows' => array(
				array(
					'label' => 'Decimal hours',
					'value' => '0',
				),
				array(
					'label' => 'Regular hours',
					'value' => '0',
				),
				array(
					'label' => 'Overtime hours',
					'value' => '0',
				),
				array(
					'label' => 'Gross pay',
					'value' => '$0.00',
				),
			),
			'note' => 'Overtime is calculated at time and a half above forty hours in a week, which is the federal FLSA rule. Some states, California among them, also pay overtime above eight hours in a single day.',
		),
		'explainer' => array(
			array(
				'heading' => 'Overtime is weekly, not daily, under federal law',
				'body' => 'The federal FLSA requires time and a half above forty hours in a workweek. Nine hours on Monday does not trigger overtime by itself if the week totals under forty. Several states are stricter, California among them, where overtime also starts above eight hours in a single day.',
				'formula' => 'Gross = regular hours × rate + overtime hours × rate × 1.5',
			),
			array(
				'heading' => 'Why breaks come off each day',
				'body' => 'Unpaid meal breaks are deducted per shift rather than from the weekly total, because a break on a short day and a break on a long day have different effects once overtime enters the calculation. Deducting at the end would quietly overstate the overtime.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'Does my employer have to give me a break?',
				'a' => 'Federal law does not require meal or rest breaks at all. Many states do, and where breaks are given, short ones must be paid. Check your state rules rather than the federal minimum.',
			),
			array(
				'q' => 'How is overtime calculated on multiple pay rates?',
				'a' => 'Usually on a weighted average of the rates worked that week. This calculator assumes a single rate, so a multi-rate week needs a payroll system rather than a timesheet.',
			),
		),
		'related' => array(
			'hours-calculator',
			'overtime-calculator',
			'work-hours-calculator',
		),
		'disclaimer' => '',
	);
