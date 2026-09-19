<?php
/**
 * An SEO audit of every page the plugin renders.
 *
 * It reads the built preview, which carries the real head tags and the real
 * structured data, so what is checked here is what the server sends rather
 * than what the templates look like they would send.
 *
 * Every check is a thing a crawler or a search result actually does: a title
 * that gets truncated, two pages claiming the same one, a canonical pointing
 * somewhere else, a description missing so the snippet gets written for you,
 * a page nothing links to. Counts matter more than individual hits, because a
 * rule that fires once is a typo and one that fires a hundred times is a
 * decision somebody made.
 *
 * Usage: php tools/seo-audit.php <preview-directory>
 */

$dir = isset( $argv[1] ) ? rtrim( $argv[1], '/' ) : '';

if ( ! $dir || ! is_dir( $dir ) ) {
	fwrite( STDERR, "Usage: php tools/seo-audit.php <preview-directory>\n" );
	exit( 2 );
}

/**
 * The words inside the article itself: the intro, the explainer, the questions
 * and the answers. Chrome is stripped first, because a hundred and five pages
 * that share a footer do not each own its words.
 */
function calcr_content_words( $html ) {
	$body = preg_replace( '~<(script|style|nav|header|footer|aside)[^>]*>.*?</\1>~s', ' ', $html );
	$body = preg_replace( '~<div class="calcr-house.*?</div>\s*</div>~s', ' ', $body );
	$body = preg_replace( '~<section class="calcr-related".*?</section>~s', ' ', $body );
	$body = preg_replace( '~<section class="calcr-popular".*?</section>~s', ' ', $body );
	$body = preg_replace( '~<nav class="calcr-toc".*?</nav>~s', ' ', $body );

	return str_word_count( strip_tags( $body ) );
}

$files = glob( $dir . '/*.html' );
$pages = array();

foreach ( $files as $file ) {
	$name = basename( $file );

	/*
	 * Scratch copies and the preview's own index are not pages the site has.
	 * Counting them produced six high findings that were all the same thing:
	 * a file I had duplicated for a screenshot, reported as a page competing
	 * with the one it was copied from.
	 */
	if ( preg_match( '/^(_|chk-|index\.html$|bf-hash|.*-(dark|light)\.html$)/', $name ) ) {
		continue;
	}

	$html = file_get_contents( $file );

	$get = function ( $pattern ) use ( $html ) {
		return preg_match( $pattern, $html, $m ) ? html_entity_decode( trim( $m[1] ), ENT_QUOTES, 'UTF-8' ) : '';
	};

	$pages[ $name ] = array(
		'file'        => $name,
		'title'       => $get( '~<title>(.*?)</title>~s' ),
		'description' => $get( '~<meta name="description" content="([^"]*)"~' ),
		'canonical'   => $get( '~<link rel="canonical" href="([^"]*)"~' ),
		'robots'      => $get( '~<meta name="robots" content="([^"]*)"~' ),
		'ogTitle'     => $get( '~<meta property="og:title" content="([^"]*)"~' ),
		'ogImage'     => $get( '~<meta property="og:image" content="([^"]*)"~' ),
		'h1'          => preg_match_all( '~<h1[^>]*>(.*?)</h1>~s', $html, $hm ) ? array_map( function ( $x ) { return trim( strip_tags( $x ) ); }, $hm[1] ) : array(),
		'h2count'     => preg_match_all( '~<h2[^>]*>~', $html ),
		'jsonld'      => preg_match_all( '~<script type="application/ld\+json">(.*?)</script>~s', $html, $jm ) ? $jm[1] : array(),
		/*
		 * The page's own words, not the site's. Counting the whole document
		 * adds the navigation, the sidebar promo and the footer to every page
		 * equally, which lifted the median from 222 to 627 and would have
		 * reported a genuinely thin page as a comfortable one.
		 */
		'words'       => calcr_content_words( $html ),
		'links'       => preg_match_all( '~<a[^>]+href="([^"#?][^"]*)"~', $html, $lm ) ? $lm[1] : array(),
		'html'        => $html,
	);
}

$findings = array();
$note = function ( $severity, $id, $what, $detail = array() ) use ( &$findings ) {
	if ( ! $detail && 'info' !== $severity ) {
		return;
	}
	$findings[] = compact( 'severity', 'id', 'what', 'detail' );
};

