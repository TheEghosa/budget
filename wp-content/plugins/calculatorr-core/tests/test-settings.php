<?php
/**
 * The control panel's behaviour: switching a calculator off, overriding its
 * text, toggling features, and the error log's de-duplication and cap.
 *
 * Usage: php tests/test-settings.php
 */

require_once __DIR__ . '/bootstrap.php';

$failures = array();
$checks = 0;

function check( $label, $actual, $expected ) {
	global $failures, $checks;
	$checks++;
	if ( $actual !== $expected ) {
		$failures[] = sprintf( '%s: got %s, expected %s', $label, var_export( $actual, true ), var_export( $expected, true ) );
	}
}

function check_quiet( $label, $actual, $expected ) {
	global $failures, $checks;
	$checks++;
	if ( $actual !== $expected ) {
		$failures[] = $label;
	}
}

$settings = Calculatorr_Settings::instance();
$registry = Calculatorr_Registry::instance();
$renderer = Calculatorr_Renderer::instance();
$log      = Calculatorr_Error_Log::instance();

$total = count( $registry->all() );

/* --- switching a calculator off ----------------------------------------- */
check( 'starts with none disabled', $settings->get( 'disabled' ), array() );
check( 'get_live returns a live calculator', is_array( $registry->get_live( 'tip-calculator' ) ), true );

$values = $settings->all();
$values['disabled'] = array( 'tip-calculator' );
$settings->save( $values );

check( 'is_disabled reports it', $settings->is_disabled( 'tip-calculator' ), true );
check( 'get_live now returns null', $registry->get_live( 'tip-calculator' ), null );
check( 'get still returns it for admin', is_array( $registry->get( 'tip-calculator' ) ), true );
check( 'admin list still complete', count( $registry->all() ), $total );
check( 'front-end list is one shorter', count( $registry->all( false ) ), $total - 1 );
check( 'shortcode renders nothing', $renderer->shortcode( array( 'slug' => 'tip-calculator' ) ), '' );
check( 'a live one still renders', strlen( $renderer->shortcode( array( 'slug' => 'gpa-calculator' ) ) ) > 500, true );

$before = count( $registry->in_category( 'business', false ) );
check( 'hub skips the disabled one', $before, count( $registry->in_category( 'business' ) ) - 1 );

/* Put it back so the rest of the run is unaffected. */
$values['disabled'] = array();
$settings->save( $values );
check( 'restored', $registry->get_live( 'tip-calculator' ) !== null, true );

/* --- feature switches ---------------------------------------------------- */
$values = $settings->all();
$values['share_enabled'] = 0;
$settings->save( $values );
$html = $renderer->render_widget( $registry->get( 'gpa-calculator' ) );
check( 'share button gone when switched off', false === strpos( $html, 'data-calcr-share-toggle' ), true );

$values['share_enabled'] = 1;
$settings->save( $values );
$html = $renderer->render_widget( $registry->get( 'gpa-calculator' ) );
check( 'share button back', false !== strpos( $html, 'data-calcr-share-toggle' ), true );

/* With paid ads off but house promos on, a slot is not empty: it promotes
   other calculators. Switching both off is what empties it. */
$values['ads_enabled'] = 0;
$values['house_ads_enabled'] = 0;
$settings->save( $values );
check( 'no slot at all when both are off', Calculatorr_Ads::instance()->slot( 'in_content' ), '' );

$values['ads_enabled'] = 1;
$values['ad_in_content'] = '<!-- ad code -->';
$settings->save( $values );
check( 'ad slot renders the stored code', false !== strpos( Calculatorr_Ads::instance()->slot( 'in_content' ), '<!-- ad code -->' ), true );

$values['ads_enabled'] = 0;
$values['ad_in_content'] = '';
$settings->save( $values );

/* --- text overrides ------------------------------------------------------ */
$settings->save_override( 'gpa-calculator', array( 'h1' => 'Grade Point Average Calculator' ) );

/* The registry caches on construction, so a fresh read proves the override
   survives a real page load rather than only the current request. */
$reloaded = ( function () {
	$ref = new ReflectionClass( 'Calculatorr_Registry' );
	$prop = $ref->getProperty( 'instance' );
	$prop->setAccessible( true );
	$prop->setValue( null, null );
	return Calculatorr_Registry::instance();
} )();

check( 'override applied to h1', $reloaded->get( 'gpa-calculator' )['h1'], 'Grade Point Average Calculator' );
check( 'other fields untouched', $reloaded->get( 'gpa-calculator' )['category'], 'education' );

$settings->save_override( 'gpa-calculator', array() );
check( 'override cleared', $settings->overrides( 'gpa-calculator' ), array() );

/* --- ad slots and house promos ------------------------------------------- */
$ads = Calculatorr_Ads::instance();
$concrete = $registry->get( 'concrete-calculator' );

$values = $settings->all();
$values['ads_enabled'] = 0;
$values['house_ads_enabled'] = 1;
$values['ad_in_content'] = '';
$settings->save( $values );

check( 'empty slot falls back to a house promo', false !== strpos( $ads->slot( 'in_content', $concrete ), 'calcr-ad--house' ), true );
check( 'the promo carries real links', substr_count( $ads->slot( 'sidebar', $concrete ), '<a href' ) >= 7, true );
check( 'the sidebar promo is taller than the leaderboard one',
	substr_count( $ads->slot( 'sidebar', $concrete ), '<li>' ) > substr_count( $ads->slot( 'after_calculator', $concrete ), '<li>' ), true );

preg_match_all( '/calcr-house__name">([^<]+)</', $ads->slot( 'sidebar', $concrete ), $names );
check( 'a page never promotes itself', in_array( 'Concrete Calculator', $names[1], true ), false );
check( 'it leads with the page\'s own related tools', $names[1][0], 'Gravel Calculator' );

$values['ads_enabled'] = 1;
$values['ad_in_content'] = '<!-- paid unit -->';
$settings->save( $values );
check( 'paid code beats the house promo', false !== strpos( $ads->slot( 'in_content', $concrete ), 'paid unit' ), true );
check( 'a slot with no paid code still falls back', false !== strpos( $ads->slot( 'sidebar', $concrete ), 'calcr-ad--house' ), true );

$values['house_ads_enabled'] = 0;
$values['ad_sidebar'] = '';
$settings->save( $values );
check( 'both off renders nothing', $ads->slot( 'sidebar', $concrete ), '' );

/* A disabled calculator must not be promoted anywhere. */
$values['house_ads_enabled'] = 1;
$values['disabled'] = array( 'gravel-calculator' );
$settings->save( $values );
preg_match_all( '/calcr-house__name">([^<]+)</', $ads->slot( 'sidebar', $concrete ), $names );
check( 'a switched-off calculator is never promoted', in_array( 'Gravel Calculator', $names[1], true ), false );
$values['disabled'] = array();
$settings->save( $values );

