<?php
/**
 * GPA Calculator.
 *
 * Ninth in the build order because it needs add and remove rows, which is the
 * last input pattern the shared template has to support.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$grades = array(
	'4.0' => 'A  (4.0)',
	'3.7' => 'A- (3.7)',
	'3.3' => 'B+ (3.3)',
	'3.0' => 'B  (3.0)',
	'2.7' => 'B- (2.7)',
	'2.3' => 'C+ (2.3)',
	'2.0' => 'C  (2.0)',
	'1.7' => 'C- (1.7)',
	'1.3' => 'D+ (1.3)',
	'1.0' => 'D  (1.0)',
	'0.0' => 'F  (0.0)',
);

return array(
	'slug'        => 'gpa-calculator',
	'title'       => 'GPA Calculator',
	'category'    => 'education',
	'description' => 'Work out your grade point average on the four point scale from your course grades and credit hours.',
	'keyword'     => 'GPA Calculator',
	'h1'          => 'GPA Calculator',
	'meta_title'  => 'GPA Calculator - 4.0 Scale, Weighted by Credit Hours',
	'meta_description' => 'Free GPA calculator on the 4.0 scale. Enter each course grade and its credit hours for an accurate weighted average, with quality points shown alongside.',
	'fields'      => array(
		array(
			'id'    => 'courses',
			'label' => 'Your courses',
			'type'  => 'repeater',
			'rows'  => 3,
			'row'   => array(
				array( 'id' => 'grade', 'label' => 'Grade', 'type' => 'select', 'options' => $grades, 'default' => '4.0' ),
				array( 'id' => 'credits', 'label' => 'Credit hours', 'type' => 'number', 'default' => 3 ),
			),
		),
	),
	'default_result' => array(
		'label' => 'Grade point average',
		'value' => '4.00',
		'rows'  => array(
			array( 'label' => 'Courses counted', 'value' => '3' ),
			array( 'label' => 'Total credits', 'value' => '9' ),
			array( 'label' => 'Quality points', 'value' => '36' ),
		),
	),
	'explainer'   => array(
		array(
			'heading' => 'How a GPA is actually calculated',
			'body'    => 'Each grade converts to a point value, that value is multiplied by the credit hours for the course to give quality points, and the total quality points are divided by the total credits. Weighting by credits is what stops a one credit elective from moving your average as much as a four credit core course, and it is the step people skip when they try to average their grades directly.',
			'formula' => 'GPA = &Sigma;(grade points &times; credits) &divide; &Sigma;credits',
		),
		array(
			'heading' => 'Weighted versus unweighted',
			'body'    => 'This calculator uses the unweighted four point scale, where an A is worth four points regardless of how hard the course was. Many high schools use a weighted scale that awards five points for an A in an honours or AP course, which is why a weighted GPA can exceed 4.0. If your school weights, add the extra point to those courses before entering them, or check whether the institution you are applying to recalculates on an unweighted basis anyway, since a great many do.',
		),
	),
	'faqs'        => array(
		array(
			'q' => 'How do I raise my GPA?',
			'a' => 'The arithmetic gets less forgiving the more credits you have already accumulated, because each new course is a smaller share of the total. A student with fifteen credits can move their average substantially in one term, while a student with ninety cannot. Prioritise the high credit courses, since those carry the most weight in both directions.',
		),
		array(
			'q' => 'Do failed courses count?',
			'a' => 'Usually yes, and they hurt twice: the zero drags the average down and the credits still count in the denominator. Some institutions replace the grade when a course is retaken while others average both attempts, so check your own registrar&rsquo;s policy rather than assuming.',
		),
	),
	'related'     => array( 'percentage-calculator' ),
);
