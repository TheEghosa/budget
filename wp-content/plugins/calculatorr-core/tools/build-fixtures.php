<?php
/**
 * Regenerates tests/fixtures/fields.json.
 *
 * The sandbox parity test runs in node and needs every calculator's fields to
 * build its input vectors from, and the configs they come from are PHP. Rather
 * than teach node to read PHP, the fields are dumped here and the test checks
 * that what it is holding still covers every calculator on disk, so a dump
 * that has gone stale fails rather than quietly testing yesterday's site.
 *
 * Usage: php tools/build-fixtures.php
 */

define( 'ABSPATH', __DIR__ );

$root = dirname( __DIR__ );
$out  = array();

foreach ( glob( $root . '/calculators/*.php' ) as $file ) {
	if ( '_' === basename( $file )[0] ) {
		continue;
	}

	$config = include $file;
	$out[]  = array( 'slug' => $config['slug'], 'fields' => $config['fields'] );
}

file_put_contents(
	$root . '/tests/fixtures/fields.json',
	json_encode( $out, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) . "\n"
);

printf( "wrote %d calculators to tests/fixtures/fields.json\n", count( $out ) );
