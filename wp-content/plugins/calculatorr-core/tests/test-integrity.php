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
	/* Files beginning with an underscore are build output rather than
	   calculators, matching what the registry skips. */
	if ( '_' === basename( $file )[0] ) {
		continue;
	}

	$config = include $file;
	$configs[ $config['slug'] ] = $config;

	if ( basename( $file, '.php' ) !== $config['slug'] ) {
		$errors[] = sprintf( 'filename %s does not match slug %s', basename( $file ), $config['slug'] );
	}
}

$errors = isset( $errors ) ? $errors : array();

/*
 * Calculators defined in JSON stand beside the PHP ones from here on. They
 * carry their own formula rather than having one in the bundle, so they have
 * to join both lists or the check below would report every one of them as a
 * config with no formula behind it.
 */
$json = array();

foreach ( glob( $root . '/calculators/json/*.json' ) as $file ) {
	$definition = json_decode( file_get_contents( $file ), true );

	if ( ! is_array( $definition ) || empty( $definition['slug'] ) ) {
		$errors[] = 'unreadable JSON definition: ' . basename( $file );
		continue;
	}

	$slug = $definition['slug'];

	if ( basename( $file, '.json' ) !== $slug ) {
		$errors[] = sprintf( 'filename %s does not match slug %s', basename( $file ), $slug );
	}

	if ( isset( $configs[ $slug ] ) ) {
		$errors[] = sprintf(
			'%s is defined twice, once as %s and again in %s',
			$slug,
			in_array( $slug, $json, true ) ? 'another JSON definition' : 'a PHP config',
			basename( $file )
		);
		continue;
	}

	if ( empty( $definition['formula'] ) ) {
		$errors[] = "JSON definition carries no formula: $slug";
	}

	if ( empty( $definition['default_result']['value'] ) ) {
		$errors[] = "JSON definition carries no worked answer: $slug";
	}

	$configs[ $slug ] = $definition;
	$json[] = $slug;
}

$js = file_get_contents( $root . '/assets/js/formulas.js' );
preg_match_all( "/formulas\[ '([a-z0-9\-]+)' \]/", $js, $matches );
$formulas = array_merge( array_unique( $matches[1] ), $json );
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

/*
 * The version is declared twice: in the plugin header, which is what WordPress
 * reads, and as a constant, which is what cache-busts the stylesheets. If they
 * drift apart an update ships without the browser refetching the CSS, so the
 * new design simply does not appear.
 */
$bootstrap = file_get_contents( $root . '/calculatorr-core.php' );
preg_match( '/^ \* Version:\s+(\S+)/m', $bootstrap, $header_version );
preg_match( "/define\(\s*'CALCULATORR_VERSION',\s*'([^']+)'/", $bootstrap, $constant_version );

if ( empty( $header_version[1] ) || empty( $constant_version[1] ) ) {
	$errors[] = 'Could not read both version declarations from the plugin bootstrap.';
} elseif ( $header_version[1] !== $constant_version[1] ) {
	$errors[] = sprintf(
		'Version mismatch: plugin header says %s, CALCULATORR_VERSION says %s.',
		$header_version[1],
		$constant_version[1]
	);
}

/* Every stylesheet the plugin enqueues has to exist, since a 404 on site.css
   is invisible in PHP and very visible on the page. */
foreach ( array( 'tokens.css', 'calculator.css', 'site.css' ) as $sheet ) {
	if ( ! file_exists( $root . '/assets/css/' . $sheet ) ) {
		$errors[] = 'Missing stylesheet: assets/css/' . $sheet;
	}
}

printf( "%d configs (%d of them JSON), %d formulas\n", count( $configs ), count( $json ), count( $formulas ) );

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
