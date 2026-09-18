<?php
/**
 * Board Foot Calculator.
 *
 * Part of the construction group, whose calculators share one shape:
 * a volume or area worked out from the measurements, converted into the
 * unit the supplier actually sells in, with an allowance on top.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'board-foot-calculator',
		'title' => 'Board Foot Calculator',
		'category' => 'home-diy',
		'description' => 'Work out board feet for hardwood lumber and what the total will cost.',
		'keyword' => 'Board Foot Calculator',
		'h1' => 'Board Foot Calculator',
		'meta_title' => 'Board Foot Calculator - Lumber Volume & Cost',
		'meta_description' => 'Free board foot calculator for hardwood lumber. Enter thickness, width, length and quantity for total board feet and the cost at your price per board foot.',
		'fields' => array(
			array(
				'id' => 'thickness',
				'label' => 'Thickness',
				'type' => 'number',
				'suffix' => 'in',
				'default' => 2,
				'min' => 0,
				'hint' => 'Use the nominal rough thickness, which is what the yard charges for.',
			),
			array(
				'id' => 'width',
				'label' => 'Width',
				'type' => 'number',
				'suffix' => 'in',
				'default' => 6,
				'min' => 0,
			),
			array(
				'id' => 'length',
				'label' => 'Length',
				'type' => 'number',
				'suffix' => 'ft',
				'default' => 8,
				'min' => 0,
			),
			array(
				'id' => 'quantity',
				'label' => 'How many boards',
				'type' => 'number',
				'default' => 1,
				'min' => 1,
				'step' => 1,
			),
			array(
				'id' => 'price',
				'label' => 'Price per board foot',
				'type' => 'number',
				'prefix' => '$',
				'default' => 0,
				'min' => 0,
				'hint' => 'Leave at zero if you only want the volume.',
			),
		),
		'default_result' => array(
			'label' => 'Board feet',
			'value' => '8 bd ft',
			'rows' => array(
				array(
					'label' => 'Per piece',
					'value' => '8 bd ft',
				),
				array(
					'label' => 'Pieces',
					'value' => '1',
				),
				array(
					'label' => 'Total cost',
					'value' => '$0.00',
				),
			),
			'note' => 'Board feet are measured on nominal rough thickness, so a board sold as one inch is counted as one inch even after it is planed down to three quarters.',
		),
		'explainer' => array(
			array(
				'heading' => 'What a board foot actually is',
				'body' => 'A board foot is 144 cubic inches of lumber, which is one inch thick by twelve inches wide by twelve inches long. It is a volume measure rather than a length one, which is why two boards of the same length can cost very different amounts.',
				'formula' => 'Board feet = (T&quot; &times; W&quot; &times; L ft) &divide; 12',
			),
			array(
				'heading' => 'Nominal versus actual, and why you pay for the difference',
				'body' => 'Hardwood is priced on its rough sawn dimensions, before planing. A board sold as four quarter, meaning one inch, arrives at about thirteen sixteenths after surfacing, but you pay for the full inch because that is the wood that was cut. This catches people moving from softwood, where a two by four is famously neither.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What does 4/4 mean in lumber?',
				'a' => 'Quarters of an inch of rough thickness. Four quarter is one inch, eight quarter is two inches, and so on. The convention exists because hardwood is milled in quarter inch steps and sold before it is surfaced.',
			),
			array(
				'q' => 'How is board footage different from linear feet?',
				'a' => 'Linear feet count only length, so it is useful for trim and moulding where the profile is fixed. Board feet account for thickness and width too, which is how hardwood is sold because the boards come in random widths.',
			),
			array(
				'q' => 'Do I add waste to a lumber order?',
				'a' => 'Yes, and more than you would for sheet goods. Fifteen to thirty per cent is normal for hardwood because boards arrive in random widths and lengths, and the defects have to be cut around. Figured or highly graded stock needs the higher end.',
			),
		),
		'related' => array(
			'deck-calculator',
			'stair-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
