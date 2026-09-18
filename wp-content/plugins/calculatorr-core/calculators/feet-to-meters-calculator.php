<?php
/**
 * Feet to Meters Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'feet-to-meters-calculator',
		'title' => 'Feet to Meters Calculator',
		'category' => 'convert',
		'description' => 'Convert feet to metres and back.',
		'keyword' => 'Feet to Meters Calculator',
		'h1' => 'Feet to Meters Calculator',
		'meta_title' => 'Feet to Meters Calculator - Exact Conversion',
		'meta_description' => 'Free feet to metres converter. Change feet into metres using the exact definition, with centimetres and inches shown alongside for reference.',
		'fields' => array(
			array(
				'id' => 'value',
				'label' => 'Feet',
				'type' => 'number',
				'default' => 6,
				'step' => 'any',
			),
		),
		'default_result' => array(
			'label' => '6 ft in m',
			'value' => '1.8288 m',
			'rows' => array(
				array(
					'label' => 'Centimetres',
					'value' => '182.88',
				),
				array(
					'label' => 'Metres',
					'value' => '1.8288',
				),
				array(
					'label' => 'Inches',
					'value' => '72',
				),
				array(
					'label' => 'Feet',
					'value' => '6',
				),
			),
			'note' => 'A foot is exactly 0.3048 metres by definition, so three feet is a little under a metre and the two are close enough that people often confuse them.',
		),
		'explainer' => array(
			array(
				'heading' => 'Exact, not approximate',
				'body' => 'A foot is defined as exactly 0.3048 metres, so this conversion introduces no error at all. Three feet is 0.9144 metres, which is a yard and just under a metre, and that near-miss is why the two are so often confused in casual use.',
				'formula' => 'metres = feet × 0.3048',
			),
			array(
				'heading' => 'Where it matters',
				'body' => 'Aviation still uses feet for altitude almost everywhere while most other measurements went metric, which produces some genuinely dangerous conversion work in international operations. Outside that, the main use is height and property dimensions.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How many metres is 6 feet?',
				'a' => '1.8288 metres, usually quoted as 1.83.',
			),
			array(
				'q' => 'Is a yard the same as a metre?',
				'a' => 'Close but not equal. A yard is 0.9144 metres, about eight per cent short, which is enough to matter over any real distance.',
			),
		),
		'related' => array(
			'unit-converter',
			'inches-to-feet-calculator',
			'mm-to-inches-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
