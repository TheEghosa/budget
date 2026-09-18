<?php
/**
 * Paint Calculator.
 *
 * Part of the construction group, whose calculators share one shape:
 * a volume or area worked out from the measurements, converted into the
 * unit the supplier actually sells in, with an allowance on top.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'paint-calculator',
		'title' => 'Paint Calculator',
		'category' => 'home-diy',
		'description' => 'Work out how much paint a room needs, allowing for doors, windows and coats.',
		'keyword' => 'Paint Calculator',
		'h1' => 'Paint Calculator',
		'meta_title' => 'Paint Calculator - How Many Gallons for a Room',
		'meta_description' => 'Free paint calculator. Enter room size, wall height, coats and openings to get the gallons to buy, the exact requirement and the paintable area in square feet.',
		'fields' => array(
			array(
				'id' => 'length',
				'label' => 'Room length',
				'type' => 'number',
				'suffix' => 'ft',
				'default' => 12,
				'min' => 0,
			),
			array(
				'id' => 'width',
				'label' => 'Room width',
				'type' => 'number',
				'suffix' => 'ft',
				'default' => 10,
				'min' => 0,
			),
			array(
				'id' => 'height',
				'label' => 'Wall height',
				'type' => 'number',
				'suffix' => 'ft',
				'default' => 8,
				'min' => 0,
			),
			array(
				'id' => 'doors',
				'label' => 'Doors',
				'type' => 'number',
				'default' => 1,
				'min' => 0,
				'step' => 1,
				'hint' => 'Counted at 21 sq ft each.',
			),
			array(
				'id' => 'windows',
				'label' => 'Windows',
				'type' => 'number',
				'default' => 2,
				'min' => 0,
				'step' => 1,
				'hint' => 'Counted at 15 sq ft each.',
			),
			array(
				'id' => 'coats',
				'label' => 'Coats',
				'type' => 'segmented',
				'options' => array(
					'1' => '1 coat',
					'2' => '2 coats',
					'3' => '3 coats',
				),
				'default' => '2',
			),
			array(
				'id' => 'ceiling',
				'label' => 'Painting the ceiling too',
				'type' => 'segmented',
				'options' => array(
					'no' => 'No',
					'yes' => 'Yes',
				),
				'default' => 'no',
			),
			array(
				'id' => 'coverage',
				'label' => 'Coverage per gallon',
				'type' => 'number',
				'suffix' => 'sq ft',
				'default' => 350,
				'min' => 1,
				'hint' => '350 is typical. The tin will state its own figure.',
			),
		),
		'default_result' => array(
			'label' => 'Paint to buy',
			'value' => '2 gallons',
			'rows' => array(
				array(
					'label' => 'Exact requirement',
					'value' => '1.72 gal',
				),
				array(
					'label' => 'Paintable area',
					'value' => '301 sq ft',
				),
				array(
					'label' => 'Wall area before openings',
					'value' => '352 sq ft',
				),
				array(
					'label' => 'Openings deducted',
					'value' => '51 sq ft',
				),
				array(
					'label' => 'Litres',
					'value' => '6.5',
				),
			),
			'note' => 'Coverage is rounded up to whole cans because paint is not sold by the fraction. A bare, porous or sharply darker wall drinks more, so budget an extra coat on a colour change.',
		),
		'explainer' => array(
			array(
				'heading' => 'Two coats is the realistic default',
				'body' => 'One coat covers only when you are repainting the same colour in the same sheen with a quality paint. Any colour change, any move from flat to satin, and any bare or patched wall needs two, and a dramatic change such as white over deep red needs a primer plus two. Buying for one coat and discovering you need two is the most common way a room ends up with two dye lots on the same wall.',
				'formula' => 'Gallons = (Paintable area &times; Coats) &divide; Coverage per gallon',
			),
			array(
				'heading' => 'Why openings are deducted but trim is not',
				'body' => 'A door is about 21 square feet of wall you do not have to paint, and a window about 15, so deducting them meaningfully reduces a small room. Trim, skirting and architrave are not deducted because they usually get painted too, just in a different product, and their area is small enough to sit inside the rounding to whole cans.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How much paint do I need for a 12x12 room?',
				'a' => 'With eight foot walls, one door and two windows, about 1.8 gallons for two coats, so two gallons. Add roughly another half gallon if the ceiling is being painted in the same product.',
			),
			array(
				'q' => 'Do I need primer?',
				'a' => 'Over bare drywall, patched areas, stains or a dramatic colour change, yes. Primer is not just cheaper paint, it seals porous surfaces so the topcoat sits on the surface rather than soaking in, which is what makes two coats look like two coats.',
			),
			array(
				'q' => 'Is it better to buy one big can or several small?',
				'a' => 'One larger can, and mix it before you start if you have more than one. Paint varies slightly between tins even within a batch, and boxing them together into a single container eliminates the faint edge that otherwise shows where one can ran out.',
			),
		),
		'related' => array(
			'square-footage-calculator',
			'tile-calculator',
		),
		'disclaimer' => '',
	);
