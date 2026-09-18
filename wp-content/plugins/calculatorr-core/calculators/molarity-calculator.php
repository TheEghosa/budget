<?php
/**
 * Molarity Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'molarity-calculator',
		'title' => 'Molarity Calculator',
		'category' => 'education',
		'description' => 'Calculate molarity, mass or volume for a solution.',
		'keyword' => 'Molarity Calculator',
		'h1' => 'Molarity Calculator',
		'meta_title' => 'Molarity Calculator - Moles per Litre of Solution',
		'meta_description' => 'Free molarity calculator. Solve for molarity, the mass of solute needed or the volume of solution, with moles and millimolar shown alongside.',
		'fields' => array(
			array(
				'id' => 'solve',
				'label' => 'Solve for',
				'type' => 'segmented',
				'options' => array(
					'molarity' => 'Molarity',
					'mass' => 'Mass',
					'volume' => 'Volume',
				),
				'default' => 'molarity',
			),
			array(
				'id' => 'mass',
				'label' => 'Mass of solute',
				'type' => 'number',
				'suffix' => 'g',
				'default' => 58.44,
				'step' => 'any',
			),
			array(
				'id' => 'molarMass',
				'label' => 'Molar mass',
				'type' => 'number',
				'suffix' => 'g/mol',
				'default' => 58.44,
				'step' => 'any',
				'hint' => 'Sum of the atomic masses in the formula.',
			),
			array(
				'id' => 'volume',
				'label' => 'Volume of solution',
				'type' => 'number',
				'suffix' => 'L',
				'default' => 1,
				'step' => 'any',
			),
			array(
				'id' => 'molarity',
				'label' => 'Molarity',
				'type' => 'number',
				'suffix' => 'M',
				'default' => 1,
				'step' => 'any',
				'show_when' => array(
					'solve' => array(
						'mass',
						'volume',
					),
				),
			),
		),
		'default_result' => array(
			'label' => 'Molarity',
			'value' => '1 M',
			'rows' => array(
				array(
					'label' => 'Moles of solute',
					'value' => '1 mol',
				),
				array(
					'label' => 'Molar mass',
					'value' => '58.44 g/mol',
				),
				array(
					'label' => 'Millimolar',
					'value' => '1,000 mM',
				),
			),
			'note' => 'Molarity is moles of solute per litre of solution, not per litre of solvent. Dissolving something changes the volume, so make the solution up to the mark rather than adding solute to a litre of water.',
		),
		'explainer' => array(
			array(
				'heading' => 'Per litre of solution, not of solvent',
				'body' => 'Molarity is moles of solute divided by the volume of the finished solution. Dissolving something changes the volume, so adding a mole to one litre of water does not give a one molar solution. You dissolve in less, then make it up to the mark in a volumetric flask.',
				'formula' => 'M = moles of solute ÷ litres of solution',
			),
			array(
				'heading' => 'Getting the molar mass right',
				'body' => 'Molar mass is the sum of the atomic masses in the formula, in grams per mole. Sodium chloride is 22.99 plus 35.45, giving 58.44. Hydrates matter here: copper sulfate pentahydrate weighs considerably more than the anhydrous salt, and using the wrong one throws the concentration out badly.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What is the difference between molarity and molality?',
				'a' => 'Molarity is per litre of solution and changes with temperature because volume does. Molality is per kilogram of solvent and does not, which is why it is preferred for work across a temperature range.',
			),
			array(
				'q' => 'How do I make a 0.5 M solution?',
				'a' => 'Multiply 0.5 by your target volume in litres to get moles, multiply that by the molar mass for grams, dissolve in part of the solvent and make up to the final volume.',
			),
		),
		'related' => array(
			'percentage-calculator',
			'unit-converter',
		),
		'disclaimer' => '',
	);
