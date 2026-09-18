<?php
/**
 * Derivative Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'derivative-calculator',
		'title' => 'Derivative Calculator',
		'category' => 'math',
		'description' => 'Differentiate a polynomial or simple function and evaluate the slope.',
		'keyword' => 'Derivative Calculator',
		'h1' => 'Derivative Calculator',
		'meta_title' => 'Derivative Calculator - Step-Free Polynomial Solver',
		'meta_description' => 'Free derivative calculator for polynomials and common functions. Enter an expression to get the derivative and the slope at any point you choose.',
		'fields' => array(
			array(
				'id' => 'expression',
				'label' => 'Expression in x',
				'type' => 'text',
				'default' => '3x^2+5x-7',
				'hint' => 'Powers, sin(x), cos(x), e^x, ln(x) and sqrt(x), added or subtracted.',
			),
			array(
				'id' => 'at',
				'label' => 'Evaluate slope at x =',
				'type' => 'number',
				'default' => 2,
				'step' => 'any',
			),
		),
		'default_result' => array(
			'label' => 'Derivative',
			'value' => 'f\'(x) = 6x + 5',
			'rows' => array(
				array(
					'label' => 'Original',
					'value' => 'f(x) = 3x^2 + 5x - 7',
				),
				array(
					'label' => 'Slope at x = 2',
					'value' => '17',
				),
				array(
					'label' => 'f(x) at that point',
					'value' => '15',
				),
			),
			'note' => 'Differentiated term by term with the power rule. Understood: powers such as 3x^2, plain terms such as 5x or 7, sin(x), cos(x), e^x, ln(x) and sqrt(x), added or subtracted. Products, quotients and nested functions are not supported yet.',
		),
		'explainer' => array(
			array(
				'heading' => 'The power rule',
				'body' => 'Every polynomial term differentiates the same way: multiply by the exponent and reduce the exponent by one. Constants vanish entirely, because a constant has no slope. That single rule handles the overwhelming majority of derivatives anyone needs.',
				'formula' => 'd/dx (ax&#8319;) = a &times; n &times; x&#8319;&#8315;&sup1;',
			),
			array(
				'heading' => 'What this tool does not do',
				'body' => 'It differentiates sums of terms and nothing more. The product, quotient and chain rules are not implemented, so a nested function like sin(2x) or a product like x&middot;e^x will be rejected rather than answered incorrectly. That refusal is deliberate: a calculus tool that is confidently wrong is worse than one that admits its limits.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What does the derivative actually mean?',
				'a' => 'The slope of the curve at a point, which is the instantaneous rate of change. On a distance graph it is speed. On a cost curve it is marginal cost.',
			),
			array(
				'q' => 'Why is the derivative of a constant zero?',
				'a' => 'Because a constant never changes, so its rate of change is nothing. A horizontal line has no slope.',
			),
		),
		'related' => array(
			'integral-calculator',
			'slope-calculator',
			'quadratic-formula-calculator',
		),
		'disclaimer' => '',
	);
