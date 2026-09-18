<?php
/**
 * Area Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'area-calculator',
		'title' => 'Area Calculator',
		'category' => 'geometry',
		'description' => 'Find the area of a rectangle, circle, triangle, trapezoid or ellipse.',
		'keyword' => 'Area Calculator',
		'h1' => 'Area Calculator',
		'meta_title' => 'Area Calculator - Rectangle, Circle, Triangle and More',
		'meta_description' => 'Free area calculator for rectangles, circles, triangles, trapezoids and ellipses. Enter your measurements and get the area in whatever unit you used.',
		'fields' => array(
			array(
				'id' => 'shape',
				'label' => 'Shape',
				'type' => 'segmented',
				'options' => array(
					'rectangle' => 'Rectangle',
					'circle' => 'Circle',
					'triangle' => 'Triangle',
					'trapezoid' => 'Trapezoid',
					'ellipse' => 'Ellipse',
				),
				'default' => 'rectangle',
			),
			array(
				'id' => 'length',
				'label' => 'Length',
				'type' => 'number',
				'default' => 12,
				'show_when' => array(
					'shape' => 'rectangle',
				),
			),
			array(
				'id' => 'width',
				'label' => 'Width',
				'type' => 'number',
				'default' => 10,
				'show_when' => array(
					'shape' => 'rectangle',
				),
			),
			array(
				'id' => 'radius',
				'label' => 'Radius',
				'type' => 'number',
				'default' => 6,
				'show_when' => array(
					'shape' => array(
						'circle',
						'ellipse',
					),
				),
			),
			array(
				'id' => 'radius2',
				'label' => 'Second radius',
				'type' => 'number',
				'default' => 4,
				'show_when' => array(
					'shape' => 'ellipse',
				),
			),
			array(
				'id' => 'base',
				'label' => 'Base',
				'type' => 'number',
				'default' => 12,
				'show_when' => array(
					'shape' => array(
						'triangle',
						'trapezoid',
					),
				),
			),
			array(
				'id' => 'base2',
				'label' => 'Second base',
				'type' => 'number',
				'default' => 8,
				'show_when' => array(
					'shape' => 'trapezoid',
				),
			),
			array(
				'id' => 'heightVal',
				'label' => 'Height',
				'type' => 'number',
				'default' => 8,
				'show_when' => array(
					'shape' => array(
						'triangle',
						'trapezoid',
					),
				),
			),
		),
		'default_result' => array(
			'label' => 'Rectangle area',
			'value' => '120 sq units',
			'rows' => array(
				array(
					'label' => 'If units are feet',
					'value' => '120 sq ft',
				),
				array(
					'label' => 'If units are metres',
					'value' => '120 m2',
				),
				array(
					'label' => 'Square inches to square feet',
					'value' => '0.8333',
				),
			),
			'note' => 'Area is unitless here: whatever unit you measure in, the answer is in that unit squared. Mixing feet and inches in the same calculation is the usual source of a wrong answer.',
		),
		'explainer' => array(
			array(
				'heading' => 'Area is always in squared units',
				'body' => 'Whatever unit you measure in, the answer comes back in that unit squared. Feet in, square feet out. The most common mistake is mixing units within one shape, measuring length in feet and width in inches, which produces a number that means nothing.',
				'formula' => 'Rectangle = L × W, Circle = πr², Triangle = ½ bh',
			),
			array(
				'heading' => 'Breaking awkward shapes apart',
				'body' => 'Almost no real room or plot is one clean shape. Split it into rectangles and triangles that meet at the edges, work each one out separately and add them. Subtract anything that is a hole rather than a surface.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How do I find the area of an irregular shape?',
				'a' => 'Divide it into rectangles and triangles, calculate each and add the results. Subtract any cut-outs.',
			),
			array(
				'q' => 'What is the area of a circle with a 10 foot diameter?',
				'a' => 'About 78.5 square feet. Halve the diameter to get the radius first, since squaring the diameter by mistake gives four times the right answer.',
			),
		),
		'related' => array(
			'square-footage-calculator',
			'circumference-calculator',
			'volume-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