/* ---- titles ------------------------------------------------------------- */
$titles = array();
$longTitles = array();
$shortTitles = array();
$noTitle = array();

foreach ( $pages as $p ) {
	$t = $p['title'];
	if ( '' === $t ) { $noTitle[] = $p['file']; continue; }
	$titles[ $t ][] = $p['file'];
	/* Google truncates around 580px, which is roughly 60 characters of mixed
	   case. Past that the tail is cut and the keyword in it is wasted. */
	if ( mb_strlen( $t ) > 60 ) { $longTitles[] = $p['file'] . '  (' . mb_strlen( $t ) . ') ' . $t; }
	if ( mb_strlen( $t ) < 20 ) { $shortTitles[] = $p['file'] . '  (' . mb_strlen( $t ) . ') ' . $t; }
}

$dupeTitles = array();
foreach ( $titles as $t => $where ) {
	if ( count( $where ) > 1 ) { $dupeTitles[] = '"' . $t . '" on ' . implode( ', ', $where ); }
}

$note( 'high', 'title-missing', 'page with no title', $noTitle );
$note( 'high', 'title-duplicate', 'the same title on more than one page', $dupeTitles );
$note( 'medium', 'title-long', 'title over 60 characters, so the tail is cut in the result', $longTitles );
$note( 'low', 'title-short', 'title under 20 characters', $shortTitles );

/* ---- descriptions ------------------------------------------------------- */
$descs = array();
$noDesc = array();
$longDesc = array();
$shortDesc = array();

foreach ( $pages as $p ) {
	$d = $p['description'];
	if ( '' === $d ) { $noDesc[] = $p['file']; continue; }
	$descs[ $d ][] = $p['file'];
	if ( mb_strlen( $d ) > 160 ) { $longDesc[] = $p['file'] . '  (' . mb_strlen( $d ) . ')'; }
	if ( mb_strlen( $d ) < 70 ) { $shortDesc[] = $p['file'] . '  (' . mb_strlen( $d ) . ') ' . $d; }
}

$dupeDesc = array();
foreach ( $descs as $d => $where ) {
	if ( count( $where ) > 1 ) { $dupeDesc[] = '"' . mb_substr( $d, 0, 60 ) . '..." on ' . implode( ', ', $where ); }
}

$note( 'high', 'description-missing', 'no meta description, so the snippet is written for you', $noDesc );
$note( 'high', 'description-duplicate', 'the same description on more than one page', $dupeDesc );
$note( 'medium', 'description-long', 'description over 160 characters', $longDesc );
$note( 'low', 'description-short', 'description under 70 characters, which wastes the snippet', $shortDesc );

/* ---- canonical and robots ----------------------------------------------- */
$noCanon = array();
$canons = array();
$blocked = array();

foreach ( $pages as $p ) {
	if ( '' === $p['canonical'] ) { $noCanon[] = $p['file']; } else { $canons[ $p['canonical'] ][] = $p['file']; }
	if ( $p['robots'] && preg_match( '~noindex~i', $p['robots'] ) ) { $blocked[] = $p['file'] . '  ' . $p['robots']; }
}

$dupeCanon = array();
foreach ( $canons as $c => $where ) {
	if ( count( $where ) > 1 ) { $dupeCanon[] = $c . ' claimed by ' . implode( ', ', $where ); }
}

$note( 'high', 'canonical-missing', 'no canonical link', $noCanon );
$note( 'high', 'canonical-shared', 'two pages claiming the same canonical', $dupeCanon );
$note( 'medium', 'noindex', 'page asking not to be indexed', $blocked );

/* ---- headings ----------------------------------------------------------- */
$noH1 = array(); $manyH1 = array(); $h1Titles = array();

foreach ( $pages as $p ) {
	$n = count( $p['h1'] );
	if ( 0 === $n ) { $noH1[] = $p['file']; }
	if ( $n > 1 ) { $manyH1[] = $p['file'] . '  (' . $n . ') ' . implode( ' | ', $p['h1'] ); }
	if ( 1 === $n ) { $h1Titles[ $p['h1'][0] ][] = $p['file']; }
}

$dupeH1 = array();
foreach ( $h1Titles as $h => $where ) {
	if ( count( $where ) > 1 ) { $dupeH1[] = '"' . $h . '" on ' . implode( ', ', $where ); }
}

$note( 'high', 'h1-missing', 'page with no h1', $noH1 );
$note( 'high', 'h1-multiple', 'page with more than one h1', $manyH1 );
$note( 'medium', 'h1-duplicate', 'the same h1 on more than one page', $dupeH1 );

