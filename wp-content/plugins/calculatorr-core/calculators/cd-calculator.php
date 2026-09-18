<?php
/**
 * CD Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'cd-calculator',
		'title' => 'CD Calculator',
		'category' => 'finance',
		'description' => 'Work out what a certificate of deposit will be worth at maturity.',
		'keyword' => 'CD Calculator',
		'h1' => 'CD Calculator',
		'meta_title' => 'CD Calculator - Certificate of Deposit Maturity Value',
		'meta_description' => 'Free CD calculator. Work out the value of a certificate of deposit at maturity, the interest earned and the effective annual yield for any term.',
		'fields' => array(
			array(
				'id' => 'principal',
				'label' => 'Deposit',
				'type' => 'number',
				'prefix' => '$',
				'default' => 10000,
			),
			array(
				'id' => 'rate',
				'label' => 'Interest rate (APY)',
				'type' => 'number',
				'suffix' => '%',
				'default' => 4.5,
			),
			array(
				'id' => 'months',
				'label' => 'Term',
				'type' => 'number',
				'suffix' => 'months',
				'default' => 12,
			),
			array(
				'id' => 'compounds',
				'label' => 'Compounded',
				'type' => 'segmented',
				'options' => array(
					'1' => 'Yearly',
					'4' => 'Quarterly',
					'12' => 'Monthly',
					'365' => 'Daily',
				),
				'default' => '12',
			),
		),
		'default_result' => array(
			'label' => 'Value at maturity',
			'value' => '$10,459.40',
			'rows' => array(
				array(
					'label' => 'Interest earned',
					'value' => '$459.40',
				),
				array(
					'label' => 'Principal',
					'value' => '$10,000.00',
				),
				array(
					'label' => 'Effective annual yield',
					'value' => '4.594%',
				),
				array(
					'label' => 'Term',
					'value' => '12 months',
				),
				array(
					'label' => 'Interest per month, averaged',
					'value' => '$38.28',
				),
			),
			'note' => 'The rate is locked for the term, which is the point of a CD and also its cost: withdrawing early usually forfeits several months of interest, and you cannot take advantage if rates rise.',
		),
		'explainer' => array(
			array(
				'heading' => 'The rate is locked, and that cuts both ways',
				'body' => 'A CD fixes your rate for the whole term, which protects you if rates fall and strands you if they rise. Early withdrawal usually forfeits several months of interest, so the money genuinely has to be money you will not need.',
				'formula' => 'Value = principal × (1 + rate ÷ n)^(n × years)',
			),
			array(
				'heading' => 'APY already includes compounding',
				'body' => 'A quoted APY is the effective annual yield with compounding baked in, which is why it is the figure to compare between banks. A nominal rate with a compounding frequency attached is not directly comparable to an APY, and some advertising blurs the two.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'Is a CD worth it?',
				'a' => 'When you have a known date you need the money and rates are attractive, yes. When rates are low or your horizon is long, other options generally beat it.',
			),
			array(
				'q' => 'What is a CD ladder?',
				'a' => 'Splitting money across CDs maturing at staggered intervals, so some matures regularly. It keeps most of the higher long-term rate while restoring some access to the cash.',
			),
		),
		'related' => array(
			'compound-interest-calculator',
			'future-value-calculator',
			'dividend-calculator',
		),
		'disclaimer' => 'For planning only, not tax or financial advice. Rates and thresholds change, so confirm current figures before acting on a projection.',
	);
