<?php
/**
 * Volume Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'volume-calculator',
		'title' => 'Volume Calculator',
		'category' => 'geometry',
		'description' => 'Find the volume of a box, cylinder, sphere, cone or pyramid.',
		'keyword' => 'Volume Calculator',
		'h1' => 'Volume Calculator',
		'meta_title' => 'Volume Calculator - Box, Cylinder, Sphere and Cone',
		'meta_description' => 'Free volume calculator for boxes, cylinders, spheres, cones and pyramids. Get cubic units plus conversions to cubic yards, cubic feet and gallons.',
		'fields' => array(
			array(
				'id' => 'shape',
				'label' => 'Shape',
				'type' => 'segmented',
				'options' => array(
					'box' => 'Box',
					'cylinder' => 'Cylinder',
					'sphere' => 'Sphere',
					'cone' => 'Cone',
					'pyramid' => 'Pyramid',
				),
				'default' => 'box',
			),
			array(
				'id' => 'length',
				'label' => 'Length',
				'type' => 'number',
				'default' => 4,
				'show_when' => array(
					'shape' => array(
						'box',
						'pyramid',
					),
				),
			),
			array(
				'id' => 'width',
				'label' => 'Width',
				'type' => 'number',
				'default' => 3,
				'show_when' => array(
					'shape' => array(
						'box',
						'pyramid',
					),
				),
			),
			array(
				'id' => 'radius',
				'label' => 'Radius',
				'type' => 'number',
				'default' => 3,
				'show_when' => array(
					'shape' => array(
						'cylinder',
						'sphere',
						'cone',
					),
				),
			),
			array(
				'id' => 'heightVal',
				'label' => 'Height',
				'type' => 'number',
				'default' => 5,
				'show_when' => array(
					'shape' => array(
						'box',
						'cylinder',
						'cone',
						'pyramid',
					),
				),
			),
		),
		'default_result' => array(
			'label' => 'Box volume',
			'value' => '60 cubic units',
			'rows' => array(
				array(
					'label' => 'If units are feet',
					'value' => '60 cu ft',
				),
				array(
					'label' => 'That in cubic yards',
					'value' => '2.2222',
				),
				array(
					'label' => 'If units are inches, in cubic feet',
					'value' => '0.03472',
				),
				array(
					'label' => 'If feet, in US gallons',
					'value' => '448.83',
				),
			),
			'note' => 'A cone and a pyramid are each exactly one third of the box or cylinder that would enclose them, which is worth remembering as a sanity check.',
		),
		'explainer' => array(
			array(
				'heading' => 'The one third rule',
				'body' => 'A cone holds exactly one third of the cylinder that would enclose it, and a pyramid one third of its box. That is not an approximation, it is exact, and it makes a useful sanity check: if your cone comes out anywhere near the cylinder’s volume, something has gone wrong.',
				'formula' => 'Cone = ⅓πr²h, Sphere = ⁴⁄₃πr³',
			),
			array(
				'heading' => 'Radius, not diameter',
				'body' => 'Every circular formula takes the radius. Feeding in the diameter by mistake overstates a cylinder’s volume by a factor of four and a sphere’s by eight, which is the single most common error in this group.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How do I convert cubic feet to gallons?',
				'a' => 'Multiply by 7.48. A cubic foot holds just under seven and a half US gallons.',
			),
			array(
				'q' => 'What is the volume of a sphere?',
				'a' => 'Four thirds pi r cubed. Doubling the radius makes it eight times larger, which is why a slightly bigger ball holds far more than it looks.',
			),
		),
		'related' => array(
			'cubic-feet-calculator',
			'cylinder-volume-calculator',
			'cubic-yard-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
