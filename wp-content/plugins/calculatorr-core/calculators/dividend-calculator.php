<?php
/**
 * Dividend Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'dividend-calculator',
		'title' => 'Dividend Calculator',
		'category' => 'finance',
		'description' => 'Work out dividend income, yield and reinvested growth.',
		'keyword' => 'Dividend Calculator',
		'h1' => 'Dividend Calculator',
		'meta_title' => 'Dividend Calculator - Income, Yield and Reinvestment',
		'meta_description' => 'Free dividend calculator. Work out annual dividend income, the yield on your investment and what reinvesting does to the share count over time.',
		'fields' => array(
			array(
				'id' => 'shares',
				'label' => 'Shares owned',
				'type' => 'number',
				'default' => 500,
			),
			array(
				'id' => 'price',
				'label' => 'Share price',
				'type' => 'number',
				'prefix' => '$',
				'default' => 50,
				'step' => 'any',
			),
			array(
				'id' => 'dividend',
				'label' => 'Dividend per share',
				'type' => 'number',
				'prefix' => '$',
				'default' => 0.6,
				'step' => 'any',
			),
			array(
				'id' => 'frequency',
				'label' => 'Paid',
				'type' => 'segmented',
				'options' => array(
					'1' => 'Yearly',
					'2' => 'Twice yearly',
					'4' => 'Quarterly',
					'12' => 'Monthly',
				),
				'default' => '4',
			),
			array(
				'id' => 'growth',
				'label' => 'Annual dividend growth',
				'type' => 'number',
				'suffix' => '%',
				'default' => 5,
			),
			array(
				'id' => 'years',
				'label' => 'Years',
				'type' => 'number',
				'default' => 20,
			),
			array(
				'id' => 'reinvest',
				'label' => 'Reinvest dividends',
				'type' => 'segmented',
				'options' => array(
					'yes' => 'Yes',
					'no' => 'No',
				),
				'default' => 'yes',
			),
		),
		'default_result' => array(
			'label' => 'Annual dividend income',
			'value' => '$1,200.00',
			'rows' => array(
				array(
					'label' => 'Dividend yield',
					'value' => '4.8%',
				),
				array(
					'label' => 'Per payment',
					'value' => '$300.00',
				),
				array(
					'label' => 'Invested',
					'value' => '$25,000.00',
				),
				array(
					'label' => 'Income over 20 years',
					'value' => '$69,043',
				),
				array(
					'label' => 'Shares after 20 years',
					'value' => '1,277.01',
				),
				array(
					'label' => 'Portfolio value then',
					'value' => '$169,415',
				),
			),
			'note' => 'Reinvesting means each payment buys more shares, which then pay their own dividends. That compounding is where most of the long-run return in dividend investing comes from.',
		),
		'explainer' => array(
			array(
				'heading' => 'Yield is a ratio, not a quality',
				'body' => 'Dividend yield is the annual payment divided by the share price, so it rises when the price falls. An unusually high yield often signals a market that expects the dividend to be cut rather than a bargain, which is why yield alone is a poor screen.',
				'formula' => 'Yield = annual dividend ÷ price × 100',
			),
			array(
				'heading' => 'Where the compounding comes from',
				'body' => 'Reinvesting means each payment buys more shares, which pay their own dividends the following quarter. Over decades that effect, combined with companies raising their payouts, produces most of the total return in dividend investing. Taking the cash removes it entirely.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What is a good dividend yield?',
				'a' => 'Context decides it. Two to four per cent is typical for established payers. Anything far above the sector norm deserves investigation rather than enthusiasm.',
			),
			array(
				'q' => 'Are dividends taxed?',
				'a' => 'Usually yes, though qualified dividends are taxed at lower rates in the US, and dividends inside a retirement account are generally sheltered. The calculator shows gross figures.',
			),
		),
		'related' => array(
			'future-value-calculator',
			'compound-interest-calculator',
			'cd-calculator',
		),
		'disclaimer' => 'For planning only, not tax or financial advice. Rates and thresholds change, so confirm current figures before acting on a projection.',
		'sources' => array(),
	);
