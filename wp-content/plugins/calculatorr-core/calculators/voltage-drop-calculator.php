<?php
/**
 * Voltage Drop Calculator.
 *
 * Part of the construction group, whose calculators share one shape:
 * a volume or area worked out from the measurements, converted into the
 * unit the supplier actually sells in, with an allowance on top.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'voltage-drop-calculator',
		'title' => 'Voltage Drop Calculator',
		'category' => 'home-diy',
		'description' => 'Work out voltage drop over a cable run and whether it stays inside NEC limits.',
		'keyword' => 'Voltage Drop Calculator',
		'h1' => 'Voltage Drop Calculator',
		'meta_title' => 'Voltage Drop Calculator - NEC 3% Check by Wire Size',
		'meta_description' => 'Free voltage drop calculator for copper and aluminium wire. Enter gauge, current, distance and voltage to see the drop, the percentage and the NEC check.',
		'fields' => array(
			array(
				'id' => 'material',
				'label' => 'Conductor',
				'type' => 'segmented',
				'options' => array(
					'copper' => 'Copper',
					'aluminum' => 'Aluminium',
				),
				'default' => 'copper',
			),
			array(
				'id' => 'gauge',
				'label' => 'Wire size',
				'type' => 'select',
				'options' => array(
					'14' => 'AWG 14',
					'12' => 'AWG 12',
					'10' => 'AWG 10',
					'8' => 'AWG 8',
					'6' => 'AWG 6',
					'4' => 'AWG 4',
					'3' => 'AWG 3',
					'2' => 'AWG 2',
					'1' => 'AWG 1',
					'1/0' => 'AWG 1/0',
					'2/0' => 'AWG 2/0',
					'3/0' => 'AWG 3/0',
					'4/0' => 'AWG 4/0',
				),
				'default' => '12',
			),
			array(
				'id' => 'amps',
				'label' => 'Load current',
				'type' => 'number',
				'suffix' => 'A',
				'default' => 20,
				'min' => 0,
			),
			array(
				'id' => 'distance',
				'label' => 'One-way distance',
				'type' => 'number',
				'suffix' => 'ft',
				'default' => 100,
				'min' => 0,
				'hint' => 'The run in one direction. The return leg is already accounted for.',
			),
			array(
				'id' => 'volts',
				'label' => 'Supply voltage',
				'type' => 'number',
				'suffix' => 'V',
				'default' => 120,
				'min' => 1,
			),
			array(
				'id' => 'phase',
				'label' => 'System',
				'type' => 'segmented',
				'options' => array(
					'single' => 'Single phase',
					'three' => 'Three phase',
				),
				'default' => 'single',
			),
		),
		'default_result' => array(
			'label' => 'Voltage drop',
			'value' => '7.9 V',
			'rows' => array(
				array(
					'label' => 'Percentage drop',
					'value' => '6.58%',
				),
				array(
					'label' => 'Voltage at the load',
					'value' => '112.1 V',
				),
				array(
					'label' => 'Conductor',
					'value' => 'AWG 12, copper',
				),
				array(
					'label' => '3% branch circuit limit',
					'value' => 'Exceeded',
				),
			),
			'note' => 'Over the three per cent the NEC recommends for a branch circuit. Go up a wire size, shorten the run, or raise the supply voltage.',
		),
		'explainer' => array(
			array(
				'heading' => 'Why distance costs you voltage',
				'body' => 'Every conductor has resistance, and resistance over a long run turns some of your supply voltage into heat before it reaches the load. The effect scales with current and with length, so a small load on a short run is fine while the same wire feeding a workshop two hundred feet away is not.',
				'formula' => 'Vd = (2 &times; K &times; I &times; L) &divide; Circular mils',
			),
			array(
				'heading' => 'The three and five per cent figures',
				'body' => 'The NEC recommends no more than three per cent drop on a branch circuit and no more than five per cent across feeder and branch combined. These are recommendations in informational notes rather than hard requirements, but they exist because motors run hot, lights dim and electronics misbehave beyond them. Treat three per cent as the target.',
			),
			array(
				'heading' => 'The fix is almost always a bigger wire',
				'body' => 'Shortening the run is rarely possible after the fact and raising the supply voltage usually is not either, so the practical remedy is to go up a conductor size. Each step up in AWG roughly increases the circular mil area by about a quarter, which cuts the drop proportionally.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What is an acceptable voltage drop?',
				'a' => 'Three per cent or less on a branch circuit, and five per cent or less for feeder and branch together, per NEC informational notes. Under three per cent, most equipment behaves exactly as intended.',
			),
			array(
				'q' => 'Is aluminium wire worse for voltage drop?',
				'a' => 'Yes, for the same size. Aluminium has roughly 1.6 times the resistivity of copper, so an aluminium conductor needs to be about two sizes larger to match a copper one. It is still widely used for larger feeders because it is considerably cheaper and lighter at that scale.',
			),
			array(
				'q' => 'Does voltage drop matter on a short run?',
				'a' => 'Rarely. A twenty amp circuit on 12 AWG copper stays inside three per cent to roughly 50 feet at 120 volts. The problem starts on long runs to garages, sheds, wells and outbuildings, which is exactly where people tend to guess at the wire size.',
			),
		),
		'related' => array(
			'board-foot-calculator',
		),
		'disclaimer' => 'For planning only. Electrical work must be designed and installed to the National Electrical Code and any local amendments, and inspected where required.',
	);
