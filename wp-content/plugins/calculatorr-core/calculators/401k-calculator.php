<?php
/**
 * 401(k) Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => '401k-calculator',
		'title' => '401(k) Calculator',
		'category' => 'finance',
		'description' => 'Project a 401(k) balance including the employer match.',
		'keyword' => '401(k) Calculator',
		'h1' => '401(k) Calculator',
		'meta_title' => '401(k) Calculator - Projected Balance With Match',
		'meta_description' => 'Free 401(k) calculator. Project your retirement balance from contributions, employer match, salary growth and returns, with the match limit flagged.',
		'fields' => array(
			array(
				'id' => 'current',
				'label' => 'Current balance',
				'type' => 'number',
				'prefix' => '$',
				'default' => 25000,
			),
			array(
				'id' => 'salary',
				'label' => 'Annual salary',
				'type' => 'number',
				'prefix' => '$',
				'default' => 75000,
			),
			array(
				'id' => 'contribution',
				'label' => 'You contribute',
				'type' => 'number',
				'suffix' => '%',
				'default' => 6,
			),
			array(
				'id' => 'match',
				'label' => 'Employer matches',
				'type' => 'number',
				'suffix' => '%',
				'default' => 50,
				'hint' => '50 means fifty cents per dollar.',
			),
			array(
				'id' => 'matchLimit',
				'label' => 'Up to this much of salary',
				'type' => 'number',
				'suffix' => '%',
				'default' => 6,
			),
			array(
				'id' => 'raise',
				'label' => 'Annual salary growth',
				'type' => 'number',
				'suffix' => '%',
				'default' => 3,
			),
			array(
				'id' => 'rate',
				'label' => 'Annual return',
				'type' => 'number',
				'suffix' => '%',
				'default' => 7,
			),
			array(
				'id' => 'years',
				'label' => 'Years until retirement',
				'type' => 'number',
				'default' => 30,
			),
		),
		'default_result' => array(
			'label' => 'Balance after 30 years',
			'value' => '$1,095,898',
			'rows' => array(
				array(
					'label' => 'Your contributions',
					'value' => '$239,089',
				),
				array(
					'label' => 'Employer match',
					'value' => '$107,045',
				),
				array(
					'label' => 'Investment growth',
					'value' => '$749,764',
				),
				array(
					'label' => 'First year contribution',
					'value' => '$4,500',
				),
				array(
					'label' => 'First year match',
					'value' => '$2,250',
				),
			),
			'note' => 'Contributing at or above the match limit, which is the first thing to get right before anything else in a retirement plan.',
		),
		'explainer' => array(
			array(
				'heading' => 'The match is the only guaranteed return you will ever get',
				'body' => 'If your employer matches fifty cents on the dollar up to six per cent of salary, contributing less than six per cent means declining a fifty per cent instant return. No investment available anywhere offers that. The calculator flags it when your contribution falls below the match limit, because it is the single most valuable thing to fix.',
				'formula' => 'Match = salary × min(your rate, match limit) × match percentage',
			),
			array(
				'heading' => 'Why the growth line dominates over time',
				'body' => 'In the early years your contributions are most of the balance. Past roughly twenty years the growth on previous growth overtakes them, which is why starting early beats contributing more later, and why the breakdown bar shifts so dramatically across a long projection.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How much should I contribute to my 401(k)?',
				'a' => 'At minimum enough to capture the full employer match. Beyond that, a commonly cited target is fifteen per cent of salary including the match.',
			),
			array(
				'q' => 'What return should I assume?',
				'a' => 'Run the projection at a pessimistic, middling and optimistic rate rather than picking one. A plan that only works at the optimistic rate is a hope.',
			),
			array(
				'q' => 'What happens if I leave my job?',
				'a' => 'Your own contributions are always yours. The employer match may be subject to a vesting schedule, so check how much of it you would keep before resigning.',
			),
		),
		'related' => array(
			'roth-ira-calculator',
			'future-value-calculator',
			'compound-interest-calculator',
		),
		'disclaimer' => 'For planning only, not tax or financial advice. Rates and thresholds change, so confirm current figures before acting on a projection.',
	);
