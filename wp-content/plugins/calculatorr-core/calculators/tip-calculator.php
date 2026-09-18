<?php
/**
 * Tip Calculator.
 *
 * Sixth in the build order because it is mobile-first by nature, which makes
 * it the honest stress test of the responsive layout.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'slug'        => 'tip-calculator',
	'title'       => 'Tip Calculator',
	'category'    => 'business',
	'description' => 'Work out the tip on a bill, the total to pay, and what each person owes when the bill is split.',
	'keyword'     => 'Tip Calculator',
	'h1'          => 'Tip Calculator',
	'meta_title'  => 'Tip Calculator - Tip, Total & Split the Bill',
	'meta_description' => 'Free tip calculator: work out the tip on any bill, the total to pay, and what each person owes when the bill is split. Choose from 10% to 25% in one tap.',
	'fields'      => array(
		array( 'id' => 'bill', 'label' => 'Bill amount', 'type' => 'number', 'prefix' => '$', 'default' => 85, 'min' => 0 ),
		array(
			'id'      => 'tip',
			'label'   => 'Tip',
			'type'    => 'segmented',
			'options' => array( 10 => '10%', 15 => '15%', 18 => '18%', 20 => '20%', 25 => '25%' ),
			'default' => 18,
		),
		array( 'id' => 'people', 'label' => 'Split between', 'type' => 'number', 'suffix' => 'people', 'default' => 2, 'min' => 1, 'step' => 1 ),
	),
	'default_result' => array(
		'label' => 'Total to pay',
		'value' => '$100.30',
		'rows'  => array(
			array( 'label' => 'Bill', 'value' => '$85.00' ),
			array( 'label' => 'Tip at 18%', 'value' => '$15.30' ),
			array( 'label' => 'Each person pays', 'value' => '$50.15' ),
			array( 'label' => 'Tip per person', 'value' => '$7.65' ),
		),
	),
	'explainer'   => array(
		array(
			'heading' => 'Tipping before or after tax',
			'body'    => 'Custom varies and neither answer is wrong, though tipping on the pre-tax amount is the more common convention in the United States. The difference is small on an ordinary bill, usually under a dollar, so it is rarely worth the arithmetic unless the bill is large. This calculator tips on whatever figure you enter, so put in the pre-tax subtotal if that is your preference.',
			'formula' => 'Tip = Bill &times; (Rate &divide; 100)',
		),
		array(
			'heading' => 'Splitting a bill that is not split evenly',
			'body'    => 'An even split is fine when everyone ordered roughly the same, and quietly unfair when one person had a starter and a bottle of wine. If the gap is large enough to notice, total each person&rsquo;s items first and apply the same tip percentage to each subtotal, which keeps the tip proportional to what each person actually spent.',
		),
	),
	'faqs'        => array(
		array(
			'q' => 'How much should I tip?',
			'a' => 'In the United States, fifteen to twenty per cent is standard for table service, with twenty being the common default in cities. Norms differ sharply elsewhere: service is often included in Europe and tipping is uncommon or unwelcome in parts of Asia, so check local custom rather than exporting your own.',
		),
		array(
			'q' => 'Should I tip on the delivery fee?',
			'a' => 'The delivery fee usually goes to the platform rather than the driver, so tipping on the food subtotal and treating the fee as a separate charge is the fairer approach. If you are unsure where the fee lands, tipping on the full amount errs in the driver&rsquo;s favour.',
		),
	),
	'related'     => array( 'discount-calculator', 'sales-tax-calculator', 'percentage-calculator' ),
);
