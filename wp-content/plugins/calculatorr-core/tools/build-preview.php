<?php
/**
 * Exports every calculator and category hub as a standalone HTML page, so the
 * whole site can be reviewed before it is anywhere near a WordPress install.
 *
 * The pages use the real renderer, the real CSS and the real formulas, so what
 * you click here is what the plugin produces. Only the surrounding theme is
 * approximated, because that is the one part the plugin does not own.
 *
 * Usage: php tools/build-preview.php <output-directory>
 */

require_once dirname( __DIR__ ) . '/tests/bootstrap.php';

$out = isset( $argv[1] ) ? rtrim( $argv[1], '/' ) : sys_get_temp_dir() . '/calcr-preview';
@mkdir( $out, 0777, true );
@mkdir( $out . '/assets', 0777, true );

/* Preview pages sit flat, so links between them are file names. */
$GLOBALS['calcr_preview'] = true;

$settings = Calculatorr_Settings::instance();
$values = $settings->all();
$values['render_heading'] = 1;
/* Paid ads off and house promos on, which is how the plugin ships and what
   the site will actually look like until AdSense approves it. */
$values['ads_enabled'] = 0;
$values['house_ads_enabled'] = 1;
$values['ad_after_calculator'] = '';
$values['ad_in_content'] = '';
$values['ad_sidebar'] = '';
$settings->save( $values );

$registry = Calculatorr_Registry::instance();
$renderer = Calculatorr_Renderer::instance();
$schema   = Calculatorr_Schema::instance();
$seo      = Calculatorr_SEO::instance();

foreach ( array( 'css/tokens.css', 'css/calculator.css', 'css/site.css', 'js/formula-kit.js', 'js/formula-runner.js', 'js/formulas.js', 'js/sandbox.js', 'js/share.js', 'js/calculator.js', 'js/site.js' ) as $asset ) {
	@mkdir( dirname( $out . '/assets/' . $asset ), 0777, true );
	copy( CALCULATORR_PATH . 'assets/' . $asset, $out . '/assets/' . $asset );
}

function preview_file( $slug ) { return $slug . '.html'; }

/**
 * The renderer emits real site URLs, which is correct everywhere except here.
 * Rewriting them to flat file names is what makes the preview navigable
 * offline, and it is done at the end rather than by faking the URL helpers, so
 * the markup being reviewed is genuinely what the plugin produces.
 */
function preview_localise( $html, $registry ) {
	$map = array();

	foreach ( $registry->all() as $slug => $config ) {
		$map[ home_url( '/' . Calculatorr_Pages::path_for( $config ) . '/' ) ] = preview_file( $slug );
	}

	foreach ( $registry->categories() as $key => $category ) {
		$map[ home_url( '/' . $category['slug'] . '/' ) ] = preview_file( 'hub-' . $key );
	}

	$map[ home_url( '/' ) ] = 'index.html';

	/* Longest first, so a category URL is not replaced inside a calculator
	   URL that begins with it. */
	uksort( $map, function ( $a, $b ) { return strlen( $b ) - strlen( $a ); } );

	foreach ( $map as $url => $file ) {
		$html = str_replace( 'href="' . esc_url( $url ) . '"', 'href="' . $file . '"', $html );
	}

	return $html;
}

function preview_shell( $title, $description, $body, $head, $json, $nav ) {
	return '<!doctype html><html lang="en"><head><meta charset="utf-8">'
		. '<meta name="viewport" content="width=device-width, initial-scale=1">'
		. '<title>' . esc_html( $title ) . '</title>'
		. $head . $json
		. '<link rel="stylesheet" href="assets/css/tokens.css">'
		. '<link rel="stylesheet" href="assets/css/calculator.css">'
		. '<link rel="stylesheet" href="assets/css/site.css">'
		. '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Source+Sans+3:wght@400;600&display=swap">'
		. '<style>' . preview_chrome_css() . '</style>'
		. '</head><body>' . $nav
		. '<main id="content" class="site-main page"><div class="page-content">' . $body . '</div></main>'
		. preview_footer()
		/* The kit before the formulas, because they are written against it,
		   and the sandbox address before the runtime, because a JSON
		   calculator has no formula in the bundle to fall back on. */
		. '<script src="assets/js/formula-kit.js"></script>'
		. '<script src="assets/js/formulas.js"></script>'
		. '<script>window.CalculatorrConfig = { sandboxUrl: "assets/js/sandbox.js" };</script>'
		. '<script src="assets/js/share.js"></script>'
		. '<script src="assets/js/calculator.js"></script>'
		. '<script src="assets/js/site.js"></script>'
		. '</body></html>';
}

