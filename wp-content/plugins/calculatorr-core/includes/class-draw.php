<?php
/**
 * The drawing primitives every visualiser is built from.
 *
 * A calculator that answers with a number can be summarised by a search engine
 * and never visited. A calculator that answers with a scale drawing cannot,
 * which is the whole strategic argument for this file existing.
 *
 * Everything here works in real-world units, feet or inches or metres, and the
 * class converts to pixels once. That matters more than it sounds: the reason
 * competitors' drawings are useless is that they are drawn at whatever sizes
 * looked balanced, so a picture that appears authoritative is quietly lying
 * about proportion. A drawing produced here is to scale or it is not produced.
 *
 * Colours come from the design tokens rather than being written in, so a
 * drawing follows the site into dark mode without a second copy of itself.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Calculatorr_Draw {

	/** @var float Pixels per unit, computed once from the content box. */
	private $scale;

	/** @var float Drawing origin in pixels, which is the padding. */
	private $pad;

	/** @var float Content size in units. */
	private $units_w;
	private $units_h;

	/** @var string[] Accumulated SVG fragments. */
	private $parts = array();

	/** @var array Legend entries, drawn last so they sit above everything. */
	private $legend = array();

	private $width;
	private $height;

	/**
	 * @param float $units_w Content width in real units.
	 * @param float $units_h Content height in real units.
	 * @param array $opts    box: the pixel width to fit into. pad: pixels of
	 *                       margin for dimension lines and labels.
	 */
	public function __construct( $units_w, $units_h, $opts = array() ) {
		$opts = array_merge( array( 'box' => 640, 'pad' => 56 ), $opts );

		$this->units_w = max( (float) $units_w, 0.0001 );
		$this->units_h = max( (float) $units_h, 0.0001 );
		$this->pad     = (float) $opts['pad'];

		/* One scale for both axes, because two would distort the shape and a
		   distorted shape is the failure this class exists to prevent. */
		$inner       = (float) $opts['box'] - ( 2 * $this->pad );
		$this->scale = $inner / max( $this->units_w, $this->units_h );

		$this->width  = ( $this->units_w * $this->scale ) + ( 2 * $this->pad );
		$this->height = ( $this->units_h * $this->scale ) + ( 2 * $this->pad );
	}

	/* ---------- Transforms ---------- */

	public function x( $units ) { return round( $this->pad + ( $units * $this->scale ), 2 ); }
	public function y( $units ) { return round( $this->pad + ( $units * $this->scale ), 2 ); }
	public function len( $units ) { return round( $units * $this->scale, 2 ); }

	/* ---------- Shapes ---------- */

	public function rect( $x, $y, $w, $h, $fill = 'none', $stroke = 'var(--calcr-line-strong)', $opts = array() ) {
		$attrs = array(
			'x'            => $this->x( $x ),
			'y'            => $this->y( $y ),
			'width'        => $this->len( $w ),
			'height'       => $this->len( $h ),
			'fill'         => $fill,
			'stroke'       => $stroke,
			'stroke-width' => isset( $opts['weight'] ) ? $opts['weight'] : 1.5,
		);

		if ( isset( $opts['radius'] ) ) { $attrs['rx'] = $opts['radius']; }
		if ( isset( $opts['dash'] ) )   { $attrs['stroke-dasharray'] = $opts['dash']; }
		if ( isset( $opts['opacity'] ) ){ $attrs['fill-opacity'] = $opts['opacity']; }

		$this->parts[] = '<rect ' . self::attrs( $attrs ) . ' />';
		return $this;
	}

	public function circle( $cx, $cy, $r_units, $fill, $stroke = 'none' ) {
		$this->parts[] = '<circle ' . self::attrs( array(
			'cx'     => $this->x( $cx ),
			'cy'     => $this->y( $cy ),
			'r'      => $this->len( $r_units ),
			'fill'   => $fill,
			'stroke' => $stroke,
			'stroke-width' => 1.5,
		) ) . ' />';
		return $this;
	}

	/**
	 * A repeating grid, which is what makes a planting bed or a tile field
	 * readable at a glance rather than a rectangle with a number beside it.
	 */
	public function grid( $x, $y, $w, $h, $step, $stroke = 'var(--calcr-line)' ) {
		$lines = array();

		for ( $i = $step; $i < $w - 0.0001; $i += $step ) {
			$lines[] = '<line ' . self::attrs( array(
				'x1' => $this->x( $x + $i ), 'y1' => $this->y( $y ),
				'x2' => $this->x( $x + $i ), 'y2' => $this->y( $y + $h ),
				'stroke' => $stroke, 'stroke-width' => 1,
			) ) . ' />';
		}

		for ( $j = $step; $j < $h - 0.0001; $j += $step ) {
			$lines[] = '<line ' . self::attrs( array(
				'x1' => $this->x( $x ), 'y1' => $this->y( $y + $j ),
				'x2' => $this->x( $x + $w ), 'y2' => $this->y( $y + $j ),
				'stroke' => $stroke, 'stroke-width' => 1,
			) ) . ' />';
		}

		$this->parts[] = implode( '', $lines );
		return $this;
	}

	/* ---------- Annotation ---------- */

	/**
	 * A dimension line with ticks at both ends and its measurement on it.
	 *
	 * The numbers go on the drawing rather than in a caption underneath,
	 * because the test a drawing has to pass is whether a screenshot of it
	 * alone still answers the question, and a caption does not travel.
	 *
	 * @param string $side One of top, bottom, left, right.
	 */
	public function dimension( $from, $to, $along, $label, $side = 'bottom' ) {
		$horizontal = in_array( $side, array( 'top', 'bottom' ), true );
		$colour     = 'var(--calcr-muted)';

		if ( $horizontal ) {
			$x1 = $this->x( $from ); $x2 = $this->x( $to ); $yy = $this->y( $along );
			$tick = 5;
			$this->parts[] = '<line ' . self::attrs( array( 'x1' => $x1, 'y1' => $yy, 'x2' => $x2, 'y2' => $yy, 'stroke' => $colour, 'stroke-width' => 1 ) ) . ' />'
				. '<line ' . self::attrs( array( 'x1' => $x1, 'y1' => $yy - $tick, 'x2' => $x1, 'y2' => $yy + $tick, 'stroke' => $colour, 'stroke-width' => 1 ) ) . ' />'
				. '<line ' . self::attrs( array( 'x1' => $x2, 'y1' => $yy - $tick, 'x2' => $x2, 'y2' => $yy + $tick, 'stroke' => $colour, 'stroke-width' => 1 ) ) . ' />';

			$this->label( ( $from + $to ) / 2, $along, $label, array(
				'dy' => 'bottom' === $side ? 16 : -8,
				'anchor' => 'middle',
			) );
		} else {
			$y1 = $this->y( $from ); $y2 = $this->y( $to ); $xx = $this->x( $along );
			$tick = 5;
			$this->parts[] = '<line ' . self::attrs( array( 'x1' => $xx, 'y1' => $y1, 'x2' => $xx, 'y2' => $y2, 'stroke' => $colour, 'stroke-width' => 1 ) ) . ' />'
				. '<line ' . self::attrs( array( 'x1' => $xx - $tick, 'y1' => $y1, 'x2' => $xx + $tick, 'y2' => $y1, 'stroke' => $colour, 'stroke-width' => 1 ) ) . ' />'
				. '<line ' . self::attrs( array( 'x1' => $xx - $tick, 'y1' => $y2, 'x2' => $xx + $tick, 'y2' => $y2, 'stroke' => $colour, 'stroke-width' => 1 ) ) . ' />';

			/* Rotated so a long measurement does not push the drawing sideways
			   to make room for its own label. */
			$cx = $xx; $cy = ( $y1 + $y2 ) / 2;
			$dx = 'left' === $side ? -10 : 14;
			$this->parts[] = '<text ' . self::attrs( array(
				'x' => $cx + $dx, 'y' => $cy,
				'fill' => $colour, 'font-size' => 13, 'font-family' => 'var(--calcr-font-body)',
				'text-anchor' => 'middle', 'dominant-baseline' => 'middle',
				'transform' => 'rotate(-90 ' . ( $cx + $dx ) . ' ' . $cy . ')',
			) ) . '>' . esc_html( $label ) . '</text>';
		}

		return $this;
	}

	public function label( $x, $y, $text, $opts = array() ) {
		$opts = array_merge( array( 'dy' => 0, 'dx' => 0, 'anchor' => 'middle', 'size' => 13, 'colour' => 'var(--calcr-muted)', 'weight' => 400 ), $opts );

		$this->parts[] = '<text ' . self::attrs( array(
			'x' => $this->x( $x ) + $opts['dx'],
			'y' => $this->y( $y ) + $opts['dy'],
			'fill' => $opts['colour'],
			'font-size' => $opts['size'],
			'font-weight' => $opts['weight'],
			'font-family' => 400 === $opts['weight'] ? 'var(--calcr-font-body)' : 'var(--calcr-font-display)',
			'text-anchor' => $opts['anchor'],
			'dominant-baseline' => 'middle',
		) ) . '>' . esc_html( $text ) . '</text>';

		return $this;
	}

	public function legend_item( $colour, $text ) {
		$this->legend[] = array( 'colour' => $colour, 'text' => $text );
		return $this;
	}

	/* ---------- Output ---------- */

	/**
	 * @param string $title A complete sentence, because it is the accessible
	 *                      name and the thing a crawler reads instead of the
	 *                      picture it cannot see.
	 * @param string $desc  The same information in prose, at more length.
	 */
	public function render( $title, $desc ) {
		$height = $this->height;
		$body   = implode( "\n\t", $this->parts );

		if ( $this->legend ) {
			$height += 28;
			$x = $this->pad;
			$y = $this->height + 8;
			$row = array();

			foreach ( $this->legend as $item ) {
				$row[] = '<rect ' . self::attrs( array( 'x' => $x, 'y' => $y - 6, 'width' => 11, 'height' => 11, 'rx' => 3, 'fill' => $item['colour'] ) ) . ' />'
					. '<text ' . self::attrs( array(
						'x' => $x + 17, 'y' => $y,
						'fill' => 'var(--calcr-muted)', 'font-size' => 13,
						'font-family' => 'var(--calcr-font-body)', 'dominant-baseline' => 'middle',
					) ) . '>' . esc_html( $item['text'] ) . '</text>';

				/* Advanced by a rough character width, which is enough because
				   legends here carry two or three short entries by design. */
				$x += 34 + ( strlen( $item['text'] ) * 6.6 );
			}

			$body .= "\n\t" . implode( "\n\t", $row );
		}

		$id = 'd' . substr( md5( $title . $desc ), 0, 8 );

		return '<svg class="calcr-draw" viewBox="0 0 ' . round( $this->width ) . ' ' . round( $height ) . '"'
			. ' role="img" aria-labelledby="' . $id . 't ' . $id . 'd"'
			. ' xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet">'
			. '<title id="' . $id . 't">' . esc_html( $title ) . '</title>'
			. '<desc id="' . $id . 'd">' . esc_html( $desc ) . '</desc>'
			. "\n\t" . $body . "\n" . '</svg>';
	}

	private static function attrs( $attrs ) {
		$out = array();

		foreach ( $attrs as $key => $value ) {
			$out[] = $key . '="' . esc_attr( $value ) . '"';
		}

		return implode( ' ', $out );
	}
}