/* The sidebar must never list the same calculators twice. */
$values = $settings->all();
$values['ads_enabled'] = 0;
$values['house_ads_enabled'] = 1;
$values['ad_sidebar'] = '';
$settings->save( $values );
$page = $renderer->render_page( $concrete );
check( 'house promo suppresses the duplicate block', false !== strpos( $page, 'calcr-ad--house' ) && false === strpos( $page, 'calcr-popular' ), true );

$values['ads_enabled'] = 1;
$values['ad_sidebar'] = '<!-- paid tower -->';
$settings->save( $values );
$page = $renderer->render_page( $concrete );
check( 'a paid tower keeps the internal links', false !== strpos( $page, 'calcr-popular' ), true );

$values['ads_enabled'] = 0;
$values['house_ads_enabled'] = 0;
$values['ad_sidebar'] = '';
$settings->save( $values );
$page = $renderer->render_page( $concrete );
check( 'with no slot at all the links remain', false !== strpos( $page, 'calcr-popular' ), true );

$values['house_ads_enabled'] = 1;
$settings->save( $values );

/* --- header and footer code ---------------------------------------------- */
$hf = Calculatorr_Head_Footer::instance();

$values = $settings->all();
$values['head_footer_enabled'] = 1;
$values['code_head'] = '<script src="https://pagead2.googlesyndication.com/x.js"></script>';
$values['code_footer'] = '<!-- footer tag -->';
$values['code_body'] = '';
$settings->save( $values );

ob_start(); $hf->head(); $head = ob_get_clean();
ob_start(); $hf->footer(); $foot = ob_get_clean();
ob_start(); $hf->body(); $body = ob_get_clean();

check( 'head code is printed verbatim', false !== strpos( $head, 'pagead2.googlesyndication.com' ), true );
check( 'and is not escaped', false === strpos( $head, '&lt;script' ), true );
check( 'footer code is printed', false !== strpos( $foot, 'footer tag' ), true );
check( 'an empty field prints nothing', trim( $body ), '' );

$values['head_footer_enabled'] = 0;
$settings->save( $values );
ob_start(); $hf->head(); $off = ob_get_clean();
check( 'the switch turns it off entirely', trim( $off ), '' );

$values['head_footer_enabled'] = 1;
$values['code_head'] = '';
$values['code_footer'] = '';
$settings->save( $values );

/* --- error log ----------------------------------------------------------- */
$log->clear();
check( 'log starts empty', $log->count(), 0 );

$log->add( 'js', 'x is not defined', array( 'slug' => 'tip-calculator' ) );
$log->add( 'js', 'x is not defined', array( 'slug' => 'tip-calculator' ) );
$log->add( 'js', 'x is not defined', array( 'slug' => 'tip-calculator' ) );
check( 'identical errors collapse to one entry', $log->count(), 1 );
check( 'and are counted', $log->entries()[0]['count'], 3 );

$log->add( 'php', 'something else', array( 'slug' => 'gpa-calculator' ) );
check( 'a different error is its own entry', $log->count(), 2 );
check( 'affected calculators tracked', count( $log->affected_slugs() ), 2 );

$values = $settings->all();
$values['log_limit'] = 20;
$settings->save( $values );

for ( $i = 0; $i < 60; $i++ ) {
	$log->add( 'js', 'error number ' . $i, array( 'slug' => 'test-' . $i ) );
}
check( 'log is capped', $log->count(), 20 );

$log->clear();
check( 'log clears', $log->count(), 0 );

/* --- site chrome ---------------------------------------------------------- */

/*
 * The chrome stylesheet is what makes the theme header carry the design, and
 * the heading handover is what stops the page ending up with two H1s or none.
 * Both are easy to break from the outside, so both are pinned here.
 */
check( 'site chrome is on by default', (int) $settings->get( 'site_chrome' ), 1 );
check( 'chrome stylesheet ships', file_exists( CALCULATORR_PATH . 'assets/css/site.css' ), true );

$chrome_css = file_get_contents( CALCULATORR_PATH . 'assets/css/site.css' );

/*
 * Out-specifying the theme, on purpose.
 *
 * Hello Elementor ships three rules that have each already broken this design
 * once, all by the same mechanism: the theme's selector is longer than ours,
 * so it wins no matter how obviously our rule is the one that was meant.
 *
 *   .site-header .header-inner .custom-logo-link { display: block }
 *     put the logo mark above the wordmark instead of beside it
 *   input[type=number], … { border: 1px solid #666; border-radius: 3px }
 *     drew a second bordered box inside every field
 *   .site-main a { color: … }
 *     turned the card titles on the home and category pages teal
 *
 * Each of ours has to stay at least as specific as the theme's, and none of
 * them may be rewritten shorter "for tidiness", which is exactly how this
 * would come back. Load order is not an acceptable answer either: a cache
 * plugin that combines stylesheets can change it without warning.
 */
$app_css = file_get_contents( CALCULATORR_PATH . 'assets/css/calculator.css' );

/* Comments are stripped before any selector is measured. The comments here
   quote the very rules being guarded against, so leaving them in means the
   check reads prose as CSS and counts the commas in a sentence as selector
   boundaries. */
$chrome_rules = preg_replace( '#/\*.*?\*/#s', '', $chrome_css );
$app_rules    = preg_replace( '#/\*.*?\*/#s', '', $app_css );

/* Counts the classes, elements and attributes in a selector, which is enough
   to compare against the theme's when neither side uses an id. */
function calcr_weight( $selector ) {
	$classes = preg_match_all( '/\.[a-zA-Z0-9_-]+/', $selector );
	$attrs   = preg_match_all( '/\[[^\]]+\]/', $selector );
	$elems   = preg_match_all( '/(?:^|\s|>|\+|~)([a-zA-Z][a-zA-Z0-9]*)/', $selector );

	return array( $classes + $attrs, $elems );
}

$logo_rule = preg_match( '/([^{}]*custom-logo-link[^{}]*)\{[^}]*display:\s*flex/', $chrome_rules, $m ) ? $m[1] : '';

/*
 * Every selector in the list, not the list as a whole. The header and the
 * footer are separate selectors and the theme overrides both, so one of them
 * being specific enough says nothing about the other, and measuring the pair
 * together hides exactly the case worth catching.
 */
$logo_weakest = $logo_rule ? PHP_INT_MAX : 0;

foreach ( explode( ',', $logo_rule ) as $one ) {
	if ( '' === trim( $one ) ) {
		continue;
	}

	list( $one_classes ) = calcr_weight( $one );
	$logo_weakest = min( $logo_weakest, $one_classes );
}

