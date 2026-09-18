<?php
/**
 * Triangle Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'triangle-calculator',
		'title' => 'Triangle Calculator',
		'category' => 'geometry',
		'description' => 'Solve any triangle from its three sides.',
		'keyword' => 'Triangle Calculator',
		'h1' => 'Triangle Calculator',
		'meta_title' => 'Triangle Calculator - Area, Angles and Type',
		'meta_description' => 'Free triangle calculator. Enter three sides for the area by Heron’s formula, all three angles, the perimeter and whether the triangle is even possible.',
		'fields' => array(
			array(
				'id' => 'a',
				'label' => 'Side a',
				'type' => 'number',
				'default' => 3,
			),
			array(
				'id' => 'b',
				'label' => 'Side b',
				'type' => 'number',
				'default' => 4,
			),
			array(
				'id' => 'c',
				'label' => 'Side c',
				'type' => 'number',
				'default' => 5,
			),
		),
		'default_result' => array(
			'label' => 'Right triangle area',
			'value' => '6',
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
					'label' => 'Perimeter',
					'value' => '12',
				),
				array(
					'label' => 'Semi-perimeter',
					'value' => '6',
				),
				array(
					'label' => 'Height on side a',
					'value' => '4',
				),
			),
			'note' => 'Area is found with Heron’s formula, which needs only the three sides and no angle at all.',
		),
		'explainer' => array(
			array(
				'heading' => 'Heron’s formula needs no angles',
				'body' => 'Most area formulas want a base and a perpendicular height, which is awkward to measure on an irregular triangle. Heron’s formula works from the three side lengths alone, which is exactly what you can measure with a tape.',
				'formula' => 'Area = √(s(s−a)(s−b)(s−c)), where s is half the perimeter',
			),
			array(
				'heading' => 'Not every three numbers make a triangle',
				'body' => 'Any two sides must add to more than the third, or the shape cannot close. Sides of 1, 2 and 10 describe nothing at all, and the calculator says so rather than returning a meaningless number.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How do I find the angles from three sides?',
				'a' => 'The law of cosines gives each one. The calculator applies it and reports all three, which must total 180.',
			),
			array(
				'q' => 'What makes a triangle isosceles?',
				'a' => 'Two sides of equal length, which also means two equal angles. All three equal makes it equilateral with three 60 degree angles.',
			),
		),
		'related' => array(
			'right-triangle-calculator',
			'pythagorean-theorem-calculator',
			'area-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
