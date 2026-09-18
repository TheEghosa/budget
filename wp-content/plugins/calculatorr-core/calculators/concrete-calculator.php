<?php
/**
 * Concrete Calculator.
 *
 * Part of the construction group, whose calculators share one shape:
 * a volume or area worked out from the measurements, converted into the
 * unit the supplier actually sells in, with an allowance on top.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'concrete-calculator',
		'title' => 'Concrete Calculator',
		'category' => 'home-diy',
		'description' => 'Work out how much concrete a slab, footing or column needs, in cubic yards and in bags.',
		'keyword' => 'Concrete Calculator',
		'h1' => 'Concrete Calculator',
		'meta_title' => 'Concrete Calculator - Cubic Yards & Bags Needed',
		'meta_description' => 'Free concrete calculator for slabs, footings and columns. Get cubic yards, cubic feet and the number of 80, 60 or 40 lb bags, with waste allowed for.',
		'fields' => array(
			array(
				'id' => 'shape',
				'label' => 'What are you pouring',
				'type' => 'segmented',
				'options' => array(
					'slab' => 'Slab',
					'footing' => 'Footing',
					'column' => 'Column',
				),
				'default' => 'slab',
			),
			array(
				'id' => 'length',
				'label' => 'Length',
				'type' => 'number',
				'suffix' => 'ft',
				'default' => 10,
				'min' => 0,
				'show_when' => array(
					'shape' => array(
						'slab',
						'footing',
					),
				),
			),
			array(
				'id' => 'width',
				'label' => 'Width',
				'type' => 'number',
				'suffix' => 'ft',
				'default' => 10,
				'min' => 0,
				'show_when' => array(
					'shape' => 'slab',
				),
			),
			array(
				'id' => 'thickness',
				'label' => 'Thickness',
				'type' => 'number',
				'suffix' => 'in',
				'default' => 4,
				'min' => 0,
				'show_when' => array(
					'shape' => 'slab',
				),
				'hint' => '4 inches is standard for a patio or walkway, 6 for a driveway.',
			),
			array(
				'id' => 'footWidth',
				'label' => 'Footing width',
				'type' => 'number',
				'suffix' => 'in',
				'default' => 16,
				'min' => 0,
				'show_when' => array(
					'shape' => 'footing',
				),
			),
			array(
				'id' => 'footDepth',
				'label' => 'Footing depth',
				'type' => 'number',
				'suffix' => 'in',
				'default' => 8,
				'min' => 0,
				'show_when' => array(
					'shape' => 'footing',
				),
			),
			array(
				'id' => 'diameter',
				'label' => 'Column diameter',
				'type' => 'number',
				'suffix' => 'in',
				'default' => 12,
				'min' => 0,
				'show_when' => array(
					'shape' => 'column',
				),
			),
			array(
				'id' => 'height',
				'label' => 'Column height',
				'type' => 'number',
				'suffix' => 'ft',
				'default' => 8,
				'min' => 0,
				'show_when' => array(
					'shape' => 'column',
				),
			),
			array(
				'id' => 'quantity',
				'label' => 'How many',
				'type' => 'number',
				'default' => 1,
				'min' => 1,
				'step' => 1,
			),
			array(
				'id' => 'waste',
				'label' => 'Waste allowance',
				'type' => 'number',
				'suffix' => '%',
				'default' => 10,
				'min' => 0,
				'hint' => 'Ten per cent covers spillage and uneven subgrade. Raise it if the ground is rough.',
			),
		),
		'default_result' => array(
			'label' => 'Concrete to order',
			'value' => '1.36 cubic yards',
			'rows' => array(
				array(
					'label' => 'Volume',
					'value' => '36.7 cu ft',
				),
				array(
					'label' => 'Metric',
					'value' => '1.04 m3',
				),
				array(
					'label' => '80 lb bags',
					'value' => '62',
				),
				array(
					'label' => '60 lb bags',
					'value' => '82',
				),
				array(
					'label' => '40 lb bags',
					'value' => '123',
				),
			),
			'note' => 'Above roughly one cubic yard, ready-mix delivered by truck is usually cheaper and far less work than bags.',
		),
		'explainer' => array(
			array(
				'heading' => 'How concrete is measured',
				'body' => 'Concrete is sold by volume, so every calculation here comes down to length times width times depth, converted into cubic yards because that is the unit a ready-mix truck is billed in. The thickness is entered in inches and divided by twelve, which is where most hand calculations go wrong.',
				'formula' => 'Cubic yards = (L &times; W &times; T &divide; 12) &divide; 27',
			),
			array(
				'heading' => 'Bags or a truck',
				'body' => 'The crossover sits at roughly one cubic yard. Below that, bags are cheaper and you can mix at your own pace. Above it, bags become a punishing amount of lifting and most suppliers will deliver ready-mix, though almost all charge a short-load fee under their minimum. A cubic yard is about forty-five 80 lb bags, which is close to two tons of material carried by hand.',
			),
			array(
				'heading' => 'Why the waste allowance matters more here than elsewhere',
				'body' => 'Concrete cannot be topped up later. Once a pour starts setting, a second batch bonds badly and leaves a visible cold joint, so running short is far more expensive than ordering a little extra. Subgrade is never perfectly flat either, and the low spots drink more than the arithmetic suggests.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How many bags of concrete are in a cubic yard?',
				'a' => 'About forty-five 80 lb bags, sixty 60 lb bags, or ninety 40 lb bags, since a cubic yard is twenty-seven cubic feet and an 80 lb bag yields roughly 0.6 cubic feet of mixed concrete. That is also the point where bags stop being sensible, because forty-five bags is close to two tons to move by hand.',
			),
			array(
				'q' => 'How thick should a concrete slab be?',
				'a' => 'Four inches suits a patio, shed base or footpath. Six inches is the usual minimum for a driveway carrying cars, and heavier vehicles or poor ground call for more plus reinforcement. Local code and soil conditions decide it, so check before you order.',
			),
			array(
				'q' => 'Should I add rebar or mesh?',
				'a' => 'For anything load-bearing, yes. Reinforcement does not stop concrete cracking, it holds the crack closed so the slab keeps working. It does not change the volume you order, since the steel displaces very little.',
			),
		),
		'related' => array(
			'gravel-calculator',
			'cubic-yard-calculator',
			'square-footage-calculator',
		),
		'disclaimer' => 'An estimate for ordering. Confirm depths and reinforcement against local building code before you pour.',
	);
