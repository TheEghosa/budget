<?php
/**
 * Rasterises the calculatorr logo to PNG.
 *
 * The brand mark is pure geometry, so it is drawn rather than traced from the
 * SVG: a rounded square, two rounded bars and one accent dot, at the same
 * coordinates the SVG uses on a 64 unit grid. Everything is drawn at SCALE
 * times the target size and then scaled down, because GD has no analytic
 * antialiasing and supersampling is what gives the corners a clean edge.
 *
 * Usage: php tools/make-logo-png.php <font.ttf> <out-dir>
 */

const SCALE = 4;

const TEAL  = array( 0x0E, 0x6E, 0x63 );
const INK   = array( 0x14, 0x18, 0x1D );
const AMBER = array( 0xE9, 0xA2, 0x3B );
const WHITE = array( 0xFF, 0xFF, 0xFF );

function canvas( $w, $h ) {
	$im = imagecreatetruecolor( $w, $h );
	imagealphablending( $im, false );
	imagesavealpha( $im, true );
	imagefilledrectangle( $im, 0, 0, $w, $h, imagecolorallocatealpha( $im, 0, 0, 0, 127 ) );
	imagealphablending( $im, true );
	return $im;
}

function rgb( $im, array $c ) {
	return imagecolorallocate( $im, $c[0], $c[1], $c[2] );
}

/** Rounded rectangle as a centre cross plus four corner discs. */
function rounded_rect( $im, $x, $y, $w, $h, $r, $colour ) {
	$r = min( $r, $w / 2, $h / 2 );
	imagefilledrectangle( $im, (int) round( $x + $r ), (int) round( $y ), (int) round( $x + $w - $r ), (int) round( $y + $h ), $colour );
	imagefilledrectangle( $im, (int) round( $x ), (int) round( $y + $r ), (int) round( $x + $w ), (int) round( $y + $h - $r ), $colour );
	$d = (int) round( $r * 2 );
	foreach ( array( array( $x + $r, $y + $r ), array( $x + $w - $r, $y + $r ), array( $x + $r, $y + $h - $r ), array( $x + $w - $r, $y + $h - $r ) ) as $c ) {
		imagefilledellipse( $im, (int) round( $c[0] ), (int) round( $c[1] ), $d, $d, $colour );
	}
}

/** Draws the 64x64 mark with its top-left corner at ($ox,$oy), $u units per grid unit. */
function draw_mark( $im, $ox, $oy, $u, $tile = true ) {
	if ( $tile ) {
		rounded_rect( $im, $ox, $oy, 64 * $u, 64 * $u, 15 * $u, rgb( $im, TEAL ) );
	}
	$white = rgb( $im, WHITE );
	rounded_rect( $im, $ox + 15 * $u, $oy + 23.5 * $u, 34 * $u, 6.5 * $u, 3.25 * $u, $white );
	rounded_rect( $im, $ox + 15 * $u, $oy + 35.5 * $u, 21 * $u, 6.5 * $u, 3.25 * $u, $white );
	$d = (int) round( 9.5 * $u );
	imagefilledellipse( $im, (int) round( $ox + 44.5 * $u ), (int) round( $oy + 38.75 * $u ), $d, $d, rgb( $im, AMBER ) );
}

/**
 * Measures where every glyph in a tracked string lands, so the wordmark can
 * carry the SVG's negative letter-spacing and change colour partway through.
 * Each position comes from the width of the prefix before it, which keeps the
 * kerning pairs the font defines rather than throwing them away.
 *
 * Returns the pen offsets for each glyph plus the total advance.
 */
function track_text( $font, $size, $text, $tracking ) {
	$len    = mb_strlen( $text );
	$widths = array( 0.0 );
	for ( $i = 1; $i <= $len; $i++ ) {
		$box      = imagettfbbox( $size, 0, $font, mb_substr( $text, 0, $i ) );
		$widths[] = (float) $box[2];
	}
	$offsets = array();
	for ( $i = 0; $i < $len; $i++ ) {
		$offsets[] = $widths[ $i ] + ( $i * $tracking );
	}
	return array(
		'offsets' => $offsets,
		'width'   => $widths[ $len ] + ( $len - 1 ) * $tracking,
	);
}