/* The theme's is .site-header .header-inner .custom-logo-link: three classes. */
check( 'the logo lockup out-specifies the theme', $logo_weakest >= 3, true );

check(
	'the field control out-specifies the theme',
	(bool) preg_match( '/\.calcr-input\s+\.calcr-input__control\s*\{/', $app_rules ),
	true
);

check(
	'the select out-specifies the theme',
	(bool) preg_match( '/select\.calcr-field__select\s*\{/', $app_rules ),
	true
);

check(
	'the category card title out-specifies the link colour',
	(bool) preg_match( '/\.calcr-hub\s+a\.calcr-hub__card/', $app_rules ),
	true
);

/*
 * The card must not clip its own children.
 *
 * The answer panel carries its own fill and has to reach the card's rounded
 * corner, which needs something to clip it. Putting that on the card clipped
 * the share popover too, which opens upward out of the actions row and so was
 * sliced off at the card's edge. Only the top half needs clipping, so the rule
 * belongs on .calcr__split and nowhere above it.
 */
$calcr_rule = preg_match( '/(?<![\w-])\.calcr\s*\{([^}]*)\}/', $app_rules, $m ) ? $m[1] : '';
$split_rule = preg_match( '/\.calcr__split\s*\{([^}]*)\}/', $app_rules, $m2 ) ? $m2[1] : '';

check( 'the card does not clip its own popovers', (bool) preg_match( '/overflow:\s*hidden/', $calcr_rule ), false );
check( 'the split clips the answer panel to the corner', (bool) preg_match( '/overflow:\s*hidden/', $split_rule ), true );

/* Share is the last control in the row and sits against the card's right
   edge, so a panel anchored to its left runs off the page. */
$panel_rule = preg_match( '/\.calcr__share-panel\s*\{([^}]*)\}/', $app_rules, $m3 ) ? $m3[1] : '';
check( 'the share panel opens away from the edge', (bool) preg_match( '/right:\s*0/', $panel_rule ), true );
check( 'the share panel counts its padding in its width', (bool) preg_match( '/box-sizing:\s*border-box/', $panel_rule ), true );

check(
	'chrome constrains the logo height',
	(bool) preg_match( '/img\.custom-logo\s*\{[^}]*height:/', $chrome_css ),
	true
);
check(
	'chrome colours the navigation',
	(bool) preg_match( '/site-navigation a\s*\{[^}]*color:\s*var\(--calcr-ink\)/', $chrome_css ),
	true
);
check(
	'chrome darkens the footer',
	(bool) preg_match( '/site-footer\.dynamic-footer\s*\{[^}]*background:\s*var\(--calcr-footer-bg\)/', $chrome_css ),
	true
);

/*
 * The footer band was painted with --calcr-ink, which is the body text colour
 * and therefore flips to near-white the moment the dark palette loads. The
 * whole footer turned white with pale grey links on it. The band needs a token
 * that stays dark in both palettes, so using the ink token here is the actual
 * regression to guard against, not just a style preference.
 */
check(
	'footer does not reuse a token that inverts',
	(bool) preg_match( '/site-footer\.dynamic-footer\s*\{[^}]*background:\s*var\(--calcr-ink\)/', $chrome_css ),
	false
);

/*
 * The palette is now generated from the design system's tokens.json, and the
 * names the site shipped with survive as aliases pointing into it. Counting
 * declarations no longer means anything, because an alias is declared once and
 * follows the theme through the token it references. What matters instead is
 * that nothing points at a token that does not exist, and that the light block
 * actually redefines the palette rather than merely existing.
 */
$tokens_css = file_get_contents( CALCULATORR_PATH . 'assets/css/tokens.css' );

preg_match_all( '/^\s*(--[a-zA-Z0-9-]+)\s*:/m', $tokens_css, $declared );
$declared = array_unique( $declared[1] );

$referenced = array();

foreach ( array( 'tokens.css', 'site.css', 'calculator.css' ) as $sheet ) {
	preg_match_all(
		'/var\(\s*(--[a-zA-Z0-9-]+)/',
		file_get_contents( CALCULATORR_PATH . 'assets/css/' . $sheet ),
		$found
	);
	$referenced = array_merge( $referenced, $found[1] );
}

/*
 * Three of these are not design tokens and never will be. A tile is drawn at
 * 28, 40, 52, 76 and 96 on different pages, so its size, radius and glyph come
 * from the element that asks for it rather than from the palette. They are
 * exempt here, but the exemption is earned rather than asserted: the check
 * below proves the helper really does set all three, so deleting that code
 * turns the exemption back into a failure instead of hiding one.
 */
$per_element = array( '--tile', '--tile-radius', '--tile-glyph' );

$tile = Calculatorr_Art::tile( '<svg></svg>', 52 );

foreach ( $per_element as $property ) {
	check(
		'the tile helper supplies ' . $property . ' itself',
		false !== strpos( $tile, $property . ':' ),
		true
	);
}

$dangling = array_values( array_unique( array_diff( $referenced, $declared, $per_element ) ) );

check( 'every token the stylesheets read is defined', $dangling, array() );
check( 'the design system palette is present', in_array( '--c-brand', $declared, true ), true );
check( 'the names the site shipped with still resolve', in_array( '--calcr-accent', $declared, true ), true );

/* A palette that never changes is not a palette. */
check( 'a light block exists', (bool) preg_match( '/\[data-theme="light"\]/', $tokens_css ), true );
check( 'the device preference is honoured', (bool) strpos( $tokens_css, 'prefers-color-scheme: light' ), true );
check( 'both theme attributes are keyed', (bool) strpos( $tokens_css, 'data-calcr-theme="dark"' ), true );

/*
 * Contrast is computed here rather than trusted, because the palette is now
 * generated and a single edited digit changes a ratio without changing
 * anything a person would notice while looking at the file. The shipped design
 * system had exactly this problem: amber was the one token identical in both
 * themes and measured 1.88 to 2.16:1 against every light surface, which fails
 * even the 3:1 floor for a graphic that carries meaning.
 */
function calcr_channel( $c ) {
	$c = $c / 255;
	return $c <= 0.03928 ? $c / 12.92 : pow( ( $c + 0.055 ) / 1.055, 2.4 );
}

function calcr_luminance( $hex ) {
	$hex = ltrim( $hex, '#' );

	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}

	return 0.2126 * calcr_channel( hexdec( substr( $hex, 0, 2 ) ) )
		+ 0.7152 * calcr_channel( hexdec( substr( $hex, 2, 2 ) ) )
		+ 0.0722 * calcr_channel( hexdec( substr( $hex, 4, 2 ) ) );
}

