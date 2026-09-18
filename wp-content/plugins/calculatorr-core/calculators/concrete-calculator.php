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
				'heading' => 'How to calculate how much concrete you need',
				'body' => 'Concrete is sold by volume, so every calculation comes down to length times width times depth. The one step that catches people out is the depth, which is measured in inches while everything else is in feet, so it has to be divided by twelve before the three are multiplied together. That gives cubic feet, and dividing by twenty-seven gives cubic yards, which is the unit a ready-mix truck is billed in.',
				'formula' => 'Cubic yards = (Length ft &times; Width ft &times; Thickness in &divide; 12) &divide; 27',
				'steps' => array(
					'Measure the length and width of the pour in feet.',
					'Decide the thickness in inches. Four is standard for a patio or walkway, six for a driveway carrying vehicles.',
					'Divide the thickness by twelve to convert it into feet.',
					'Multiply the three figures together for the volume in cubic feet.',
					'Divide by twenty-seven to get cubic yards.',
					'Add ten per cent for spillage and uneven subgrade, which is what the waste field above does for you.',
				),
				'example' => 'A patio measuring 12 feet by 10 feet at 4 inches thick is 12 &times; 10 &times; 0.333, which comes to 40 cubic feet, or 1.48 cubic yards. Adding ten per cent for waste brings it to 1.63 cubic yards. That sits just above the point where bags stop being sensible, so this pour would be ordered as ready-mix rather than carried in by hand.',
			),
			array(
				'heading' => 'How much area does a cubic yard of concrete cover?',
				'body' => 'One cubic yard is twenty-seven cubic feet, so the area it covers depends entirely on how thick you pour it. Halving the thickness doubles the coverage. The table below is worth a glance before you measure anything, because it tells you immediately what scale of pour you are dealing with and whether this is a bag job or a truck job.',
				'table' => array(
					'caption' => 'Area covered by one cubic yard of concrete at common slab thicknesses.',
					'head' => array(
						'Thickness',
						'Area covered',
						'Typical use',
					),
					'rows' => array(
						array(
							'2 inches',
							'162 sq ft',
							'Thin overlay, not structural',
						),
						array(
							'3 inches',
							'108 sq ft',
							'Footpath or shed base',
						),
						array(
							'4 inches',
							'81 sq ft',
							'Patio, walkway, standard slab',
						),
						array(
							'5 inches',
							'65 sq ft',
							'Light vehicle parking',
						),
						array(
							'6 inches',
							'54 sq ft',
							'Driveway or garage floor',
						),
						array(
							'8 inches',
							'41 sq ft',
							'Heavy vehicle, thickened edge',
						),
						array(
							'12 inches',
							'27 sq ft',
							'Footings and piers',
						),
					),
				),
			),
			array(
				'heading' => 'Bags or a ready-mix truck?',
				'body' => 'The crossover sits at roughly one cubic yard. Below that, bags are cheaper and you can mix at your own pace. Above it, bags become a punishing amount of work: a cubic yard is about forty-five 80 lb bags, close to two tons of material to carry, open and mix by hand, and it all has to happen fast enough that the first batch has not begun setting before the last one goes in. Most suppliers will deliver less than a full truck, though nearly all charge a short-load fee below their minimum.',
				'table' => array(
					'caption' => 'Bags needed per cubic yard, based on the yield printed on the bag.',
					'head' => array(
						'Bag size',
						'Yield per bag',
						'Bags per cubic yard',
					),
					'rows' => array(
						array(
							'80 lb',
							'0.60 cu ft',
							'45',
						),
						array(
							'60 lb',
							'0.45 cu ft',
							'60',
						),
						array(
							'40 lb',
							'0.30 cu ft',
							'90',
						),
					),
				),
			),
			array(
				'heading' => 'Why the waste allowance matters more here than anywhere else',
				'body' => 'Concrete cannot be topped up later. Once a pour begins setting, a second batch bonds badly against it and leaves a cold joint that is both visible and a weak line through the slab, so running short is far more expensive than ordering slightly too much. Subgrade is never perfectly flat either, and the low spots drink more than the arithmetic suggests. Ten per cent is the normal allowance on prepared ground, and fifteen is sensible on rough ground or in a hand-dug footing where the sides are not true.',
			),
			array(
				'heading' => 'What this calculator deliberately leaves out',
				'body' => 'It gives you volume, which is what you order. It does not size reinforcement, specify a mix strength, or account for a thickened edge, a step-down or a slope, all of which add volume that has to be measured and added separately. Reinforcement barely changes the volume, since steel displaces very little, but it does decide whether the slab survives, so it is not optional on anything load-bearing.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How many bags of concrete are in a cubic yard?',
				'a' => 'About forty-five 80 lb bags, sixty 60 lb bags, or ninety 40 lb bags. A cubic yard is twenty-seven cubic feet and an 80 lb bag yields roughly 0.6 cubic feet of mixed concrete, so twenty-seven divided by 0.6 gives forty-five. That is also the point where bags stop making sense, because forty-five bags is close to two tons to move by hand.',
			),
			array(
				'q' => 'How much does a yard of concrete cover?',
				'a' => 'Eighty-one square feet at four inches thick, or fifty-four square feet at six inches. Coverage is simply twenty-seven cubic feet divided by the thickness in feet, which is why halving the thickness doubles the area covered.',
			),
			array(
				'q' => 'How thick should a concrete slab be?',
				'a' => 'Four inches suits a patio, shed base or footpath. Six inches is the usual minimum for a driveway carrying cars, and heavier vehicles or poor ground call for more plus reinforcement. Local building code and your soil conditions decide it, so confirm before ordering rather than after.',
			),
			array(
				'q' => 'How much concrete is in a truck?',
				'a' => 'A standard ready-mix truck holds eight to ten cubic yards, though most suppliers will deliver considerably less. Below their minimum load, commonly one to three yards, expect a short-load fee that can be large relative to the cost of the concrete itself.',
			),
			array(
				'q' => 'How much does a yard of concrete weigh?',
				'a' => 'Around four thousand pounds, which is two US tons. That matters if you are moving it in a trailer or a wheelbarrow, and it is the reason the crossover from bags to a truck arrives sooner than most people expect.',
			),
			array(
				'q' => 'Do I need rebar or wire mesh?',
				'a' => 'For anything load-bearing, yes. Reinforcement does not stop concrete cracking, because concrete always cracks. It holds the crack closed so the slab keeps working as a single piece. It adds almost nothing to the volume you order, since steel displaces very little.',
			),
		),
		'related' => array(
			'gravel-calculator',
			'cubic-yard-calculator',
			'square-footage-calculator',
		),
		'disclaimer' => 'An estimate for ordering. Confirm depths and reinforcement against local building code before you pour.',
		'sources' => array(
			array(
				'name' => 'Portland Cement Association',
				'url' => 'https://www.cement.org/',
				'detail' => 'for mix proportions, curing and the properties of concrete.',
			),
			array(
				'name' => 'Manufacturer bag yield data',
				'detail' => 'Quikrete and Sakrete both publish 0.60, 0.45 and 0.30 cubic feet for 80, 60 and 40 lb bags.',
			),
			array(
				'name' => 'ACI 318, Building Code Requirements for Structural Concrete',
				'detail' => 'for anything structural. Your local code adopts a version of it and that takes precedence over any figure here.',
			),
		),
	);
