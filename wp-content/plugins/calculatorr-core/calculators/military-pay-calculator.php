<?php
/**
 * Military Pay Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'military-pay-calculator',
		'title' => 'Military Pay Calculator',
		'category' => 'finance',
		'description' => 'Work out total military compensation including tax-free allowances.',
		'keyword' => 'Military Pay Calculator',
		'h1' => 'Military Pay Calculator',
		'meta_title' => 'Military Pay Calculator - Base Pay, BAH, BAS and RMC',
		'meta_description' => 'Free military pay calculator. Combine base pay, BAH and BAS into gross and Regular Military Compensation, showing what the tax-free allowances are really worth.',
		'fields' => array(
			array(
				'id' => 'basePay',
				'label' => 'Monthly base pay',
				'type' => 'number',
				'prefix' => '$',
				'default' => 3500,
				'hint' => 'From the current DFAS pay table or your LES.',
			),
			array(
				'id' => 'bah',
				'label' => 'Monthly BAH',
				'type' => 'number',
				'prefix' => '$',
				'default' => 1800,
				'hint' => 'Depends on duty ZIP code, paygrade and dependants.',
			),
			array(
				'id' => 'bas',
				'label' => 'Monthly BAS',
				'type' => 'number',
				'prefix' => '$',
				'default' => 460,
			),
			array(
				'id' => 'special',
				'label' => 'Special and incentive pay',
				'type' => 'number',
				'prefix' => '$',
				'default' => 0,
			),
			array(
				'id' => 'taxRate',
				'label' => 'Your marginal tax rate',
				'type' => 'number',
				'suffix' => '%',
				'default' => 22,
			),
		),
		'default_result' => array(
			'label' => 'Monthly gross',
			'value' => '$5,760.00',
			'rows' => array(
				array(
					'label' => 'Taxable pay',
					'value' => '$3,500.00',
				),
				array(
					'label' => 'Tax-free allowances',
					'value' => '$2,260.00',
				),
				array(
					'label' => 'Annual gross',
					'value' => '$69,120',
				),
				array(
					'label' => 'Allowances grossed up',
					'value' => '$2,897.44',
				),
				array(
					'label' => 'Regular Military Compensation',
					'value' => '$6,397.44',
				),
				array(
					'label' => 'RMC annualised',
					'value' => '$76,769',
				),
			),
			'note' => 'Base pay, BAH and BAS are entered rather than looked up, because the tables change every January and BAH depends on duty ZIP code and dependant status. Take the current figures from your LES or the DFAS pay tables. Regular Military Compensation is the honest number to compare against a civilian salary, because it reflects that the allowances arrive untaxed.',
		),
		'explainer' => array(
			array(
				'heading' => 'Why a military salary cannot be compared directly to a civilian one',
				'body' => 'BAH and BAS arrive untaxed, so a dollar of allowance is worth more than a dollar of salary. Grossing the allowances up to their pre-tax equivalent gives Regular Military Compensation, which is the figure the services themselves use for comparison and the only honest basis for weighing an offer outside.',
				'formula' => 'RMC = base pay + special + (allowances ÷ (1 − tax rate))',
			),
			array(
				'heading' => 'Why the figures are entered rather than looked up',
				'body' => 'Pay tables change every January, BAH varies by duty ZIP code and dependant status and is revised annually, and special pays depend on assignment. A built-in table would be stale within months, so the current numbers come from your LES or the DFAS tables and the calculator does the part that is actually hard.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What is RMC?',
				'a' => 'Regular Military Compensation: base pay plus the tax-advantaged value of BAH and BAS. It is the number to compare against a civilian gross salary.',
			),
			array(
				'q' => 'Is BAH taxable?',
				'a' => 'No, and neither is BAS. That tax exemption is a large part of why military compensation compares better than the base pay figure alone suggests.',
			),
		),
		'related' => array(
			'take-home-pay-calculator',
			'pay-raise-calculator',
		),
		'disclaimer' => 'For planning only, not tax or financial advice. Rates and thresholds change, so confirm current figures before acting on a projection.',
	);