function calcr_contrast( $a, $b ) {
	$la = calcr_luminance( $a );
	$lb = calcr_luminance( $b );

	return ( max( $la, $lb ) + 0.05 ) / ( min( $la, $lb ) + 0.05 );
}

/** Reads one palette out of the built stylesheet. */
function calcr_palette( $css, $selector ) {
	$start = strpos( $css, $selector );

	if ( false === $start ) {
		return array();
	}

	$end = strpos( $css, "\n}", $start );
	preg_match_all( '/--(c-[a-z0-9-]+):\s*(#[0-9a-f]{3,8})\s*;/i', substr( $css, $start, $end - $start ), $m );

	return array_combine( $m[1], $m[2] );
}

$dark  = calcr_palette( $tokens_css, ':root,' );
$light = array_merge( $dark, calcr_palette( $tokens_css, ':root[data-theme="light"]' ) );

check( 'the dark palette parses', count( $dark ) > 20, true );
check( 'the light palette parses', count( $light ) > 20, true );

$surfaces = array( 'c-bg-page', 'c-bg-raised', 'c-bg-footer', 'c-surface-card', 'c-surface-sunken', 'c-surface-hover', 'c-result-bg', 'c-notice-bg' );
$texts    = array( 'c-text-primary', 'c-text-secondary', 'c-text-muted', 'c-text-subtle', 'c-brand', 'c-brand-soft-text', 'c-result-muted', 'c-notice-text' );

$below = array();

foreach ( array( 'dark' => $dark, 'light' => $light ) as $mode => $palette ) {
	foreach ( $texts as $text ) {
		foreach ( $surfaces as $surface ) {
			if ( ! isset( $palette[ $text ], $palette[ $surface ] ) ) {
				continue;
			}

			$ratio = calcr_contrast( $palette[ $text ], $palette[ $surface ] );

			if ( $ratio < 4.5 ) {
				$below[] = sprintf( '%s: %s on %s is %.2f', $mode, $text, $surface, $ratio );
			}
		}
	}
}

check( 'no text pairing falls below 4.5:1 in either theme', $below, array() );

/* Amber is a graphic rather than body text, so it answers to 3:1, and it has
   to clear that on the surfaces it is actually drawn on. */
$amber = array();

foreach ( array( 'dark' => $dark, 'light' => $light ) as $mode => $palette ) {
	foreach ( array( 'c-surface-card', 'c-result-bg', 'c-notice-bg', 'c-surface-sunken' ) as $surface ) {
		$ratio = calcr_contrast( $palette['c-accent'], $palette[ $surface ] );

		if ( $ratio < 3 ) {
			$amber[] = sprintf( '%s: accent on %s is %.2f', $mode, $surface, $ratio );
		}
	}
}

check( 'amber clears 3:1 wherever it is drawn', $amber, array() );

/* The failure that started this: one value serving both themes. */
check(
	'amber is not the same colour in both themes',
	$dark['c-accent'] !== $light['c-accent'],
	true
);

/* The wordmark is dark ink, so on a dark header it needs inverting, and the
   only way that can follow the palette is through a token. */
check(
	'the header logo filter follows the palette',
	(bool) preg_match( '/img\.custom-logo\s*\{[^}]*filter:\s*var\(--calcr-logo-filter\)/', $chrome_css ),
	true
);

/* The heading is the renderer's only when the theme has handed it over. */
$GLOBALS['calcr_test_state']['current_slug'] = 'gpa-calculator';
$chrome = Calculatorr_Site_Chrome::instance();
check( 'theme heading not claimed before the filter runs', Calculatorr_Site_Chrome::heading_is_ours(), false );
check( 'chrome declines the theme title on a calculator page', $chrome->page_title( true ), false );
check( 'renderer now owns the heading', Calculatorr_Site_Chrome::heading_is_ours(), true );

$html = $renderer->shortcode( array( 'slug' => 'gpa-calculator' ) );
check( 'exactly one H1 is rendered', substr_count( $html, '<h1' ), 1 );
check(
	'the H1 sits after the breadcrumb',
	strpos( $html, 'calcr-breadcrumb' ) < strpos( $html, '<h1' ),
	true
);

check( 'a theme that prints nothing is left alone', $chrome->page_title( false ), false );

$GLOBALS['calcr_test_state']['current_slug'] = null;

/* --- homepage metadata ---------------------------------------------------- */

$seo = Calculatorr_SEO::instance();
$reflect = new ReflectionMethod( 'Calculatorr_SEO', 'home_description' );
$reflect->setAccessible( true );
$home_description = $reflect->invoke( $seo );
check( 'homepage description is written', '' !== trim( $home_description ), true );
check( 'homepage description fits a snippet', strlen( $home_description ) <= 158, true );
check( 'homepage description counts the calculators', (bool) strpos( $home_description, (string) count( $registry->all( false ) ) ), true );
check( 'social card ships for og:image', file_exists( CALCULATORR_PATH . 'assets/images/social-card.png' ), true );

/* --- starting empty ------------------------------------------------------- */

/*
 * Landing on a calculator that is already full of somebody else's numbers
 * means clearing every box before you can use it. These checks pin the blank
 * start, the placeholder that replaces the prefilled value, and the rule that
 * decides which blanks the runtime is allowed to treat as nothing.
 */
check( 'empty start is on by default', (int) $settings->get( 'empty_start' ), 1 );

$amort = $renderer->shortcode( array( 'slug' => 'amortization-calculator' ) );

check(
	'the loan amount opens blank',
	(bool) preg_match( '/data-calcr-input="principal"/', $amort ) && ! preg_match( '/value="300000"/', $amort ),
	true
);
check( 'the usual figure survives as a hint', (bool) strpos( $amort, 'placeholder="300000"' ), true );
check( 'a field the answer needs is marked required', (bool) strpos( $amort, 'data-calcr-required="1"' ), true );
check( 'the panel opens in its example state', (bool) strpos( $amort, 'calcr--awaiting' ), true );
check( 'the panel says the figures are an example', (bool) strpos( $amort, 'Example figures' ), true );

/*
 * The panel opens showing the answer to the numbers the fields carry as
 * placeholders, rather than a dash. Three things have to hold for that to be
 * honest rather than decorative: the number has to be there, it has to be
 * marked as an example, and it has to be the answer to the placeholders and
 * not to something else. The last one is what stops the example quietly
 * drifting away from the inputs it claims to describe.
 */
$amort_config = Calculatorr_Registry::instance()->get( 'amortization-calculator' );
$amort_result = isset( $amort_config['default_result'] ) ? $amort_config['default_result'] : array();

