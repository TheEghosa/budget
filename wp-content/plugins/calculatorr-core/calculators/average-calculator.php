<?php
/**
 * Average Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'average-calculator',
		'title' => 'Average Calculator',
		'category' => 'math',
		'description' => 'Find the mean, median, mode and range of a set of numbers.',
		'keyword' => 'Average Calculator',
		'h1' => 'Average Calculator',
		'meta_title' => 'Average Calculator - Mean, Median, Mode and Range',
		'meta_description' => 'Free average calculator. Paste in a list of numbers for the mean, median, mode, range, count and sum, with the outliers explained rather than hidden.',
		'fields' => array(
			array(
				'id' => 'numbers',
				'label' => 'Your numbers',
				'type' => 'text',
				'default' => '4, 8, 15, 16, 23, 42',
				'hint' => 'Separate numbers with spaces or commas.',
			),
		),
		'default_result' => array(
			'label' => 'Mean average',
			'value' => '18',
			'rows' => array(
				array(
					'label' => 'Median',
					'value' => '15.5',
				),
				array(
					'label' => 'Mode',
					'value' => 'none repeats',
				),
				array(
					'label' => 'Count',
					'value' => '6',
				),
				array(
					'label' => 'Sum',
					'value' => '108',
				),
				array(
					'label' => 'Range',
					'value' => '38',
				),
			),
			'note' => 'The mean is pulled by outliers and the median is not, so when the two disagree sharply the median usually describes the data better.',
		),
		'explainer' => array(
			array(
				'heading' => 'Three different averages, three different answers',
				'body' => 'The mean adds everything and divides by the count. The median is the middle value once sorted. The mode is whatever appears most often. They agree on symmetric data and diverge sharply on skewed data, which is exactly when the choice matters most.',
				'formula' => 'Mean = Sum &divide; Count',
			),
			array(
				'heading' => 'Which one to quote',
				'body' => 'Use the median for anything involving money, because incomes, house prices and salaries all have a long tail at the top that drags the mean upward. A single billionaire in a room of a hundred people makes the mean net worth meaningless while leaving the median untouched.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What is the difference between mean and average?',
				'a' => 'In everyday use they mean the same thing. Strictly, average covers the mean, median and mode, and the mean is the one people usually intend.',
			),
			array(
				'q' => 'Can a data set have more than one mode?',
				'a' => 'Yes. If two values tie for the most frequent, the set is bimodal and both are reported. If nothing repeats, there is no mode at all.',
			),
			array(
				'q' => 'Why is median better for salary data?',
				'a' => 'Because pay distributions have a long upper tail. A handful of very high earners lift the mean well above what a typical person earns, while the median stays at the middle of the actual workforce.',
			),
		),
		'related' => array(
			'standard-deviation-calculator',
			'percentage-calculator',
		),
		'disclaimer' => '',
	);
