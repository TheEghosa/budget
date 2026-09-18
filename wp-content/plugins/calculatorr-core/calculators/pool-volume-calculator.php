<?php
/**
 * Pool Volume Calculator.
 *
 * Part of the construction group, whose calculators share one shape:
 * a volume or area worked out from the measurements, converted into the
 * unit the supplier actually sells in, with an allowance on top.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'pool-volume-calculator',
		'title' => 'Pool Volume Calculator',
		'category' => 'home-diy',
		'description' => 'Work out how many gallons a rectangular, round or oval pool holds.',
		'keyword' => 'Pool Volume Calculator',
		'h1' => 'Pool Volume Calculator',
		'meta_title' => 'Pool Volume Calculator - Gallons in Your Pool',
		'meta_description' => 'Free pool volume calculator for rectangular, round and oval pools. Enter the dimensions and depths to get the volume in gallons, litres and cubic feet.',
		'fields' => array(
			array(
				'id' => 'shape',
				'label' => 'Pool shape',
				'type' => 'segmented',
				'options' => array(
					'rectangle' => 'Rectangle',
					'round' => 'Round',
					'oval' => 'Oval',
				),
				'default' => 'rectangle',
			),
			array(
				'id' => 'length',
				'label' => 'Length',
				'type' => 'number',
				'suffix' => 'ft',
				'default' => 32,
				'min' => 0,
				'show_when' => array(
					'shape' => array(
						'rectangle',
						'oval',
					),
				),
			),
			array(
				'id' => 'width',
				'label' => 'Width',
				'type' => 'number',
				'suffix' => 'ft',
				'default' => 16,
				'min' => 0,
				'show_when' => array(
					'shape' => array(
						'rectangle',
						'oval',
					),
				),
			),
			array(
				'id' => 'diameter',
				'label' => 'Diameter',
				'type' => 'number',
				'suffix' => 'ft',
				'default' => 18,
				'min' => 0,
				'show_when' => array(
					'shape' => 'round',
				),
			),
			array(
				'id' => 'shallow',
				'label' => 'Shallow end depth',
				'type' => 'number',
				'suffix' => 'ft',
				'default' => 3,
				'min' => 0,
			),
			array(
				'id' => 'deep',
				'label' => 'Deep end depth',
				'type' => 'number',
				'suffix' => 'ft',
				'default' => 8,
				'min' => 0,
				'hint' => 'For a flat-bottomed pool, put the same figure in both depth fields.',
			),
		),
		'default_result' => array(
			'label' => 'Pool volume',
			'value' => '21,065 gallons',
			'rows' => array(
				array(
					'label' => 'Litres',
					'value' => '79,740',
				),
				array(
					'label' => 'Cubic feet',
					'value' => '2,816',
				),
				array(
					'label' => 'Average depth',
					'value' => '5.5 ft',
				),
			),
			'note' => 'Averaging the shallow and deep ends is accurate for a pool with a steady slope. A pool with a sharp drop-off or a spa step holds less than this suggests.',
		),
		'explainer' => array(
			array(
				'heading' => 'Why volume is the number every chemical dose depends on',
				'body' => 'Every pool chemical is dosed per ten thousand gallons, so an inaccurate volume means every dose you ever make is wrong in the same direction. Getting this right once, and writing it on the pump housing, saves a season of guessing at chlorine and stabiliser.',
				'formula' => 'Gallons = Length &times; Width &times; Average depth &times; 7.48',
			),
			array(
				'heading' => 'Averaging the depths',
				'body' => 'A pool with a steady slope from shallow to deep holds the same as a flat pool at the average of the two depths, which is why adding them and halving works. It breaks down when the floor has a sharp drop-off or a large shallow shelf, because then more of the surface area sits at the shallow depth than the average implies. In that case measure the two sections separately and add them.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How many gallons is a 16x32 pool?',
				'a' => 'With depths of three and eight feet, about 21,000 gallons. A flat-bottomed pool of the same footprint at four feet deep holds roughly 15,300, so depth matters far more than most people expect.',
			),
			array(
				'q' => 'Why does my chlorine never hold?',
				'a' => 'Usually because the stabiliser level is wrong rather than the chlorine dose, but an overestimated volume is the next most common cause, since every dose lands short. Confirm the volume before adjusting anything else.',
			),
			array(
				'q' => 'Does the calculator work for above-ground pools?',
				'a' => 'Yes, and most above-ground pools are flat bottomed, so put the same depth in both fields. Measure the water depth rather than the wall height, since pools are rarely filled to the top.',
			),
		),
		'related' => array(
			'cubic-yard-calculator',
			'square-footage-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
