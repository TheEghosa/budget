<?php
/**
 * MM to Inches Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'mm-to-inches-calculator',
		'title' => 'MM to Inches Calculator',
		'category' => 'convert',
		'description' => 'Convert millimetres to inches and back.',
		'keyword' => 'MM to Inches Calculator',
		'h1' => 'MM to Inches Calculator',
		'meta_title' => 'MM to Inches Calculator - Millimetres to Inches',
		'meta_description' => 'Free millimetre to inch converter. Change mm to inches with the centimetre, metre and feet equivalents shown alongside for quick reference.',
		'fields' => array(
			array(
				'id' => 'value',
				'label' => 'Millimetres',
				'type' => 'number',
				'default' => 100,
				'step' => 'any',
			),
		),
		'default_result' => array(
			'label' => '100 mm in in',
			'value' => '3.93701 in',
			'rows' => array(
				array(
					'label' => 'Centimetres',
					'value' => '10',
				),
				array(
					'label' => 'Metres',
					'value' => '0.1',
				),
				array(
					'label' => 'Inches',
					'value' => '3.93701',
				),
				array(
					'label' => 'Feet',
					'value' => '0.32808',
				),
			),
			'note' => 'Divide millimetres by 25.4 to get inches. For a quick mental check, 25 mm is almost exactly an inch.',
		),
		'explainer' => array(
			array(
				'heading' => 'How millimetres convert to inches',
				'body' => '<p>Divide by 25.4. That is the whole conversion, and the number is exact rather than rounded: since 1959 an inch has been <em>defined</em> as 25.4 millimetres, so the two units are tied together by agreement rather than by measurement. Nothing is lost in the conversion itself, which means any error in your answer came from the measurement you started with or from rounding too early.</p><p>Going the other way, multiply by 25.4. It is worth committing that single number to memory, because it turns every mm-to-inch problem on a workbench into arithmetic you can do without reaching for anything.</p>',
				'formula' => 'Inches = Millimetres &divide; 25.4',
				'steps' => array(
					'Take the measurement in millimetres.',
					'Divide by 25.4 to get inches as a decimal.',
					'If you need a fraction, multiply the decimal part by 16 and round to the nearest whole number for sixteenths.',
					'Simplify the fraction if it halves cleanly: 8/16 is a half, 12/16 is three quarters.',
				),
				'example' => '<p>A shelf is 450 mm deep. 450 &divide; 25.4 = 17.717 inches. The decimal part, 0.717, multiplied by 16 gives 11.5, which rounds to 12 sixteenths, or three quarters. So the shelf is 17 and three quarter inches, and a tape measure will agree with you.</p>',
			),
			array(
				'heading' => 'Millimetres to inches at a glance',
				'body' => '<p>Most of the millimetre measurements people meet are round numbers, and the same handful come up again and again. The table below covers them, with the nearest common fraction alongside the decimal, because a tape measure is marked in fractions and a caliper is marked in decimals.</p>',
				'table' => array(
					'caption' => 'The conversions worth knowing without a calculator',
					'head' => array(
						'Millimetres',
						'Inches (decimal)',
						'Nearest fraction',
						'Where you meet it',
					),
					'rows' => array(
						array(
							'1 mm',
							'0.039"',
							'1/32"',
							'Sheet metal, wire gauges',
						),
						array(
							'3 mm',
							'0.118"',
							'1/8"',
							'Plywood underlay, acrylic sheet',
						),
						array(
							'6 mm',
							'0.236"',
							'1/4"',
							'Drill bits, dowels, glass',
						),
						array(
							'10 mm',
							'0.394"',
							'3/8"',
							'Bolt heads, spanner sizes',
						),
						array(
							'13 mm',
							'0.512"',
							'1/2"',
							'Plasterboard, pipe bore',
						),
						array(
							'19 mm',
							'0.748"',
							'3/4"',
							'Timber thickness, scaffold board',
						),
						array(
							'25 mm',
							'0.984"',
							'1"',
							'One inch, near enough for most jobs',
						),
						array(
							'50 mm',
							'1.969"',
							'2"',
							'Stud width, insulation',
						),
						array(
							'100 mm',
							'3.937"',
							'3 15/16"',
							'Brick height, block depth',
						),
						array(
							'300 mm',
							'11.811"',
							'11 13/16"',
							'Roughly a foot, but not exactly',
						),
					),
				),
			),
			array(
				'heading' => 'Why 25 mm is not quite an inch',
				'body' => '<p>The last two rows of that table are where people get caught. 25 mm is 0.984 of an inch, and 300 mm is 11.81 inches rather than 12. Both are close enough to be used as shorthand and far enough off to matter when they stack up.</p><p>Over a single measurement the gap is under half a millimetre and nobody notices. Over ten, the 300 mm approximation has cost you nearly two inches, which is the difference between a run of shelving that fits an alcove and one that does not. So use the round numbers when you are talking, and use 25.4 when you are cutting.</p>',
			),
			array(
				'heading' => 'Reading the answer on a tape measure',
				'body' => '<p>A decimal answer is useless at the point where you actually make the mark, because an imperial tape is divided into halves, quarters, eighths and sixteenths. Converting the decimal into sixteenths is the step that turns the number into something you can use.</p><p>Multiply the part after the decimal point by 16, and round to the nearest whole number. That whole number is your sixteenths. The table below saves you doing it for the values that come up most.</p>',
				'table' => array(
					'caption' => 'Turning a decimal inch into a mark on the tape',
					'head' => array(
						'Decimal',
						'Sixteenths',
						'Fraction',
						'Millimetres',
					),
					'rows' => array(
						array(
							'0.0625',
							'1',
							'1/16"',
							'1.59 mm',
						),
						array(
							'0.125',
							'2',
							'1/8"',
							'3.18 mm',
						),
						array(
							'0.1875',
							'3',
							'3/16"',
							'4.76 mm',
						),
						array(
							'0.25',
							'4',
							'1/4"',
							'6.35 mm',
						),
						array(
							'0.375',
							'6',
							'3/8"',
							'9.53 mm',
						),
						array(
							'0.5',
							'8',
							'1/2"',
							'12.7 mm',
						),
						array(
							'0.625',
							'10',
							'5/8"',
							'15.88 mm',
						),
						array(
							'0.75',
							'12',
							'3/4"',
							'19.05 mm',
						),
						array(
							'0.875',
							'14',
							'7/8"',
							'22.23 mm',
						),
					),
				),
			),
			array(
				'heading' => 'How many decimals you actually need',
				'body' => '<p>Three decimal places on an inch is about a fortieth of a millimetre, which is finer than a pencil line and far finer than most work requires. Carpentry is comfortable at one decimal place or the nearest sixteenth. Metalwork and machining care about the third, because a thousandth of an inch is a real tolerance in that world and parts are rejected over it.</p><p>The thing to avoid is rounding early. If a calculation has several steps, carry the full decimal through all of them and round once at the end, because rounding at each step compounds the error in the same direction and the result drifts further than any single rounding would suggest.</p>',
			),
			array(
				'heading' => 'The mistake that costs the most',
				'body' => '<p>Confusing millimetres with centimetres is the error that ruins a piece of material rather than a measurement. A drawing that says 25 means 25 mm on almost every engineering and joinery drawing produced outside the United States, because millimetres are the working unit and centimetres are almost never used in technical drawings at all.</p><p>If a dimension looks implausibly small, check the unit before you check your arithmetic. A door that is 2 metres tall is 2000 mm, and a drawing saying 200 is telling you about something else entirely.</p>',
			),
			array(
				'heading' => 'What this converter does not do',
				'body' => '<p>It converts length and nothing else. Square millimetres to square inches is a different conversion, because area scales with the square of the ratio: divide by 645.16 rather than 25.4. Cubic millimetres divide by 16387.064 for the same reason.</p><p>It also takes your measurement at face value. It cannot tell you whether the thing you measured is the dimension the job actually needs, which for anything being fitted into an existing space is usually the harder question. Measure the opening rather than the old panel, and measure it in three places, because walls are rarely as parallel as they look.</p>',
			),
		),
		'faqs' => array(
			array(
				'q' => 'Is 25.4 mm to the inch exact or rounded?',
				'a' => 'It is exact. The international yard and pound agreement of 1959 defined the inch as exactly 25.4 millimetres, so the conversion introduces no error at all. Any inaccuracy in a converted figure comes from the original measurement or from rounding the answer, never from the conversion factor.',
			),
			array(
				'q' => 'How do I convert millimetres to inches without a calculator?',
				'a' => 'Divide by 25 for a quick estimate, then take off about one and a half percent. 450 mm divided by 25 is 18, less 1.5 percent is 17.7, which is within a hundredth of the true 17.717. For anything you are about to cut, use the real figure.',
			),
			array(
				'q' => 'What is 100 mm in inches?',
				'a' => '3.937 inches, which is a shade under 3 and 15/16. People often call it 4 inches, and over a single brick that is fine, but the sixteenth you lose each time adds up quickly across a wall.',
			),
			array(
				'q' => 'Why do drawings use millimetres instead of centimetres?',
				'a' => 'Because millimetres give whole numbers for almost every dimension on a building or a machine part, and whole numbers are harder to misread than decimals. A drawing saying 2400 is unambiguous in a way that 240.0 cm is not, particularly on a photocopy or a phone screen.',
			),
			array(
				'q' => 'How precise should my answer be?',
				'a' => 'One decimal place or the nearest sixteenth is plenty for joinery and construction. Three decimal places matter in machining, where a thousandth of an inch is a working tolerance. Carry the full number through multi-step calculations and round only at the end, because rounding at each step pushes the error the same way every time.',
			),
		),
		'related' => array(
			'inches-to-feet-calculator',
			'unit-converter',
			'feet-to-meters-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
