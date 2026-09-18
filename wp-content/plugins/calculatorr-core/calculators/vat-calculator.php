<?php
/**
 * VAT Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'vat-calculator',
		'title' => 'VAT Calculator',
		'category' => 'business',
		'description' => 'Add VAT to a price or remove it from a total.',
		'keyword' => 'VAT Calculator',
		'h1' => 'VAT Calculator',
		'meta_title' => 'VAT Calculator - Add or Remove VAT From a Price',
		'meta_description' => 'Free VAT calculator. Add VAT to a net price or strip it out of a gross total at any rate, with the VAT amount shown separately.',
		'fields' => array(
			array(
				'id' => 'mode',
				'label' => 'I want to',
				'type' => 'segmented',
				'options' => array(
					'add' => 'Add VAT',
					'remove' => 'Remove VAT',
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
				'label' => 'VAT rate',
				'type' => 'number',
				'suffix' => '%',
				'default' => 20,
				'step' => 'any',
			),
		),
		'default_result' => array(
			'label' => 'Total including VAT',
			'value' => '$120.00',
			'rows' => array(
				array(
					'label' => 'Excluding VAT',
					'value' => '$100.00',
				),
				array(
					'label' => 'VAT amount',
					'value' => '$20.00',
				),
				array(
					'label' => 'Including VAT',
					'value' => '$120.00',
				),
				array(
					'label' => 'Rate applied',
					'value' => '20%',
				),
			),
			'note' => 'Removing VAT means dividing by one plus the rate, not subtracting the percentage. Subtracting gives a number that is always too low, and the gap grows with the size of the bill.',
		),
		'explainer' => array(
			array(
				'heading' => 'Removing VAT is division, not subtraction',
				'body' => 'Taking twenty per cent off a VAT-inclusive total gives the wrong answer, because the VAT was calculated on the smaller net figure. You divide by 1.2 instead. On a 120 total, subtracting twenty per cent leaves 96 while dividing correctly gives 100, and the gap grows with the invoice.',
				'formula' => 'Net = gross ÷ (1 + rate ÷ 100)',
			),
			array(
				'heading' => 'Rates differ by what you are selling',
				'body' => 'Most countries run a standard rate plus reduced and zero rates for things like food, books, children’s clothing and energy. The rate is an input here because it depends on both the country and the category, and getting it wrong on an invoice creates a liability rather than an inconvenience.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What is the VAT fraction?',
				'a' => 'For a twenty per cent rate it is one sixth of the gross, which is the shortcut UK businesses use. The general form is rate divided by one hundred plus the rate.',
			),
			array(
				'q' => 'Do I charge VAT to overseas customers?',
				'a' => 'It depends on where they are, whether they are a business, and what you are selling. Digital services in particular follow the customer’s location. This needs an accountant rather than a calculator.',
			),
		),
		'related' => array(
			'gst-calculator',
			'sales-tax-calculator',
			'margin-calculator',
		),
		'disclaimer' => '',
	);
