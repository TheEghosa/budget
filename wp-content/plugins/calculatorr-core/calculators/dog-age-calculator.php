<?php
/**
 * Dog Age Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'dog-age-calculator',
		'title' => 'Dog Age Calculator',
		'category' => 'time',
		'description' => 'Convert your dog’s age into human years by size.',
		'keyword' => 'Dog Age Calculator',
		'h1' => 'Dog Age Calculator',
		'meta_title' => 'Dog Age Calculator - Dog Years by Breed Size',
		'meta_description' => 'Free dog age calculator. Convert dog years to human years using the veterinary size table and the 2020 epigenetic formula, not the old times-seven rule.',
		'fields' => array(
			array(
				'id' => 'years',
				'label' => 'Dog’s age (years)',
				'type' => 'number',
				'default' => 5,
			),
			array(
				'id' => 'months',
				'label' => 'Extra months',
				'type' => 'number',
				'default' => 0,
			),
			array(
				'id' => 'size',
				'label' => 'Breed size',
				'type' => 'segmented',
				'options' => array(
					'small' => 'Small',
					'medium' => 'Medium',
					'large' => 'Large',
					'giant' => 'Giant',
				),
				'default' => 'medium',
			),
		),
		'default_result' => array(
			'label' => 'In human years',
			'value' => '39 years',
			'rows' => array(
				array(
					'label' => 'By the size table',
					'value' => '39 years',
				),
				array(
					'label' => 'By the epigenetic formula',
					'value' => '56.8 years',
				),
				array(
					'label' => 'Old times-seven rule',
					'value' => '35 years',
				),
				array(
					'label' => 'Dog age entered',
					'value' => '5 years',
				),
			),
			'note' => 'The size table is shown as the headline because breed size affects ageing more than anything else: small dogs commonly reach sixteen while giant breeds rarely pass ten. The times-seven rule is included only to show how far off it is early on.',
		),
		'explainer' => array(
			array(
				'heading' => 'The times-seven rule is wrong and always was',
				'body' => 'A one year old dog is not a seven year old child, it is closer to a fifteen year old adolescent: sexually mature, fully grown and nearly done developing. Dogs age very fast at first and then slow down, so a straight multiplier cannot describe the curve at any point.',
				'formula' => 'Year 1 ≈ 15 human years, year 2 adds ~9, then 4 to 7 per year by size',
			),
			array(
				'heading' => 'Why size matters more than anything',
				'body' => 'Large dogs age faster after maturity. A small terrier commonly reaches sixteen while a Great Dane rarely passes ten, so the same chronological age means something quite different for each. The epigenetic formula from a 2020 study is shown alongside for comparison, since it fits young dogs particularly well.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'How old is a 1 year old dog in human years?',
				'a' => 'About fifteen, regardless of size. The divergence between breeds starts after the second year.',
			),
			array(
				'q' => 'When is a dog considered senior?',
				'a' => 'Around seven for large and giant breeds and closer to ten for small ones, which is roughly where age-related screening is usually recommended.',
			),
		),
		'related' => array(
			'chronological-age-calculator',
			'age-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
