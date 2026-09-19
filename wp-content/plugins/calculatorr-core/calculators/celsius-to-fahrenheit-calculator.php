<?php
/**
 * Celsius to Fahrenheit Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'celsius-to-fahrenheit-calculator',
		'title' => 'Celsius to Fahrenheit Calculator',
		'category' => 'convert',
		'description' => 'Convert between Celsius, Fahrenheit and Kelvin.',
		'keyword' => 'Celsius to Fahrenheit Calculator',
		'h1' => 'Celsius to Fahrenheit Calculator',
		'meta_title' => 'Celsius to Fahrenheit Calculator - Plus Kelvin',
		'meta_description' => 'Free Celsius to Fahrenheit converter. Change temperatures between Celsius, Fahrenheit and Kelvin in one step, with freezing and boiling points marked.',
		'fields' => array(
			array(
				'id' => 'temperature',
				'label' => 'Temperature',
				'type' => 'number',
				'default' => 20,
				'step' => 'any',
			),
			array(
				'id' => 'from',
				'label' => 'Measured in',
				'type' => 'segmented',
				'options' => array(
					'c' => 'Celsius',
					'f' => 'Fahrenheit',
					'k' => 'Kelvin',
				),
				'default' => 'c',
			),
		),
		'default_result' => array(
			'label' => '20°C converted',
			'value' => '68°F',
			'rows' => array(
				array(
					'label' => 'Celsius',
					'value' => '20°C',
				),
				array(
					'label' => 'Fahrenheit',
					'value' => '68°F',
				),
				array(
					'label' => 'Kelvin',
					'value' => '293.15 K',
				),
				array(
					'label' => 'Reference',
					'value' => 'liquid water range',
				),
			),
			'note' => 'The two scales meet at minus forty, which is the same temperature in Celsius and Fahrenheit and the only point where they agree.',
		),
		'explainer' => array(
			array(
				'heading' => 'How the conversion works',
				'body' => '<p>Multiply by nine fifths and add thirty two. The multiplication handles the fact that a Fahrenheit degree is smaller than a Celsius one, and the addition handles the fact that the two scales start counting from different places: water freezes at zero Celsius and at thirty two Fahrenheit.</p><p>Those two jobs are why you cannot simply scale one into the other. Every conversion between these scales is a stretch and a shift, and forgetting the shift is the single most common mistake people make.</p>',
				'formula' => '&deg;F = (&deg;C &times; 9 &divide; 5) + 32',
				'steps' => array(
					'Take the temperature in Celsius.',
					'Multiply it by 9.',
					'Divide by 5, which is the same as multiplying by 1.8.',
					'Add 32.',
				),
				'example' => '<p>A summer afternoon at 28&deg;C. 28 &times; 9 = 252. 252 &divide; 5 = 50.4. 50.4 + 32 = 82.4&deg;F. Warm, and the sort of day an American forecast would call low eighties.</p>',
			),
			array(
				'heading' => 'The temperatures worth memorising',
				'body' => '<p>You rarely need to convert an arbitrary number. What you usually want is to place a temperature you have been given somewhere on a scale you have a feel for, and a handful of anchors does that better than arithmetic. These are the ones worth carrying around.</p>',
				'table' => array(
					'caption' => 'What each temperature actually feels like',
					'head' => array(
						'Celsius',
						'Fahrenheit',
						'What it means',
					),
					'rows' => array(
						array(
							'-40&deg;C',
							'-40&deg;F',
							'The one point where both scales agree',
						),
						array(
							'0&deg;C',
							'32&deg;F',
							'Water freezes, roads ice over',
						),
						array(
							'10&deg;C',
							'50&deg;F',
							'Coat weather',
						),
						array(
							'16&deg;C',
							'61&deg;F',
							'A cool room, the edge of comfortable',
						),
						array(
							'20&deg;C',
							'68&deg;F',
							'Room temperature as most thermostats define it',
						),
						array(
							'25&deg;C',
							'77&deg;F',
							'Warm, pleasant, shirt sleeves',
						),
						array(
							'30&deg;C',
							'86&deg;F',
							'Hot, the point most people stop enjoying it',
						),
						array(
							'37&deg;C',
							'98.6&deg;F',
							'Normal body temperature',
						),
						array(
							'38&deg;C',
							'100.4&deg;F',
							'A fever, and the usual clinical threshold',
						),
						array(
							'100&deg;C',
							'212&deg;F',
							'Water boils at sea level',
						),
					),
				),
			),
			array(
				'heading' => 'The shortcut that works in your head',
				'body' => '<p>Double it and add thirty. It is not exact, but it is close enough to tell you whether to take a jacket, and you can do it while somebody is still speaking.</p><p>The error grows as the temperature does, because doubling overshoots the true factor of 1.8. At 10&deg;C the shortcut gives 50 against a true 50, which is perfect. At 20&deg;C it gives 70 against 68. At 30&deg;C it gives 90 against 86, and by 40&deg;C it is out by eight degrees. So trust it for weather and drop it for anything that matters.</p>',
				'table' => array(
					'caption' => 'How far the mental shortcut drifts',
					'head' => array(
						'Celsius',
						'Double and add 30',
						'True Fahrenheit',
						'Out by',
					),
					'rows' => array(
						array(
							'0&deg;C',
							'30&deg;F',
							'32&deg;F',
							'2&deg;',
						),
						array(
							'10&deg;C',
							'50&deg;F',
							'50&deg;F',
							'0&deg;',
						),
						array(
							'20&deg;C',
							'70&deg;F',
							'68&deg;F',
							'2&deg;',
						),
						array(
							'30&deg;C',
							'90&deg;F',
							'86&deg;F',
							'4&deg;',
						),
						array(
							'40&deg;C',
							'110&deg;F',
							'104&deg;F',
							'6&deg;',
						),
					),
				),
			),
			array(
				'heading' => 'Going the other way',
				'body' => '<p>Subtract thirty two first, then multiply by five ninths. The order matters and reversing it is the error that produces answers roughly seventeen degrees out, which is enough to be obviously wrong but not always obviously wrong enough to catch.</p><p>Undo the shift before you undo the stretch, every time. If the Fahrenheit figure is below thirty two you will get a negative number after the subtraction, which is correct and simply means the temperature is below freezing.</p>',
				'formula' => '&deg;C = (&deg;F &minus; 32) &times; 5 &divide; 9',
			),
			array(
				'heading' => 'Why a fever is 100.4 and not 100',
				'body' => '<p>The clinical threshold for fever is 38&deg;C, and 38 converts to exactly 100.4&deg;F. The awkward decimal is an artefact of conversion rather than a medical judgement: the number that means something is the Celsius one, and the Fahrenheit figure inherits its precision from it.</p><p>The same explains 98.6&deg;F for normal body temperature. It is 37&deg;C converted, and the three significant figures imply a precision the original never claimed. Normal body temperature varies by about half a degree Celsius through the day and between people, so treating 98.6 as a fixed point is reading more into it than the number supports.</p>',
			),
			array(
				'heading' => 'Where the two scales still divide the world',
				'body' => '<p>The United States, its territories, and a small number of Caribbean nations use Fahrenheit for weather and body temperature. Almost everywhere else uses Celsius, and scientific work everywhere uses Celsius or Kelvin regardless of local habit.</p><p>Ovens are the exception that catches people out, because American recipes give oven temperatures in Fahrenheit and European ones in Celsius or as a gas mark. Converting an oven temperature is the same arithmetic, but rounding to the nearest ten is fine there, since domestic ovens rarely hold a temperature closer than that anyway.</p>',
			),
			array(
				'heading' => 'What this converter does not tell you',
				'body' => '<p>It converts a number and nothing more. It does not know about wind chill, humidity or heat index, all of which change how a temperature feels by far more than a few degrees of conversion error. Thirty degrees in dry air and thirty degrees at ninety percent humidity are very different afternoons.</p><p>It also does not handle temperature differences. A change of 10&deg;C is a change of 18&deg;F, not 50&deg;F, because a difference has no zero point to shift and only needs the stretch. Convert absolute temperatures with this, and multiply differences by 1.8.</p>',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What is 25 degrees Celsius in Fahrenheit?',
				'a' => '77&deg;F. Multiply 25 by 9 to get 225, divide by 5 to get 45, then add 32. It is the temperature most people describe as pleasantly warm.',
			),
			array(
				'q' => 'Is there a temperature where Celsius and Fahrenheit are the same?',
				'a' => 'Yes, at minus forty. Minus 40&deg;C and minus 40&deg;F are the same temperature, which is the one point where the stretch and the shift cancel each other out exactly.',
			),
			array(
				'q' => 'Why is normal body temperature 98.6 degrees?',
				'a' => 'Because it is 37&deg;C converted, and 37 is itself a rounded average from nineteenth century measurements. The decimal implies a precision nobody ever claimed, and healthy body temperature varies by around half a degree Celsius through the day.',
			),
			array(
				'q' => 'How do I convert a temperature difference rather than a temperature?',
				'a' => 'Multiply by 1.8 and do not add 32. The 32 exists to align the two scales\' starting points, and a difference has no starting point to align. A 10&deg;C rise is an 18&deg;F rise.',
			),
			array(
				'q' => 'Is doubling and adding thirty accurate enough?',
				'a' => 'For deciding what to wear, yes. It is exact at 10&deg;C, two degrees out at 0 and 20, and drifts to six degrees out by 40&deg;C. Use the real formula for cooking, medicine or anything you are going to write down.',
			),
		),
		'related' => array(
			'unit-converter',
			'weight-converter',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
