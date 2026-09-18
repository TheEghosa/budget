<?php
/**
 * CPM Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'cpm-calculator',
		'title' => 'CPM Calculator',
		'category' => 'business',
		'description' => 'Work out cost per thousand impressions, cost or reach.',
		'keyword' => 'CPM Calculator',
		'h1' => 'CPM Calculator',
		'meta_title' => 'CPM Calculator - Cost Per Thousand Impressions',
		'meta_description' => 'Free CPM calculator. Solve for CPM, campaign cost or impressions, with clicks and effective cost per click worked out from your click-through rate.',
		'fields' => array(
			array(
				'id' => 'solve',
				'label' => 'Solve for',
				'type' => 'segmented',
				'options' => array(
					'cpm' => 'CPM',
					'cost' => 'Cost',
					'impressions' => 'Impressions',
				),
				'default' => 'cpm',
			),
			array(
				'id' => 'cost',
				'label' => 'Campaign cost',
				'type' => 'number',
				'prefix' => '$',
				'default' => 2500,
				'step' => 'any',
			),
			array(
				'id' => 'impressions',
				'label' => 'Impressions',
				'type' => 'number',
				'default' => 500000,
			),
			array(
				'id' => 'cpm',
				'label' => 'CPM',
				'type' => 'number',
				'prefix' => '$',
				'default' => 5,
				'step' => 'any',
				'show_when' => array(
					'solve' => array(
						'cost',
						'impressions',
					),
				),
			),
			array(
				'id' => 'ctr',
				'label' => 'Click-through rate',
				'type' => 'number',
				'suffix' => '%',
				'default' => 0.5,
				'step' => 'any',
			),
		),
		'default_result' => array(
			'label' => 'CPM',
			'value' => '$5.00',
			'rows' => array(
				array(
					'label' => 'Cost',
					'value' => '$2,500.00',
				),
				array(
					'label' => 'Impressions',
					'value' => '500,000',
				),
				array(
					'label' => 'CPM',
					'value' => '$5.00',
				),
				array(
					'label' => 'Clicks at 0.5% CTR',
					'value' => '2,500',
				),
				array(
					'label' => 'Effective cost per click',
					'value' => '$1.00',
				),
			),
			'note' => 'CPM is cost per thousand impressions, from the Latin mille. It measures reach rather than response, so a low CPM on an audience that never converts is more expensive than a high one that does.',
		),
		'explainer' => array(
			array(
				'heading' => 'What CPM does and does not measure',
				'body' => 'CPM is the cost of a thousand impressions, from mille, the Latin for thousand. It measures how much reach you bought and says nothing at all about whether anyone acted. A cheap CPM against an audience that never converts costs more per outcome than an expensive one that does.',
				'formula' => 'CPM = cost ÷ impressions × 1,000',
			),
			array(
				'heading' => 'Following it through to cost per click',
				'body' => 'Multiplying impressions by your click-through rate gives clicks, and dividing spend by clicks gives the effective cost per click. That is a step closer to something meaningful, though the real measure is cost per acquisition, which needs your conversion rate as well.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What is a good CPM?',
				'a' => 'Entirely dependent on channel and targeting. Broad display can run under five dollars while tightly targeted professional audiences run into the tens. Compare only within the same channel and audience.',
			),
			array(
				'q' => 'Is CPM better than CPC?',
				'a' => 'CPM suits awareness campaigns where impressions are the goal. CPC suits response campaigns where you only want to pay for engagement. Which is cheaper per outcome depends on your creative and your click-through rate.',
			),
		),
		'related' => array(
			'margin-calculator',
			'percentage-calculator',
			'ebay-fee-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
