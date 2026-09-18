<?php
/**
 * Topsoil Calculator.
 *
 * Part of the construction group, whose calculators share one shape:
 * a volume or area worked out from the measurements, converted into the
 * unit the supplier actually sells in, with an allowance on top.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'topsoil-calculator',
		'title' => 'Topsoil Calculator',
		'category' => 'home-diy',
		'description' => 'Work out how much topsoil a bed, lawn or raised planter needs, in cubic yards and tons.',
		'keyword' => 'Topsoil Calculator',
		'h1' => 'Topsoil Calculator',
		'meta_title' => 'Topsoil Calculator - Cubic Yards & Tons for Beds',
		'meta_description' => 'Free topsoil calculator for garden beds, lawns and raised planters. Enter area and depth to get cubic yards, cubic feet and the approximate weight in US tons.',
		'fields' => array(
			array(
				'id' => 'length',
				'label' => 'Length',
				'type' => 'number',
				'suffix' => 'ft',
				'default' => 20,
				'min' => 0,
			),
			array(
				'id' => 'width',
				'label' => 'Width',
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
				'default' => 6,
				'min' => 0,
				'hint' => 'Four inches to level a lawn, six to twelve for planting beds.',
			),
			array(
				'id' => 'waste',
				'label' => 'Settling allowance',
				'type' => 'number',
				'suffix' => '%',
				'default' => 15,
				'min' => 0,
				'hint' => 'Fresh soil settles, so ordering a little over is normal rather than wasteful.',
			),
		),
		'default_result' => array(
			'label' => 'Topsoil to order',
			'value' => '4.26 cubic yards',
			'rows' => array(
				array(
					'label' => 'Volume',
					'value' => '115 cu ft',
				),
				array(
					'label' => 'Metric',
					'value' => '3.26 m3',
				),
				array(
					'label' => 'Approx. weight',
					'value' => '4.69 US tons',
				),
			),
			'note' => 'Weight is an average and varies with moisture and grade, so confirm the supplier’s own figure before ordering by the ton rather than by volume.',
		),
		'explainer' => array(
			array(
				'heading' => 'Why topsoil needs a settling allowance rather than a waste one',
				'body' => 'Delivered soil is loose and full of air. Once it is spread, watered and walked on it compacts by ten to twenty per cent, so a bed filled exactly to the rim ends up visibly low within a fortnight. The allowance here accounts for that rather than for spillage.',
				'formula' => 'Cubic yards = (L &times; W &times; D &divide; 12) &divide; 27',
			),
			array(
				'heading' => 'How deep for what you are growing',
				'body' => 'Four inches is enough to level a lawn before seeding. Six inches suits annuals and vegetables. Twelve inches or more is worth it for deep-rooted perennials and shrubs, and a raised bed should be filled to its full internal depth rather than topped up over the first season.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How much does a cubic yard of topsoil weigh?',
				'a' => 'Screened dry topsoil runs about 1.1 US tons per cubic yard, roughly 2,200 pounds. Wet soil is considerably heavier, sometimes approaching 1.5 tons, which matters if you are hiring a trailer with a weight limit.',
			),
			array(
				'q' => 'What is the difference between topsoil and garden soil?',
				'a' => 'Topsoil is screened native soil sold in bulk, while bagged garden soil is topsoil already amended with compost and fertiliser. For filling volume, buy topsoil and amend it yourself, because paying bagged prices to fill a raised bed gets expensive fast.',
			),
			array(
				'q' => 'Can I put topsoil straight over grass?',
				'a' => 'Only a thin layer. Anything over about half an inch at a time smothers the grass without killing it cleanly, which leaves a rotting layer under your new soil. To replace a lawn entirely, strip or kill the existing turf first.',
			),
		),
		'related' => array(
			'mulch-calculator',
			'gravel-calculator',
			'cubic-yard-calculator',
		),
		'disclaimer' => '',
	);
