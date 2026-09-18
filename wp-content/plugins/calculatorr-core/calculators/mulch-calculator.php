<?php
/**
 * Mulch Calculator.
 *
 * Part of the construction group, whose calculators share one shape:
 * a volume or area worked out from the measurements, converted into the
 * unit the supplier actually sells in, with an allowance on top.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'mulch-calculator',
		'title' => 'Mulch Calculator',
		'category' => 'home-diy',
		'description' => 'Work out how much mulch a bed needs, in cubic yards and in two cubic foot bags.',
		'keyword' => 'Mulch Calculator',
		'h1' => 'Mulch Calculator',
		'meta_title' => 'Mulch Calculator - Cubic Yards & Bags for Beds',
		'meta_description' => 'Free mulch calculator for garden beds and borders. Enter the area and depth to get cubic yards, cubic feet and the number of two cubic foot bags to buy.',
		'fields' => array(
			array(
				'id' => 'length',
				'label' => 'Bed length',
				'type' => 'number',
				'suffix' => 'ft',
				'default' => 20,
				'min' => 0,
			),
			array(
				'id' => 'width',
				'label' => 'Bed width',
				'type' => 'number',
				'suffix' => 'ft',
				'default' => 10,
				'min' => 0,
			),
			array(
				'id' => 'depth',
				'label' => 'Depth',
				'type' => 'number',
				'suffix' => 'in',
				'default' => 3,
				'min' => 0,
				'hint' => 'Three inches suppresses weeds without suffocating roots. More is not better.',
			),
			array(
				'id' => 'waste',
				'label' => 'Extra allowance',
				'type' => 'number',
				'suffix' => '%',
				'default' => 10,
				'min' => 0,
			),
		),
		'default_result' => array(
			'label' => 'Mulch to order',
			'value' => '2.04 cubic yards',
			'rows' => array(
				array(
					'label' => 'Volume',
					'value' => '55 cu ft',
				),
				array(
					'label' => 'Metric',
					'value' => '1.56 m3',
				),
				array(
					'label' => 'Approx. weight',
					'value' => '1.02 US tons',
				),
				array(
					'label' => '2 cu ft bags',
					'value' => '28',
				),
			),
			'note' => 'Weight is an average and varies with moisture and grade, so confirm the supplier’s own figure before ordering by the ton rather than by volume.',
		),
		'explainer' => array(
			array(
				'heading' => 'Three inches, and why not more',
				'body' => 'Three inches of mulch blocks enough light to stop most weed seeds germinating while still letting water and air through. Deeper layers hold moisture against stems and trunks, which invites rot, and a mulch volcano piled against a tree trunk is one of the most common ways a young tree is quietly killed. Keep it pulled back a few inches from anything woody.',
				'formula' => 'Cubic yards = (L &times; W &times; D &divide; 12) &divide; 27',
			),
			array(
				'heading' => 'Bags or bulk',
				'body' => 'A cubic yard is thirteen and a half of the standard two cubic foot bags, and bulk delivery is usually cheaper from about two yards upward. Bags win when you have nowhere to tip a pile, or when the beds are far enough from the driveway that a wheelbarrow run would take longer than carrying.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How many bags of mulch are in a cubic yard?',
				'a' => 'Thirteen and a half bags of the standard two cubic foot size, so fourteen in practice. Three cubic foot bags work out at nine per yard.',
			),
			array(
				'q' => 'How often should mulch be replaced?',
				'a' => 'Organic mulch breaks down over a season or two and needs topping up annually, usually about an inch. That is a feature rather than a chore, since the decomposed material is feeding the soil underneath.',
			),
			array(
				'q' => 'Does mulch really stop weeds?',
				'a' => 'It stops most weed seeds already in the soil from germinating, which is the bulk of the problem. It does not stop seeds landing on top of the mulch later, and it does not stop established perennial weeds, which push straight through. Pull those first.',
			),
		),
		'related' => array(
			'topsoil-calculator',
			'square-footage-calculator',
			'cubic-yard-calculator',
		),
		'disclaimer' => '',
	);
