<?php
/**
 * Pythagorean Theorem Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'pythagorean-theorem-calculator',
		'title' => 'Pythagorean Theorem Calculator',
		'category' => 'geometry',
		'description' => 'Solve for any side of a right triangle.',
		'keyword' => 'Pythagorean Theorem Calculator',
		'h1' => 'Pythagorean Theorem Calculator',
		'meta_title' => 'Pythagorean Theorem Calculator - Solve for Any Side',
		'meta_description' => 'Free Pythagorean theorem calculator. Solve for the hypotenuse or either leg of a right triangle, with the area, perimeter and angles included.',
		'fields' => array(
			array(
				'id' => 'solve',
				'label' => 'Solve for',
				'type' => 'segmented',
				'options' => array(
					'c' => 'Hypotenuse (c)',
					'a' => 'Leg a',
					'b' => 'Leg b',
				),
				'default' => 'c',
			),
			array(
				'id' => 'a',
				'label' => 'Leg a',
				'type' => 'number',
				'default' => 3,
				'show_when' => array(
					'solve' => array(
						'c',
						'b',
					),
				),
			),
			array(
				'id' => 'b',
				'label' => 'Leg b',
				'type' => 'number',
				'default' => 4,
				'show_when' => array(
					'solve' => array(
						'c',
						'a',
					),
				),
			),
			array(
				'id' => 'c',
				'label' => 'Hypotenuse c',
				'type' => 'number',
				'default' => 5,
				'show_when' => array(
					'solve' => array(
						'a',
						'b',
					),
				),
			),
		),
		'default_result' => array(
			'label' => 'Side c',
			'value' => '5',
			'rows' => array(
				array(
					'label' => 'From',
					'value' => 'legs 3 and 4',
				),
				array(
					'label' => 'Area of the triangle',
					'value' => '6',
				),
				array(
					'label' => 'Perimeter',
					'value' => '12',
				),
				array(
					'label' => 'Angle opposite a',
					'value' => '36.87°',
				),
			),
			'note' => 'Works only for right triangles. For any other triangle, the law of cosines is the tool.',
		),
		'explainer' => array(
			array(
				'heading' => 'Squaring on the sides, not the sides themselves',
				'body' => 'The theorem says the squares of the two legs add to the square of the hypotenuse, which is a statement about areas rather than lengths. That is why you cannot simply add three and four to get five: three squared plus four squared is twenty-five, and the root of that is five.',
				'formula' => 'a² + b² = c²',
			),
			array(
				'heading' => 'Checking a corner is square',
				'body' => 'Measure three feet along one edge and four along the other. If the diagonal between those marks is exactly five, the corner is a true right angle. Builders have used this 3-4-5 check for thousands of years and it still beats any tool on site.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'Does this work on any triangle?',
				'a' => 'No, only right triangles. For any other triangle the law of cosines is the general form, and the triangle calculator handles it.',
			),
			array(
				'q' => 'What are Pythagorean triples?',
				'a' => 'Whole number sets that satisfy the theorem exactly, such as 3-4-5, 5-12-13 and 8-15-17. They are useful because they let you check square corners without a calculator.',
			),
		),
		'related' => array(
			'right-triangle-calculator',
			'triangle-calculator',
			'distance-calculator',
		),
		'disclaimer' => '',
	);
