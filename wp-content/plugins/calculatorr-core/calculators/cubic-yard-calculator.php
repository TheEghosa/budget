<?php
/**
 * Cubic Yard Calculator.
 *
 * Part of the construction group, whose calculators share one shape:
 * a volume or area worked out from the measurements, converted into the
 * unit the supplier actually sells in, with an allowance on top.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'cubic-yard-calculator',
		'title' => 'Cubic Yard Calculator',
		'category' => 'home-diy',
		'description' => 'Convert any length, width and depth into cubic yards, cubic feet and cubic metres.',
		'keyword' => 'Cubic Yard Calculator',
		'h1' => 'Cubic Yard Calculator',
		'meta_title' => 'Cubic Yard Calculator - Volume for Any Material',
		'meta_description' => 'Free cubic yard calculator. Enter length, width and depth in feet or inches to get the volume in cubic yards, cubic feet, cubic metres and cubic inches.',
		'fields' => array(
			array(
				'id' => 'length',
				'label' => 'Length',
				'type' => 'number',
				'suffix' => 'ft',
				'default' => 10,
				'min' => 0,
			),
			array(
				'id' => 'width',
				'label' => 'Width',
				'type' => 'number',
				'suffix' => 'ft',
				'default' => 10,
				'min' => 0,
			),
			array(
				'id' => 'depth',
				'label' => 'Depth',
				'type' => 'number',
				'default' => 6,
				'min' => 0,
			),
			array(
				'id' => 'depthUnit',
				'label' => 'Depth is in',
				'type' => 'segmented',
				'options' => array(
					'inches' => 'Inches',
					'feet' => 'Feet',
				),
				'default' => 'inches',
			),
			array(
				'id' => 'waste',
				'label' => 'Extra allowance',
				'type' => 'number',
				'suffix' => '%',
				'default' => 0,
				'min' => 0,
			),
		),
		'default_result' => array(
			'label' => 'Volume',
			'value' => '1.85 cubic yards',
			'rows' => array(
				array(
					'label' => 'Cubic feet',
					'value' => '50',
				),
				array(
					'label' => 'Cubic metres',
					'value' => '1.42',
				),
				array(
					'label' => 'Cubic inches',
					'value' => '86,400',
				),
			),
			'note' => 'One cubic yard is 27 cubic feet, which is the conversion most order mistakes come down to.',
		),
		'explainer' => array(
			array(
				'heading' => 'The conversion nearly everyone gets wrong',
				'body' => 'A cubic yard is not three cubic feet, it is twenty-seven, because all three dimensions are being tripled at once. That single mistake is behind most under-ordered loads of concrete, soil and stone, and it is why this calculator shows cubic feet alongside cubic yards rather than only the answer you asked for.',
				'formula' => '1 cubic yard = 3ft &times; 3ft &times; 3ft = 27 cubic feet',
			),
			array(
				'heading' => 'Depth in inches, everything else in feet',
				'body' => 'Bulk materials are almost always spread in a layer measured in inches over an area measured in feet, so the depth field takes either unit and converts for you. Mixing the two without converting is the other common source of a wrong order.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How many cubic feet are in a cubic yard?',
				'a' => 'Twenty-seven. Three feet in each of three dimensions, so three cubed.',
			),
			array(
				'q' => 'How much does a cubic yard weigh?',
				'a' => 'It depends entirely on the material. Concrete is about two tons, gravel around 1.4, topsoil about 1.1 and mulch closer to half a ton. Weight is never a safe proxy for volume across different materials.',
			),
			array(
				'q' => 'Will a cubic yard fit in my truck?',
				'a' => 'A full size pickup bed holds roughly two to two and a half cubic yards by volume, but weight is the real limit. A single cubic yard of wet soil or gravel can exceed the payload rating of a half ton truck, so check the sticker on the door frame rather than eyeballing the space.',
			),
		),
		'related' => array(
			'concrete-calculator',
			'gravel-calculator',
			'topsoil-calculator',
		),
		'disclaimer' => '',
	);