function draw_tracked( $im, $font, $size, $x, $y, $text, $tracking, array $colours ) {
	$metrics = track_text( $font, $size, $text, $tracking );
	$len     = mb_strlen( $text );
	for ( $i = 0; $i < $len; $i++ ) {
		$colour = rgb( $im, $i < $colours['split'] ? $colours['head'] : $colours['tail'] );
		imagettftext( $im, $size, 0, (int) round( $x + $metrics['offsets'][ $i ] ), (int) round( $y ), $colour, $font, mb_substr( $text, $i, 1 ) );
	}
	return $metrics['width'];
}

/** Bounding box of everything that is not fully transparent. */
function ink_box( $im ) {
	$w = imagesx( $im );
	$h = imagesy( $im );
	$x0 = $w; $y0 = $h; $x1 = -1; $y1 = -1;
	for ( $y = 0; $y < $h; $y++ ) {
		for ( $x = 0; $x < $w; $x++ ) {
			if ( ( ( imagecolorat( $im, $x, $y ) >> 24 ) & 0x7F ) < 127 ) {
				if ( $x < $x0 ) { $x0 = $x; }
				if ( $x > $x1 ) { $x1 = $x; }
				if ( $y < $y0 ) { $y0 = $y; }
				if ( $y > $y1 ) { $y1 = $y; }
			}
		}
	}
	if ( $x1 < 0 ) { return array( 0, 0, $w, $h ); }
	return array( $x0, $y0, $x1 - $x0 + 1, $y1 - $y0 + 1 );
}

function shrink( $im, $w, $h ) {
	$out = imagescale( $im, $w, $h, IMG_BICUBIC );
	imagesavealpha( $out, true );
	imagedestroy( $im );
	return $out;
}

$font = $argv[1] ?? '';
$dir  = rtrim( $argv[2] ?? '.', '/' );
if ( ! is_readable( $font ) ) {
	fwrite( STDERR, "Font not readable: {$font}\n" );
	exit( 1 );
}

/* Square mark, used for the favicon and the site icon. WordPress crops the
   site icon to 512 square, so that is the size it is generated at. */
foreach ( array( 512, 192 ) as $size ) {
	$im = canvas( $size * SCALE, $size * SCALE );
	draw_mark( $im, 0, 0, ( $size * SCALE ) / 64 );
	$im = shrink( $im, $size, $size );
	imagepng( $im, "{$dir}/logo-mark-{$size}.png" );
	imagedestroy( $im );
	echo "logo-mark-{$size}.png\n";
}

/* Horizontal lockup for the site header, on a transparent background so it
   sits on whatever colour the header turns out to be. The canvas is measured
   from the wordmark rather than assumed, because a hardcoded width is exactly
   how the SVG lockup ended up clipping its own last letter. */
$unit     = 4;                 /* grid units per CSS pixel before supersampling */
$px       = $unit * SCALE;
$size     = 34 * $px;
$tracking = -0.8 * $px;
$text     = 'calculatorr';
$gap      = 80;                /* grid units from the left edge to the wordmark */
$pad      = 6;                 /* grid units of breathing room on the right */

$metrics = track_text( $font, $size, $text, $tracking );
$height  = 64 * $px;
$width   = (int) ceil( ( $gap * $px ) + $metrics['width'] + ( $pad * $px ) );

$variants = array(
	'logo-lockup'      => array( 'head' => INK,   'tail' => TEAL,                          'split' => 9 ),
	'logo-lockup-dark' => array( 'head' => WHITE, 'tail' => array( 0x5E, 0xC9, 0xB8 ), 'split' => 9 ),
);

foreach ( $variants as $name => $colours ) {
	$im = canvas( $width, $height );
	draw_mark( $im, 0, 0, $px );
	draw_tracked( $im, $font, $size, $gap * $px, 43 * $px, $text, $tracking, $colours );
	$im = shrink( $im, (int) round( $width / SCALE ), (int) round( $height / SCALE ) );
	imagepng( $im, "{$dir}/{$name}.png" );
	imagedestroy( $im );
	echo "{$name}.png (" . (int) round( $width / SCALE ) . 'x' . (int) round( $height / SCALE ) . ")\n";
}

