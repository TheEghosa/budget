<?php
/**
 * Distance Calculator.
 *
 * Generated from the shared config format. The default result below is
 * computed from this calculator's own formula rather than typed by hand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
		'slug' => 'distance-calculator',
		'title' => 'Distance Calculator',
		'category' => 'geometry',
		'description' => 'Find the distance between two points or two coordinates.',
		'keyword' => 'Distance Calculator',
		'h1' => 'Distance Calculator',
		'meta_title' => 'Distance Calculator - Between Points or Coordinates',
		'meta_description' => 'Free distance calculator. Measure between two points on a plane, or between two latitude and longitude coordinates as a great circle distance.',
		'fields' => array(
			array(
				'id' => 'mode',
				'label' => 'Measuring',
				'type' => 'segmented',
				'options' => array(
					'plane' => 'Two points (x, y)',
					'coordinates' => 'Latitude and longitude',
				),
				'default' => 'plane',
			),
			array(
				'id' => 'x1',
				'label' => 'x₁',
				'type' => 'number',
				'default' => 0,
				'step' => 'any',
				'show_when' => array(
					'mode' => 'plane',
				),
			),
			array(
				'id' => 'y1',
				'label' => 'y₁',
				'type' => 'number',
				'default' => 0,
				'step' => 'any',
				'show_when' => array(
					'mode' => 'plane',
				),
			),
			array(
				'id' => 'x2',
				'label' => 'x₂',
				'type' => 'number',
				'default' => 3,
				'step' => 'any',
				'show_when' => array(
					'mode' => 'plane',
				),
			),
			array(
				'id' => 'y2',
				'label' => 'y₂',
				'type' => 'number',
				'default' => 4,
				'step' => 'any',
				'show_when' => array(
					'mode' => 'plane',
				),
			),
			array(
				'id' => 'lat1',
				'label' => 'Latitude 1',
				'type' => 'number',
				'default' => 40.7128,
				'step' => 'any',
				'show_when' => array(
					'mode' => 'coordinates',
				),
			),
			array(
				'id' => 'lon1',
				'label' => 'Longitude 1',
				'type' => 'number',
				'default' => -74.006,
				'step' => 'any',
				'show_when' => array(
					'mode' => 'coordinates',
				),
			),
			array(
				'id' => 'lat2',
				'label' => 'Latitude 2',
				'type' => 'number',
				'default' => 34.0522,
				'step' => 'any',
				'show_when' => array(
					'mode' => 'coordinates',
				),
			),
			array(
				'id' => 'lon2',
				'label' => 'Longitude 2',
				'type' => 'number',
				'default' => -118.2437,
				'step' => 'any',
				'show_when' => array(
					'mode' => 'coordinates',
				),
			),
		),
		'default_result' => array(
			'label' => 'Distance between the points',
			'value' => '5',
			'rows' => array(
				array(
					'label' => 'Horizontal change',
					'value' => '3',
				),
				array(
					'label' => 'Vertical change',
					'value' => '4',
				),
				array(
					'label' => 'Midpoint',
					'value' => '(1.5, 2)',
				),
				array(
					'label' => 'Slope',
					'value' => '1.3333',
				),
			),
			'note' => 'The distance formula is the Pythagorean theorem with the horizontal and vertical changes as the two legs.',
		),
		'explainer' => array(
			array(
				'heading' => 'Two different questions',
				'body' => 'On a flat plane the distance formula is the Pythagorean theorem with the horizontal and vertical changes as the legs. Between two points on the earth it is a great circle arc, which accounts for the planet’s curvature and is what an aircraft actually flies.',
				'formula' => 'Plane: d = √((x₂−x₁)² + (y₂−y₁)²)',
			),
			array(
				'heading' => 'Straight line is not driving distance',
				'body' => 'The great circle figure is the shortest path over the surface and ignores roads, water and terrain entirely. A real road route is typically twenty to forty per cent longer, so this answers how far apart rather than how far to drive.',
			),
		),
		'faqs' => array(
			array(
				'q' => 'What is the distance from New York to Los Angeles?',
				'a' => 'About 2,450 miles as a great circle, which is the default shown. Driving it is closer to 2,800.',
			),
			array(
				'q' => 'How accurate is the haversine formula?',
				'a' => 'Within about half a per cent, because it treats the earth as a perfect sphere when it is slightly flattened. That error is far smaller than the precision of most coordinates people type in.',
			),
		),
		'related' => array(
			'slope-calculator',
			'pythagorean-theorem-calculator',
			'tire-size-calculator',
		),
		'disclaimer' => '',
		'sources' => array(),
	);