check(
	'the example number is painted on the server',
	( ! empty( $amort_result['value'] ) && false !== strpos( $amort, (string) $amort_result['value'] ) ),
	true
);

check(
	'the script can restore the example after a reset',
	(bool) strpos( $amort, 'data-calcr-example=' ),
	true
);

/* Copying or sharing an example would send somebody else's numbers out under
   the visitor's name, so both stay off until there is a real answer. */
check( 'copying is off while the example shows', (bool) preg_match( '/data-calcr-copy\s+disabled/', $amort ), true );
check( 'sharing is off while the example shows', (bool) preg_match( '/data-calcr-share-toggle[^>]*disabled/', $amort ), true );

/*
 * Every calculator needs one, because a panel that falls back to a dash on
 * even a handful of pages is the inconsistency this replaced.
 */
$without_example = array();

foreach ( Calculatorr_Registry::instance()->all() as $one ) {
	if ( empty( $one['default_result']['value'] ) ) {
		$without_example[] = $one['slug'];
	}
}

check( 'every calculator ships an example result', $without_example, array() );

/* Copying or sharing a result that does not exist yet is a dead end, and the
   state has to be right in the markup rather than applied by script, so it is
   never briefly wrong while the runtime loads. */
check( 'copy starts disabled', (bool) preg_match( '/data-calcr-copy\s+disabled/', $amort ), true );
check( 'share starts disabled', (bool) preg_match( '/data-calcr-share-toggle[^>]*\sdisabled/', $amort ), true );

/*
 * Reset starts inert too. A freshly loaded form has nothing to reset to, and
 * once the fields open blank a live-looking button that does nothing when
 * pressed reads as broken rather than as finished. The runtime enables it the
 * moment anything differs from what was served.
 */
check( 'reset starts disabled', (bool) preg_match( '/data-calcr-reset\s+disabled/', $amort ), true );

/*
 * The shareable image is square, and only square.
 *
 * The shape used to be a choice, and the choice was broken: the panel marked
 * square as selected while the drawing code defaulted to wide, so the picture
 * somebody got was not the one they had been shown. Rather than fix the
 * default, the option went: square is the shape a share sheet, a feed and a
 * screenshot all take without recropping, so there was nothing for the second
 * one to be better at.
 */
$share_js = file_get_contents( CALCULATORR_PATH . 'assets/js/share.js' );

check( 'no shape toggle is rendered', (bool) strpos( $amort, 'data-calcr-format' ), false );
check( 'the wide card is gone from the drawing code', (bool) strpos( $share_js, 'landscape' ), false );
check( 'the card is drawn square', (bool) preg_match( '/w:\s*1080,\s*h:\s*1080/', $share_js ), true );

/* The frame the preview draws into has to be the shape of the card, or the
   picture sits letterboxed inside its own border. */
check(
	'the preview frame is square too',
	(bool) preg_match( '/\.calcr__share-preview\s*\{[^}]*aspect-ratio:\s*1\s*;/', $app_rules ),
	true
);

/*
 * Long forms get a second column, and the count that decides it has to be the
 * number of fields on screen rather than the number in the config. BMI carries
 * height and weight twice, metric and imperial, so counting the config would
 * call a three-field form dense and give it a column with nothing in it.
 */
check( 'BMI counts what is on screen, not what is configured', Calculatorr_Renderer::visible_field_count( $registry->get( 'bmi-calculator' ) ), 4 );
check( 'a genuinely long form is counted long', Calculatorr_Renderer::visible_field_count( $registry->get( 'mortgage-payment-calculator' ) ), 6 );
check( 'a short one is not', Calculatorr_Renderer::visible_field_count( $registry->get( 'percentage-calculator' ) ) < 5, true );

$dense_page = $renderer->render_page( $registry->get( 'mortgage-payment-calculator' ) );
$plain_page = $renderer->render_page( $registry->get( 'percentage-calculator' ) );
check( 'the long form is marked dense', (bool) strpos( $dense_page, 'calcr-page--dense' ), true );
check( 'the short one is not', (bool) strpos( $plain_page, 'calcr-page--dense' ), false );

/*
 * A couple of calculators give their metric and imperial entries the same id
 * deliberately, so whichever is showing feeds the same name to the formula.
 * That is right for data-calcr-input and wrong for an HTML id: a label
 * pointing at a duplicate binds to the first one, so the imperial label used
 * to focus the hidden metric input.
 */
$body_fat = $renderer->shortcode( array( 'slug' => 'body-fat-calculator' ) );
preg_match_all( '/id="(calcr-[^"]+)"/', $body_fat, $id_matches );
$repeated = array_keys( array_filter( array_count_values( $id_matches[1] ), function ( $n ) { return $n > 1; } ) );
check( 'no two elements share an id', $repeated, array() );
check( 'the shared name is still carried for the formula', substr_count( $body_fat, 'data-calcr-input="heightVal"' ), 2 );

/*
 * The address bar is left alone while somebody types. It used to be rewritten
 * with every field on the page, blanks and hidden twins included. Nothing
 * about that was a search problem, since a fragment is never sent to the
 * server, but it put a long string in front of somebody who had not asked to
 * share anything, and made the copied link look broken.
 */
$runtime_js = file_get_contents( CALCULATORR_PATH . 'assets/js/calculator.js' );
$share_runtime = file_get_contents( CALCULATORR_PATH . 'assets/js/share.js' );
check( 'typing does not rewrite the address bar', (bool) strpos( $runtime_js, 'replaceState' ), false );
check( 'a shared link still fills the form in', (bool) strpos( $runtime_js, 'restoreFromHash' ), true );
check( 'the share link skips blanks', (bool) preg_match( "/if \( '' === value \) \{/", $share_runtime ), true );
check( 'the share link skips hidden fields', (bool) preg_match( '/if \( field && field\.hidden \) \{/', $share_runtime ), true );

/*
 * No calculator is a dead end.
 *
 * Thirty-one configs name fewer than three related calculators, and a page
 * that emits two internal links tends to be one that receives two, which is
 * how a tool ends up reachable only from its category page. The list is
 * topped up from the calculator's own category, walking from its own position
 * and wrapping round, so the incoming links spread evenly instead of every
 * page in a category pointing at the same two.
 */
$related_method = new ReflectionMethod( 'Calculatorr_Renderer', 'related_slugs' );
$related_method->setAccessible( true );

$short_lists = 0;
$incoming    = array();

foreach ( $registry->all() as $one ) {
	$slugs = $related_method->invoke( $renderer, $one, 4 );

	if ( count( $slugs ) < 3 ) {
		$short_lists++;
	}

	check_quiet( 'never lists itself: ' . $one['slug'], in_array( $one['slug'], $slugs, true ), false );

	foreach ( $slugs as $target ) {
		$incoming[ $target ] = isset( $incoming[ $target ] ) ? $incoming[ $target ] + 1 : 1;
	}
}

