<?php
/**
 * Gravel Calculator.
 *
 * Part of the construction group, whose calculators share one shape:
 * a volume or area worked out from the measurements, converted into the
 * unit the supplier actually sells in, with an allowance on top.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'gravel-calculator',
		'title' => 'Gravel Calculator',
		'category' => 'home-diy',
		'description' => 'Work out how much gravel a driveway, path or base layer needs, by volume and by weight.',
		'keyword' => 'Gravel Calculator',
		'h1' => 'Gravel Calculator',
		'meta_title' => 'Gravel Calculator - Cubic Yards & Tons Needed',
		'meta_description' => 'Free gravel calculator for driveways, paths and base layers. Enter the area and depth for cubic yards, cubic feet and the approximate weight in US tons.',
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
				'default' => 3,
				'min' => 0,
				'hint' => 'Two to three inches for a path, four or more for a driveway that carries vehicles.',
			),
			array(
				'id' => 'waste',
				'label' => 'Waste allowance',
				'type' => 'number',
				'suffix' => '%',
				'default' => 10,
				'min' => 0,
			),
		),
		'default_result' => array(
			'label' => 'Gravel to order',
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
					'value' => '2.85 US tons',
				),
			),
			'note' => 'Weight is an average and varies with moisture and grade, so confirm the supplier’s own figure before ordering by the ton rather than by volume.',
		),
		'explainer' => array(
			array(
				'heading' => 'Volume first, weight second',
				'body' => 'Gravel is quoted both ways and the two are not interchangeable. Volume is the reliable figure because it comes straight from your measurements, while weight depends on the stone type, its grading and how wet it is on the day. Crushed stone averages around 1.4 US tons per cubic yard, which is the figure used here.',
				'formula' => 'Cubic yards = (L &times; W &times; D &divide; 12) &divide; 27',
			),
			array(
				'heading' => 'How deep to go',
				'body' => 'A footpath or garden bed is happy at two to three inches. A driveway needs four inches or more, and on soft ground it wants a coarser base layer underneath with the decorative stone on top. Going too shallow is a false economy, because thin gravel migrates into the soil and disappears within a season.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How many tons of gravel are in a cubic yard?',
				'a' => 'Roughly 1.4 US tons for typical crushed stone, though it ranges from about 1.2 to 1.7 depending on the rock and how wet it is. Order by volume where you can, and if the supplier sells by weight, ask for their own conversion rather than relying on an average.',
			),
			array(
				'q' => 'Should I use landscape fabric underneath?',
				'a' => 'On soil, yes. Fabric stops the stone working down into the ground, which is the main way a gravel surface thins out over a few years. It also suppresses weeds, though not permanently, since debris eventually builds a seedbed on top of it.',
			),
			array(
				'q' => 'What size gravel do I need?',
				'a' => 'Smaller angular stone around three eighths of an inch compacts well and stays put underfoot, which suits paths. Larger stone drains better but rolls under tyres and feet. For a driveway a compacted crushed base under a decorative top layer outlasts either on its own.',
			),
		),
		'related' => array(
			'topsoil-calculator',
			'concrete-calculator',
			'cubic-yard-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
