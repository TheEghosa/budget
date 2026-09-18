<?php
/**
 * Consistency check across the whole registry, runnable without WordPress.
 *
 * It catches the failures that are invisible until a page is loaded: a config
 * with no formula behind it, a formula nothing calls, a related link pointing
 * at a calculator that does not exist, and a show_when clause naming a field
 * that was renamed out from under it.
 *
 * Usage: php tests/test-integrity.php
 */

define( 'ABSPATH', __DIR__ );

$root = dirname( __DIR__ );
$configs = array();

foreach ( glob( $root . '/calculators/*.php' ) as $file ) {
	$config = include $file;
	$configs[ $config['slug'] ] = $config;

	if ( basename( $file, '.php' ) !== $config['slug'] ) {
		$errors[] = sprintf( 'filename %s does not match slug %s', basename( $file ), $config['slug'] );
	}
}

$js = file_get_contents( $root . '/assets/js/formulas.js' );
preg_match_all( "/formulas\[ '([a-z0-9\-]+)' \]/", $js, $matches );
$formulas = array_unique( $matches[1] );

$errors = isset( $errors ) ? $errors : array();
$warnings = array();

foreach ( $configs as $slug => $config ) {
	if ( ! in_array( $slug, $formulas, true ) ) {
		$errors[] = "no formula for config: $slug";
	}

	foreach ( array( 'title', 'category', 'description', 'meta_title', 'meta_description', 'h1' ) as $key ) {
		if ( empty( $config[ $key ] ) ) {
			$errors[] = "$slug is missing $key";
		}
	}

	if ( strlen( $config['meta_title'] ) > 60 ) {
		$errors[] = sprintf( '%s meta_title is %d chars', $slug, strlen( $config['meta_title'] ) );
	}

	$len = strlen( $config['meta_description'] );
	if ( $len < 120 || $len > 160 ) {
		$errors[] = sprintf( '%s meta_description is %d chars', $slug, $len );
	}

	$ids = array();
	foreach ( $config['fields'] as $field ) {
		$ids[] = $field['id'];
	}

	foreach ( $config['fields'] as $field ) {
		if ( empty( $field['show_when'] ) ) {
			continue;
		}
		foreach ( array_keys( $field['show_when'] ) as $depends_on ) {
			if ( ! in_array( $depends_on, $ids, true ) ) {
				$errors[] = "$slug: field {$field['id']} depends on unknown field $depends_on";
			}
		}
	}

	foreach ( $config['related'] as $related ) {
		if ( ! isset( $configs[ $related ] ) ) {
			$warnings[] = "$slug links to $related, which is not built yet";
		}
	}
}

foreach ( $formulas as $slug ) {
	if ( ! isset( $configs[ $slug ] ) ) {
		$errors[] = "formula with no config: $slug";
	}
}

printf( "%d configs, %d formulas\n", count( $configs ), count( $formulas ) );

foreach ( $warnings as $warning ) {
	echo "  WARN  $warning\n";
}

foreach ( $errors as $error ) {
	echo "  ERROR $error\n";
}

if ( $errors ) {
	printf( "\n%d errors\n", count( $errors ) );
	exit( 1 );
}

printf( "\nAll checks passed (%d warnings).\n", count( $warnings ) );
