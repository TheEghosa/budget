<?php
/**
 * Right Triangle Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'right-triangle-calculator',
		'title' => 'Right Triangle Calculator',
		'category' => 'geometry',
		'description' => 'Solve a right triangle from its two legs.',
		'keyword' => 'Right Triangle Calculator',
		'h1' => 'Right Triangle Calculator',
		'meta_title' => 'Right Triangle Calculator - Sides, Angles and Area',
		'meta_description' => 'Free right triangle calculator. Enter the two legs for the hypotenuse, both acute angles, the area, the perimeter and the inradius.',
		'fields' => array(
			array(
				'id' => 'a',
				'label' => 'Leg a',
				'type' => 'number',
				'default' => 3,
			),
			array(
				'id' => 'b',
				'label' => 'Leg b',
				'type' => 'number',
				'default' => 4,
			),
		),
		'default_result' => array(
			'label' => 'Hypotenuse',
			'value' => '5',
			'rows' => array(
				array(
					'label' => 'Angle A',
					'value' => '36.87°',
				),
				array(
					'label' => 'Angle B',
					'value' => '53.13°',
				),
				array(
					'label' => 'Angle C',
					'value' => '90°',
				),
				array(
					'label' => 'Area',
					'value' => '6',
				),
				array(
					'label' => 'Perimeter',
					'value' => '12',
				),
				array(
					'label' => 'Inradius',
					'value' => '1',
				),
			),
			'note' => 'The three angles always total 180 degrees, and one of them is fixed at 90, so knowing one of the other two gives you the third for free.',
		),
		'explainer' => array(
			array(
				'heading' => 'Angles come free',
				'body' => 'Once you have two sides, the angles follow from basic trigonometry. The angle opposite leg a is the arctangent of a over b, and because the three angles must total 180 with one fixed at 90, the other two always add to 90 themselves.',
				'formula' => 'tan A = a ÷ b',
			),
			array(
				'heading' => 'Where this gets used',
				'body' => 'Roof pitch, staircase stringers, ramp gradients, television screen sizes and the diagonal bracing on almost any frame. Any time something slopes, there is a right triangle behind it and two of its measurements are usually easy to take.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How do I find the angle of a roof?',
				'a' => 'The rise is one leg and the run the other. The angle opposite the rise is the roof pitch in degrees, which the calculator gives directly.',
			),
			array(
				'q' => 'What is the inradius for?',
				'a' => 'It is the radius of the largest circle that fits inside the triangle, which matters in machining, tiling and any fit problem where something round has to sit in a corner.',
			),
		),
		'related' => array(
			'pythagorean-theorem-calculator',
			'triangle-calculator',
			'stair-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
