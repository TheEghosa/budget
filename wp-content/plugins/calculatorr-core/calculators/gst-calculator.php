<?php
/**
 * GST Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'gst-calculator',
		'title' => 'GST Calculator',
		'category' => 'business',
		'description' => 'Add GST to a price or remove it from a total.',
		'keyword' => 'GST Calculator',
		'h1' => 'GST Calculator',
		'meta_title' => 'GST Calculator - Add or Remove GST',
		'meta_description' => 'Free GST calculator. Add GST to a price or extract it from a GST-inclusive total at any rate, showing the tax amount on its own.',
		'fields' => array(
			array(
				'id' => 'mode',
				'label' => 'I want to',
				'type' => 'segmented',
				'options' => array(
					'add' => 'Add GST',
					'remove' => 'Remove GST',
				),
				'default' => 'add',
			),
			array(
				'id' => 'amount',
				'label' => 'Amount',
				'type' => 'number',
				'prefix' => '$',
				'default' => 100,
				'step' => 'any',
			),
			array(
				'id' => 'rate',
				'label' => 'GST rate',
				'type' => 'number',
				'suffix' => '%',
				'default' => 10,
				'step' => 'any',
			),
		),
		'default_result' => array(
			'label' => 'Total including GST',
			'value' => '$110.00',
			'rows' => array(
				array(
					'label' => 'Excluding GST',
					'value' => '$100.00',
				),
				array(
					'label' => 'GST amount',
					'value' => '$10.00',
				),
				array(
					'label' => 'Including GST',
					'value' => '$110.00',
				),
				array(
					'label' => 'Rate applied',
					'value' => '10%',
				),
			),
			'note' => 'Removing GST means dividing by one plus the rate, not subtracting the percentage. Subtracting gives a number that is always too low, and the gap grows with the size of the bill.',
		),
		'explainer' => array(
			array(
				'heading' => 'Adding GST and taking it back out',
				'body' => '<p>Adding it is easy: multiply by one plus the rate. Taking it out is where people go wrong, because they subtract the rate instead of dividing by it, and the two are not the same operation.</p><p>Ten percent added to $100 gives $110. Ten percent subtracted from $110 gives $99, not $100. To recover the original you divide by 1.1, and that gap of one dollar is the error repeated on every invoice somebody reconciles by subtraction.</p>',
				'formula' => 'With GST = Amount &times; (1 + rate) &nbsp;&middot;&nbsp; Without GST = Total &divide; (1 + rate)',
				'steps' => array(
					'Decide which figure you have: the amount before tax, or the total including it.',
					'To add, multiply by 1 plus the rate as a decimal.',
					'To remove, divide the total by that same figure.',
					'The GST itself is the difference between the two.',
				),
				'example' => '<p>An invoice totals $847 including 10 percent GST. 847 &divide; 1.1 = $770 before tax, so the GST portion is $77.</p>',
			),
			array(
				'heading' => 'The rates this applies to',
				'body' => '<p>GST goes by different names and different numbers depending on where you are, and the arithmetic is identical in every case. Only the rate changes, so the table below is really a list of what to put into the same two formulas.</p>',
				'table' => array(
					'caption' => 'GST and equivalent rates by country',
					'head' => array(
						'Country',
						'Rate',
						'Multiply by to add',
						'Divide by to remove',
					),
					'rows' => array(
						array(
							'Australia',
							'10%',
							'1.10',
							'1.10',
						),
						array(
							'New Zealand',
							'15%',
							'1.15',
							'1.15',
						),
						array(
							'Singapore',
							'9%',
							'1.09',
							'1.09',
						),
						array(
							'India (standard)',
							'18%',
							'1.18',
							'1.18',
						),
						array(
							'India (reduced)',
							'5% or 12%',
							'1.05 or 1.12',
							'1.05 or 1.12',
						),
						array(
							'Canada (federal GST)',
							'5%',
							'1.05',
							'1.05',
						),
						array(
							'Malaysia (SST)',
							'6%',
							'1.06',
							'1.06',
						),
					),
				),
			),
			array(
				'heading' => 'The shortcut for a ten percent rate',
				'body' => '<p>Australia\'s ten percent has the tidiest arithmetic of any consumption tax in the world, and it is worth knowing. To find the GST inside a total, divide by eleven. Not by ten, by eleven.</p><p>A $847 total divided by 11 is $77, which is exactly the GST. The reason is that the total is eleven tenths of the pre-tax amount, so one eleventh of the total is the one tenth that was added. No other common rate divides so neatly, which is why Australian invoices are quicker to check in your head than anybody else\'s.</p>',
			),
			array(
				'heading' => 'Inclusive and exclusive pricing',
				'body' => '<p>Consumer prices in Australia, New Zealand and India are quoted inclusive of GST, so the number on the shelf is the number you pay. Business-to-business quotes are usually exclusive, with the tax added at the bottom of the invoice.</p><p>That difference is the source of most disputes about a quote, because a builder quoting $5,000 plus GST and a customer hearing $5,000 are five hundred dollars apart before anybody has done anything wrong. Ask which one a figure is, every time, and write the answer down.</p>',
				'table' => array(
					'caption' => 'The same job quoted both ways at 10 percent',
					'head' => array(
						'Quoted as',
						'Pre-tax',
						'GST',
						'You pay',
					),
					'rows' => array(
						array(
							'$5,000 plus GST',
							'$5,000',
							'$500',
							'$5,500',
						),
						array(
							'$5,000 including GST',
							'$4,545.45',
							'$454.55',
							'$5,000',
						),
						array(
							'Difference',
							'$454.55',
							'$45.45',
							'$500',
						),
					),
				),
			),
			array(
				'heading' => 'Why registered businesses care less than you might think',
				'body' => '<p>A GST-registered business claims back the GST it pays on its own purchases, so the tax passes through rather than landing on it. That is why a trade supplier quotes excluding GST as a matter of course: to their usual customer the tax genuinely is not part of the cost.</p><p>It also means the tax is only really paid at the end of the chain, by whoever is not registered, which is usually you. Every business in between collects it and hands it on, which is the design rather than a loophole.</p>',
			),
			array(
				'heading' => 'What this will not do',
				'body' => '<p>It applies one rate to one figure. It does not know which goods are zero-rated, exempt or reduced in your jurisdiction, and those categories vary enormously: basic food, some medical supplies and certain education services commonly sit outside the standard rate, and the boundaries are drawn differently in every country that has drawn them.</p><p>It is also not a substitute for advice on whether you must register. Thresholds, filing frequency and the rules on when GST becomes payable are jurisdiction-specific and change, so treat the arithmetic here as arithmetic and take the compliance questions to somebody who answers them for a living.</p>',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How do I remove 10 percent GST from a total?',
				'a' => 'Divide by 1.1, do not subtract 10 percent. Subtracting from $110 gives $99, while dividing gives the correct $100. Even quicker for a 10 percent rate: the GST inside any total is the total divided by 11.',
			),
			array(
				'q' => 'Why divide by 11 rather than 10?',
				'a' => 'Because the total is eleven tenths of the pre-tax figure once 10 percent has been added. One eleventh of the total is therefore exactly the tenth that was added, which makes Australian invoices unusually easy to check mentally.',
			),
			array(
				'q' => 'Is GST the same as VAT?',
				'a' => 'In mechanism, yes. Both are consumption taxes collected at each stage with credit for tax already paid, and both are worked out with the same arithmetic. The names, the rates and the exemptions differ by country.',
			),
			array(
				'q' => 'Does a quote of $5,000 plus GST mean I pay $5,000?',
				'a' => 'No, you pay $5,500 at a 10 percent rate. Plus GST means the tax is added on top, while including GST means it is already in the figure. Always ask which one a quote is, because the gap is the whole tax.',
			),
			array(
				'q' => 'Which goods are GST free?',
				'a' => 'That depends entirely on the country. Basic food, some medical supplies and certain education and financial services are commonly outside the standard rate, but the boundaries are drawn differently everywhere and this calculator applies whichever single rate you give it.',
			),
		),
		'related' => array(
			'vat-calculator',
			'sales-tax-calculator',
			'markup-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
