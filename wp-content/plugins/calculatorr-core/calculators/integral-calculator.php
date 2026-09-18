<?php
/**
 * Integral Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'integral-calculator',
		'title' => 'Integral Calculator',
		'category' => 'math',
		'description' => 'Integrate a polynomial or simple function, definite or indefinite.',
		'keyword' => 'Integral Calculator',
		'h1' => 'Integral Calculator',
		'meta_title' => 'Integral Calculator - Indefinite and Definite',
		'meta_description' => 'Free integral calculator for polynomials and common functions. Get the antiderivative with its constant, plus the definite integral between any two limits.',
		'fields' => array(
			array(
				'id' => 'expression',
				'label' => 'Expression in x',
				'type' => 'text',
				'default' => 'x^2',
				'hint' => 'Powers, sin(x), cos(x), e^x, ln(x) and sqrt(x), added or subtracted.',
			),
			array(
				'id' => 'from',
				'label' => 'From x =',
				'type' => 'number',
				'default' => 0,
				'step' => 'any',
			),
			array(
				'id' => 'to',
				'label' => 'To x =',
				'type' => 'number',
				'default' => 3,
				'step' => 'any',
			),
		),
		'default_result' => array(
			'label' => 'Indefinite integral',
			'value' => '∫ f(x) dx = 0.333333x^3 + C',
			'rows' => array(
				array(
					'label' => 'Original',
					'value' => 'f(x) = x^2',
				),
				array(
					'label' => 'Definite from 0 to 3',
					'value' => '9',
				),
			),
			'note' => 'Integrated term by term with the reverse power rule. The constant of integration is written as C because an indefinite integral describes a family of curves rather than one. Understood: powers such as 3x^2, plain terms such as 5x or 7, sin(x), cos(x), e^x, ln(x) and sqrt(x), added or subtracted. Products, quotients and nested functions are not supported yet.',
		),
		'explainer' => array(
			array(
				'heading' => 'The reverse power rule',
				'body' => 'Integration undoes differentiation, so the power rule runs backwards: raise the exponent by one and divide by the new exponent. The exception is x to the minus one, which integrates to the natural log rather than to x to the zero, because dividing by zero is not allowed.',
				'formula' => '&int; ax&#8319; dx = a &divide; (n+1) &times; x&#8319;&#8314;&sup1; + C',
			),
			array(
				'heading' => 'Why there is always a plus C',
				'body' => 'Any constant differentiates to zero, so infinitely many functions share the same derivative. An indefinite integral therefore describes a family of curves rather than one, and the C stands for the whole family. A definite integral cancels the constant out, which is why it returns a single number.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What is the difference between definite and indefinite?',
				'a' => 'An indefinite integral returns a function plus a constant. A definite one evaluates that function at two limits and subtracts, returning a number, which is the signed area under the curve between them.',
			),
			array(
				'q' => 'Why does area come out negative sometimes?',
				'a' => 'Because area below the x axis counts as negative in a definite integral. If you want geometric area regardless of sign, split the integral at each crossing point.',
			),
		),
		'related' => array(
			'derivative-calculator',
			'area-calculator',
			'quadratic-formula-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
