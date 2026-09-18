<?php
/**
 * Square Footage Calculator.
 *
 * Part of the construction group, whose calculators share one shape:
 * a volume or area worked out from the measurements, converted into the
 * unit the supplier actually sells in, with an allowance on top.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'square-footage-calculator',
		'title' => 'Square Footage Calculator',
		'category' => 'home-diy',
		'description' => 'Measure the area of a room or space in square feet, metres, yards or acres.',
		'keyword' => 'Square Footage Calculator',
		'h1' => 'Square Footage Calculator',
		'meta_title' => 'Square Footage Calculator - Room & Land Area',
		'meta_description' => 'Free square footage calculator for rectangles, circles and triangles. Get the area in square feet, square metres, square yards and acres from your measurements.',
		'fields' => array(
			array(
				'id' => 'shape',
				'label' => 'Shape of the space',
				'type' => 'segmented',
				'options' => array(
					'rectangle' => 'Rectangle',
					'circle' => 'Circle',
					'triangle' => 'Triangle',
				),
				'default' => 'rectangle',
			),
			array(
				'id' => 'length',
				'label' => 'Length',
				'type' => 'number',
				'suffix' => 'ft',
				'default' => 12,
				'min' => 0,
				'show_when' => array(
					'shape' => 'rectangle',
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
					'shape' => 'rectangle',
				),
			),
			array(
				'id' => 'diameter',
				'label' => 'Diameter',
				'type' => 'number',
				'suffix' => 'ft',
				'default' => 12,
				'min' => 0,
				'show_when' => array(
					'shape' => 'circle',
				),
			),
			array(
				'id' => 'base',
				'label' => 'Base',
				'type' => 'number',
				'suffix' => 'ft',
				'default' => 12,
				'min' => 0,
				'show_when' => array(
					'shape' => 'triangle',
				),
			),
			array(
				'id' => 'heightFt',
				'label' => 'Height',
				'type' => 'number',
				'suffix' => 'ft',
				'default' => 8,
				'min' => 0,
				'show_when' => array(
					'shape' => 'triangle',
				),
			),
			array(
				'id' => 'rooms',
				'label' => 'How many identical areas',
				'type' => 'number',
				'default' => 1,
				'min' => 1,
				'step' => 1,
				'hint' => 'Add up several matching rooms in one go.',
			),
		),
		'default_result' => array(
			'label' => 'Total area',
			'value' => '120 sq ft',
			'rows' => array(
				array(
					'label' => 'Square metres',
					'value' => '11.15 m2',
				),
				array(
					'label' => 'Square yards',
					'value' => '13.33 sq yd',
				),
				array(
					'label' => 'Acres',
					'value' => '0.003 ac',
				),
			),
			'note' => 'Measure at the widest points and treat alcoves as separate rectangles, because rounding a room to one rectangle is the usual reason an order comes up short.',
		),
		'explainer' => array(
			array(
				'heading' => 'Measuring a room that is not a rectangle',
				'body' => 'Almost no real room is one clean rectangle. The reliable method is to break the floor into rectangles that meet at the walls, work each one out separately, and add them together. Bay windows, chimney breasts and alcoves each become their own small rectangle, added or subtracted as appropriate.',
				'formula' => 'Area = Length &times; Width',
			),
			array(
				'heading' => 'Which unit you actually need',
				'body' => 'Flooring is usually sold by the square foot in the United States and by the square metre almost everywhere else. Carpet is often quoted in square yards, which is nine square feet, and land is measured in acres at 43,560 square feet. All four are shown so you can order in whichever unit the supplier quotes.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How do I calculate square footage of a house?',
				'a' => 'Measure each room separately and add the results, using exterior wall dimensions if you want gross floor area and interior ones for usable space. The two differ by several per cent, which matters when a figure is going into a listing or a permit application.',
			),
			array(
				'q' => 'How many square feet are in a square metre?',
				'a' => 'About 10.764. To go the other way, multiply square feet by 0.0929. Both conversions are shown automatically so you do not have to reach for a second tool.',
			),
			array(
				'q' => 'Should I include closets and hallways?',
				'a' => 'For flooring, yes, because you are covering them. For a property listing, the local convention decides it and some markets exclude unheated space entirely, so check what your market expects before publishing a number.',
			),
		),
		'related' => array(
			'tile-calculator',
			'paint-calculator',
			'area-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