/* ---- structured data ---------------------------------------------------- */
$badJson = array(); $noJson = array(); $types = array();

foreach ( $pages as $p ) {
	if ( ! $p['jsonld'] ) { $noJson[] = $p['file']; continue; }
	foreach ( $p['jsonld'] as $block ) {
		$decoded = json_decode( $block, true );
		if ( null === $decoded ) { $badJson[] = $p['file'] . ': ' . substr( trim( $block ), 0, 60 ); continue; }
		$graph = isset( $decoded['@graph'] ) ? $decoded['@graph'] : array( $decoded );
		foreach ( $graph as $node ) {
			if ( isset( $node['@type'] ) ) {
				$t = is_array( $node['@type'] ) ? implode( '+', $node['@type'] ) : $node['@type'];
				$types[ $t ] = ( isset( $types[ $t ] ) ? $types[ $t ] : 0 ) + 1;
			}
		}
	}
}

$note( 'high', 'jsonld-invalid', 'structured data that does not parse', $badJson );
$note( 'medium', 'jsonld-missing', 'page with no structured data', $noJson );

/* ---- content depth ------------------------------------------------------ */
$thin = array();
foreach ( $pages as $p ) {
	if ( $p['words'] < 300 ) { $thin[] = $p['file'] . '  ' . $p['words'] . ' words'; }
}
$note( 'medium', 'thin-content', 'under 300 words of page text', $thin );

/* ---- internal linking --------------------------------------------------- */
$incoming = array();
foreach ( $pages as $p ) {
	foreach ( array_unique( $p['links'] ) as $href ) {
		$target = basename( parse_url( $href, PHP_URL_PATH ) ?: '' );
		if ( isset( $pages[ $target ] ) && $target !== $p['file'] ) {
			$incoming[ $target ] = ( isset( $incoming[ $target ] ) ? $incoming[ $target ] : 0 ) + 1;
		}
	}
}

$orphans = array(); $weak = array();
foreach ( $pages as $name => $p ) {
	$n = isset( $incoming[ $name ] ) ? $incoming[ $name ] : 0;
	if ( 0 === $n ) { $orphans[] = $name; }
	elseif ( $n < 3 ) { $weak[] = $name . '  ' . $n . ' internal links'; }
}

$note( 'high', 'orphan', 'page nothing links to', $orphans );
$note( 'low', 'few-links', 'fewer than three internal links in', $weak );

/* ---- social ------------------------------------------------------------- */
$noOg = array();
foreach ( $pages as $p ) {
	if ( '' === $p['ogTitle'] || '' === $p['ogImage'] ) {
		$noOg[] = $p['file'] . ( '' === $p['ogTitle'] ? ' (no og:title)' : '' ) . ( '' === $p['ogImage'] ? ' (no og:image)' : '' );
	}
}
$note( 'low', 'og-incomplete', 'incomplete Open Graph tags', $noOg );

/* ---- report ------------------------------------------------------------- */
printf( "Audited %d pages\n", count( $pages ) );

$wordCounts = array_column( $pages, 'words' );
sort( $wordCounts );
printf(
	"Median %d words, thinnest %d, fattest %d\n",
	$wordCounts[ intdiv( count( $wordCounts ), 2 ) ],
	$wordCounts[0],
	end( $wordCounts )
);
printf( "Structured data types: %s\n\n", $types ? json_encode( $types ) : 'none' );

$order = array( 'high' => 0, 'medium' => 1, 'low' => 2 );
usort( $findings, function ( $a, $b ) use ( $order ) { return $order[ $a['severity'] ] <=> $order[ $b['severity'] ]; } );

$high = 0;
foreach ( $findings as $f ) {
	if ( 'high' === $f['severity'] ) { $high++; }
	printf( "[%-6s] %-22s x%-4d %s\n", strtoupper( $f['severity'] ), $f['id'], count( $f['detail'] ), $f['what'] );
	foreach ( array_slice( $f['detail'], 0, 5 ) as $d ) { echo '           - ' . $d . "\n"; }
	if ( count( $f['detail'] ) > 5 ) { printf( "           ... and %d more\n", count( $f['detail'] ) - 5 ); }
}

printf( "\n%d finding groups, %d of them high\n", count( $findings ), $high );
exit( $high ? 1 : 0 );
