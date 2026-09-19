<?php
/**
 * Renders the specimen drawings that set the standard for the visualiser work.
 *
 * These are not mockups. They come out of Calculatorr_Draw, which is the class
 * the real visualisers will use, so what you are looking at is what the engine
 * actually produces rather than a picture of what it might.
 *
 * Usage: php tools/draw-specimens.php > /tmp/specimens.html
 */

require_once dirname( __DIR__ ) . '/tests/bootstrap.php';
require_once dirname( __DIR__ ) . '/includes/class-draw.php';

/* ---------- One: a square foot garden bed ---------- */

$bed_w = 4;   // feet
$bed_l = 8;   // feet
$per_sq = 1;  // tomatoes, one per square foot on the standard square foot chart
$plants = $bed_w * $bed_l * $per_sq;

$d = new Calculatorr_Draw( $bed_w, $bed_l, array( 'box' => 460, 'pad' => 58 ) );

$d->rect( 0, 0, $bed_w, $bed_l, 'var(--calcr-surface)', 'var(--calcr-line-strong)', array( 'weight' => 2, 'radius' => 3 ) );
$d->grid( 0, 0, $bed_w, $bed_l, 1 );

for ( $col = 0; $col < $bed_w; $col++ ) {
	for ( $row = 0; $row < $bed_l; $row++ ) {
		$d->circle( $col + 0.5, $row + 0.5, 0.26, 'var(--calcr-accent)' );
	}
}

$d->dimension( 0, $bed_w, $bed_l, $bed_w . ' ft', 'bottom' );
$d->dimension( 0, $bed_l, 0, $bed_l . ' ft', 'left' );
$d->label( $bed_w / 2, -0.42, $plants . ' tomato plants', array( 'size' => 15, 'weight' => 600, 'colour' => 'var(--calcr-ink)' ) );
$d->legend_item( 'var(--calcr-accent)', 'One tomato per square foot' );

$garden = $d->render(
	'A 4 by 8 foot raised bed drawn to scale, divided into 32 one foot squares with one tomato plant in each.',
	'The bed is 4 feet wide and 8 feet long, giving 32 square feet. The square foot planting chart allows one tomato per square, so the bed holds 32 plants, drawn here one to a square. Each square is one foot on a side.'
);

/* ---------- Two: rug size under a queen bed ---------- */

$room_w = 12; $room_l = 14;                 // feet
$bed_w2 = 5;  $bed_l2 = 80 / 12;            // a queen is 60 by 80 inches
$rug_w  = 8;  $rug_l  = 10;

$rug_x  = ( $room_w - $rug_w ) / 2;         // 2 ft
$bed_x  = ( $room_w - $bed_w2 ) / 2;        // 3.5 ft
$side   = round( ( $bed_x - $rug_x ) * 12 );      // 18 in
$foot   = round( ( $rug_l - $bed_l2 ) * 12 );     // 40 in
$walk   = $rug_x;                                  // 2 ft

$r = new Calculatorr_Draw( $room_w, $room_l, array( 'box' => 460, 'pad' => 58 ) );

$r->rect( 0, 0, $room_w, $room_l, 'var(--calcr-paper)', 'var(--calcr-line-strong)', array( 'weight' => 2 ) );
$r->rect( $rug_x, 0, $rug_w, $rug_l, 'var(--calcr-accent)', 'var(--calcr-accent-line)', array( 'opacity' => 0.3, 'radius' => 2 ) );
$r->rect( $bed_x, 0, $bed_w2, $bed_l2, 'var(--calcr-surface)', 'var(--calcr-line-strong)', array( 'weight' => 1.5, 'radius' => 2 ) );

/* The headboard, drawn inside the bed's own footprint rather than above it,
   because a bar floating outside the room reads as a wall fitting and the
   thing it has to say is only which end the 40 inches is measured from. */
$r->rect( $bed_x, 0, $bed_w2, 0.25, 'var(--calcr-line-strong)', 'none' );

$r->label( $bed_x + ( $bed_w2 / 2 ), $bed_l2 / 2, 'Queen', array( 'size' => 13, 'colour' => 'var(--calcr-ink-soft)' ) );
$r->label( $room_w / 2, $rug_l - 1.1, $rug_w . ' x ' . $rug_l . ' rug', array( 'size' => 14, 'weight' => 600, 'colour' => 'var(--calcr-ink)' ) );

$r->dimension( $rug_x, $bed_x, $bed_l2 + 0.45, $side . ' in', 'bottom' );
$r->dimension( $bed_x + $bed_w2, $rug_x + $rug_w, $bed_l2 + 0.45, $side . ' in', 'bottom' );

/* On the right, in the walkway, because on the left it collided with the
   walkway label and two numbers fighting for the same strip of paper is how a
   drawing stops being readable. */
