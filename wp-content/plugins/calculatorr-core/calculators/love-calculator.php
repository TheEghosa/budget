<?php
/**
 * Love Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'love-calculator',
		'title' => 'Love Calculator',
		'category' => 'math',
		'description' => 'A light-hearted name compatibility score.',
		'keyword' => 'Love Calculator',
		'h1' => 'Love Calculator',
		'meta_title' => 'Love Calculator - Name Compatibility, Just for Fun',
		'meta_description' => 'A free love calculator that scores two names for compatibility. It is a bit of fun with no predictive value, and it gives the same answer every time.',
		'fields' => array(
			array(
				'id' => 'name1',
				'label' => 'First name',
				'type' => 'text',
				'default' => 'Alex',
			),
			array(
				'id' => 'name2',
				'label' => 'Second name',
				'type' => 'text',
				'default' => 'Sam',
			),
		),
		'default_result' => array(
			'label' => 'Compatibility',
			'value' => '89%',
			'rows' => array(
				array(
					'label' => 'Verdict',
					'value' => 'Written in the stars',
				),
				array(
					'label' => 'Names',
					'value' => 'Alex and Sam',
				),
			),
			'note' => 'For fun only. This is a hash of two names and nothing more, so it has no predictive value whatsoever. It will give the same answer every time for the same pair, which is the only honest claim it can make.',
		),
		'explainer' => array(
			array(
				'heading' => 'What this actually does',
				'body' => 'It combines the two names, runs them through a hash function and turns the result into a number between zero and a hundred. That is the entire mechanism. It is deterministic, so the same pair always gets the same score, and the order does not matter.',
				'formula' => 'A hash of two names, nothing more',
			),
			array(
				'heading' => 'Why we are telling you that',
				'body' => 'Most love calculators imply some method they do not have. This one has no insight into anyone’s relationship, personality or future, and pretending otherwise would be dishonest. It is here because it is fun, and because being upfront about a toy costs nothing.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'Is this accurate?',
				'a' => 'No, and it cannot be. It is arithmetic on letters, with no connection to anything real about either person.',
			),
			array(
				'q' => 'Why does it always give the same answer?',
				'a' => 'Because it is deterministic by design. A random score would be no less meaningful but would at least be honest about being random, and a stable answer makes the joke work better.',
			),
		),
		'related' => array(
			'percentage-calculator',
			'age-calculator',
		),
		'disclaimer' => 'Entertainment only. This has no predictive value of any kind and should not inform any decision.',
	);
