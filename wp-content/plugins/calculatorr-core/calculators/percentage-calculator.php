<?php
/**
 * Percentage Calculator.
 *
 * First in the build order because it has the highest raw search volume on the
 * roadmap and the simplest possible logic, which makes it the honest test of
 * whether the shared template works before anything harder is attempted.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'slug'        => 'percentage-calculator',
	'title'       => 'Percentage Calculator',
	'category'    => 'math',
	'description' => 'Work out a percentage of a number, what one number is as a percentage of another, or the percentage change between two figures.',
	'fields'      => array(
		array(
			'id'      => 'mode',
			'label'   => 'What do you want to work out?',
			'type'    => 'segmented',
			'options' => array(
				'of'     => '% of a number',
				'is'     => 'X is what % of Y',
				'change' => '% change',
			),
			'default' => 'of',
		),
		array(
			'id'      => 'a',
			'label'   => 'First number',
			'type'    => 'number',
			'default' => 25,
			'hint'    => 'The percentage, the part, or the starting value depending on the mode above.',
		),
		array(
			'id'      => 'b',
			'label'   => 'Second number',
			'type'    => 'number',
			'default' => 200,
			'hint'    => 'The total, the whole, or the ending value.',
		),
	),
	'default_result' => array(
		'label' => '25% of 200',
		'value' => '50',
		'rows'  => array(
			array( 'label' => 'Percentage', 'value' => '25%' ),
			array( 'label' => 'Of', 'value' => '200' ),
			array( 'label' => 'Remainder', 'value' => '150' ),
		),
	),
	'explainer'   => array(
		array(
			'heading' => 'The three questions people mean by "percentage"',
			'body'    => 'Almost every percentage problem is one of three, and picking the wrong one is the usual reason an answer looks strange. Finding a percentage of a number is the tip and discount case, where you already know the rate and want the amount. Finding what one number is as a percentage of another is the marks and share case, where you have two figures and want the ratio between them. Percentage change is the growth case, and it is the only one of the three that has a direction, since the same absolute movement is a different percentage depending on which end you measure from.',
			'formula' => 'Part = (Percentage &divide; 100) &times; Whole',
		),
		array(
			'heading' => 'Why an increase and a decrease of the same percentage do not cancel out',
			'body'    => 'Take 100, add 20 per cent to reach 120, then take 20 per cent off and you land on 96 rather than back where you started. The second percentage is calculated against the larger number, so it removes more than the first one added. This is the mechanism behind stacked discounts and behind investment returns that look better in the headline than in the account, and it is worth remembering whenever two percentages are applied one after the other.',
		),
	),
	'faqs'        => array(
		array(
			'q' => 'How do I work out a percentage in my head?',
			'a' => 'Find ten per cent first by moving the decimal point one place left, then build from it. Fifteen per cent of 80 is eight plus half of eight, which is twelve. This works because every percentage is a multiple or fraction of ten per cent, and moving a decimal point is something people do accurately under pressure in a way that long division is not.',
		),
		array(
			'q' => 'What is the difference between percentage points and per cent?',
			'a' => 'A rate moving from 4 per cent to 6 per cent has risen by two percentage points, but by fifty per cent. Both statements are true and they describe the same movement, which is exactly why the distinction gets exploited in headlines. When a number is itself a percentage, say so in points.',
		),
		array(
			'q' => 'Can a percentage be more than 100?',
			'a' => 'Yes, whenever the part is larger than the whole it is measured against. Revenue that triples year on year is a 200 per cent increase, since the increase itself is twice the original figure. A percentage above 100 only signals an error when the quantity is genuinely capped, such as a share of a fixed total.',
		),
	),
	'related'     => array( 'discount-calculator', 'sales-tax-calculator', 'tip-calculator' ),
);
