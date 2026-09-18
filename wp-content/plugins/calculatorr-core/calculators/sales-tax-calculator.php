<?php
/**
 * Sales Tax Calculator.
 *
 * Tenth in the build order because the maths is simple but the rate is region
 * dependent, which proves the pattern for the location aware variants later.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'slug'        => 'sales-tax-calculator',
	'title'       => 'Sales Tax Calculator',
	'category'    => 'business',
	'description' => 'Add sales tax to a price or strip it back out of a total, and see the tax amount on its own.',
	'keyword'     => 'Sales Tax Calculator',
	'h1'          => 'Sales Tax Calculator',
	'meta_title'  => 'Sales Tax Calculator - Add or Remove Tax From a Price',
	'meta_description' => 'Free sales tax calculator. Add tax to a price or strip it back out of a total. Removing tax is not the same as subtracting it, and this does it correctly.',
	'fields'      => array(
		array(
			'id'      => 'mode',
			'label'   => 'What are you doing?',
			'type'    => 'segmented',
			'options' => array( 'add' => 'Add tax to a price', 'remove' => 'Remove tax from a total' ),
			'default' => 'add',
		),
		array( 'id' => 'amount', 'label' => 'Amount', 'type' => 'number', 'prefix' => '$', 'default' => 100, 'min' => 0 ),
		array( 'id' => 'rate', 'label' => 'Tax rate', 'type' => 'number', 'suffix' => '%', 'default' => 8.25, 'min' => 0 ),
	),
	'default_result' => array(
		'label' => 'Total with tax',
		'value' => '$108.25',
		'rows'  => array(
			array( 'label' => 'Before tax', 'value' => '$100.00' ),
			array( 'label' => 'Sales tax', 'value' => '$8.25' ),
			array( 'label' => 'After tax', 'value' => '$108.25' ),
		),
	),
	'explainer'   => array(
		array(
			'heading' => 'Removing tax is not the same as subtracting it',
			'body'    => 'Taking 8.25 per cent off a tax inclusive total gives the wrong answer, because the tax was calculated on the smaller pre-tax figure rather than on the total you are holding. You have to divide by one plus the rate instead. On a $108.25 total, subtracting 8.25 per cent leaves $99.32 while dividing correctly gives $100.00, and that gap grows with the size of the bill.',
			'formula' => 'Before tax = Total &divide; (1 + rate)',
		),
		array(
			'heading' => 'Why there is no single rate to quote',
			'body'    => 'Sales tax in the United States is set at state level and then added to by counties and cities, so two shops a few miles apart can charge different rates. Five states have no state sales tax at all. Because of that, this calculator asks you for the rate rather than guessing it, and the right source is your state revenue department or the receipt in front of you.',
		),
	),
	'faqs'        => array(
		array(
			'q' => 'Is sales tax the same as VAT?',
			'a' => 'They are similar in effect and different in mechanism. Sales tax is collected once at the final sale, while VAT is collected in stages along the supply chain with businesses reclaiming what they paid. The practical difference for a shopper is that VAT is normally included in the displayed price while United States sales tax is added at the till.',
		),
		array(
			'q' => 'Do I charge tax based on my location or the customer\'s?',
			'a' => 'For most online sales in the United States it is the customer&rsquo;s location, under destination based sourcing rules that also depend on whether you have economic nexus in their state. The thresholds vary by state and change often enough that this is worth confirming with an accountant rather than with a calculator.',
		),
	),
	'related'     => array( 'discount-calculator', 'tip-calculator', 'percentage-calculator' ),
);
