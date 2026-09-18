<?php
/**
 * System of Equations Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'system-of-equations-calculator',
		'title' => 'System of Equations Calculator',
		'category' => 'math',
		'description' => 'Solve two linear equations in two unknowns.',
		'keyword' => 'System of Equations Calculator',
		'h1' => 'System of Equations Calculator',
		'meta_title' => 'System of Equations Calculator - Two Unknowns',
		'meta_description' => 'Free system of equations calculator. Solve two linear equations for x and y, with the determinant shown and parallel or identical lines identified.',
		'fields' => array(
			array(
				'id' => 'a1',
				'label' => 'Equation 1: a',
				'type' => 'number',
				'default' => 2,
				'step' => 'any',
			),
			array(
				'id' => 'b1',
				'label' => 'Equation 1: b',
				'type' => 'number',
				'default' => 1,
				'step' => 'any',
			),
			array(
				'id' => 'c1',
				'label' => 'Equation 1: c',
				'type' => 'number',
				'default' => 5,
				'step' => 'any',
			),
			array(
				'id' => 'a2',
				'label' => 'Equation 2: a',
				'type' => 'number',
				'default' => 1,
				'step' => 'any',
			),
			array(
				'id' => 'b2',
				'label' => 'Equation 2: b',
				'type' => 'number',
				'default' => -1,
				'step' => 'any',
			),
			array(
				'id' => 'c2',
				'label' => 'Equation 2: c',
				'type' => 'number',
				'default' => 1,
				'step' => 'any',
			),
		),
		'default_result' => array(
			'label' => 'Solution',
			'value' => 'x = 2, y = 1',
			'rows' => array(
				array(
					'label' => 'x',
					'value' => '2',
				),
				array(
					'label' => 'y',
					'value' => '1',
				),
				array(
					'label' => 'Determinant',
					'value' => '-3',
				),
			),
			'note' => 'Solved by Cramer’s rule. The determinant is non-zero, so the two lines cross at exactly one point.',
		),
		'explainer' => array(
			array(
				'heading' => 'Two lines, one crossing',
				'body' => 'Each equation describes a line, and solving the system finds where they cross. Cramer’s rule does it in one step using determinants, which is faster than substitution and never involves rearranging anything.',
				'formula' => 'x = (c&#8321;b&#8322; &minus; c&#8322;b&#8321;) &divide; (a&#8321;b&#8322; &minus; a&#8322;b&#8321;)',
			),
			array(
				'heading' => 'When there is no single answer',
				'body' => 'A determinant of zero means the lines are parallel. If they are also identical, every point on the line solves the system and there are infinitely many answers. If they are merely parallel, there is no answer at all. The calculator tells you which.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What form should my equations be in?',
				'a' => 'Standard form, ax + by = c. Rearrange first if yours are written as y equals something.',
			),
			array(
				'q' => 'What does a determinant of zero mean?',
				'a' => 'The two equations are not independent. Either they describe the same line, or they describe parallel lines that never meet.',
			),
		),
		'related' => array(
			'quadratic-formula-calculator',
			'slope-calculator',
			'proportion-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
