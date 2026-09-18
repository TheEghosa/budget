<?php
/**
 * Standard Deviation Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'standard-deviation-calculator',
		'title' => 'Standard Deviation Calculator',
		'category' => 'math',
		'description' => 'Find the standard deviation and variance of a data set.',
		'keyword' => 'Standard Deviation Calculator',
		'h1' => 'Standard Deviation Calculator',
		'meta_title' => 'Standard Deviation Calculator - Sample and Population',
		'meta_description' => 'Free standard deviation calculator. Paste a data set for the sample or population deviation, the variance, the mean and the coefficient of variation.',
		'fields' => array(
			array(
				'id' => 'numbers',
				'label' => 'Your data',
				'type' => 'text',
				'default' => '2, 4, 4, 4, 5, 5, 7, 9',
				'hint' => 'Separate numbers with spaces or commas.',
			),
			array(
				'id' => 'type',
				'label' => 'Data set is a',
				'type' => 'segmented',
				'options' => array(
					'sample' => 'Sample',
					'population' => 'Population',
				),
				'default' => 'sample',
			),
		),
		'default_result' => array(
			'label' => 'Sample standard deviation',
			'value' => '2.13809',
			'rows' => array(
				array(
					'label' => 'Variance',
					'value' => '4.571429',
				),
				array(
					'label' => 'Mean',
					'value' => '5',
				),
				array(
					'label' => 'Count',
					'value' => '8',
				),
				array(
					'label' => 'Sum of squared deviations',
					'value' => '32',
				),
				array(
					'label' => 'Coefficient of variation',
					'value' => '42.762%',
				),
			),
			'note' => 'Dividing by n minus one corrects for the fact that a sample underestimates the spread of the population it came from.',
		),
		'explainer' => array(
			array(
				'heading' => 'What it measures',
				'body' => 'Standard deviation describes how spread out the numbers are around their mean. A small one means the data clusters tightly, a large one means it is scattered. Two data sets can share an identical mean and behave completely differently, and this is the number that tells them apart.',
				'formula' => 's = &radic;( &Sigma;(x &minus; x&#772;)&sup2; &divide; (n &minus; 1) )',
			),
			array(
				'heading' => 'Sample or population, and why it changes the answer',
				'body' => 'Dividing by n minus one rather than n corrects for the fact that a sample tends to underestimate the spread of the population it came from. Use the population version only when your numbers genuinely are everything, such as the marks of every student in one class rather than a sample of them.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'When should I use sample instead of population?',
				'a' => 'Almost always sample, unless your data covers every member of the group you care about with nobody left out.',
			),
			array(
				'q' => 'What is variance?',
				'a' => 'The standard deviation squared. It is used in further statistics because it adds cleanly, but it is in squared units, which is why the deviation is the figure usually reported.',
			),
			array(
				'q' => 'What counts as a high standard deviation?',
				'a' => 'It depends entirely on the scale of the data, which is why the coefficient of variation is shown: it expresses the deviation as a percentage of the mean and can be compared across data sets.',
			),
		),
		'related' => array(
			'average-calculator',
			'percentage-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
