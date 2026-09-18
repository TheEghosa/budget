<?php
/**
 * Ovulation Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'ovulation-calculator',
		'title' => 'Ovulation Calculator',
		'category' => 'health',
		'description' => 'Estimate your fertile window and ovulation date from your cycle.',
		'keyword' => 'Ovulation Calculator',
		'h1' => 'Ovulation Calculator',
		'meta_title' => 'Ovulation Calculator - Fertile Window and Peak Days',
		'meta_description' => 'Free ovulation calculator. Enter your last period and cycle length to estimate ovulation, the fertile window and when your next period is due.',
		'fields' => array(
			array(
				'id' => 'lmp',
				'label' => 'First day of your last period',
				'type' => 'date',
				'default' => '2026-09-01',
			),
			array(
				'id' => 'cycle',
				'label' => 'Cycle length',
				'type' => 'number',
				'suffix' => 'days',
				'default' => 28,
				'hint' => 'Count from the first day of one period to the first day of the next.',
			),
		),
		'default_result' => array(
			'label' => 'Estimated ovulation',
			'value' => 'Tue, Sep 15, 2026',
			'rows' => array(
				array(
					'label' => 'Fertile window opens',
					'value' => 'Thu, Sep 10, 2026',
				),
				array(
					'label' => 'Most fertile',
					'value' => 'Mon, Sep 14, 2026 to Tue, Sep 15, 2026',
				),
				array(
					'label' => 'Fertile window closes',
					'value' => 'Wed, Sep 16, 2026',
				),
				array(
					'label' => 'Next period expected',
					'value' => 'Tue, Sep 29, 2026',
				),
				array(
					'label' => 'Cycle length used',
					'value' => '28 days',
				),
			),
			'note' => 'An estimate from cycle arithmetic, not an observation. Real ovulation shifts by several days between cycles even in regular ones, so treat the window as wider than the dates suggest and use tracking rather than a calendar if timing matters.',
		),
		'explainer' => array(
			array(
				'heading' => 'Why ovulation is counted backwards',
				'body' => 'The second half of the cycle, from ovulation to the next period, is the stable part at roughly fourteen days for almost everyone. The first half is what varies. So ovulation is found by counting back fourteen days from the next expected period rather than forward from the last one, which is why a long cycle moves ovulation later but not by the full difference.',
				'formula' => 'Ovulation ≈ Next period − 14 days',
			),
			array(
				'heading' => 'The window is wider than the day',
				'body' => 'Sperm survive up to five days in the reproductive tract and the egg lives for about twenty-four hours, so the fertile window opens several days before ovulation and closes shortly after. That is why the days leading up to ovulation matter more than the day itself.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How accurate is a calendar estimate?',
				'a' => 'Useful as a guide and no more. Ovulation shifts by several days between cycles even in regular ones, and stress, illness, travel and training all move it. Basal temperature tracking or ovulation predictor kits observe what actually happened rather than predicting it.',
			),
			array(
				'q' => 'Can I ovulate on a different day each month?',
				'a' => 'Yes, and most people do. Cycle length varies naturally, which is why a single calculation is a starting point rather than a schedule.',
			),
		),
		'related' => array(
			'period-calculator',
			'pregnancy-calculator',
		),
		'disclaimer' => 'General population estimate, not medical advice. It cannot account for your history, medication or physiology, so treat it as a starting point for a conversation with a clinician.',
	);
