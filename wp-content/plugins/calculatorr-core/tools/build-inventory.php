<?php
/**
 * Dumps the whole registry in the shape tools/build-home.py reads.
 *
 * The homepage and the all-calculators index are generated blocks rather than
 * rendered from the registry, which is right for pages that carry hand-written
 * copy and featured picks, and wrong in one respect: a newly published
 * calculator appears on its category hub straight away and on neither of those
 * two until somebody regenerates them. This is the step that stops that being
 * a thing to remember.
 *
 * It reads through Calculatorr_Registry, so JSON definitions are included on
 * exactly the same footing as the PHP configs.
 *
 * Usage:
 *   php tools/build-inventory.php > /tmp/inventory.json
 *   python3 tools/build-home.py /tmp/inventory.json content
 */

require_once dirname( __DIR__ ) . '/tests/bootstrap.php';

$registry = Calculatorr_Registry::instance();
$out      = array();

foreach ( $registry->categories() as $key => $category ) {
	$items = array();

	foreach ( $registry->in_category( $key, false ) as $slug => $config ) {
		$items[] = array(
			'slug'  => $slug,
			'name'  => $config['title'],
			/* The card blurb is the description's first sentence, because a
			   card that carries three sentences stops being scannable and the
			   description is written to open with the useful one. */
			'blurb' => calcr_first_sentence( $config['description'] ),
		);
	}

	usort( $items, function ( $a, $b ) { return strcmp( $a['name'], $b['name'] ); } );

	$out[] = array(
		'slug'  => $category['slug'],
		'h1'    => $category['h1'],
		/* The long line, not the short one. The index page gives each block a
		   paragraph of its own and the short blurb is written for a card. */
		'desc'  => $category['meta_description'],
		'items' => $items,
	);
}

function calcr_first_sentence( $text ) {
	$text = trim( wp_strip_all_tags( (string) $text ) );

	if ( preg_match( '/^(.+?[.!?])(\s|$)/u', $text, $m ) ) {
		return $m[1];
	}

	return $text;
}

echo json_encode( $out, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) . "\n";
