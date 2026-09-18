<?php
/**
 * Overtime Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'overtime-calculator',
		'title' => 'Overtime Calculator',
		'category' => 'time',
		'description' => 'Work out overtime and double time pay.',
		'keyword' => 'Overtime Calculator',
		'h1' => 'Overtime Calculator',
		'meta_title' => 'Overtime Calculator - Time and a Half Pay',
		'meta_description' => 'Free overtime calculator. Work out gross pay across regular, overtime and double time hours, with the effective hourly rate once the premium is included.',
		'fields' => array(
			array(
				'id' => 'rate',
				'label' => 'Base hourly rate',
				'type' => 'number',
				'prefix' => '$',
				'default' => 20,
			),
			array(
				'id' => 'regular',
				'label' => 'Regular hours',
				'type' => 'number',
				'default' => 40,
			),
			array(
				'id' => 'overtime',
				'label' => 'Overtime hours',
				'type' => 'number',
				'default' => 10,
			),
			array(
				'id' => 'multiplier',
				'label' => 'Overtime multiplier',
				'type' => 'number',
				'default' => 1.5,
				'step' => 'any',
				'hint' => '1.5 is time and a half, the federal standard.',
			),
			array(
				'id' => 'doubleTime',
				'label' => 'Double time hours',
				'type' => 'number',
				'default' => 0,
			),
		),
		'default_result' => array(
			'label' => 'Gross pay',
			'value' => '$1,100.00',
			'rows' => array(
				array(
					'label' => 'Regular, 40 h',
					'value' => '$800.00',
				),
				array(
					'label' => 'Overtime at 1.5x',
					'value' => '$300.00',
				),
				array(
					'label' => 'Double time',
					'value' => '$0.00',
				),
				array(
					'label' => 'Effective hourly rate',
					'value' => '$22.00',
				),
				array(
					'label' => 'Total hours',
					'value' => '50',
				),
			),
			'note' => 'Under the federal FLSA, overtime is time and a half above forty hours in a week for non-exempt employees. Several states are more generous, so check your own before assuming forty is the threshold.',
		),
		'explainer' => array(
			array(
				'heading' => 'Time and a half means the whole rate, not the half',
				'body' => 'Overtime at 1.5x pays one and a half times your base rate for those hours, not your base rate plus half. On a twenty dollar rate that is thirty dollars an hour, not thirty dollars on top. The mistake usually undercounts what you are owed.',
				'formula' => 'Overtime pay = hours × base rate × 1.5',
			),
			array(
				'heading' => 'Who actually qualifies',
				'body' => 'The FLSA requires overtime only for non-exempt employees. Exempt status depends on salary level and actual job duties rather than on a job title or on being paid a salary, and misclassification is common enough that it is worth checking against the duties tests rather than taking an employer’s word.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'Is overtime paid after 8 hours or 40?',
				'a' => 'Federally, after 40 in a week. California and a few other states also require it after 8 in a day, so the answer depends on where you work.',
			),
			array(
				'q' => 'Does my salary mean I cannot get overtime?',
				'a' => 'No. Being salaried does not by itself make you exempt. Exemption requires meeting both a salary threshold and a duties test, and many salaried workers remain entitled to overtime.',
			),
		),
		'related' => array(
			'time-card-calculator',
			'work-hours-calculator',
			'take-home-pay-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
