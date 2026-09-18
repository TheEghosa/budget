<?php
/**
 * Discount Calculator.
 *
 * Seventh in the build order because it shares most of its logic with the tip
 * calculator, making it a cheap second page in the same category.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'slug'        => 'discount-calculator',
	'title'       => 'Discount Calculator',
	'category'    => 'business',
	'description' => 'Work out the sale price after a discount, how much you save, and what stacked discounts really come to.',
	'fields'      => array(
		array( 'id' => 'price', 'label' => 'Original price', 'type' => 'number', 'prefix' => '$', 'default' => 120, 'min' => 0 ),
		array( 'id' => 'discount', 'label' => 'Discount', 'type' => 'number', 'suffix' => '%', 'default' => 25, 'min' => 0, 'max' => 100 ),
		array( 'id' => 'extra', 'label' => 'Extra discount', 'type' => 'number', 'suffix' => '%', 'default' => 0, 'min' => 0, 'max' => 100, 'hint' => 'For a coupon applied on top of a sale price. Leave at zero if there is only one discount.' ),
	),
	'default_result' => array(
		'label' => 'You pay',
		'value' => '$90.00',
		'rows'  => array(
			array( 'label' => 'Original price', 'value' => '$120.00' ),
			array( 'label' => 'You save', 'value' => '$30.00' ),
			array( 'label' => 'Effective discount', 'value' => '25%' ),
		),
	),
	'explainer'   => array(
		array(
			'heading' => 'Why stacked discounts are smaller than they sound',
			'body'    => 'Twenty per cent off followed by a further ten per cent is not thirty per cent off. The second discount applies to the already reduced price, so it removes less than it would have from the original, and the combined saving comes to twenty-eight per cent. This is arithmetic rather than sharp practice, but it is worth knowing before a sale convinces you a coupon is worth more than it is.',
			'formula' => 'Final = Price &times; (1 &minus; d<sub>1</sub>) &times; (1 &minus; d<sub>2</sub>)',
		),
		array(
			'heading' => 'Working back from the sale price',
			'body'    => 'If you know what you paid and what the original price was, the discount is the saving divided by the original price. That is the calculation worth running on a sale that advertises &ldquo;up to&rdquo; a headline percentage, because the item in your hand is frequently not the one the headline was describing.',
		),
	),
	'faqs'        => array(
		array(
			'q' => 'How do I calculate a discount in my head?',
			'a' => 'Work out ten per cent by moving the decimal one place left, then scale it. Thirty per cent off forty pounds is three lots of four pounds, so twelve off, leaving twenty-eight. For twenty-five per cent, quartering the price is usually faster than any percentage method.',
		),
		array(
			'q' => 'Does the order of two discounts change the result?',
			'a' => 'No, multiplication is commutative, so twenty then ten gives exactly the same final price as ten then twenty. Where the order does matter is with tax, since tax applied before a discount produces a different total from tax applied after.',
		),
	),
	'related'     => array( 'sales-tax-calculator', 'tip-calculator', 'percentage-calculator' ),
);
