<?php
/**
 * Slope Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'slope-calculator',
		'title' => 'Slope Calculator',
		'category' => 'math',
		'description' => 'Find the slope, equation, angle and distance between two points.',
		'keyword' => 'Slope Calculator',
		'h1' => 'Slope Calculator',
		'meta_title' => 'Slope Calculator - Gradient Between Two Points',
		'meta_description' => 'Free slope calculator. Enter two points for the gradient, the line equation, the angle in degrees and the distance between the points.',
		'fields' => array(
			array(
				'id' => 'x1',
				'label' => 'x₁',
				'type' => 'number',
				'default' => 1,
				'step' => 'any',
			),
			array(
				'id' => 'y1',
				'label' => 'y₁',
				'type' => 'number',
				'default' => 2,
				'step' => 'any',
			),
			array(
				'id' => 'x2',
				'label' => 'x₂',
				'type' => 'number',
				'default' => 5,
				'step' => 'any',
			),
			array(
				'id' => 'y2',
				'label' => 'y₂',
				'type' => 'number',
				'default' => 10,
				'step' => 'any',
			),
		),
		'default_result' => array(
			'label' => 'Slope',
			'value' => '2',
			'rows' => array(
				array(
					'label' => 'Equation',
					'value' => 'y = 2x + 0',
				),
				array(
					'label' => 'Rise over run',
					'value' => '8 / 4',
				),
				array(
					'label' => 'Angle',
					'value' => '63.435°',
				),
				array(
					'label' => 'Distance between points',
					'value' => '8.944272',
				),
			),
			'note' => '',
		),
		'explainer' => array(
			array(
				'heading' => 'Rise over run',
				'body' => 'Slope is how much the line climbs for each step across. A slope of two means two up for every one along. A negative slope falls, and a slope of zero is flat. It is the same idea as the gradient of a road or a wheelchair ramp, just expressed as a ratio rather than a percentage.',
				'formula' => 'm = (y&#8322; &minus; y&#8321;) &divide; (x&#8322; &minus; x&#8321;)',
			),
			array(
				'heading' => 'The vertical line problem',
				'body' => 'When both points share an x value the run is zero, and nothing can be divided by zero, so the slope is undefined rather than infinite. That is why a vertical line cannot be written as y equals mx plus b at all.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What does a negative slope mean?',
				'a' => 'The line falls from left to right. Every step across takes you down rather than up.',
			),
			array(
				'q' => 'How do I find the equation of the line?',
				'a' => 'Once you have the slope, substitute either point into y = mx + b and solve for b. The calculator does this and shows the finished equation.',
			),
		),
		'related' => array(
			'distance-calculator',
			'pythagorean-theorem-calculator',
			'derivative-calculator',
		),
		'disclaimer' => '',
	);
