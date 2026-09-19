<?php
/**
 * Percent Off Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'percent-off-calculator',
		'title' => 'Percent Off Calculator',
		'category' => 'business',
		'description' => 'Work out a sale price and what you save.',
		'keyword' => 'Percent Off Calculator',
		'h1' => 'Percent Off Calculator',
		'meta_title' => 'Percent Off Calculator - Sale Price and Savings',
		'meta_description' => 'Free percent off calculator. Enter a price and a discount to see what you pay, what you save and the discount as a share of the original price.',
		'fields' => array(
			array(
				'id' => 'price',
				'label' => 'Original price',
				'type' => 'number',
				'prefix' => '$',
				'default' => 80,
				'step' => 'any',
			),
			array(
				'id' => 'percent',
				'label' => 'Percent off',
				'type' => 'number',
				'suffix' => '%',
				'default' => 30,
			),
		),
		'default_result' => array(
			'label' => 'You pay',
			'value' => '$56.00',
			'rows' => array(
				array(
					'label' => 'Original price',
					'value' => '$80.00',
				),
				array(
					'label' => 'You save',
					'value' => '$24.00',
				),
				array(
					'label' => 'Percent off',
					'value' => '30%',
				),
			),
			'note' => 'Half off is the only discount most people can do reliably in their head. For everything else, take ten per cent and scale it.',
		),
		'explainer' => array(
			array(
				'heading' => 'Working out what you actually pay',
				'body' => '<p>Multiply the price by the discount, then take that off. Or, faster, multiply the price by what is left: a thirty percent discount means you pay seventy percent, so multiply by 0.7 and you are done in one step instead of two.</p><p>The one-step version is worth the small effort of getting used to, because it removes the subtraction where mistakes happen and it makes stacked discounts obvious, which is where most shop-floor arithmetic falls apart.</p>',
				'formula' => 'You pay = Price &times; (100 &minus; Discount) &divide; 100',
				'steps' => array(
					'Subtract the discount from 100 to get the percentage you pay.',
					'Turn that into a decimal by dividing by 100.',
					'Multiply the original price by it.',
				),
				'example' => '<p>A coat is &pound;85 with 30 percent off. You pay 70 percent, so 85 &times; 0.7 = &pound;59.50, and you saved &pound;25.50.</p>',
			),
			array(
				'heading' => 'The discounts you see most, done for you',
				'body' => '<p>Sale signage uses the same handful of percentages almost everywhere, and knowing what each one does to a price lets you judge an offer before you reach the till. The middle column is the multiplier worth memorising.</p>',
				'table' => array(
					'caption' => 'What each sale sign really means',
					'head' => array(
						'Sign says',
						'You pay',
						'Multiply by',
						'&pound;60 becomes',
					),
					'rows' => array(
						array(
							'10% off',
							'90%',
							'0.90',
							'&pound;54.00',
						),
						array(
							'15% off',
							'85%',
							'0.85',
							'&pound;51.00',
						),
						array(
							'20% off',
							'80%',
							'0.80',
							'&pound;48.00',
						),
						array(
							'25% off',
							'75%',
							'0.75',
							'&pound;45.00',
						),
						array(
							'30% off',
							'70%',
							'0.70',
							'&pound;42.00',
						),
						array(
							'40% off',
							'60%',
							'0.60',
							'&pound;36.00',
						),
						array(
							'50% off',
							'50%',
							'0.50',
							'&pound;30.00',
						),
						array(
							'70% off',
							'30%',
							'0.30',
							'&pound;18.00',
						),
					),
				),
			),
			array(
				'heading' => 'Why an extra 20% off 50% is not 70% off',
				'body' => '<p>Stacked discounts multiply, and the second one is taken from the already reduced price rather than from the original. That makes the combined saving smaller than adding the two percentages suggests, which is exactly why the phrasing is so popular on signage.</p><p>Fifty percent off, then a further twenty percent off, leaves you paying 0.5 &times; 0.8 = 0.4, which is sixty percent off rather than seventy. On a &pound;100 coat that is a &pound;10 difference between what the sign implies and what you pay.</p>',
				'table' => array(
					'caption' => 'What stacked offers really come to',
					'head' => array(
						'The sign',
						'Looks like',
						'Actually is',
						'&pound;100 becomes',
					),
					'rows' => array(
						array(
							'50% then 20%',
							'70% off',
							'60% off',
							'&pound;40.00',
						),
						array(
							'30% then 20%',
							'50% off',
							'44% off',
							'&pound;56.00',
						),
						array(
							'25% then 25%',
							'50% off',
							'43.75% off',
							'&pound;56.25',
						),
						array(
							'40% then 10%',
							'50% off',
							'46% off',
							'&pound;54.00',
						),
						array(
							'20% then 20% then 20%',
							'60% off',
							'48.8% off',
							'&pound;51.20',
						),
					),
				),
			),
			array(
				'heading' => 'Reading the original price sceptically',
				'body' => '<p>A percentage is only as meaningful as the price it is taken from, and the reference price is the part nobody checks. In the UK, pricing rules expect the higher price to have been the genuine selling price for a reasonable period before the sale, which is a rule that exists because the practice it addresses is common.</p><p>The useful habit is to ignore the percentage and look at the final figure. Ask what you would pay for the item if there were no sign at all, and compare that to the number on the ticket. The discount describes the shop\'s pricing history, and the price describes your decision.</p>',
			),
			array(
				'heading' => 'Discount then tax, or tax then discount',
				'body' => '<p>It does not matter, and it is worth knowing why, because people worry about it. Multiplication is commutative, so applying a discount to a tax-inclusive price gives exactly the same result as applying the tax to a discounted price.</p><p>&pound;100 plus 20 percent VAT is &pound;120, less 30 percent is &pound;84. Or &pound;100 less 30 percent is &pound;70, plus 20 percent is &pound;84. The order changes nothing at all, so use whichever is easier to do in your head.</p>',
			),
			array(
				'heading' => 'What the calculator cannot tell you',
				'body' => '<p>It gives you the price and the saving, and neither of those is the same as value. A seventy percent discount on something you will not use is a hundred percent waste of whatever you paid, and the percentage is the part of the sign designed to stop you asking that question.</p><p>It also does not know about delivery, which on a small discounted order can easily exceed the saving. Compare the total at checkout rather than the ticket price, because that is the only number that leaves your account.</p>',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How do I work out 30 percent off quickly?',
				'a' => 'Multiply by 0.7, since paying is easier arithmetic than subtracting. A &pound;85 coat becomes 85 times 0.7, which is &pound;59.50. The one-step version avoids the subtraction where errors usually creep in.',
			),
			array(
				'q' => 'Is 50 percent off then 20 percent off the same as 70 percent off?',
				'a' => 'No, it is 60 percent off. The second discount comes off the already reduced price, so the two multiply rather than add: 0.5 times 0.8 is 0.4, meaning you pay 40 percent.',
			),
			array(
				'q' => 'Does it matter whether tax is applied before or after the discount?',
				'a' => 'No. Multiplication works in either order, so a discount on a tax-inclusive price gives exactly the same total as tax on a discounted price. Use whichever is easier to work out.',
			),
			array(
				'q' => 'How do I check a discount is genuine?',
				'a' => 'Ignore the percentage and look at the final price, then ask what you would have paid for the item with no sign at all. UK pricing rules expect the higher reference price to have been the real selling price for a reasonable period, which tells you the practice is common enough to need a rule.',
			),
			array(
				'q' => 'What does a 33 percent discount leave me paying?',
				'a' => 'Two thirds of the price, so multiply by 0.67. A third off is the same as paying 67 percent, and on &pound;90 that comes to &pound;60.30.',
			),
		),
		'related' => array(
			'discount-calculator',
			'sales-tax-calculator',
			'percentage-decrease-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