function preview_chrome_css() {
	return 'body{margin:0;background:var(--calcr-paper);color:var(--calcr-ink);font-family:var(--calcr-font-body)}'
		. '.preview-wordmark{font-family:var(--calcr-font-display);font-size:26px;font-weight:700;letter-spacing:-.02em;color:var(--calcr-ink)}'
		. '.preview-wordmark span{color:var(--calcr-accent)}'
				. '.site-foot{background:var(--calcr-ink);color:#C9CDCB;padding:28px 24px;font-size:14px;text-align:center}'
		. '.calcr-demo-ad{width:100%;height:100%;min-height:88px;display:flex;align-items:center;justify-content:center;border:2px dashed var(--calcr-line-strong);border-radius:10px;background:var(--calcr-sunken);color:var(--calcr-muted);font-family:var(--calcr-font-display);font-size:13px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;text-align:center;padding:10px}'
		. '.calcr-demo-ad--tall{min-height:600px}'
		. '.preview-banner{background:#FDF3E6;border-bottom:1px solid #F0DFC5;color:#6B4413;padding:9px 24px;font-size:13px;text-align:center}'
		. '.hub-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:14px;margin:22px 0}'
		. '.hub-card{display:flex;flex-direction:column;gap:5px;padding:18px 20px;background:var(--calcr-surface);border:1px solid var(--calcr-line);border-radius:12px;text-decoration:none}'
		. '.hub-card b{font-family:var(--calcr-font-display);font-size:16px;color:var(--calcr-ink)}'
		. '.hub-card span{font-size:13px;line-height:1.5;color:var(--calcr-muted)}'
		. '.hub-count{font-size:12px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--calcr-accent)}';
}

function preview_footer() {
	return '<footer class="site-foot">calculatorr.org preview build &middot; every calculator below is live and computing</footer>';
}

function preview_nav( $registry ) {
	/* The real theme's class names, so site.css styles this header the way it
	   styles the live one and the preview stops flattering itself. The lockup
	   stands in for the uploaded logo plus the injected mark, which needs
	   WordPress to assemble. */
	$nav = '<div class="preview-banner">Preview build. The calculators are live, so type into them. Only the surrounding theme is a stand-in.</div>'
		. '<header id="site-header" class="site-header dynamic-header"><div class="header-inner">'
		. '<div class="site-branding show-logo"><div class="site-logo show">'
		. '<a href="index.html" class="custom-logo-link" rel="home">'
		. '<span class="calcr-brand__mark">' . Calculatorr_Art::mark( 36 ) . '</span>'
		. '<span class="preview-wordmark">calculato<span>rr</span></span>'
		. '</a></div></div>'
		. '<nav class="site-navigation show" aria-label="Main menu"><ul id="menu-primary" class="menu">';
	$nav .= '<li class="menu-item"><a href="index.html">All calculators</a></li>';
	foreach ( $registry->categories() as $key => $category ) {
		$nav .= '<li class="menu-item"><a href="' . esc_attr( preview_file( 'hub-' . $key ) ) . '">' . esc_html( $category['name'] ) . '</a></li>';
	}
	return $nav . '</ul></nav></div></header>';
}

$nav = preview_nav( $registry );
$written = 0;

/* --- every calculator ---------------------------------------------------- */
foreach ( $registry->all() as $slug => $config ) {
	$GLOBALS['calcr_test_state']['current_slug'] = $slug;
	$GLOBALS['calcr_test_state']['current_category'] = null;

	ob_start(); $seo->head_tags(); $head = ob_get_clean();
	ob_start(); $schema->output(); $json = ob_get_clean();

	$body = $renderer->render_page( $config );
	$page = preview_shell( $config['meta_title'], $config['meta_description'], $body, $head, $json, $nav );
	file_put_contents( $out . '/' . preview_file( $slug ), preview_localise( $page, $registry ) );
	$written++;
}

/* --- category hubs -------------------------------------------------------- */
foreach ( $registry->categories() as $key => $category ) {
	$GLOBALS['calcr_test_state']['current_slug'] = null;
	$GLOBALS['calcr_test_state']['current_category'] = $key;

	ob_start(); $seo->head_tags(); $head = ob_get_clean();
	ob_start(); $schema->output(); $json = ob_get_clean();

	/* The category block prints its own breadcrumb and heading now, exactly as
	   it does on the server once the theme's title is handed over, so adding
	   them here would show every hub page two of each. */
	$body = $renderer->category_shortcode( array( 'key' => $key ) );

	$page = preview_shell( $category['meta_title'], $category['meta_description'], $body, $head, $json, $nav );
	file_put_contents( $out . '/' . preview_file( 'hub-' . $key ), preview_localise( $page, $registry ) );
	$written++;
}

/* --- index ---------------------------------------------------------------- */
$index = '<h1 class="calcr-page__title">calculatorr.org</h1>'
	. '<p class="calcr-page__intro">' . count( $registry->all() ) . ' calculators across ' . count( $registry->categories() )
	. ' categories. Everything here is the real plugin output: the formulas run, the share panel draws a real image, and the structured data is in the head of every page.</p>';

foreach ( $registry->categories() as $key => $category ) {
	$items = $registry->in_category( $key );
	$index .= '<h2 style="font-family:var(--calcr-font-display);font-size:24px;margin:34px 0 4px">'
		. esc_html( $category['name'] ) . '</h2>'
		. '<p class="hub-count">' . count( $items ) . ' calculators &middot; <a href="' . esc_attr( preview_file( 'hub-' . $key ) ) . '">view the hub page</a></p>'
		. '<div class="hub-grid">';
	foreach ( $items as $slug => $config ) {
		$index .= '<a class="hub-card" href="' . esc_attr( preview_file( $slug ) ) . '"><b>' . esc_html( $config['h1'] )
			. '</b><span>' . esc_html( $config['description'] ) . '</span></a>';
	}
	$index .= '</div>';
}

file_put_contents( $out . '/index.html', preview_shell( 'calculatorr.org preview', 'Preview build', $index, '', '', $nav ) );
$written++;

printf( "wrote %d pages to %s\n", $written, $out );
