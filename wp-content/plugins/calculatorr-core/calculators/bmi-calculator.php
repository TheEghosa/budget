<?php
/**
 * BMI Calculator.
 *
 * Second in the build order because the demand is enormous and the maths is
 * trivial, but mostly because it forces the metric and imperial unit switch to
 * be solved early rather than retrofitted across ninety other pages.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'slug'        => 'bmi-calculator',
	'title'       => 'BMI Calculator',
	'category'    => 'health',
	'description' => 'Calculate body mass index from your height and weight in metric or imperial units, and see which range the result falls in.',
	'fields'      => array(
		array(
			'id'      => 'units',
			'label'   => 'Units',
			'type'    => 'segmented',
			'options' => array( 'metric' => 'Metric', 'imperial' => 'Imperial' ),
			'default' => 'metric',
		),
		array( 'id' => 'height', 'label' => 'Height', 'type' => 'number', 'suffix' => 'cm', 'default' => 175, 'min' => 0, 'show_when' => array( 'units' => 'metric' ) ),
		array( 'id' => 'weight', 'label' => 'Weight', 'type' => 'number', 'suffix' => 'kg', 'default' => 70, 'min' => 0, 'show_when' => array( 'units' => 'metric' ) ),
		array( 'id' => 'feet', 'label' => 'Height (feet)', 'type' => 'number', 'suffix' => 'ft', 'default' => 5, 'min' => 0, 'show_when' => array( 'units' => 'imperial' ) ),
		array( 'id' => 'inches', 'label' => 'Height (inches)', 'type' => 'number', 'suffix' => 'in', 'default' => 9, 'min' => 0, 'show_when' => array( 'units' => 'imperial' ) ),
		array( 'id' => 'pounds', 'label' => 'Weight', 'type' => 'number', 'suffix' => 'lb', 'default' => 154, 'min' => 0, 'show_when' => array( 'units' => 'imperial' ) ),
	),
	'default_result' => array(
		'label' => 'Body mass index',
		'value' => '22.9',
		'rows'  => array(
			array( 'label' => 'Category', 'value' => 'Healthy weight' ),
			array( 'label' => 'Healthy range', 'value' => '18.5 to 24.9' ),
		),
		'note'  => 'BMI cannot tell muscle from fat, so it overstates risk for athletes and understates it for anyone sedentary with a light frame.',
	),
	'explainer'   => array(
		array(
			'heading' => 'How BMI is calculated',
			'body'    => 'Body mass index divides your weight by the square of your height, which produces a single number that can be compared across people of different sizes. Squaring the height is what makes the comparison work at all, since weight grows roughly with volume while height grows in one dimension only.',
			'formula' => 'BMI = weight (kg) &divide; height (m)<sup>2</sup>',
		),
		array(
			'heading' => 'What the ranges mean, and what they miss',
			'body'    => 'Below 18.5 is classed as underweight, 18.5 to 24.9 as a healthy weight, 25 to 29.9 as overweight and 30 or above as obese. These bands come from population studies rather than from individual diagnosis, which is the source of most of the criticism aimed at BMI. The index cannot distinguish muscle from fat and knows nothing about where fat sits on the body, so a heavily trained athlete is routinely classed as overweight while someone sedentary with a light frame can sit inside the healthy band with a genuinely unhealthy body composition. Treat the number as one reading among several rather than as a verdict.',
		),
	),
	'faqs'        => array(
		array(
			'q' => 'Is BMI accurate for athletes?',
			'a' => 'Not usefully, because muscle is denser than fat and the formula only sees total weight. A rugby forward and a sedentary person of the same height and weight produce an identical BMI despite having very different bodies. For anyone carrying significant muscle, a body fat percentage measurement or a waist to height ratio tells you more.',
		),
		array(
			'q' => 'Does BMI work the same for children?',
			'a' => 'No. Children are still growing, so their BMI is read against age and sex percentile charts rather than against the adult bands. A child whose BMI falls in the adult overweight range may be entirely typical for their age, which is why paediatric BMI should be interpreted by a clinician with the growth chart in front of them.',
		),
		array(
			'q' => 'What should I use instead?',
			'a' => 'Waist to height ratio is a better single indicator for most people, since where fat sits matters more for health risk than how much of it there is in total. Keeping your waist under half your height is the common rule of thumb, and it needs nothing more than a tape measure.',
		),
	),
	'disclaimer'  => 'This is a general population formula and not medical advice. It cannot account for your medical history, medication or body composition, so treat the result as a starting point for a conversation with a clinician.',
	'related'     => array( 'tdee-calculator', 'percentage-calculator' ),
);
