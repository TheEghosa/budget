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
				'heading' => 'The extraction formula',
				'body' => 'To pull GST out of an inclusive price, divide by one plus the rate. At ten per cent that means dividing by 1.1, and the GST itself is one eleventh of the gross, which is the shortcut used in Australia and New Zealand.',
				'formula' => 'Net = gross ÷ 1.1 at a 10% rate',
			),
			array(
				'heading' => 'Rates vary widely by country',
				'body' => 'Australia and New Zealand apply a single flat rate, while India runs several slabs by category and Canada layers federal GST with provincial taxes into HST in some provinces. The rate is entered rather than assumed for that reason.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How do I calculate GST inclusive and exclusive prices?',
				'a' => 'Multiply by 1.1 to add ten per cent GST, divide by 1.1 to remove it. Switch the mode above to do either.',
			),
			array(
				'q' => 'Is GST the same as VAT?',
				'a' => 'Mechanically almost identical: both are collected in stages with businesses reclaiming what they paid. The name differs by country rather than the concept.',
			),
		),
		'related' => array(
			'vat-calculator',
			'sales-tax-calculator',
			'markup-calculator',
		),
		'disclaimer' => '',
	);
