<?php
/**
 * Mortgage Payment Calculator.
 *
 * Third in the build order because loans carry the best advertising rates on
 * the site, and because the amortisation logic here is reused by four other
 * calculators in the same category.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'slug'        => 'mortgage-payment-calculator',
	'title'       => 'Mortgage Payment Calculator',
	'category'    => 'loans',
	'description' => 'Work out the monthly payment on a home loan including property tax and insurance, and see how much of it goes to interest.',
	'fields'      => array(
		array( 'id' => 'price', 'label' => 'Home price', 'type' => 'number', 'prefix' => '$', 'default' => 450000, 'min' => 0, 'step' => 1000 ),
		array( 'id' => 'downPct', 'label' => 'Down payment', 'type' => 'number', 'suffix' => '%', 'default' => 20, 'min' => 0, 'max' => 100 ),
		array( 'id' => 'rate', 'label' => 'Interest rate', 'type' => 'number', 'suffix' => '%', 'default' => 6.5, 'min' => 0, 'hint' => 'Annual, fixed' ),
		array(
			'id'      => 'term',
			'label'   => 'Loan term',
			'type'    => 'segmented',
			'options' => array( 15 => '15 years', 20 => '20 years', 30 => '30 years' ),
			'default' => 30,
		),
		array( 'id' => 'tax', 'label' => 'Property tax per year', 'type' => 'number', 'prefix' => '$', 'default' => 4200, 'min' => 0 ),
		array( 'id' => 'insurance', 'label' => 'Home insurance per year', 'type' => 'number', 'prefix' => '$', 'default' => 1600, 'min' => 0 ),
	),
	'default_result' => array(
		'label' => 'Monthly payment',
		'value' => '$2,759',
		'rows'  => array(
			array( 'label' => 'Principal & interest', 'value' => '$2,275' ),
			array( 'label' => 'Property tax', 'value' => '$350' ),
			array( 'label' => 'Home insurance', 'value' => '$133' ),
			array( 'label' => 'Loan amount', 'value' => '$360,000' ),
			array( 'label' => 'Total interest paid', 'value' => '$459,144' ),
		),
	),
	'explainer'   => array(
		array(
			'heading' => 'How the payment is calculated',
			'body'    => 'Principal and interest come from the standard amortisation formula, which spreads the loan evenly across every month of the term so the payment never changes even though the split between principal and interest does. In the early years most of each payment is interest, because interest accrues on a balance that has barely moved yet.',
			'formula' => 'M = P &times; [ r(1 + r)<sup>n</sup> ] &divide; [ (1 + r)<sup>n</sup> &minus; 1 ]',
		),
		array(
			'heading' => 'Why tax and insurance are added separately',
			'body'    => 'Lenders collect property tax and home insurance through escrow rather than through the loan itself, so those two amounts can rise during the term while the principal and interest portion stays fixed. Quoting them separately is more honest than folding them into one number, since only part of what you pay each month is actually locked in.',
		),
		array(
			'heading' => 'What a shorter term really saves',
			'body'    => 'Cutting a thirty year term to fifteen roughly doubles the monthly payment but usually saves more than half the total interest, because interest accrues on the outstanding balance and a shorter term keeps that balance falling faster. The right answer depends on whether the higher payment is comfortable rather than merely possible, since a fifteen year loan you have to refinance under pressure costs more than the thirty year loan you could always afford.',
		),
	),
	'faqs'        => array(
		array(
			'q' => 'Why is my real quote higher than this figure?',
			'a' => 'Lenders add mortgage insurance when the deposit is under twenty per cent, plus HOA dues where they apply and any closing costs rolled into the loan. This calculator leaves those out deliberately, because they vary by lender and by property and an estimate built on invented averages would be worse than no estimate at all.',
		),
		array(
			'q' => 'What counts as a good interest rate?',
			'a' => 'Rates move weekly and depend on your credit score, deposit size and loan type, so there is no fixed number worth quoting. Pull the current average from a rate tracker on the day you are comparing offers and use it as your baseline rather than as a target.',
		),
		array(
			'q' => 'Does overpaying actually help?',
			'a' => 'Considerably, and earlier is better. An extra payment made in year two removes interest from every remaining month of the loan, while the same payment made in year twenty-five has almost nothing left to save. Check first whether your lender charges an early repayment penalty, since a few still do.',
		),
	),
	'disclaimer'  => 'An estimate for planning rather than a lender quote. It excludes mortgage insurance, HOA dues and closing costs.',
	'related'     => array( 'compound-interest-calculator', 'percentage-calculator' ),
);