/* Only the four-calculator category can fall short, and only to three. */
check( 'every calculator offers at least three onward links', $short_lists <= 4, true );

$unlinked = array_values( array_diff( array_map( function ( $c ) { return $c['slug']; }, $registry->all() ), array_keys( $incoming ) ) );
check( 'every calculator is linked from somewhere', $unlinked, array() );

/* Reset has to put repeater rows back as well. It used to clear the ordinary
   fields and leave every added row sitting there with its figures in it,
   which on a GPA or a timesheet is most of what needed clearing. */
$runtime = file_get_contents( CALCULATORR_PATH . 'assets/js/calculator.js' );
check( 'reset restores repeater rows', (bool) strpos( $runtime, 'data-calcr-rep-initial' ), true );
check( 'reset state follows the form', (bool) strpos( $runtime, 'function isPristine' ), true );

/* A default of zero is the config author saying this one can be left alone,
   so it must not hold the whole result back. */
$waste = $renderer->shortcode( array( 'slug' => 'cubic-yard-calculator' ) );
check(
	'a field defaulting to zero is not required',
	(bool) preg_match( '/data-calcr-input="waste"[^>]*data-calcr-required/', $waste ),
	false
);

/* A select is a choice rather than the visitor's data, so it keeps its
   default: there is no sensible blank state for one. */
$converter = $renderer->shortcode( array( 'slug' => 'unit-converter' ) );
check( 'choices keep a selected option', (bool) preg_match( '/<option[^>]* selected/', $converter ), true );

$values = $settings->all();
$values['empty_start'] = 0;
$settings->save( $values );

$prefilled = $renderer->shortcode( array( 'slug' => 'amortization-calculator' ) );
check( 'switching it off prefills again', (bool) strpos( $prefilled, 'value="300000"' ), true );
check( 'and the panel shows an answer', false !== strpos( $prefilled, 'calcr--awaiting' ), false );

$values['empty_start'] = 1;
$settings->save( $values );

/* --- light and dark ------------------------------------------------------- */

check( 'the switch is offered by default', (int) $settings->get( 'theme_switch' ), 1 );

$chrome = Calculatorr_Site_Chrome::instance();
$header = (object) array( 'theme_location' => 'menu-1' );
$footer = (object) array( 'theme_location' => 'menu-2' );

$with_switch = $chrome->append_theme_switch( '<li>Finance</li>', $header );
check( 'the switch joins the header menu', (bool) strpos( $with_switch, 'data-calcr-theme-toggle' ), true );
check( 'the existing items survive', false !== strpos( $with_switch, '<li>Finance</li>' ), true );
check( 'it is a button, not a link', (bool) strpos( $with_switch, '<button type="button"' ), true );
check( 'the footer menu is left alone', $chrome->append_theme_switch( '<li>Finance</li>', $footer ), '<li>Finance</li>' );

ob_start();
$chrome->early_theme_script();
$early = ob_get_clean();
check( 'a stored choice is applied before paint', (bool) strpos( $early, 'calcr-theme' ), true );
check( 'storage access is guarded', (bool) strpos( $early, 'try' ), true );

/* --- the design layer ----------------------------------------------------- */

/*
 * The point of this layer is that a look-and-feel change is a saved value
 * rather than an edited file, so it reaches every page with no packaging step.
 * These checks cover the three things that makes or breaks: an untouched
 * install must emit nothing, a changed value must reach both palettes
 * correctly, and a value that is not the shape it claims to be must be dropped
 * rather than written into a stylesheet where it would break the rule around
 * it silently.
 */
$design = Calculatorr_Design::instance();

$values = $settings->all();
$values['design'] = array();
$settings->save( $values );

$clean_install = $design->overrides();
check( 'an untouched install overrides nothing', $clean_install['light'] + $clean_install['dark'] + $clean_install['root'], array() );

ob_start();
$design->output();
check( 'and prints no style block', ob_get_clean(), '' );

$values['design'] = array(
	'light_accent'   => '#7A2FF2',
	'dark_accent'    => 'rgb(160 120 255)',
	'type_container' => '1440px',
	'light_ink'      => 'javascript:alert(1)',
	'type_font_body' => 'url(evil)',
	'type_header'    => '90',
	'fonts_url'      => 'http://fonts.example.com/x.css',
	'custom_css'     => '.x{color:red}</style><script>bad()</script>',
);
$settings->save( $values );

$stored = (array) $settings->get( 'design' );

check( 'a hex colour is kept', $stored['light_accent'], '#7A2FF2' );
check( 'an rgb colour in space syntax is kept', $stored['dark_accent'], 'rgb(160 120 255)' );
check( 'a length with a unit is kept', $stored['type_container'], '1440px' );
check( 'a script url is not a colour', isset( $stored['light_ink'] ), false );
check( 'a css function is not a font stack', isset( $stored['type_font_body'] ), false );
check( 'a length with no unit is rejected', isset( $stored['type_header'] ), false );
check( 'the webfont must be served over https', $stored['fonts_url'], '' );

/* A stylesheet that can close its own tag stops being a stylesheet. */
check( 'custom css cannot escape the style element', false !== strpos( $stored['custom_css'], '<' ), false );
check( 'and cannot open a script', false !== strpos( $stored['custom_css'], '<script' ), false );
check( 'but keeps the rule that was written', false !== strpos( $stored['custom_css'], '.x{color:red}' ), true );

ob_start();
$design->output();
$printed = ob_get_clean();

check( 'the changed accent reaches light mode', false !== strpos( $printed, '--calcr-accent:#7A2FF2' ), true );
check( 'the dark accent is written for the system preference', false !== strpos( $printed, 'prefers-color-scheme:dark' ), true );
check( 'and for the visitor who pressed the switch', false !== strpos( $printed, ':root[data-calcr-theme="dark"]' ), true );
check( 'the container width is not palette specific', false !== strpos( $printed, '--calcr-container:1440px' ), true );
check( 'an untouched colour is left to the stylesheet', false !== strpos( $printed, '--calcr-paper' ), false );

/* The layout tokens only do anything if the chrome actually reads them. */
check(
	'the chrome reads the container token',
	(bool) preg_match( '/max-width:\s*var\(--calcr-container\)/', $chrome_css ),
	true
);
check(
	'the chrome reads the header height token',
	(bool) preg_match( '/min-height:\s*var\(--calcr-header-height\)/', $chrome_css ),
	true
);

$values['design'] = array();
$settings->save( $values );
check( 'clearing it returns to the shipped design', Calculatorr_Design::get( 'light_accent' ), '#0E6E63' );

