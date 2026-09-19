<?php
/**
 * Feet to Meters Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'feet-to-meters-calculator',
		'title' => 'Feet to Meters Calculator',
		'category' => 'convert',
		'description' => 'Convert feet to metres and back.',
		'keyword' => 'Feet to Meters Calculator',
		'h1' => 'Feet to Meters Calculator',
		'meta_title' => 'Feet to Meters Calculator - Exact Conversion',
		'meta_description' => 'Free feet to metres converter. Change feet into metres using the exact definition, with centimetres and inches shown alongside for reference.',
		'fields' => array(
			array(
				'id' => 'value',
				'label' => 'Feet',
				'type' => 'number',
				'default' => 6,
				'step' => 'any',
			),
		),
		'default_result' => array(
			'label' => '6 ft in m',
			'value' => '1.8288 m',
			'rows' => array(
				array(
					'label' => 'Centimetres',
					'value' => '182.88',
				),
				array(
					'label' => 'Metres',
					'value' => '1.8288',
				),
				array(
					'label' => 'Inches',
					'value' => '72',
				),
				array(
					'label' => 'Feet',
					'value' => '6',
				),
			),
			'note' => 'A foot is exactly 0.3048 metres by definition, so three feet is a little under a metre and the two are close enough that people often confuse them.',
		),
		'explainer' => array(
			array(
				'heading' => 'How feet convert to metres',
				'body' => '<p>Multiply by 0.3048. Like the inch, the foot is defined against the metric system rather than measured against it, so the number is exact: since 1959 an international foot has been exactly 0.3048 metres. Nothing is approximated in the conversion, which means the only error in your answer is whatever was already in your measurement.</p><p>Going back the other way, divide by 0.3048, or multiply by 3.28084 if you prefer. Both give the same answer and the first is easier to remember, since it is the same number you already know.</p>',
				'formula' => 'Metres = Feet &times; 0.3048',
				'steps' => array(
					'Take the measurement in feet.',
					'If it includes inches, divide the inches by 12 and add them on as a decimal.',
					'Multiply the total by 0.3048.',
				),
				'example' => '<p>A room is 14 feet 6 inches long. Six inches is 6 &divide; 12 = 0.5 feet, so the length is 14.5 feet. 14.5 &times; 0.3048 = 4.42 metres.</p>',
			),
			array(
				'heading' => 'The measurements you actually meet',
				'body' => '<p>Most feet-to-metres questions come from a handful of familiar heights and lengths rather than arbitrary numbers, so it is worth having the common ones to hand. Ceiling heights, door openings, room dimensions and human heights make up almost all of it.</p>',
				'table' => array(
					'caption' => 'Common lengths in both units',
					'head' => array(
						'Feet',
						'Metres',
						'What it usually is',
					),
					'rows' => array(
						array(
							'3 ft',
							'0.91 m',
							'Roughly a yard, a desk height',
						),
						array(
							'5 ft',
							'1.52 m',
							'A short adult',
						),
						array(
							'6 ft',
							'1.83 m',
							'A tall adult, a standard fence panel',
						),
						array(
							'8 ft',
							'2.44 m',
							'A standard ceiling, a sheet of plywood',
						),
						array(
							'10 ft',
							'3.05 m',
							'A generous ceiling, a small room',
						),
						array(
							'12 ft',
							'3.66 m',
							'A single garage width',
						),
						array(
							'16 ft',
							'4.88 m',
							'A double garage width',
						),
						array(
							'20 ft',
							'6.10 m',
							'A shipping container, a long room',
						),
						array(
							'100 ft',
							'30.48 m',
							'A short street frontage',
						),
					),
				),
			),
			array(
				'heading' => 'Feet and inches together',
				'body' => '<p>Heights and room dimensions are almost never whole feet, and the mixed unit is where the arithmetic goes wrong. The inches have to become a decimal fraction of a foot before anything is multiplied, because twelve inches make a foot and not ten.</p><p>Five foot nine is not 5.9 feet. It is 5 plus 9&divide;12, which is 5.75 feet, and the difference between those two readings is about four and a half centimetres. That is the gap between a correct height and one that would be noticed.</p>',
				'table' => array(
					'caption' => 'Heights in feet and inches, converted',
					'head' => array(
						'Feet and inches',
						'Decimal feet',
						'Metres',
						'Centimetres',
					),
					'rows' => array(
						array(
							'5 ft 0 in',
							'5.00',
							'1.524 m',
							'152 cm',
						),
						array(
							'5 ft 4 in',
							'5.33',
							'1.626 m',
							'163 cm',
						),
						array(
							'5 ft 6 in',
							'5.50',
							'1.676 m',
							'168 cm',
						),
						array(
							'5 ft 8 in',
							'5.67',
							'1.727 m',
							'173 cm',
						),
						array(
							'5 ft 10 in',
							'5.83',
							'1.778 m',
							'178 cm',
						),
						array(
							'6 ft 0 in',
							'6.00',
							'1.829 m',
							'183 cm',
						),
						array(
							'6 ft 2 in',
							'6.17',
							'1.880 m',
							'188 cm',
						),
						array(
							'6 ft 4 in',
							'6.33',
							'1.930 m',
							'193 cm',
						),
					),
				),
			),
			array(
				'heading' => 'The estimate that works in your head',
				'body' => '<p>Divide by three and add a little. Three feet is 0.91 metres, so dividing by three undershoots by about nine percent, and adding roughly a tenth back closes most of the gap.</p><p>For 20 feet: 20 &divide; 3 is 6.67, add a tenth of that and you get 7.3 against a true 6.1. That overshoots, which shows the limit of the trick. It works well up to about ten feet and drifts after that, so use it for a doorway and not for a garden.</p>',
			),
			array(
				'heading' => 'Where each unit is still the working one',
				'body' => '<p>Aviation is the clearest survival. Altitudes are given in feet almost everywhere in the world, including in countries that are metric in every other respect, because air traffic control standardised early and the cost of changing mid-system is measured in lives rather than money.</p><p>Construction in the United States runs in feet and inches, and sheet materials are still sold as four by eight. Britain is genuinely mixed: road signs and personal heights are imperial, building drawings are metric, and a builder will quote you a room in metres while describing their own height in feet without noticing the switch.</p>',
			),
			array(
				'heading' => 'What this converter will not catch',
				'body' => '<p>It converts a length, so it cannot tell you whether you measured the right one. For anything fitted into an existing opening, measure the opening in three places rather than the object that came out of it, because walls are rarely parallel and the narrowest point is the one that matters.</p><p>It also handles length only. Square feet to square metres divides by 10.7639 rather than multiplying by 0.3048, because area scales with the square of the ratio. Cubic feet to cubic metres divides by 35.3147 for the same reason, and using the length factor on an area is an error of roughly three and a half times.</p>',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How many metres is 6 feet?',
				'a' => '1.829 metres, usually written as 1.83 m or given as 183 cm. Multiply 6 by 0.3048, which is exact rather than rounded, since the foot has been defined against the metre since 1959.',
			),
			array(
				'q' => 'How do I convert 5 foot 9 into metres?',
				'a' => 'Turn the inches into a decimal first. Nine inches is 9 divided by 12, which is 0.75, so the height is 5.75 feet. Multiplied by 0.3048 that gives 1.75 metres, or 175 cm.',
			),
			array(
				'q' => 'Why do aeroplanes still use feet for altitude?',
				'a' => 'Because air traffic control standardised on feet before most of the world went metric, and changing a system where separation between aircraft is measured in those units carries a risk that nobody has judged worth taking. Most countries that are metric in every other respect still fly in feet.',
			),
			array(
				'q' => 'Is 0.3048 exact or rounded?',
				'a' => 'Exact. The international agreement of 1959 defined the foot as precisely 0.3048 metres, so the conversion adds no error of its own.',
			),
			array(
				'q' => 'How do I convert square feet to square metres?',
				'a' => 'Divide by 10.7639, not by 3.28. Area scales with the square of the length ratio, so using the length factor on an area leaves you out by a factor of about three and a half.',
			),
		),
		'related' => array(
			'unit-converter',
			'inches-to-feet-calculator',
			'mm-to-inches-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
