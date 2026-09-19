<?php
/**
 * What happens when a dedicated SEO plugin is installed alongside this one.
 *
 * This is its own file because the answer depends on a constant that has to be
 * defined before the plugin classes load, and the other suites need the
 * opposite case. Rank Math stands in for the group: the plugin checks for
 * Yoast, Rank Math, AIOSEO, SEOPress and The SEO Framework the same way.
 *
 * Usage: php tests/test-seo-handover.php
 */

define( 'RANK_MATH_VERSION', '1.0.0-test' );

require_once __DIR__ . '/bootstrap.php';

$failures = array();
$checks   = 0;

function check( $label, $actual, $expected ) {
	global $failures, $checks;
	$checks++;
	if ( $actual !== $expected ) {
		$failures[] = sprintf( '%s: got %s, expected %s', $label, var_export( $actual, true ), var_export( $expected, true ) );
	}
}

$seo    = Calculatorr_SEO::instance();
$schema = Calculatorr_Schema::instance();

check( 'defers the head to the other plugin', $seo->is_deferring(), true );
check( 'reports the other plugin as active', $seo->other_plugin_active(), true );

/* --- what the schema emits alongside it ---------------------------------- */

function graph_for( $slug, $category = null ) {
	global $schema;
	$GLOBALS['calcr_test_state']['current_slug']     = $slug;
	$GLOBALS['calcr_test_state']['current_category'] = $category;

	ob_start();
	$schema->output();
	$html = ob_get_clean();

	$GLOBALS['calcr_test_state']['current_slug']     = null;
	$GLOBALS['calcr_test_state']['current_category'] = null;

	if ( ! preg_match( '#<script type="application/ld\+json">(.*?)</script>#s', $html, $m ) ) {
		return array();
	}

	$decoded = json_decode( $m[1], true );

	return isset( $decoded['@graph'] ) ? $decoded['@graph'] : array();
}

function types( $graph ) {
	return array_map(
		function ( $node ) {
			return isset( $node['@type'] ) ? $node['@type'] : '?';
		},
		$graph
	);
}

$calculator = graph_for( 'compound-interest-calculator' );
$found      = types( $calculator );

check( 'no duplicate Organization', in_array( 'Organization', $found, true ), false );
check( 'no duplicate WebSite', in_array( 'WebSite', $found, true ), false );
check( 'no duplicate WebPage', in_array( 'WebPage', $found, true ), false );
check( 'still describes the tool', in_array( 'SoftwareApplication', $found, true ), true );
check( 'still describes the trail', in_array( 'BreadcrumbList', $found, true ), true );
check( 'still describes the questions', in_array( 'FAQPage', $found, true ), true );

/* Nothing may point at a node that is no longer in the graph. */
$ids = array();

foreach ( $calculator as $node ) {
	if ( isset( $node['@id'] ) ) {
		$ids[] = $node['@id'];
	}
}

$dangling = array();

array_walk_recursive(
	$calculator,
	function ( $value, $key ) use ( $ids, &$dangling ) {
		if ( '@id' === $key && ! in_array( $value, $ids, true ) ) {
			$dangling[] = $value;
		}
	}
);

/* The breadcrumb reference on a node this graph still carries is fine; what
   would not be fine is a pointer to the Organization or WebSite it dropped. */
check( 'no reference to a dropped node', $dangling, array() );

foreach ( $calculator as $node ) {
	if ( isset( $node['@type'] ) && 'SoftwareApplication' === $node['@type'] ) {
		check( 'publisher is written out rather than referenced', isset( $node['publisher']['name'] ), true );
	}
}

$category = graph_for( null, 'finance' );
$found    = types( $category );

check( 'category keeps its list', in_array( 'CollectionPage', $found, true ), true );
check( 'category drops Organization', in_array( 'Organization', $found, true ), false );

/* --- feeding the other plugin -------------------------------------------- */

$GLOBALS['calcr_test_state']['current_slug'] = 'compound-interest-calculator';
$config = Calculatorr_Registry::instance()->get( 'compound-interest-calculator' );

check( 'hands the other plugin our title', $seo->filter_title_string( 'whatever' ), $config['meta_title'] );
check( 'hands the other plugin our description', $seo->filter_description_string( 'whatever' ), $config['meta_description'] );

$GLOBALS['calcr_test_state']['current_slug'] = null;

check( 'leaves an unrelated page alone', $seo->filter_title_string( 'Some blog post' ), 'Some blog post' );

/* --- results -------------------------------------------------------------- */
printf( "%d checks\n\n", $checks );
foreach ( $failures as $failure ) { echo "  FAIL $failure\n"; }
printf( "\n%d failures\n", count( $failures ) );
exit( $failures ? 1 : 0 );