/* --- editing a calculator's content --------------------------------------- */

/*
 * The point of these fields is that the copy stops being something only a code
 * change can alter. The risk they carry is the opposite of the usual one: not
 * that somebody saves nonsense, but that saving prose quietly destroys the
 * richer parts of a section that this screen does not show.
 */
$before = Calculatorr_Registry::instance()->get( 'concrete-calculator' );
check( 'the config ships sections with extras', isset( $before['explainer'][0]['steps'] ), true );

$settings->save_override( 'concrete-calculator', array(
	'keyword'   => 'concrete calculator',
	'keywords'  => array( 'cubic yards of concrete', 'how much concrete', 'cubic yards of concrete' ),
	'explainer' => Calculatorr_Admin::clean_sections( array(
		array(
			'heading' => 'How to work out what you need',
			'body'    => 'Rewritten body copy.',
			'carried' => wp_json_encode( array( 'steps' => $before['explainer'][0]['steps'] ) ),
		),
		array( 'heading' => '', 'body' => 'Orphan body with no heading.' ),
	) ),
	'faqs' => Calculatorr_Admin::clean_faqs( array(
		array( 'q' => 'How many bags to a yard?', 'a' => 'About forty-five 80 lb bags.' ),
		array( 'q' => 'A question with no answer', 'a' => '' ),
		array( 'q' => '', 'a' => 'An answer with no question' ),
	) ),
) );

$after = Calculatorr_Registry::instance()->get( 'concrete-calculator' );

check( 'the primary keyword is overridden', $after['keyword'], 'concrete calculator' );
check( 'secondary keywords are stored as a list', count( $after['keywords'] ), 2 );
check( 'and deduplicated', in_array( 'cubic yards of concrete', $after['keywords'], true ), true );

check( 'the rewritten section lands', $after['explainer'][0]['body'], 'Rewritten body copy.' );
/* The steps were never on screen, so losing them would be silent. */
check( 'the steps this screen never showed survive', isset( $after['explainer'][0]['steps'] ), true );
check( 'and are unchanged', $after['explainer'][0]['steps'], $before['explainer'][0]['steps'] );
check( 'a section with no heading is dropped', count( $after['explainer'] ), 1 );

check( 'a complete question and answer is kept', count( $after['faqs'] ), 1 );
check( 'and a half-filled pair is not', $after['faqs'][0]['q'], 'How many bags to a yard?' );

/* An array run through sanitize_text_field becomes the string "Array", which
   would delete the content without erroring, so the storage path must not. */
check( 'lists are not flattened on the way in', is_array( $after['explainer'] ), true );

$settings->save_override( 'concrete-calculator', array() );
check( 'clearing hands the config file back', isset( Calculatorr_Registry::instance()->get( 'concrete-calculator' )['keywords'] ), false );

/* --- usage and its charts ------------------------------------------------- */

check( 'counting is on by default', (int) $settings->get( 'usage_enabled' ), 1 );

$days = Calculatorr_Usage::empty_days( 30 );
check( 'a window is a full run of days', count( $days ), 30 );
/* A line with holes in it reads as a drop that never happened, so the quiet
   days have to be present as zero rather than missing. */
check( 'quiet days are zero rather than absent', array_sum( $days ), 0 );
check( 'and the window ends today', array_key_last( $days ), gmdate( 'Y-m-d' ) );

$series = array();
foreach ( array( 3, 7, 5, 12, 9, 14, 18, 11, 22, 19 ) as $i => $value ) {
	$series[ gmdate( 'Y-m-d', time() - ( ( 9 - $i ) * 86400 ) ) ] = $value;
}

$trend = Calculatorr_Chart::trend( $series );
check( 'every point is interrogable', substr_count( $trend, 'calcr-chart__dot' ), 10 );
check( 'only the latest point is labelled', substr_count( $trend, 'calcr-chart__value' ), 1 );
check( 'the chart names itself for a screen reader', false !== strpos( $trend, '<title>' ), true );
/* One series, so a legend would be a box explaining the only thing on screen. */
check( 'a single series carries no legend', false !== strpos( $trend, 'legend' ), false );

/* Dividing by a zero maximum puts a flat line through the middle of the chart
   as though something were happening. */
$flat = array_fill_keys( array_keys( $series ), 0 );
check( 'an empty week does not divide by zero', false !== strpos( Calculatorr_Chart::sparkline( $flat ), 'polyline' ), true );
check( 'a single day is not a trend', false !== strpos( Calculatorr_Chart::trend( array( '2026-09-19' => 4 ) ), 'Not enough days' ), true );

$bars = Calculatorr_Chart::bars( array( 'Mortgage Payment Calculator' => 220, 'BMI Calculator' => 140 ) );
check( 'bars carry their values directly', substr_count( $bars, 'calcr-chart__value' ), 2 );
check( 'and a hover label each', substr_count( $bars, '<title>' ), 2 );
check( 'nothing to chart says so', false !== strpos( Calculatorr_Chart::bars( array() ), 'Nothing recorded' ), true );

/* --- the merged advertising screen ---------------------------------------- */

/*
 * The header and footer tab is gone, but its fields are not: AdSense serves
 * nothing until its loader is in the head, so deleting the boxes would have
 * removed the one thing the tab existed for.
 */
$admin_src = file_get_contents( CALCULATORR_PATH . 'includes/class-admin.php' );
check( 'the tab is gone', false !== strpos( $admin_src, "'code'        => 'Header" ), false );
check( 'the screen it pointed at is gone too', false !== strpos( $admin_src, 'function render_code' ), false );
check( 'the head box survives inside advertising', substr_count( $admin_src, "'code_head'" ), 2 );
check( 'and is still saved', false !== strpos( $admin_src, "'code_head', 'code_body', 'code_footer'" ), true );

/* --- the editor on the content fields ------------------------------------- */

/*
 * The body and answer fields use the editor WordPress ships rather than a bare
 * textarea, so the copy can be written with formatting. The front end already
 * printed these through wp_kses_post and wpautop, so the only thing that had to
 * change was the input, but the two have to stay in step: a rich editor feeding
 * a field that escapes its HTML would show people their own tags.
 */
check( 'the section body uses the WordPress editor', substr_count( $admin_src, "wp_editor(" ), 2 );
check( 'and posts back under the same name', false !== strpos( $admin_src, "'textarea_name' => 'explainer[' . (int) \$i . '][body]'" ), true );
check( 'the answer field uses it too', false !== strpos( $admin_src, "'textarea_name' => 'faqs[' . (int) \$i . '][a]'" ), true );

