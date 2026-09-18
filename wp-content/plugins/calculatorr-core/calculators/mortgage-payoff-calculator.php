<?php
/**
 * Mortgage Payoff Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'mortgage-payoff-calculator',
		'title' => 'Mortgage Payoff Calculator',
		'category' => 'loans',
		'description' => 'See what overpaying saves in time and interest.',
		'keyword' => 'Mortgage Payoff Calculator',
		'h1' => 'Mortgage Payoff Calculator',
		'meta_title' => 'Mortgage Payoff Calculator - Pay Off Early Savings',
		'meta_description' => 'Free mortgage payoff calculator. See how much time and interest an extra monthly payment saves, and what your new payoff date would be.',
		'fields' => array(
			array(
				'id' => 'balance',
				'label' => 'Current balance',
				'type' => 'number',
				'prefix' => '$',
				'default' => 250000,
			),
			array(
				'id' => 'rate',
				'label' => 'Interest rate',
				'type' => 'number',
				'suffix' => '%',
				'default' => 6.5,
				'step' => 'any',
			),
			array(
				'id' => 'yearsLeft',
				'label' => 'Years remaining',
				'type' => 'number',
				'default' => 25,
			),
			array(
				'id' => 'extra',
				'label' => 'Extra per month',
				'type' => 'number',
				'prefix' => '$',
				'default' => 200,
			),
		),
		'default_result' => array(
			'label' => 'Paid off in',
			'value' => '19 yr 6 mo',
			'rows' => array(
				array(
					'label' => 'Interest saved',
					'value' => '$64,928',
				),
				array(
					'label' => 'Time saved',
					'value' => '5 yr 6 mo',
				),
				array(
					'label' => 'New monthly payment',
					'value' => '$1,888.02',
				),
				array(
					'label' => 'Original payment',
					'value' => '$1,688.02',
				),
				array(
					'label' => 'Interest without overpaying',
					'value' => '$256,405',
				),
				array(
					'label' => 'Interest when overpaying',
					'value' => '$191,477',
				),
			),
			'note' => 'Every extra dollar goes straight at the principal, which removes its interest from every remaining month. That is why overpaying early saves so much more than overpaying later.',
		),
		'explainer' => array(
			array(
				'heading' => 'Every extra dollar goes straight at the principal',
				'body' => 'Your scheduled payment covers that month’s interest first and reduces the balance with what is left. Anything above the scheduled amount has no interest to cover, so all of it reduces the balance, and that reduction removes interest from every remaining month of the loan.',
				'formula' => 'Extra payments compound their own saving over the whole remaining term',
			),
			array(
				'heading' => 'Whether to do it at all',
				'body' => 'Overpaying a mortgage is a guaranteed return equal to your interest rate, tax free. That is excellent against a seven per cent mortgage and poor against a three per cent one when other options exist. Clear higher-rate debt first, secure the employer match on a retirement plan, and keep an emergency fund before overpaying a cheap mortgage.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'Is it better to overpay or invest?',
				'a' => 'Compare your mortgage rate against the return you would realistically get elsewhere, after tax. Overpaying is certain, investing is not, and that certainty has real value.',
			),
			array(
				'q' => 'Will my lender charge a penalty?',
				'a' => 'Most US mortgages have no prepayment penalty, but some do and many mortgages elsewhere do. Check before making a large overpayment.',
			),
		),
		'related' => array(
			'amortization-calculator',
			'mortgage-payment-calculator',
			'heloc-calculator',
		),
		'disclaimer' => 'For planning only, not tax or financial advice. Rates and thresholds change, so confirm current figures before acting on a projection.',
	);
