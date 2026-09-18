<?php
/**
 * Renders every calculator page and every category hub, then checks the
 * output for the faults that only show up once the markup exists: PHP
 * notices, missing form controls, unlabelled inputs, empty results and
 * broken structured data.
 *
 * Usage: php tests/test-render.php
 */

require_once __DIR__ . '/bootstrap.php';

/* Anything PHP wants to complain about is a failure, not a footnote. */
$GLOBALS['calcr_php_problems'] = array();
set_error_handler( function ( $no, $str, $file, $line ) {
	$GLOBALS['calcr_php_problems'][] = sprintf( '%s at %s:%d', $str, basename( $file ), $line );
	return true;
} );
error_reporting( E_ALL );

$registry = Calculatorr_Registry::instance();
$renderer = Calculatorr_Renderer::instance();
$schema   = Calculatorr_Schema::instance();
$seo      = Calculatorr_SEO::instance();

$errors = array();
$warnings = array();
$checked = 0;

foreach ( $registry->all() as $slug => $config ) {
	$GLOBALS['calcr_test_state']['current_slug'] = $slug;
	$GLOBALS['calcr_test_state']['current_category'] = null;
	$GLOBALS['calcr_php_problems'] = array();

	$html = $renderer->render_page( $config );

	ob_start();
	$schema->output();
	$json = ob_get_clean();

	ob_start();
	$seo->head_tags();
	$head = ob_get_clean();

	$checked++;
	$p = function ( $msg ) use ( &$errors, $slug ) { $errors[] = "$slug: $msg"; };
	$w = function ( $msg ) use ( &$warnings, $slug ) { $warnings[] = "$slug: $msg"; };

	foreach ( $GLOBALS['calcr_php_problems'] as $problem ) {
		$p( 'PHP: ' . $problem );
	}

	/* --- the calculator itself ------------------------------------------ */
	if ( strlen( $html ) < 500 ) {
		$p( 'rendered only ' . strlen( $html ) . ' bytes' );
	}
	if ( false === strpos( $html, 'data-calcr-slug="' . $slug . '"' ) ) {
		$p( 'root element missing its slug' );
	}
	if ( false === strpos( $html, 'data-calcr-result' ) ) {
		$p( 'no result panel' );
	}
	if ( false === strpos( $html, 'data-calcr-share-toggle' ) ) {
		$p( 'no share button' );
	}

	$inputs = substr_count( $html, 'data-calcr-input=' ) + substr_count( $html, 'data-calcr-rep-cell=' );
	if ( $inputs < 1 ) {
		$p( 'no input controls rendered' );
	}

	/* Every visible control needs a label or an aria-label, or a keyboard and
	   screen reader user cannot tell what it is for. */
	preg_match_all( '/<input[^>]*>/', $html, $tags );
	foreach ( $tags[0] as $tag ) {
		$hasId = preg_match( '/\bid="([^"]+)"/', $tag, $m );
		$labelled = $hasId && false !== strpos( $html, 'for="' . $m[1] . '"' );
		if ( ! $labelled && false === strpos( $tag, 'aria-label' ) && false === strpos( $tag, 'type="hidden"' ) ) {
			$w( 'input without a label: ' . substr( $tag, 0, 70 ) );
		}
	}

	/* --- the answer ------------------------------------------------------ */
	$result = $config['default_result'];
	if ( empty( $result['value'] ) || '—' === $result['value'] ) {
		$w( 'default result is empty, so the page loads with no answer' );
	}
	foreach ( array( 'NaN', 'undefined', 'Infinity', 'null' ) as $bad ) {
		if ( false !== strpos( (string) $result['value'], $bad ) ) {
			$p( "default result contains $bad" );
		}
		foreach ( $result['rows'] as $row ) {
			if ( false !== strpos( (string) $row['value'], $bad ) ) {
				$p( "row '{$row['label']}' contains $bad" );
			}
		}
	}

	/* --- SEO ------------------------------------------------------------- */
	if ( strlen( $config['meta_title'] ) > 60 ) { $p( 'meta_title too long' ); }
	$mdLen = strlen( $config['meta_description'] );
	if ( $mdLen < 120 || $mdLen > 160 ) { $p( "meta_description is $mdLen chars" ); }
	if ( false === strpos( $head, 'rel="canonical"' ) ) { $p( 'no canonical tag' ); }
	if ( false === strpos( $head, 'og:title' ) ) { $p( 'no Open Graph title' ); }

	/* --- structured data -------------------------------------------------- */
	if ( ! preg_match( '/<script type="application\/ld\+json">(.*)<\/script>/s', $json, $m ) ) {
		$p( 'no JSON-LD emitted' );
	} else {
		$decoded = json_decode( $m[1], true );
		if ( null === $decoded ) {
			$p( 'JSON-LD is not valid JSON: ' . json_last_error_msg() );
		} else {
			$types = array_column( $decoded['@graph'], '@type' );
			foreach ( array( 'Organization', 'WebSite', 'BreadcrumbList', 'WebPage', 'SoftwareApplication' ) as $need ) {
				if ( ! in_array( $need, $types, true ) ) { $p( "JSON-LD missing $need" ); }
			}
			if ( ! empty( $config['faqs'] ) && ! in_array( 'FAQPage', $types, true ) ) {
				$p( 'has FAQs but no FAQPage node' );
			}
		}
	}

	/* --- content ---------------------------------------------------------- */
	if ( count( $config['faqs'] ) < 2 )      { $w( 'fewer than two FAQs' ); }
	if ( count( $config['explainer'] ) < 2 ) { $w( 'fewer than two explainer sections' ); }
	foreach ( $config['related'] as $rel ) {
		if ( ! $registry->get( $rel ) ) { $w( "related link to missing $rel" ); }
		if ( $rel === $slug )           { $p( 'links to itself as related' ); }
	}
}

/* --- category hubs --------------------------------------------------------- */
foreach ( $registry->categories() as $key => $category ) {
	$GLOBALS['calcr_test_state']['current_slug'] = null;
	$GLOBALS['calcr_test_state']['current_category'] = $key;
	$GLOBALS['calcr_php_problems'] = array();

	$html = $renderer->category_shortcode( array( 'key' => $key ) );
	ob_start(); $schema->output(); $json = ob_get_clean();
	$checked++;

	if ( strlen( $html ) < 200 ) { $errors[] = "$key hub: rendered only " . strlen( $html ) . ' bytes'; }
	if ( ! $registry->in_category( $key ) ) { $errors[] = "$key hub: no calculators in it"; }
	if ( strlen( $category['meta_title'] ) > 60 ) { $errors[] = "$key hub: meta_title too long"; }
	$len = strlen( $category['meta_description'] );
	if ( $len < 120 || $len > 160 ) { $errors[] = "$key hub: meta_description is $len chars"; }
	if ( false === strpos( $json, 'CollectionPage' ) ) { $errors[] = "$key hub: no CollectionPage schema"; }
	foreach ( $GLOBALS['calcr_php_problems'] as $problem ) { $errors[] = "$key hub PHP: $problem"; }
}

restore_error_handler();

printf( "checked %d pages\n\n", $checked );

foreach ( array_slice( $warnings, 0, 40 ) as $warning ) { echo "  WARN  $warning\n"; }
if ( count( $warnings ) > 40 ) { printf( "  ... and %d more warnings\n", count( $warnings ) - 40 ); }
foreach ( $errors as $error ) { echo "  ERROR $error\n"; }

printf( "\n%d errors, %d warnings\n", count( $errors ), count( $warnings ) );
exit( $errors ? 1 : 0 );
