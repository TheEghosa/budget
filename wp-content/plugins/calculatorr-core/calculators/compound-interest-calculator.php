<?php
/**
 * Compound Interest Calculator.
 *
 * Fourth in the build order because it proves the results-and-breakdown
 * pattern that every projection tool on the site will reuse.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'slug'        => 'compound-interest-calculator',
	'title'       => 'Compound Interest Calculator',
	'category'    => 'finance',
	'description' => 'See what a starting balance plus regular monthly contributions grows into, and how much of the total is interest rather than your own money.',
	'keyword'     => 'Compound Interest Calculator',
	'h1'          => 'Compound Interest Calculator',
	'meta_title'  => 'Compound Interest Calculator - Growth With Contributions',
	'meta_description' => 'Free compound interest calculator with monthly contributions. See what savings grow into, and how much of the balance is interest rather than your own money.',
	'fields'      => array(
		array( 'id' => 'principal', 'label' => 'Starting amount', 'type' => 'number', 'prefix' => '$', 'default' => 10000, 'min' => 0 ),
		array( 'id' => 'monthly', 'label' => 'Monthly contribution', 'type' => 'number', 'prefix' => '$', 'default' => 250, 'min' => 0 ),
		array( 'id' => 'rate', 'label' => 'Annual return', 'type' => 'number', 'suffix' => '%', 'default' => 7, 'min' => 0 ),
		array( 'id' => 'years', 'label' => 'Years', 'type' => 'number', 'default' => 20, 'min' => 0 ),
		array(
			'id'      => 'frequency',
			'label'   => 'Compounding',
			'type'    => 'segmented',
			'options' => array( 1 => 'Yearly', 4 => 'Quarterly', 12 => 'Monthly' ),
			'default' => 12,
		),
	),
	'default_result' => array(
		'label' => 'Balance after 20 years',
		'value' => '$170,617',
		'rows'  => array(
			array( 'label' => 'You put in', 'value' => '$70,000' ),
			array( 'label' => 'Interest earned', 'value' => '$100,617' ),
			array( 'label' => 'Starting amount', 'value' => '$10,000' ),
			array( 'label' => 'Monthly contribution', 'value' => '$250' ),
		),
	),
	'explainer'   => array(
		array(
			'heading' => 'Why compounding looks slow and then does not',
			'body'    => 'Compound interest pays you on the interest you have already earned, so the balance grows on a curve rather than a straight line. For the first several years the curve is nearly flat and the whole thing feels like a waste of effort, which is when most people stop. The steep part arrives late, and it arrives only for money that was left alone long enough to reach it.',
			'formula' => 'FV = P(1 + i)<sup>n</sup> + PMT &times; [ ((1 + i)<sup>n</sup> &minus; 1) &divide; i ]',
		),
		array(
			'heading' => 'What this projection quietly assumes',
			'body'    => 'It assumes the return holds steady for the whole period, which no real investment does, and it ignores tax, inflation and fees, all three of which reduce what the final balance is actually worth. A seven per cent nominal return with three per cent inflation is closer to four per cent in purchasing power, so run the numbers again with a lower rate if you want a figure you can plan against rather than one that simply looks good.',
		),
	),
	'faqs'        => array(
		array(
			'q' => 'Does compounding frequency matter much?',
			'a' => 'Less than most people expect. Moving from yearly to monthly compounding on a seven per cent return adds roughly a fifth of a percentage point to the effective rate. The contribution amount and the number of years both matter far more, so optimise those before worrying about the compounding schedule.',
		),
		array(
			'q' => 'What return should I assume?',
			'a' => 'There is no safe single answer, since it depends entirely on what you are invested in. Rather than pick one number, run the calculation three times at a pessimistic, a middling and an optimistic rate, and make sure your plan survives the pessimistic one. A projection you can only afford at the optimistic rate is a hope rather than a plan.',
		),
		array(
			'q' => 'Is it better to start with a lump sum or contribute monthly?',
			'a' => 'A lump sum invested earlier beats the same total drip fed in later, because every month of compounding counts. In practice most people do not have the lump sum, and the monthly contribution is what actually gets invested, so the comparison is usually academic. Contributing something consistently beats waiting until you can contribute a lot.',
		),
	),
	'related'     => array( 'mortgage-payment-calculator', 'percentage-calculator' ),
);