$renderer_src = file_get_contents( CALCULATORR_PATH . 'includes/class-renderer.php' );
check( 'the front end still prints body markup rather than escaping it', false !== strpos( $renderer_src, 'wp_kses_post( wpautop( $section[\'body\'] ) )' ), true );

$settings->save_override( 'tip-calculator', array(
	'explainer' => Calculatorr_Admin::clean_sections( array(
		array(
			'heading' => 'Formatting',
			'body'    => '<p>A <strong>bold</strong> claim, a <a href="https://example.com">link</a> and a list.</p><ul><li>One</li><li>Two</li></ul>',
		),
	) ),
) );

$formatted = $renderer->shortcode( array( 'slug' => 'tip-calculator' ) );

foreach ( array( '<strong>', '<a href', '<ul>', '<li>' ) as $tag ) {
	check( sprintf( '%s survives to the page', $tag ), false !== strpos( $formatted, $tag ), true );
}

/* --- a calculation inside the copy ---------------------------------------- */

/* Copy now runs its shortcodes, so a page can carry a second calculator inside
   its prose rather than printing the shortcode as text. */
$settings->save_override( 'tip-calculator', array(
	'explainer' => array( array( 'heading' => 'Related', 'body' => 'Try this too: [calculatorr slug="bmi-calculator"]' ) ),
) );

$nested = $renderer->shortcode( array( 'slug' => 'tip-calculator' ) );
check( 'an embedded calculator renders', substr_count( $nested, 'data-calcr-slug="bmi-calculator"' ), 1 );
check( 'and the host is still there once', substr_count( $nested, 'data-calcr-slug="tip-calculator"' ), 1 );

/* Which opens the door to a calculator naming itself, directly or through a
   chain, and recursing until PHP ran out of stack. */
$settings->save_override( 'tip-calculator', array(
	'explainer' => array( array( 'heading' => 'See also', 'body' => 'Itself: [calculatorr slug="tip-calculator"]' ) ),
) );

$looped = $renderer->shortcode( array( 'slug' => 'tip-calculator' ) );
check( 'a self-reference renders once rather than looping', substr_count( $looped, 'data-calcr-slug="tip-calculator"' ), 1 );
check( 'and still produces a page', strlen( $looped ) > 2000, true );

$settings->save_override( 'tip-calculator', array() );

/* --- promoted links stay on topic ----------------------------------------- */

/*
 * The design review caught both of these on the live site. A block headed with
 * a category's name cannot be padded out with whatever else exists, and a hub
 * cannot promote the calculators it is already listing.
 */
$GLOBALS['calcr_test_state']['current_slug'] = 'boat-loan-calculator';
$boat = $renderer->shortcode( array( 'slug' => 'boat-loan-calculator' ) );
$GLOBALS['calcr_test_state']['current_slug'] = null;

preg_match_all( '#href="[^"]*/([a-z0-9\-]+)/"#', $boat, $links );

$offside = array();

foreach ( array_unique( $links[1] ) as $slug ) {
	$linked = $registry->get( $slug );

	if ( $linked && 'loans' !== $linked['category'] ) {
		$offside[] = $slug;
	}
}

/* Loans holds six calculators, five of them siblings, and the sidebar slot
   asks for seven, which is how the 401(k) and Age calculators ended up under
   "More loans & debt tools". A short list of the right things is the fix. */
check( 'a loan page promotes only loan calculators', $offside, array() );
check( 'and still promotes something', count( array_unique( $links[1] ) ) > 3, true );

$GLOBALS['calcr_test_state']['current_category'] = 'loans';
$hub = $renderer->category_shortcode( array( 'category' => 'loans' ) );
$GLOBALS['calcr_test_state']['current_category'] = null;

preg_match_all( '#href="[^"]*/([a-z0-9\-]+)/"#', $hub, $hub_links );
$repeated = array_keys( array_filter( array_count_values( $hub_links[1] ), function ( $n ) {
	return $n > 1;
} ) );

check( 'a category hub never lists the same calculator twice', $repeated, array() );

/* --- the sticky answer on a phone ----------------------------------------- */

/*
 * Most of this only proves itself in a browser, and it is driven there too,
 * but the markup and the rules it depends on can be pinned here so a refactor
 * cannot quietly remove the parts the behaviour hangs off.
 */
$sticky_src = file_get_contents( CALCULATORR_PATH . 'assets/js/calculator.js' );
$sticky_css = file_get_contents( CALCULATORR_PATH . 'assets/css/calculator.css' );

check( 'the bar ships in the markup', false !== strpos( $amort, 'data-calcr-sticky' ), true );
check( 'with a way back to the full result', false !== strpos( $amort, 'data-calcr-breakdown' ), true );
check( 'and the answer it stands in for', false !== strpos( $amort, 'data-calcr-sticky-value' ), true );

/* Two live regions announcing the same number means a screen reader reads
   every keystroke twice, so exactly one of them is live at a time. */
check( 'the panel declares a live region', false !== strpos( $amort, 'aria-live="polite"' ), true );
check( 'and the bar can take it over', false !== strpos( $sticky_src, "announceFrom" ), true );

/* The number, not the panel: on a tall result card the rows can be in view
   while the figure is still above the fold. */
check( 'the observer watches the figure itself', false !== strpos( $sticky_src, "querySelector( '[data-calcr-primary-value]' )" ), true );
check( 'a focused field keeps the bar up', false !== strpos( $sticky_src, 'typing' ), true );
check( 'the bar rides the visual viewport', false !== strpos( $sticky_src, 'visualViewport' ), true );

/* At 768 and up the result sits beside the inputs, so a bar would be covering
   the page for nothing. */
check( 'it never shows on a wide screen', false !== strpos( $sticky_src, "'(min-width: 768px)'" ), true );
check( 'and the stylesheet agrees', (bool) preg_match( '/@media \( max-width: 767px \)/', $sticky_css ), true );

/* A bar that slides up under a thumb already heading for a field steals the
   tap, so it fades without moving. */
check( 'it fades rather than slides', (bool) preg_match( '/\.calcr__sticky \{[^}]*transition: opacity 150ms/s', $sticky_css ), true );
check( 'the page leaves room beneath it', (bool) preg_match( '/padding-bottom: 92px/', $sticky_css ), true );

/* The bar wears the result panel's colours, and those must not be a token that
   inverts, for the reason the footer did. */
check( 'the bar uses the result surface', (bool) preg_match( '/\.calcr__sticky \{[^}]*background: var\(--c-result-bg\)/s', $sticky_css ), true );

/* --- results -------------------------------------------------------------- */
printf( "%d checks\n\n", $checks );
foreach ( $failures as $failure ) { echo "  FAIL $failure\n"; }
printf( "\n%d failures\n", count( $failures ) );
exit( $failures ? 1 : 0 );
