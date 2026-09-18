<?php
/**
 * Cylinder Volume Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'cylinder-volume-calculator',
		'title' => 'Cylinder Volume Calculator',
		'category' => 'geometry',
		'description' => 'Find the volume and surface area of a cylinder.',
		'keyword' => 'Cylinder Volume Calculator',
		'h1' => 'Cylinder Volume Calculator',
		'meta_title' => 'Cylinder Volume Calculator - Tanks, Pipes and Tubes',
		'meta_description' => 'Free cylinder volume calculator. Enter a radius or diameter with the height for the volume, gallons, curved surface area and total surface area.',
		'fields' => array(
			array(
				'id' => 'measure',
				'label' => 'I know the',
				'type' => 'segmented',
				'options' => array(
					'radius' => 'Radius',
					'diameter' => 'Diameter',
				),
				'default' => 'radius',
			),
			array(
				'id' => 'radius',
				'label' => 'Radius',
				'type' => 'number',
				'default' => 3,
				'show_when' => array(
					'measure' => 'radius',
				),
			),
			array(
				'id' => 'diameter',
				'label' => 'Diameter',
				'type' => 'number',
				'default' => 6,
				'show_when' => array(
					'measure' => 'diameter',
				),
			),
			array(
				'id' => 'heightVal',
				'label' => 'Height',
				'type' => 'number',
				'default' => 10,
			),
		),
		'default_result' => array(
			'label' => 'Cylinder volume',
			'value' => '282.7433 cubic units',
			'rows' => array(
				array(
					'label' => 'Radius used',
					'value' => '3',
				),
				array(
					'label' => 'If units are inches, in gallons',
					'value' => '1.224',
				),
				array(
					'label' => 'If units are feet, in gallons',
					'value' => '2,115.07',
				),
				array(
					'label' => 'Curved surface area',
					'value' => '188.496',
				),
				array(
					'label' => 'Total surface area',
					'value' => '245.044',
				),
			),
			'note' => 'Doubling the radius quadruples the volume while doubling the height only doubles it, which is why wide tanks hold so much more than tall ones of the same material.',
		),
		'explainer' => array(
			array(
				'heading' => 'Radius dominates the answer',
				'body' => 'Volume scales with the square of the radius but only linearly with height. Doubling the radius quadruples the capacity while doubling the height merely doubles it, which is why wide squat tanks hold so much more than tall narrow ones built from the same amount of material.',
				'formula' => 'V = πr²h',
			),
			array(
				'heading' => 'Gallons from inches or from feet',
				'body' => 'The conversion differs depending on which unit you measured in: divide cubic inches by 231, or multiply cubic feet by 7.48. Both are shown so you do not have to remember which is which.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How do I find the volume of a pipe?',
				'a' => 'Use the internal radius, not the external one. Pipe is specified by nominal bore or outside diameter, and using the wrong one overstates capacity noticeably on thick-walled pipe.',
			),
			array(
				'q' => 'How many gallons in a 55 gallon drum?',
				'a' => 'About 55, unsurprisingly, but the real internal volume is usually a little higher, around 57 to 58, since drums are not filled to the brim.',
			),
		),
		'related' => array(
			'volume-calculator',
			'pool-volume-calculator',
			'circumference-calculator',
		),
		'disclaimer' => '',
	);
