<?php
/**
 * Pay Raise Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'pay-raise-calculator',
		'title' => 'Pay Raise Calculator',
		'category' => 'finance',
		'description' => 'Work out a raise in cash terms and after inflation.',
		'keyword' => 'Pay Raise Calculator',
		'h1' => 'Pay Raise Calculator',
		'meta_title' => 'Pay Raise Calculator - What a Raise Is Really Worth',
		'meta_description' => 'Free pay raise calculator. See your new salary, the increase per month and week, and whether the raise beats inflation in real terms.',
		'fields' => array(
			array(
				'id' => 'current',
				'label' => 'Current salary',
				'type' => 'number',
				'prefix' => '$',
				'default' => 60000,
			),
			array(
				'id' => 'mode',
				'label' => 'I know the',
				'type' => 'segmented',
				'options' => array(
					'percent' => 'Percentage',
					'salary' => 'New salary',
				),
				'default' => 'percent',
			),
			array(
				'id' => 'percent',
				'label' => 'Raise',
				'type' => 'number',
				'suffix' => '%',
				'default' => 4,
				'show_when' => array(
					'mode' => 'percent',
				),
			),
			array(
				'id' => 'newSalary',
				'label' => 'New salary',
				'type' => 'number',
				'prefix' => '$',
				'default' => 64000,
				'show_when' => array(
					'mode' => 'salary',
				),
			),
			array(
				'id' => 'inflation',
				'label' => 'Inflation rate',
				'type' => 'number',
				'suffix' => '%',
				'default' => 3,
			),
		),
		'default_result' => array(
			'label' => 'New salary',
			'value' => '$62,400',
			'rows' => array(
				array(
					'label' => 'Increase',
					'value' => '$2,400',
				),
				array(
					'label' => 'Percentage raise',
					'value' => '4%',
				),
				array(
					'label' => 'Extra per month',
					'value' => '$200.00',
				),
				array(
					'label' => 'Extra per week',
					'value' => '$46.15',
				),
				array(
					'label' => 'Real raise after 3% inflation',
					'value' => '1%',
				),
			),
			'note' => 'A raise only counts once inflation is taken out, which is why the real figure matters more than the headline one.',
		),
		'explainer' => array(
			array(
				'heading' => 'The real raise is the one that counts',
				'body' => 'A four per cent raise against three per cent inflation is a one per cent raise. Against five per cent inflation it is a pay cut, even though the number on the payslip went up and it will be presented to you as good news. The real figure is the one to negotiate against.',
				'formula' => 'Real raise = nominal raise − inflation',
			),
			array(
				'heading' => 'Compounding works on salaries too',
				'body' => 'A raise is permanent and every future raise is calculated on top of it, so an extra two per cent now is worth far more than two per cent of one year’s salary. Over a career the gap between accepting and negotiating compounds into a very large number.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What is a good annual raise?',
				'a' => 'Enough to beat inflation, which is the floor rather than the target. Cost of living adjustments typically track inflation, and a merit increase should sit above it.',
			),
			array(
				'q' => 'How much is a 5% raise on $60,000?',
				'a' => 'Three thousand a year, which is 250 a month before tax. After tax and inflation the felt difference is considerably smaller, which is why the real figure matters.',
			),
		),
		'related' => array(
			'take-home-pay-calculator',
			'future-value-calculator',
			'percent-change-calculator',
		),
		'disclaimer' => 'For planning only, not tax or financial advice. Rates and thresholds change, so confirm current figures before acting on a projection.',
	);
