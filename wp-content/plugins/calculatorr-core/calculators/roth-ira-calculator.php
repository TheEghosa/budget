<?php
/**
 * Roth IRA Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'roth-ira-calculator',
		'title' => 'Roth IRA Calculator',
		'category' => 'finance',
		'description' => 'Project tax-free growth in a Roth IRA.',
		'keyword' => 'Roth IRA Calculator',
		'h1' => 'Roth IRA Calculator',
		'meta_title' => 'Roth IRA Calculator - Tax-Free Retirement Growth',
		'meta_description' => 'Free Roth IRA calculator. Project your balance at retirement, see how much of it is growth, and how much tax that growth escapes entirely.',
		'fields' => array(
			array(
				'id' => 'current',
				'label' => 'Current balance',
				'type' => 'number',
				'prefix' => '$',
				'default' => 10000,
			),
			array(
				'id' => 'annual',
				'label' => 'Annual contribution',
				'type' => 'number',
				'prefix' => '$',
				'default' => 7000,
			),
			array(
				'id' => 'years',
				'label' => 'Years until retirement',
				'type' => 'number',
				'default' => 30,
			),
			array(
				'id' => 'rate',
				'label' => 'Annual return',
				'type' => 'number',
				'suffix' => '%',
				'default' => 7,
			),
			array(
				'id' => 'taxRate',
				'label' => 'Expected retirement tax rate',
				'type' => 'number',
				'suffix' => '%',
				'default' => 22,
			),
		),
		'default_result' => array(
			'label' => 'Tax-free balance at retirement',
			'value' => '$760,491',
			'rows' => array(
				array(
					'label' => 'You contributed',
					'value' => '$220,000',
				),
				array(
					'label' => 'Tax-free growth',
					'value' => '$540,491',
				),
				array(
					'label' => 'Tax avoided on the growth',
					'value' => '$118,908',
				),
				array(
					'label' => 'Annual contribution',
					'value' => '$7,000',
				),
			),
			'note' => 'Roth contributions are made from money already taxed, so nothing is deducted now and nothing is owed on the growth later. That trade favours you when your tax rate in retirement is higher than it is today, which is usually the case early in a career.',
		),
		'explainer' => array(
			array(
				'heading' => 'What you are actually buying',
				'body' => 'A Roth is funded with money already taxed, so there is no deduction now. In exchange, every dollar of growth is withdrawn tax free later. On a thirty year projection the growth is usually several times the contributions, which is why the tax avoided can be very large indeed.',
				'formula' => 'Tax avoided = growth × your future tax rate',
			),
			array(
				'heading' => 'When the trade favours you',
				'body' => 'A Roth wins when your tax rate in retirement is higher than it is today, which is typically the case early in a career, and when you value certainty, since it removes future tax policy from your planning entirely. A traditional account wins when you are at peak earnings now and expect a lower rate later.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'Roth or traditional?',
				'a' => 'Roth if your current tax rate is low relative to where you expect to be. Traditional if you are at peak earnings now. Many people hold both deliberately, which hedges against future rate changes.',
			),
			array(
				'q' => 'Can I withdraw contributions early?',
				'a' => 'Contributions can generally be withdrawn at any time without tax or penalty, since they were already taxed. Earnings are a different matter and usually cannot be touched before 59 and a half without cost.',
			),
		),
		'related' => array(
			'401k-calculator',
			'future-value-calculator',
			'compound-interest-calculator',
		),
		'disclaimer' => 'For planning only, not tax or financial advice. Rates and thresholds change, so confirm current figures before acting on a projection.',
	);