$r->dimension( $bed_l2, $rug_l, $rug_x + $rug_w + 0.42, $foot . ' in past the foot', 'right' );
$r->dimension( 0, $room_w, $room_l, $room_w . ' x ' . $room_l . ' ft room', 'bottom' );
$r->label( $walk / 2, 2.2, $walk . ' ft', array( 'size' => 12 ) );
$r->label( $room_w - ( $walk / 2 ), 2.2, $walk . ' ft', array( 'size' => 12 ) );

$r->legend_item( 'var(--calcr-accent)', '8 x 10 rug' );
$r->legend_item( 'var(--calcr-line-strong)', 'Queen bed, 60 x 80 in' );

$rug = $r->render(
	'An 8 by 10 foot rug under a queen bed in a 12 by 14 foot room, drawn to scale.',
	'The rug is 8 feet by 10 feet, centred on a 12 foot wall. A queen bed measuring 60 by 80 inches sits on it with the headboard against the wall, leaving 18 inches of rug showing on each side of the bed and 40 inches beyond the foot. A 2 foot walkway remains between the rug and each side wall.'
);

/* ---------- The page ---------- */

$tokens = file_get_contents( dirname( __DIR__ ) . '/assets/css/tokens.css' );

echo '<!doctype html><html lang="en"><head><meta charset="utf-8">'
	. '<meta name="viewport" content="width=device-width, initial-scale=1">'
	. '<title>Drawing specimens</title>'
	. '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Source+Sans+3:wght@400;600&display=swap">'
	. '<style>' . $tokens . '
	body{margin:0;background:var(--calcr-paper);color:var(--calcr-ink);
		font-family:var(--calcr-font-body);padding:40px 24px 72px}
	.wrap{max-width:1040px;margin:0 auto}
	h1{font-family:var(--calcr-font-display);font-size:30px;letter-spacing:-.02em;margin:0 0 6px}
	.lede{color:var(--calcr-muted);margin:0 0 36px;max-width:64ch;line-height:1.6}
	.pair{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:24px}
	@media (max-width:860px){.pair{grid-template-columns:minmax(0,1fr)}}
	figure{margin:0;background:var(--calcr-surface);border:1px solid var(--calcr-line);
		border-radius:20px;padding:22px}
	.calcr-draw{width:100%;height:auto;display:block}
	figcaption{margin-top:14px;color:var(--calcr-muted);font-size:14px;line-height:1.55}
	figcaption strong{color:var(--calcr-ink);font-weight:600}
	.sizes{display:flex;gap:20px;align-items:flex-start;margin-top:40px;flex-wrap:wrap}
	.sizes figure{padding:14px}
	.w320{width:320px}
	.note{margin-top:40px;padding:18px 20px;background:var(--calcr-accent-soft);
		border:1px solid var(--calcr-accent-line);border-radius:14px;
		color:var(--calcr-ink-soft);font-size:14px;line-height:1.6;max-width:70ch}
	@media print{body{background:#fff}figure{break-inside:avoid;border-color:#ccc}}
	</style></head><body><div class="wrap">'
	. '<h1>Drawing specimens</h1>'
	. '<p class="lede">Both of these came out of Calculatorr_Draw, which is the engine the real '
	. 'visualisers would use. They are to scale, dimensioned on the drawing itself, themed from '
	. 'the site tokens, and they carry a title and description for anyone who cannot see them.</p>'
	. '<div class="pair">'
	. '<figure>' . $garden . '<figcaption><strong>Square foot garden, 4 by 8 bed.</strong> '
	. 'Screenshot this on its own and it still answers the question, which is the test every '
	. 'visualiser has to pass before it gets built.</figcaption></figure>'
	. '<figure>' . $rug . '<figcaption><strong>Rug size under a queen bed.</strong> '
	. 'The 18 inches each side and 40 inches past the foot are the actual recommendation, drawn '
	. 'rather than described, which is the part every page currently ranking for this gets wrong.'
	. '</figcaption></figure>'
	. '</div>'
	. '<div class="sizes"><figure class="w320">' . $garden . '</figure>'
	. '<p class="note" style="margin-top:0">Legibility at 320 pixels is a rule rather than a hope, '
	. 'because half of these readers are standing in a garden centre holding a phone. The same '
	. 'drawing is on the left at that width. Nothing is redrawn for small screens: the SVG scales '
	. 'and the type stays proportional, so there is one drawing rather than two to keep in step.</p>'
	. '</div>'
	. '<div class="note">Switch your system to light mode and reload. The drawings follow, because '
	. 'every colour in them is a design token rather than a hex code written into the SVG. That is '
	. 'also why they print correctly without a second stylesheet.</div>'
	. '</div></body></html>';
