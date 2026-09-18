<?php
/**
 * Dumps every calculator's field defaults so the build step can compute the
 * server-rendered result from the real formula.
 *
 * Usage: php tools/build-defaults.php > defaults-input.json
 */

define( 'ABSPATH', __DIR__ );

$out = array();

foreach ( glob( dirname( __DIR__ ) . '/calculators/*.php' ) as $file ) {
	if ( basename( $file )[0] === '_' ) {
		continue;
	}

	$config = include $file;
	$values = array();

	foreach ( $config['fields'] as $field ) {
		if ( 'repeater' === $field['type'] ) {
			$row = array();
			foreach ( $field['row'] as $cell ) {
				$row[ $cell['id'] ] = (string) $cell['default'];
			}
			$values[ $field['id'] ] = array_fill( 0, isset( $field['rows'] ) ? (int) $field['rows'] : 3, $row );
			continue;
		}

		$values[ $field['id'] ] = (string) $field['default'];
	}

	$out[] = array( 'slug' => $config['slug'], 'values' => $values );
}

echo wp_json_encode_fallback( $out );

function wp_json_encode_fallback( $data ) {
	return json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
}
