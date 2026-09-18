<?php
/**
 * Stair Calculator.
 *
 * Part of the construction group, whose calculators share one shape:
 * a volume or area worked out from the measurements, converted into the
 * unit the supplier actually sells in, with an allowance on top.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'stair-calculator',
		'title' => 'Stair Calculator',
		'category' => 'home-diy',
		'description' => 'Work out riser height, tread depth, total run and stringer length from a total rise.',
		'keyword' => 'Stair Calculator',
		'h1' => 'Stair Calculator',
		'meta_title' => 'Stair Calculator - Risers, Treads & Stringer Length',
		'meta_description' => 'Free stair calculator. Enter total rise for the number of steps, exact riser height, total run and stringer length, plus a check against code limits.',
		'fields' => array(
			array(
				'id' => 'totalRise',
				'label' => 'Total rise',
				'type' => 'number',
				'suffix' => 'in',
				'default' => 108,
				'min' => 0,
				'hint' => 'Finished floor to finished floor, not floor to subfloor.',
			),
			array(
				'id' => 'targetRiser',
				'label' => 'Preferred riser height',
				'type' => 'number',
				'suffix' => 'in',
				'default' => 7,
				'min' => 0,
				'hint' => 'Seven inches is comfortable. The calculator adjusts to divide the rise evenly.',
			),
			array(
				'id' => 'tread',
				'label' => 'Tread depth',
				'type' => 'number',
				'suffix' => 'in',
				'default' => 10,
				'min' => 0,
			),
		),
		'default_result' => array(
			'label' => 'Steps needed',
			'value' => '15 risers',
			'rows' => array(
				array(
					'label' => 'Riser height',
					'value' => '7.2 in',
				),
				array(
					'label' => 'Tread depth',
					'value' => '10 in',
				),
				array(
					'label' => 'Total run',
					'value' => '140 in',
				),
				array(
					'label' => 'Stringer length',
					'value' => '176.82 in',
				),
				array(
					'label' => 'Rule of 25 check',
					'value' => '24.4 in',
				),
			),
			'note' => 'Within typical residential limits, though local code always wins over any rule of thumb. Two risers plus one tread should land between 24 and 25 inches.',
		),
		'explainer' => array(
			array(
				'heading' => 'The rise divides evenly or not at all',
				'body' => 'Every riser in a flight must be the same height to within about three eighths of an inch, because the foot learns the rhythm on the first step and stops looking after that. An uneven riser, especially the last one, is the single most common cause of a fall on stairs. So the total rise is divided by the nearest whole number of steps and the riser height comes out of that division, rather than the other way round.',
				'formula' => 'Riser = Total rise &divide; Number of risers',
			),
			array(
				'heading' => 'One fewer tread than risers',
				'body' => 'The top tread is the landing itself, so a flight with fifteen risers has fourteen treads. Forgetting this is why stair runs are routinely calculated a full tread too long, which matters when the space at the bottom is tight.',
			),
			array(
				'heading' => 'The rule of 25',
				'body' => 'Two risers plus one tread should land between 24 and 25 inches. It encodes the relationship between stride length and step height, and a flight that breaks it feels wrong to walk even when every individual dimension passes code. Check it before cutting the stringers.',
				'formula' => '(2 &times; Riser) + Tread = 24 to 25 in',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What is the maximum riser height?',
				'a' => 'Most residential codes in the United States cap it at 7.75 inches, with a minimum tread depth of 10 inches. Commercial limits are tighter. Your local code is what governs, and inspectors do measure.',
			),
			array(
				'q' => 'How do I measure total rise correctly?',
				'a' => 'From finished floor to finished floor, including whatever flooring will eventually go down at both ends. Measuring to bare subfloor and then laying three quarter inch hardwood at the top changes every riser in the flight.',
			),
			array(
				'q' => 'How long a stringer board do I need?',
				'a' => 'The stringer length shown here is the diagonal, so buy a board longer than that figure to leave room for the cuts and the connection at each end. Two feet of extra length is the usual allowance.',
			),
		),
		'related' => array(
			'deck-calculator',
			'board-foot-calculator',
		),
		'disclaimer' => 'Check every dimension against your local building code before cutting. Code takes precedence over any rule of thumb shown here.',
		'sources' => array(),
	);
