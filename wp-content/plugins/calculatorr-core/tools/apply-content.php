<?php
/**
 * Merges authored content into a calculator's config file.
 *
 * Content is written as JSON in content/calculators/<slug>.json rather than
 * straight into the PHP, for two reasons. Hand-editing a nested PHP array
 * across a hundred files invites exactly the kind of quiet syntax damage that
 * a missing comma causes, and the same JSON can be posted to the plugin's
 * content route to update a live site without shipping a new build.
 *
 * The config file keeps everything it already had. Only explainer and faqs are
 * replaced, because those are the two the writing owns.
 *
 * Usage: php tools/apply-content.php [slug ...]      (all of them if none given)
 */

$root = dirname( __DIR__ );
$dir  = $root . '/content/calculators';

$slugs = array_slice( $argv, 1 );

if ( ! $slugs ) {
	$slugs = array_map(
		function ( $p ) { return basename( $p, '.json' ); },
		glob( $dir . '/*.json' )
	);
}

if ( ! $slugs ) {
	fwrite( STDERR, "No authored content found in content/calculators.\n" );
	exit( 1 );
}

/**
 * Renders a value as PHP source, indented to match the config files, which are
 * written with tabs and one item per line.
 */
function calcr_export( $value, $depth = 2 ) {
	$pad = str_repeat( "\t", $depth );

	if ( is_array( $value ) ) {
		$isList = array_keys( $value ) === range( 0, count( $value ) - 1 );
		$out    = "array(\n";

		foreach ( $value as $k => $v ) {
			$out .= $pad . "\t";

			if ( ! $isList ) {
				$out .= "'" . str_replace( "'", "\\'", $k ) . "' => ";
			}

			$out .= calcr_export( $v, $depth + 1 ) . ",\n";
		}

		return $out . $pad . ')';
	}

	if ( is_int( $value ) || is_float( $value ) ) {
		return (string) $value;
	}

	if ( is_bool( $value ) ) {
		return $value ? 'true' : 'false';
	}

	if ( null === $value ) {
		return 'null';
	}

	return "'" . str_replace( array( '\\', "'" ), array( '\\\\', "\\'" ), (string) $value ) . "'";
}

/**
 * Swaps one top-level key in the config source.
 *
 * The file is a single `return array( ... );` and the keys are one per line at
 * a known indent, so the block for a key runs from its opening line to the
 * line that closes it at the same indent. Matching on that rather than parsing
 * the PHP keeps the rest of the file, including its comments, exactly as it
 * was.
 */
function calcr_replace_key( $source, $key, $php ) {
	$open = "\n\t\t'" . $key . "' => array(";
	$at   = strpos( $source, $open );

	if ( false === $at ) {
		/* Not there yet, so add it before the closing parenthesis. */
		$close = strrpos( $source, "\n);" );

		if ( false === $close ) {
			return null;
		}

		return substr( $source, 0, $close ) . "\n\t\t'" . $key . "' => " . $php . ',' . substr( $source, $close );
	}

	/* Walk to the line that closes this key at its own indent. */
	$end = strpos( $source, "\n\t\t),", $at + strlen( $open ) );

	if ( false === $end ) {
		return null;
	}

	$end += strlen( "\n\t\t)," );

	return substr( $source, 0, $at ) . "\n\t\t'" . $key . "' => " . $php . ',' . substr( $source, $end );
}

$done = 0;
$failed = 0;

foreach ( $slugs as $slug ) {
	$json = $dir . '/' . $slug . '.json';
	$php  = $root . '/calculators/' . $slug . '.php';

	if ( ! file_exists( $json ) ) {
		fwrite( STDERR, "  no content for $slug\n" );
		$failed++;
		continue;
	}

	if ( ! file_exists( $php ) ) {
		fwrite( STDERR, "  no calculator called $slug\n" );
		$failed++;
		continue;
	}

	$content = json_decode( file_get_contents( $json ), true );

	if ( ! is_array( $content ) ) {
		fwrite( STDERR, "  $slug: content is not valid JSON\n" );
		$failed++;
		continue;
	}

	$source = file_get_contents( $php );
	$before = $source;

	foreach ( array( 'explainer', 'faqs' ) as $key ) {
		if ( ! isset( $content[ $key ] ) ) {
			continue;
		}

		$next = calcr_replace_key( $source, $key, calcr_export( $content[ $key ], 2 ) );

		if ( null === $next ) {
			fwrite( STDERR, "  $slug: could not find where $key lives\n" );
			$next = $source;
			$failed++;
		}

		$source = $next;
	}

	if ( $source === $before ) {
		continue;
	}

	file_put_contents( $php, $source );

	/* A config that no longer parses is worse than one that was never
	   touched, so it is checked before the next one is written. */
	exec( 'php -l ' . escapeshellarg( $php ) . ' 2>&1', $out, $code );

	if ( 0 !== $code ) {
		file_put_contents( $php, $before );
		fwrite( STDERR, "  $slug: would not parse, reverted\n" );
		$failed++;
		continue;
	}

	$sections = count( isset( $content['explainer'] ) ? $content['explainer'] : array() );
	$faqs     = count( isset( $content['faqs'] ) ? $content['faqs'] : array() );
	printf( "  %-36s %d sections, %d questions\n", $slug, $sections, $faqs );
	$done++;
}

printf( "\n%d applied, %d failed\n", $done, $failed );
exit( $failed ? 1 : 0 );
