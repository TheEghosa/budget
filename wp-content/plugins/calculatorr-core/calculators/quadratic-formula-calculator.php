<?php
/**
 * Quadratic Formula Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'quadratic-formula-calculator',
		'title' => 'Quadratic Formula Calculator',
		'category' => 'math',
		'description' => 'Solve any quadratic equation and see the discriminant and vertex.',
		'keyword' => 'Quadratic Formula Calculator',
		'h1' => 'Quadratic Formula Calculator',
		'meta_title' => 'Quadratic Formula Calculator - Roots and Vertex',
		'meta_description' => 'Free quadratic formula calculator. Enter a, b and c to get the real or complex roots, the discriminant, the vertex and the axis of symmetry.',
		'fields' => array(
			array(
				'id' => 'a',
				'label' => 'a',
				'type' => 'number',
				'default' => 1,
				'step' => 'any',
			),
			array(
				'id' => 'b',
				'label' => 'b',
				'type' => 'number',
				'default' => -3,
				'step' => 'any',
			),
			array(
				'id' => 'c',
				'label' => 'c',
				'type' => 'number',
				'default' => 2,
				'step' => 'any',
			),
		),
		'default_result' => array(
			'label' => 'Two real roots',
			'value' => 'x = 2 or 1',
			'rows' => array(
				array(
					'label' => 'Discriminant',
					'value' => '1',
				),
				array(
					'label' => 'Vertex',
					'value' => '(1.5, -0.25)',
				),
				array(
					'label' => 'Axis of symmetry',
					'value' => 'x = 1.5',
				),
			),
			'note' => 'A positive discriminant means the parabola crosses the x axis twice.',
		),
		'explainer' => array(
			array(
				'heading' => 'What the discriminant tells you before you solve',
				'body' => 'The part under the square root, b squared minus 4ac, decides everything. Positive means two real roots and the parabola crosses the x axis twice. Zero means one repeated root and it touches. Negative means no real roots at all and it never reaches the axis.',
				'formula' => 'x = (&minus;b &plusmn; &radic;(b&sup2; &minus; 4ac)) &divide; 2a',
			),
			array(
				'heading' => 'The vertex',
				'body' => 'The turning point sits at minus b over two a, which is the axis of symmetry and also the midpoint of the two roots when they exist. For any real problem modelled by a quadratic, that is usually the answer being looked for: the maximum height, the minimum cost, the optimal price.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What if a is zero?',
				'a' => 'Then the equation is linear rather than quadratic and has a single root at minus c over b. The calculator detects this and says so.',
			),
			array(
				'q' => 'What does a negative discriminant mean?',
				'a' => 'The parabola never crosses the x axis, so the roots are complex. That is a real answer rather than an error, and the calculator gives it in a plus bi form.',
			),
		),
		'related' => array(
			'square-root-calculator',
			'system-of-equations-calculator',
			'slope-calculator',
		),
		'disclaimer' => '',
	);
