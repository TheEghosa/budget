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

/* --- results -------------------------------------------------------------- */
printf( "%d checks\n\n", $checks );
foreach ( $failures as $failure ) { echo "  FAIL $failure\n"; }
printf( "\n%d failures\n", count( $failures ) );
exit( $failures ? 1 : 0 );