/* Wordmark on its own. The design system is explicit that the logotype, not
   the tile, is the site's signature, so this is what the header carries; the
   tile only survives where a square is unavoidable, which means the favicon. */
$wm_width = (int) ceil( $metrics['width'] + ( 8 * $px ) );
$wm_pairs = array(
	'logo-wordmark'      => array( 'head' => INK,   'tail' => TEAL,                      'split' => 9 ),
	'logo-wordmark-dark' => array( 'head' => WHITE, 'tail' => array( 0x4F, 0xB8, 0xA8 ), 'split' => 9 ),
);
foreach ( $wm_pairs as $name => $colours ) {
	$im = canvas( $wm_width, $height );
	draw_tracked( $im, $font, $size, 4 * $px, 43 * $px, $text, $tracking, $colours );
	/* Trimmed to the ink, because WordPress scales a custom logo by its
	   overall height and any transparent margin would shrink the letters. */
	$box  = ink_box( $im );
	$pad  = (int) round( 0.5 * $px );
	$crop = imagecrop( $im, array(
		'x'      => max( 0, $box[0] - $pad ),
		'y'      => max( 0, $box[1] - $pad ),
		'width'  => $box[2] + ( 2 * $pad ),
		'height' => $box[3] + ( 2 * $pad ),
	) );
	imagedestroy( $im );
	imagesavealpha( $crop, true );
	$im = shrink( $crop, (int) round( imagesx( $crop ) / SCALE ), (int) round( imagesy( $crop ) / SCALE ) );
	imagepng( $im, "{$dir}/{$name}.png" );
	echo "{$name}.png (" . imagesx( $im ) . 'x' . imagesy( $im ) . ")\n";
	imagedestroy( $im );
}

/* The Open Graph card. Without a file at this path the SEO module skips
   og:image altogether, which means every link shared anywhere renders as a
   bare blue rectangle. 1200x630 is the size Facebook, LinkedIn and X all
   accept without recropping. */
$cw = 1200 * SCALE;
$chh = 630 * SCALE;
$im  = canvas( $cw, $chh );
imagefilledrectangle( $im, 0, 0, $cw, $chh, rgb( $im, array( 0xFB, 0xFA, 0xF8 ) ) );
imagefilledrectangle( $im, 0, 0, $cw, (int) round( 14 * SCALE ), rgb( $im, TEAL ) );

$card_px   = 1.9 * SCALE;
$card_size = 34 * $card_px;
$card_trk  = -0.8 * $card_px;
$card_m    = track_text( $font, $card_size, $text, $card_trk );
$mark_w    = 64 * $card_px;
$group_w   = $mark_w + ( 16 * $card_px ) + $card_m['width'];
$group_x   = ( $cw - $group_w ) / 2;
$group_y   = ( $chh / 2 ) - ( 64 * $card_px ) + ( 20 * $card_px );

draw_mark( $im, $group_x, $group_y, $card_px );
draw_tracked(
	$im,
	$font,
	$card_size,
	$group_x + $mark_w + ( 16 * $card_px ),
	$group_y + ( 43 * $card_px ),
	$text,
	$card_trk,
	array( 'head' => INK, 'tail' => TEAL, 'split' => 9 )
);

$tag      = 'A calculator for everything in life';
$tag_size = 15 * $card_px;
$tag_m    = track_text( $font, $tag_size, $tag, 0 );
draw_tracked(
	$im,
	$font,
	$tag_size,
	( $cw - $tag_m['width'] ) / 2,
	$group_y + ( 118 * $card_px ),
	$tag,
	0,
	array( 'head' => array( 0x5A, 0x64, 0x72 ), 'tail' => array( 0x5A, 0x64, 0x72 ), 'split' => 999 )
);

$im = shrink( $im, 1200, 630 );
imagepng( $im, "{$dir}/social-card.png" );
imagedestroy( $im );
echo "social-card.png (1200x630)\n";

/* The SVG lockups share the same geometry, so report the viewBox width the
   wordmark actually needs at 34 units. */
$ref = track_text( $font, 34, $text, -0.8 );
echo 'svg viewBox width should be ' . (int) ceil( $gap + $ref['width'] + $pad ) . "\n";
