<?php
/**
 * Age Calculator.
 *
 * Fifth in the build order: trivial to build, high volume, and it validates
 * the date handling that the other nine time calculators depend on.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'slug'        => 'age-calculator',
	'title'       => 'Age Calculator',
	'category'    => 'time',
	'description' => 'Work out an exact age in years, months and days from a date of birth, plus the total in days, weeks and months.',
	'keyword'     => 'Age Calculator',
	'h1'          => 'Age Calculator',
	'meta_title'  => 'Age Calculator - Exact Age in Years, Months & Days',
	'meta_description' => 'Free age calculator: enter a date of birth for an exact age in years, months and days, plus totals in days, weeks and months. Works for any past or future date.',
	'fields'      => array(
		array( 'id' => 'dob', 'label' => 'Date of birth', 'type' => 'date', 'default' => '1990-01-01' ),
		array( 'id' => 'upto', 'label' => 'Age at this date', 'type' => 'date', 'default' => '', 'hint' => 'Leave empty to use today.' ),
	),
	/* No precomputed result here, because any figure baked into the page would
	   be wrong the following morning. JavaScript fills it on load instead. */
	'default_result' => array( 'label' => 'Age', 'value' => '—' ),
	'explainer'   => array(
		array(
			'heading' => 'Why age arithmetic is fiddlier than it looks',
			'body'    => 'Subtracting two years gets you close and is wrong for roughly half the year, because whether a birthday has already passed changes the answer. Months make it worse, since they run between 28 and 31 days, so borrowing a day across a month boundary has to borrow the right number rather than a fixed thirty. This calculator counts from the birth date forward, carrying months and days properly, which is why a birthday on the 31st behaves sensibly in February.',
		),
		array(
			'heading' => 'Leap years and the 29th of February',
			'body'    => 'Someone born on a leap day has a legal birthday that varies by jurisdiction, with some treating it as the 28th of February in common years and others as the 1st of March. The total days count is unaffected either way, so if the exact figure matters for a deadline rather than a celebration, use the day count rather than the years figure.',
		),
	),
	'faqs'        => array(
		array(
			'q' => 'How many days old am I?',
			'a' => 'Enter your date of birth and read the total days row. The count includes every leap day between then and now, which is why it will not match your age multiplied by 365.',
		),
		array(
			'q' => 'Can I calculate age at a past or future date?',
			'a' => 'Yes, put that date in the second field. This is the usual way to check whether someone met an age requirement on a specific day, such as a cut-off date for a school year or an eligibility deadline.',
		),
	),
	'related'     => array( 'percentage-calculator' ),
);
