<?php
/**
 * eBay Fee Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'ebay-fee-calculator',
		'title' => 'eBay Fee Calculator',
		'category' => 'business',
		'description' => 'Work out eBay fees and the profit left on a sale.',
		'keyword' => 'eBay Fee Calculator',
		'h1' => 'eBay Fee Calculator',
		'meta_title' => 'eBay Fee Calculator - Seller Fees and Real Profit',
		'meta_description' => 'Free eBay fee calculator. Work out final value fees, promoted listing costs and the actual profit on a sale, including your item and shipping costs.',
		'fields' => array(
			array(
				'id' => 'price',
				'label' => 'Item price',
				'type' => 'number',
				'prefix' => '$',
				'default' => 50,
				'step' => 'any',
			),
			array(
				'id' => 'shipping',
				'label' => 'Shipping charged to buyer',
				'type' => 'number',
				'prefix' => '$',
				'default' => 8,
				'step' => 'any',
			),
			array(
				'id' => 'cost',
				'label' => 'Your item cost',
				'type' => 'number',
				'prefix' => '$',
				'default' => 20,
				'step' => 'any',
			),
			array(
				'id' => 'shipCost',
				'label' => 'Your shipping cost',
				'type' => 'number',
				'prefix' => '$',
				'default' => 7,
				'step' => 'any',
			),
			array(
				'id' => 'feeRate',
				'label' => 'Final value fee',
				'type' => 'number',
				'suffix' => '%',
				'default' => 13.25,
				'step' => 'any',
				'hint' => 'Varies by category and store subscription.',
			),
			array(
				'id' => 'perOrder',
				'label' => 'Per order fee',
				'type' => 'number',
				'prefix' => '$',
				'default' => 0.3,
				'step' => 'any',
			),
			array(
				'id' => 'promoted',
				'label' => 'Promoted listing rate',
				'type' => 'number',
				'suffix' => '%',
				'default' => 0,
				'step' => 'any',
			),
		),
		'default_result' => array(
			'label' => 'Profit on the sale',
			'value' => '$23.02',
			'rows' => array(
				array(
					'label' => 'Buyer pays',
					'value' => '$58.00',
				),
				array(
					'label' => 'eBay fees',
					'value' => '$7.99',
				),
				array(
					'label' => 'Your item cost',
					'value' => '$20.00',
				),
				array(
					'label' => 'Your shipping cost',
					'value' => '$7.00',
				),
				array(
					'label' => 'Profit margin',
					'value' => '39.68%',
				),
				array(
					'label' => 'Break-even sale price',
					'value' => '$23.47',
				),
			),
			'note' => 'The fee rate is entered rather than fixed, because eBay charges differently by category and by store subscription and changes the rates periodically. Note that the final value fee applies to the shipping the buyer pays as well as to the item price, which is the part sellers most often miss.',
		),
		'explainer' => array(
			array(
				'heading' => 'The fee applies to shipping too',
				'body' => 'eBay charges the final value fee on the total the buyer pays, which includes the shipping they were charged. Sellers who price shipping high to keep the item price low are paying fees on that shipping, and the free shipping strategy is not as expensive as it first appears once this is accounted for.',
				'formula' => 'Fee = (item + shipping) × rate + per order fee',
			),
			array(
				'heading' => 'Break-even is the number to know',
				'body' => 'The break-even row shows the lowest price at which you do not lose money, accounting for fees, item cost and your real shipping cost. That is the floor for any offer you accept, and it is usually higher than sellers expect.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What percentage does eBay take?',
				'a' => 'Commonly around 13.25 per cent for most categories plus a per order fee, though it varies by category and store subscription and eBay changes it periodically. Enter your own rate rather than relying on a figure printed on a website.',
			),
			array(
				'q' => 'Do I pay fees on refunded orders?',
				'a' => 'The final value fee is generally credited back on a refund, though the per order fee often is not. Promoted listing fees are usually not refunded either.',
			),
		),
		'related' => array(
			'margin-calculator',
			'markup-calculator',
			'sales-tax-calculator',
		),
		'disclaimer' => '',
	);
