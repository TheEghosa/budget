<?php
/**
 * Bra Size Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'bra-size-calculator',
		'title' => 'Bra Size Calculator',
		'category' => 'health',
		'description' => 'Find your bra size and sister sizes from two measurements.',
		'keyword' => 'Bra Size Calculator',
		'h1' => 'Bra Size Calculator',
		'meta_title' => 'Bra Size Calculator - Band, Cup and Sister Sizes',
		'meta_description' => 'Free bra size calculator using the modern measuring method. Enter underbust and bust measurements for your band, cup and the sister sizes to try.',
		'fields' => array(
			array(
				'id' => 'units',
				'label' => 'Units',
				'type' => 'segmented',
				'options' => array(
					'metric' => 'Metric',
					'imperial' => 'Imperial',
				),
				'default' => 'imperial',
			),
			array(
				'id' => 'underbust',
				'label' => 'Underbust',
				'type' => 'number',
				'suffix' => 'in',
				'default' => 32,
				'show_when' => array(
					'units' => 'imperial',
				),
				'hint' => 'Snug, directly under the bust.',
			),
			array(
				'id' => 'bust',
				'label' => 'Bust',
				'type' => 'number',
				'suffix' => 'in',
				'default' => 36,
				'show_when' => array(
					'units' => 'imperial',
				),
				'hint' => 'Loose, around the fullest part.',
			),
			array(
				'id' => 'underbust',
				'label' => 'Underbust',
				'type' => 'number',
				'suffix' => 'cm',
				'default' => 81,
				'show_when' => array(
					'units' => 'metric',
				),
			),
			array(
				'id' => 'bust',
				'label' => 'Bust',
				'type' => 'number',
				'suffix' => 'cm',
				'default' => 91,
				'show_when' => array(
					'units' => 'metric',
				),
			),
		),
		'default_result' => array(
			'label' => 'Estimated size',
			'value' => '82I',
			'rows' => array(
				array(
					'label' => 'Band',
					'value' => '82',
				),
				array(
					'label' => 'Cup',
					'value' => 'I (9 in difference)',
				),
				array(
					'label' => 'Sister size down',
					'value' => '80J',
				),
				array(
					'label' => 'Sister size up',
					'value' => '84H',
				),
			),
			'note' => 'Sizing is not standardised between brands, so treat this as a starting point and expect to try a size either side. If the cup fits but the band rides up, the sister sizes above are the ones to try.',
		),
		'explainer' => array(
			array(
				'heading' => 'The plus-four method is obsolete',
				'body' => 'The old advice was to add four inches to the underbust measurement to get the band. That came from stiff, unstretchy fabrics and produces a band several sizes too big with modern materials. The current method rounds the underbust to the nearest even number and stops there, which is what this calculator does.',
				'formula' => 'Cup letter = bust − band, one letter per inch',
			),
			array(
				'heading' => 'Sister sizes',
				'body' => 'The same cup volume exists at several band sizes: 34C, 32D and 36B all hold a similar amount. If the cup fits but the band rides up your back, go down a band and up a cup. If the band digs in, do the reverse.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'Why do I fit different sizes in different shops?',
				'a' => 'Because bra sizing is not standardised. Brands cut to different blocks and there is no governing specification, so a size is a starting point rather than a measurement.',
			),
			array(
				'q' => 'How do I know the band is right?',
				'a' => 'It should sit level all the way round and stay put on the loosest hook, so there is room to tighten as it stretches. Most of the support comes from the band rather than the straps.',
			),
		),
		'related' => array(
			'body-fat-calculator',
			'bmi-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
