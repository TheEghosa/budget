<?php
/**
 * Deck Calculator.
 *
 * Part of the construction group, whose calculators share one shape:
 * a volume or area worked out from the measurements, converted into the
 * unit the supplier actually sells in, with an allowance on top.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'deck-calculator',
		'title' => 'Deck Calculator',
		'category' => 'home-diy',
		'description' => 'Work out decking boards, joists and area for a rectangular deck.',
		'keyword' => 'Deck Calculator',
		'h1' => 'Deck Calculator',
		'meta_title' => 'Deck Calculator - Boards, Joists & Materials',
		'meta_description' => 'Free deck calculator. Enter the deck size, board width and joist spacing for the linear feet of decking, number of board rows and joist count you will need.',
		'fields' => array(
			array(
				'id' => 'length',
				'label' => 'Deck length',
				'type' => 'number',
				'suffix' => 'ft',
				'default' => 16,
				'min' => 0,
				'hint' => 'The direction the boards run.',
			),
			array(
				'id' => 'width',
				'label' => 'Deck width',
				'type' => 'number',
				'suffix' => 'ft',
				'default' => 12,
				'min' => 0,
			),
			array(
				'id' => 'boardWidth',
				'label' => 'Board width',
				'type' => 'number',
				'suffix' => 'in',
				'default' => 5.5,
				'min' => 0,
				'hint' => 'A nominal 2x6 actually measures 5.5 inches.',
			),
			array(
				'id' => 'gap',
				'label' => 'Gap between boards',
				'type' => 'number',
				'suffix' => 'in',
				'default' => 0.25,
				'min' => 0,
			),
			array(
				'id' => 'joistSpacing',
				'label' => 'Joist spacing',
				'type' => 'segmented',
				'options' => array(
					'12' => '12in',
					'16' => '16in',
					'24' => '24in',
				),
				'default' => '16',
			),
			array(
				'id' => 'waste',
				'label' => 'Waste allowance',
				'type' => 'number',
				'suffix' => '%',
				'default' => 10,
				'min' => 0,
			),
		),
		'default_result' => array(
			'label' => 'Decking to buy',
			'value' => '458 linear ft',
			'rows' => array(
				array(
					'label' => 'Deck area',
					'value' => '192 sq ft',
				),
				array(
					'label' => 'Board rows',
					'value' => '26',
				),
				array(
					'label' => 'Joists at 16in centres',
					'value' => '13',
				),
				array(
					'label' => 'If buying 16ft boards',
					'value' => '29 boards',
				),
			),
			'note' => 'Joist count assumes a simple rectangular frame and excludes rim joists, blocking and stairs, which are worth adding before you order.',
		),
		'explainer' => array(
			array(
				'heading' => 'Boards are counted by rows, not by area',
				'body' => 'Decking is bought in linear feet, so the useful number is how many rows of board fit across the width and how long each row has to be. Dividing the deck area by the area of one board gives a figure that ignores the gaps and the fact that boards come in fixed lengths, which is why it always comes out short.',
				'formula' => 'Rows = Deck width in inches &divide; (Board width + Gap)',
			),
			array(
				'heading' => 'Why the gap is not optional',
				'body' => 'Wood moves with moisture, and composite moves with temperature. A quarter inch gap lets boards expand without buckling and lets water and debris fall through instead of sitting on the joists and rotting them. Pressure treated lumber is often installed wet and shrinks, so it is sometimes laid tighter in the knowledge that it will open up.',
			),
			array(
				'heading' => 'What this deliberately leaves out',
				'body' => 'The joist count here covers the field joists on a simple rectangle. It does not include rim joists, beams, posts, footings, blocking, stair stringers or fasteners, all of which need adding before you place an order. A deck is a structure rather than a surface, and the framing below is where the code requirements live.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What spacing should deck joists be?',
				'a' => 'Sixteen inches on centre is standard for most decking laid perpendicular to the joists. Twelve inches is required for boards laid diagonally and for many composite products, which sag more than wood. Always check the decking manufacturer’s own span table, because it overrides the general rule.',
			),
			array(
				'q' => 'How much decking waste should I allow?',
				'a' => 'Ten per cent for a plain rectangular deck laid straight. Fifteen to twenty for diagonal or herringbone patterns, or for a deck with angles and cutouts, because every direction change creates an offcut too short to reuse.',
			),
			array(
				'q' => 'Do I need a permit for a deck?',
				'a' => 'Almost always, once it is attached to the house or raised above a modest height. The threshold varies by jurisdiction and an unpermitted deck can block a house sale years later, so check before building rather than after.',
			),
		),
		'related' => array(
			'board-foot-calculator',
			'stair-calculator',
			'square-footage-calculator',
		),
		'disclaimer' => 'Covers decking and field joists only. Beams, posts, footings, blocking and fasteners must be sized to local code.',
		'sources' => array(),
	);
