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
$GLOBALS['calcr_depth'] = array();

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

	/* --- the page scaffold, as drawn in the design ----------------------- */
	foreach ( array(
		'calcr-breadcrumb'  => 'breadcrumb trail',
		'calcr-layout'      => 'two column layout',
		'calcr-sidebar'     => 'sidebar',
		'calcr-related'     => 'related calculators',
		'calcr-prose'       => 'explainer',
	) as $needle => $label ) {
		if ( false === strpos( $html, $needle ) ) {
			$p( 'missing ' . $label );
		}
	}

	/* The sidebar must always offer the neighbouring calculators, but it does
	   so either as a house promo or as the more-in-category block depending on
	   what is filling the ad slot, and never as both. */
	$hasPromo = false !== strpos( $html, 'calcr-ad--house' );
	$hasPopular = false !== strpos( $html, 'calcr-popular' );

	if ( ! $hasPromo && ! $hasPopular ) {
		$p( 'sidebar offers no links to neighbouring calculators' );
	}
	if ( $hasPromo && $hasPopular ) {
		$p( 'sidebar lists the same calculators twice' );
	}

	/* The breadcrumb has to be a real trail rather than a single crumb, and
	   the visible one must agree with the BreadcrumbList markup. */
	if ( substr_count( $html, '<li>' ) < 3 ) {
		$p( 'breadcrumb has fewer than three levels' );
	}
	if ( false === strpos( $html, 'aria-current="page"' ) ) {
		$p( 'breadcrumb does not mark the current page' );
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

			/* The visible trail and the marked-up one describing different
			   things is worse than having neither. */
			foreach ( $decoded['@graph'] as $node ) {
				if ( 'BreadcrumbList' !== $node['@type'] ) {
					continue;
				}
				if ( count( $node['itemListElement'] ) !== 3 ) {
					$p( 'BreadcrumbList has ' . count( $node['itemListElement'] ) . ' levels, not 3' );
				}
				$last = end( $node['itemListElement'] );
				if ( $last['name'] !== $config['h1'] ) {
					$p( 'breadcrumb markup names "' . $last['name'] . '" but the page is "' . $config['h1'] . '"' );
				}
			}
			foreach ( array( 'Organization', 'WebSite', 'BreadcrumbList', 'WebPage', 'SoftwareApplication' ) as $need ) {
				if ( ! in_array( $need, $types, true ) ) { $p( "JSON-LD missing $need" ); }
			}
			if ( ! empty( $config['faqs'] ) && ! in_array( 'FAQPage', $types, true ) ) {
				$p( 'has FAQs but no FAQPage node' );
			}
		}
	}

	/* --- content ---------------------------------------------------------- */
	$words = str_word_count( wp_strip_all_tags( $config['description'] . ' ' . $config['meta_description'] . ' ' . $config['disclaimer'] ) );
	foreach ( $config['explainer'] as $section ) {
		$words += str_word_count( wp_strip_all_tags(
			$section['heading'] . ' ' . $section['body'] . ' ' . ( isset( $section['formula'] ) ? $section['formula'] : '' )
			. ' ' . ( isset( $section['example'] ) ? $section['example'] : '' )
			. ' ' . ( isset( $section['steps'] ) ? implode( ' ', $section['steps'] ) : '' )
		) );
	}
	foreach ( $config['faqs'] as $faq ) {
		$words += str_word_count( wp_strip_all_tags( $faq['q'] . ' ' . $faq['a'] ) );
	}

	$GLOBALS['calcr_depth'][ $slug ] = $words;

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

/* --- stylesheet guards ----------------------------------------------------
 *
 * Conditional fields are hidden by setting the hidden attribute, and any
 * author rule that sets display beats the browser's own [hidden] rule. Without
 * an explicit override every calculator with a unit switch shows both sets of
 * inputs at once, which is invisible to a PHP test and obvious to a visitor.
 */
$css = file_get_contents( CALCULATORR_PATH . 'assets/css/calculator.css' );

if ( ! preg_match( '/\[hidden\][^{]*\{[^}]*display:\s*none/', $css ) ) {
	$errors[] = 'stylesheet: nothing overrides display for [hidden], so conditional fields will stay visible';
}

foreach ( array( '.calcr-field', '.calcr__share-panel', '.calcr__sticky' ) as $selector ) {
	if ( false === strpos( $css, $selector ) ) {
		$errors[] = 'stylesheet: ' . $selector . ' has no rule at all';
	}
}

/*
 * The generated home and hub pages.
 *
 * These are built by tools/build-home.py rather than rendered by the plugin, so
 * nothing above this point looks at them, and a regression there would ship
 * silently. The three checks below are each a bug that actually happened.
 */
foreach ( array( 'home.html', 'all-calculators.html' ) as $page ) {
	$path = dirname( __DIR__ ) . '/content/' . $page;

	if ( ! file_exists( $path ) ) {
		$errors[] = 'content: ' . $page . ' has not been generated';
		continue;
	}

	$markup = file_get_contents( $path );

	/*
	 * Card titles came out teal once, because `.ch a` sets the link colour at
	 * specificity (0,1,1) and the card's own `color:inherit` sits at (0,1,0)
	 * and loses. The fix has to out-specify the link rule, so the check is that
	 * the winning selector is still there rather than that some rule mentions
	 * the colour.
	 */
	if ( false === strpos( $markup, '.ch a.ch-cat,.ch a.ch-tool{color:var(--ch-ink)}' ) ) {
		$errors[] = 'content: ' . $page . ' no longer forces card titles back to the text colour, so they will inherit the teal link colour';
	}

	/*
	 * The floating chips carry a small line over a solid brand or accent fill.
	 * Fading it, which the artboard does at 0.75, drops it to 3.5:1 on the
	 * light-mode amber, and nothing below 0.95 clears 4.5:1, so the fade is
	 * never the right answer here.
	 */
	if ( preg_match( '/\.ch-float\s+small\{[^}]*opacity/', $markup ) ) {
		$errors[] = 'content: ' . $page . ' fades the chip caption, which fails the contrast floor on the light-mode amber';
	}

	/*
	 * The chips are anchored to a stage that is wider than the card. Negative
	 * offsets put them outside the hero column instead, which crowded the
	 * viewport edge at 1440.
	 */
	if ( preg_match( '/\.ch-float--(bmi|tip|pct)\{[^}]*:-\d/', $markup ) ) {
		$errors[] = 'content: ' . $page . ' hangs a floating chip on a negative offset, which puts it outside the hero column';
	}
}

restore_error_handler();

$depth = $GLOBALS['calcr_depth'];
sort( $depth );
$median = $depth[ intdiv( count( $depth ), 2 ) ];

printf( "checked %d pages\n", $checked );
printf( "content depth: median %d words, %d pages under 400, %d under 700\n\n",
	$median,
	count( array_filter( $depth, function ( $n ) { return $n < 400; } ) ),
	count( array_filter( $depth, function ( $n ) { return $n < 700; } ) )
);

foreach ( array_slice( $warnings, 0, 40 ) as $warning ) { echo "  WARN  $warning\n"; }
if ( count( $warnings ) > 40 ) { printf( "  ... and %d more warnings\n", count( $warnings ) - 40 ); }
foreach ( $errors as $error ) { echo "  ERROR $error\n"; }

printf( "\n%d errors, %d warnings\n", count( $errors ), count( $warnings ) );
exit( $errors ? 1 : 0 );
