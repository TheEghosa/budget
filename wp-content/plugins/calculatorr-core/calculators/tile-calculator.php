<?php
/**
 * Tile Calculator.
 *
 * Part of the construction group, whose calculators share one shape:
 * a volume or area worked out from the measurements, converted into the
 * unit the supplier actually sells in, with an allowance on top.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'tile-calculator',
		'title' => 'Tile Calculator',
		'category' => 'home-diy',
		'description' => 'Work out how many tiles and how many boxes a floor or wall needs.',
		'keyword' => 'Tile Calculator',
		'h1' => 'Tile Calculator',
		'meta_title' => 'Tile Calculator - Tiles & Boxes Needed for a Room',
		'meta_description' => 'Free tile calculator for floors and walls. Enter the room and tile size for the number of tiles, the boxes to buy, and how many spares end up in the last box.',
		'fields' => array(
			array(
				'id' => 'length',
				'label' => 'Area length',
				'type' => 'number',
				'suffix' => 'ft',
				'default' => 12,
				'min' => 0,
			),
			array(
				'id' => 'width',
				'label' => 'Area width',
				'type' => 'number',
				'suffix' => 'ft',
				'default' => 10,
				'min' => 0,
			),
			array(
				'id' => 'tileWidth',
				'label' => 'Tile width',
				'type' => 'number',
				'suffix' => 'in',
				'default' => 12,
				'min' => 0,
			),
			array(
				'id' => 'tileHeight',
				'label' => 'Tile height',
				'type' => 'number',
				'suffix' => 'in',
				'default' => 12,
				'min' => 0,
			),
			array(
				'id' => 'perBox',
				'label' => 'Tiles per box',
				'type' => 'number',
				'default' => 10,
				'min' => 1,
				'step' => 1,
				'hint' => 'Printed on the box, and it varies by tile size.',
			),
			array(
				'id' => 'waste',
				'label' => 'Waste allowance',
				'type' => 'number',
				'suffix' => '%',
				'default' => 10,
				'min' => 0,
				'hint' => 'Ten per cent for a straight layout, fifteen for diagonal or herringbone.',
			),
		),
		'default_result' => array(
			'label' => 'Tiles to buy',
			'value' => '132 tiles',
			'rows' => array(
				array(
					'label' => 'Area to cover',
					'value' => '120 sq ft',
				),
				array(
					'label' => 'Each tile covers',
					'value' => '1 sq ft',
				),
				array(
					'label' => 'Boxes to buy',
					'value' => '14',
				),
				array(
					'label' => 'Spare tiles in the last box',
					'value' => '8',
				),
			),
			'note' => 'Ten per cent waste covers ordinary cuts. Go to fifteen for a diagonal or herringbone layout, and buy the whole job in one batch because dye lots shift between production runs.',
		),
		'explainer' => array(
			array(
				'heading' => 'Buy the whole job in one batch',
				'body' => 'Tile is fired in batches and the colour shifts measurably between them, which is why boxes carry a dye lot or shade code. Running out halfway and returning for one more box frequently means a visible band across the floor that cannot be fixed without lifting tiles. Buy everything at once, including the spares.',
				'formula' => 'Tiles = (Area &times; (1 + waste)) &divide; Area of one tile',
			),
			array(
				'heading' => 'Keep the leftovers',
				'body' => 'The spares in the last box are not waste. A cracked tile three years from now is either a five minute repair or a whole floor problem, entirely depending on whether a matching tile is in the garage. Keep at least a few, and keep them dry and flat.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How much tile waste should I allow?',
				'a' => 'Ten per cent for a straight layout in a simple rectangular room. Fifteen for a diagonal or herringbone pattern, and up to twenty for a room with many angles, alcoves or fixtures to cut around. Large format tiles need more than small ones, because each mistake costs more area.',
			),
			array(
				'q' => 'Do I need to account for grout lines?',
				'a' => 'Not meaningfully. Grout lines are a small fraction of the area and the waste allowance covers them several times over. Where they do matter is layout planning, since a sixteenth of an inch across forty tiles adds up to visible drift if the lines are not kept square.',
			),
			array(
				'q' => 'Should I buy extra for repairs?',
				'a' => 'Yes, and the last box usually provides it. A few spare tiles from the original dye lot are worth far more than the shelf space they take up, because matching tile becomes impossible once a line is discontinued.',
			),
		),
		'related' => array(
			'square-footage-calculator',
			'paint-calculator',
		),
		'disclaimer' => '',
	);
