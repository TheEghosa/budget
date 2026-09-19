<?php
/**
 * Circumference Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'circumference-calculator',
		'title' => 'Circumference Calculator',
		'category' => 'geometry',
		'description' => 'Find the circumference of a circle from any known measurement.',
		'keyword' => 'Circumference Calculator',
		'h1' => 'Circumference Calculator',
		'meta_title' => 'Circumference Calculator - From Radius, Diameter or Area',
		'meta_description' => 'Free circumference calculator. Start from the radius, diameter, circumference or area of a circle and get all four, plus the quarter arc length.',
		'fields' => array(
			array(
				'id' => 'from',
				'label' => 'I know the',
				'type' => 'segmented',
				'options' => array(
					'radius' => 'Radius',
					'diameter' => 'Diameter',
					'circumference' => 'Circumference',
					'area' => 'Area',
				),
				'default' => 'radius',
			),
			array(
				'id' => 'value',
				'label' => 'Value',
				'type' => 'number',
				'default' => 5,
				'step' => 'any',
			),
		),
		'default_result' => array(
			'label' => 'Circumference',
			'value' => '31.415927',
			'rows' => array(
				array(
					'label' => 'Radius',
					'value' => '5',
				),
				array(
					'label' => 'Diameter',
					'value' => '10',
				),
				array(
					'label' => 'Area',
					'value' => '78.539816',
				),
				array(
					'label' => 'Quarter turn along the edge',
					'value' => '7.853982',
				),
			),
			'note' => 'Every circle has the same ratio of circumference to diameter, and that ratio is pi. It is why one measurement of a circle gives you all the others.',
		),
		'explainer' => array(
			array(
				'heading' => 'One measurement gives you all the others',
				'body' => '<p>Every circle has the same ratio of circumference to diameter, and that ratio is pi. Because the constant never changes, any single measurement of a circle determines every other one, which is why you can measure the easiest thing to reach and calculate the rest.</p><p>That matters more in practice than it sounds. You can rarely measure the diameter of a tree or a pipe in the ground, but you can almost always get a tape around it, and one wrap gives you the diameter, the radius and the area without touching anything else.</p>',
				'formula' => 'Circumference = 2 &times; &pi; &times; radius = &pi; &times; diameter',
				'steps' => array(
					'Measure whichever dimension you can reach.',
					'If you have the radius, double it to get the diameter.',
					'Multiply the diameter by pi, which is 3.14159.',
					'To go backwards, divide the circumference by pi to get the diameter.',
				),
				'example' => '<p>A tree measures 145 cm around. 145 &divide; 3.14159 = 46.2 cm across, so its radius is 23.1 cm.</p>',
			),
			array(
				'heading' => 'The relationships, in one table',
				'body' => '<p>Four measurements describe a circle and each one determines the rest. The table converts between them so you can start from whichever you have rather than the one a formula assumes.</p>',
				'table' => array(
					'caption' => 'Start from whatever you can actually measure',
					'head' => array(
						'If you know',
						'Diameter is',
						'Circumference is',
						'Area is',
					),
					'rows' => array(
						array(
							'Radius r',
							'2r',
							'2&pi;r',
							'&pi;r&sup2;',
						),
						array(
							'Diameter d',
							'd',
							'&pi;d',
							'&pi;d&sup2; &divide; 4',
						),
						array(
							'Circumference C',
							'C &divide; &pi;',
							'C',
							'C&sup2; &divide; 4&pi;',
						),
						array(
							'Area A',
							'2&radic;(A &divide; &pi;)',
							'2&radic;(&pi;A)',
							'A',
						),
					),
				),
			),
			array(
				'heading' => 'Common circles worked out',
				'body' => '<p>Pipe, tube and container sizes repeat constantly, and the same few diameters come up in plumbing, gardening and cooking. These are the ones worth recognising rather than recalculating.</p>',
				'table' => array(
					'caption' => 'Everyday circles and the tape length around them',
					'head' => array(
						'Diameter',
						'Radius',
						'Circumference',
						'Area',
					),
					'rows' => array(
						array(
							'10 cm',
							'5 cm',
							'31.4 cm',
							'78.5 cm&sup2;',
						),
						array(
							'15 cm',
							'7.5 cm',
							'47.1 cm',
							'176.7 cm&sup2;',
						),
						array(
							'20 cm',
							'10 cm',
							'62.8 cm',
							'314.2 cm&sup2;',
						),
						array(
							'30 cm',
							'15 cm',
							'94.2 cm',
							'706.9 cm&sup2;',
						),
						array(
							'50 cm',
							'25 cm',
							'157.1 cm',
							'1,963 cm&sup2;',
						),
						array(
							'1 m',
							'50 cm',
							'3.14 m',
							'0.785 m&sup2;',
						),
						array(
							'2 m',
							'1 m',
							'6.28 m',
							'3.14 m&sup2;',
						),
					),
				),
			),
			array(
				'heading' => 'Why the area is not simply double',
				'body' => '<p>Doubling a circle\'s diameter doubles its circumference and quadruples its area, because circumference grows with the radius while area grows with the radius squared. This catches people out whenever a decision involves both.</p><p>A 30 cm pizza has four times the food of a 15 cm one, not twice, which is usually the better buy by some margin. A pipe of twice the bore carries roughly four times the flow. The fence around a field doubles while the grazing quadruples. Once you notice the pattern it changes how you read almost every circular thing you buy.</p>',
			),
			array(
				'heading' => 'How much pi you need',
				'body' => '<p>3.14 is enough for anything you are cutting by hand. 3.14159 is enough for engineering. Beyond that you are adding digits that no physical measurement can support, because the tape or the caliper you used introduced more uncertainty than the fifth decimal of pi ever will.</p><p>Using 22/7 is fine for mental arithmetic and is out by about a twentieth of a percent, which on a metre of circumference is half a millimetre. Fine for a rough cut, not fine for a gasket.</p>',
			),
			array(
				'heading' => 'What this does not cover',
				'body' => '<p>It assumes a true circle, and most real things are slightly oval. A pipe that has been leaned on, a tree that grew against a wall, a lid that has been dropped: measure in two directions at ninety degrees and average them, because one measurement of an oval tells you about that one direction only.</p><p>It also measures the outside of what you wrapped. On a pipe that means the outer diameter, and plumbing sizes are usually quoted as the bore, which is the inside. The wall thickness sits between the two, so a pipe sold as 15 mm will not measure 15 mm around the outside with a tape.</p>',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How do I find the diameter from the circumference?',
				'a' => 'Divide by pi. A tree measuring 145 cm around has a diameter of 145 divided by 3.14159, which is 46.2 cm. It is the most useful version of the formula, because the circumference is usually the only dimension you can actually reach.',
			),
			array(
				'q' => 'What is the circumference of a circle with a radius of 10 cm?',
				'a' => '62.8 cm. Multiply the radius by 2 to get a 20 cm diameter, then multiply by pi. The area of the same circle is 314 square centimetres.',
			),
			array(
				'q' => 'Why is a 30 cm pizza more than twice a 15 cm one?',
				'a' => 'Because area grows with the square of the radius. Doubling the diameter quadruples the area, so a 30 cm pizza carries four times the food of a 15 cm one while usually costing nowhere near four times as much.',
			),
			array(
				'q' => 'How many decimal places of pi do I need?',
				'a' => 'Two for hand cutting, five for engineering. Past that you are adding precision the measurement cannot support, since the tape you used will be less accurate than the fifth decimal of pi.',
			),
			array(
				'q' => 'Does this work for pipes?',
				'a' => 'For the outside, yes. Plumbing sizes are normally quoted as the internal bore, so a pipe sold as 15 mm has an outer diameter larger than that by twice the wall thickness, and a tape measures the outside.',
			),
		),
		'related' => array(
			'area-calculator',
			'cylinder-volume-calculator',
			'tire-size-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
