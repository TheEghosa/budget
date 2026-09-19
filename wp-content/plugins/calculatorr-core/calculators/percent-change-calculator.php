<?php
/**
 * Percent Change Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'percent-change-calculator',
		'title' => 'Percent Change Calculator',
		'category' => 'math',
		'description' => 'Find the percentage change between two figures, in either direction.',
		'keyword' => 'Percent Change Calculator',
		'h1' => 'Percent Change Calculator',
		'meta_title' => 'Percent Change Calculator - Between Two Numbers',
		'meta_description' => 'Free percent change calculator. Enter a before and after value to get the percentage increase or decrease, plus the absolute change between them.',
		'fields' => array(
			array(
				'id' => 'from',
				'label' => 'From',
				'type' => 'number',
				'default' => 50,
			),
			array(
				'id' => 'to',
				'label' => 'To',
				'type' => 'number',
				'default' => 75,
			),
		),
		'default_result' => array(
			'label' => 'Percentage increase',
			'value' => '50%',
			'rows' => array(
				array(
					'label' => 'From',
					'value' => '50',
				),
				array(
					'label' => 'To',
					'value' => '75',
				),
				array(
					'label' => 'Absolute change',
					'value' => '25',
				),
			),
			'note' => '',
		),
		'explainer' => array(
			array(
				'heading' => 'How percentage change is worked out',
				'body' => '<p>Take the difference, divide by the number you started with, and turn it into a percentage. The part people get wrong is the middle step, because the answer depends entirely on which number you treat as the starting point, and swapping them gives a different percentage from the same pair of figures.</p><p>That is not a quirk to be worked around. A percentage change is always a change <em>relative to something</em>, and the something is the earlier value. Get that right and the rest is arithmetic.</p>',
				'formula' => 'Change % = (New &minus; Old) &divide; Old &times; 100',
				'steps' => array(
					'Subtract the old value from the new one.',
					'Divide by the old value.',
					'Multiply by 100.',
					'A negative answer is a decrease, which is the arithmetic telling you the direction rather than an error.',
				),
				'example' => '<p>Rent goes from &pound;950 to &pound;1,045. The difference is &pound;95. 95 &divide; 950 = 0.1, which is a 10 percent increase.</p>',
			),
			array(
				'heading' => 'Why a rise and a fall of the same percentage do not cancel',
				'body' => '<p>This is the most useful thing on this page. Something that falls fifty percent and then rises fifty percent does not come back to where it started, because the two percentages are taken from different bases. The fall is measured against the original and the rise is measured against the reduced figure, which is smaller.</p><p>&pound;100 falls fifty percent to &pound;50. Fifty percent of &pound;50 is &pound;25, so it rises to &pound;75. To get back to &pound;100 it would have to rise by a hundred percent. The bigger the fall, the more lopsided this gets.</p>',
				'table' => array(
					'caption' => 'What it takes to recover from a fall',
					'head' => array(
						'Fall',
						'Value left from &pound;100',
						'Rise needed to recover',
						'Ratio',
					),
					'rows' => array(
						array(
							'10%',
							'&pound;90',
							'11.1%',
							'1.1&times;',
						),
						array(
							'20%',
							'&pound;80',
							'25%',
							'1.25&times;',
						),
						array(
							'33%',
							'&pound;67',
							'50%',
							'1.5&times;',
						),
						array(
							'50%',
							'&pound;50',
							'100%',
							'2&times;',
						),
						array(
							'75%',
							'&pound;25',
							'300%',
							'4&times;',
						),
						array(
							'90%',
							'&pound;10',
							'900%',
							'10&times;',
						),
					),
				),
			),
			array(
				'heading' => 'Percentage points are not percentages',
				'body' => '<p>If an interest rate moves from 4 percent to 5 percent, that is a rise of one percentage point and a rise of twenty five percent. Both statements are true and they describe the same move, which is exactly why the distinction gets exploited.</p><p>Use percentage points when you are comparing two percentages, and percentages when you are describing how much something grew. A headline saying a rate rose twenty five percent is technically defensible and usually misleading, because most readers will hear it as twenty five points.</p>',
			),
			array(
				'heading' => 'Stacking changes in a row',
				'body' => '<p>Successive percentage changes multiply rather than add. Two consecutive ten percent rises are not twenty percent, they are twenty one, because the second ten percent is taken from the already increased figure.</p><p>Over a few steps the gap is small enough to ignore in conversation and large enough to matter in money. Over many steps it is the whole story, which is what compound interest is and why it works.</p>',
				'formula' => 'Total factor = (1 + r&#8321;) &times; (1 + r&#8322;) &times; ... &times; (1 + r&#8345;)',
				'example' => '<p>A price rises 10 percent, then 10 percent again, then falls 15 percent. The factor is 1.1 &times; 1.1 &times; 0.85 = 1.0285, so the price ended up 2.85 percent higher than it started, not 5 percent.</p>',
			),
			array(
				'heading' => 'When the starting value is zero',
				'body' => '<p>Percentage change from zero has no answer, and any tool that gives you one is inventing it. Dividing by zero is undefined, so a jump from nought to fifty is not an infinite increase or a five thousand percent one. It is simply a change that percentages cannot describe.</p><p>Say it in absolute terms instead. Going from zero sales to fifty sales is fifty sales, and that is both more honest and more informative than any percentage anyone could write.</p>',
			),
			array(
				'heading' => 'What this calculator does not decide for you',
				'body' => '<p>It does the arithmetic and leaves the judgement alone. Whether a change is meaningful depends on how big the numbers are underneath: a hundred percent rise in something that happened twice last month is two extra events, and a one percent rise in a million is ten thousand.</p><p>It also cannot tell you whether the comparison is fair. Comparing this January to last January controls for the season, and comparing January to December does not. The percentage will be equally correct either way, which is precisely the problem.</p>',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What is the difference between percentage change and percentage points?',
				'a' => 'A move from 4 percent to 5 percent is one percentage point and a twenty five percent increase. Percentage points compare two percentages directly, while percentage change describes how much one of them grew relative to itself.',
			),
			array(
				'q' => 'Why does a 50 percent fall need a 100 percent rise to recover?',
				'a' => 'Because the two are measured from different starting points. The fall is taken from the original figure and the recovery is taken from what is left, which is half as big, so the same amount of money is twice the percentage.',
			),
			array(
				'q' => 'Do two 10 percent rises make 20 percent?',
				'a' => 'No, they make 21 percent. The second rise is calculated on the already increased figure, so the changes multiply rather than add. Multiply 1.1 by 1.1 to get 1.21.',
			),
			array(
				'q' => 'What is the percentage change from zero?',
				'a' => 'There isn\'t one. Dividing by zero is undefined, so there is no percentage that describes a move away from nothing. Report the absolute change instead, since that is the only honest description.',
			),
			array(
				'q' => 'Is a negative answer wrong?',
				'a' => 'No, it is the direction. A negative percentage change means the value fell, and the size of the number is how far. Most people then quote it as a decrease and drop the minus sign, which is fine as long as the word decrease goes with it.',
			),
		),
		'related' => array(
			'percentage-increase-calculator',
			'percentage-decrease-calculator',
			'percentage-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
