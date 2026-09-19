<?php
/**
 * The charts on the dashboard, drawn as inline SVG.
 *
 * No charting library, for three reasons that all matter here: a library is a
 * download on an admin screen nobody is waiting on, every one of them fights
 * the WordPress admin stylesheet, and the shapes needed are a line, a bar and a
 * sparkline, which is an afternoon of geometry rather than a dependency.
 *
 * All three functions take a plain day => count array and return markup, so the
 * maths is testable without a browser or a database anywhere near it.
 *
 * On colour: every chart here is one series, so there is nothing to tell apart
 * and no categorical palette to get wrong. The mark colour is a brighter
 * sibling of the brand teal, chosen because the brand teal itself sits below
 * the chroma floor for a data mark and reads as grey next to white. Labels and
 * values wear text colours rather than the series colour, so identity is never
 * carried by colour alone.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Calculatorr_Chart {

	/**
	 * A small line with no axes, for a table cell or a stat tile. It says
	 * shape, not value, so it carries no labels and no grid.
	 *
	 * @param array $series day => count, in order.
	 */
	public static function sparkline( $series, $width = 120, $height = 28 ) {
		$values = array_values( $series );
		$count  = count( $values );

		if ( $count < 2 ) {
			return '<span class="calcr-chart__empty" aria-hidden="true">&mdash;</span>';
		}

		$max = max( $values );
		$pad = 3;
		$inner_h = $height - ( $pad * 2 );

		$points = array();

		foreach ( $values as $i => $value ) {
			$x = ( $count > 1 ) ? ( $i / ( $count - 1 ) ) * $width : 0;
			/* A flat run of zeroes sits on the floor rather than halfway up,
			   which is what dividing by a zero maximum would otherwise do. */
			$y = $max > 0 ? $height - $pad - ( ( $value / $max ) * $inner_h ) : $height - $pad;
			$points[] = round( $x, 1 ) . ',' . round( $y, 1 );
		}

		$total = array_sum( $values );
		$label = sprintf(
			/* translators: 1: total uses, 2: number of days. */
			'%1$s uses over %2$d days',
			number_format_i18n( $total ),
			$count
		);

		return sprintf(
			'<svg class="calcr-chart calcr-chart--spark" viewBox="0 0 %1$d %2$d" width="%1$d" height="%2$d" role="img" aria-label="%3$s" preserveAspectRatio="none"><title>%3$s</title><polyline fill="none" stroke="var(--calcr-chart-series)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" points="%4$s"/></svg>',
			$width,
			$height,
			esc_attr( $label ),
			esc_attr( implode( ' ', $points ) )
		);
	}

	/**
	 * The trend: one series over time, drawn as a line on a soft fill with a
	 * marker and a direct label on the most recent point only. Labelling every
	 * point turns a trend into a table that is harder to read than a table.
	 */
	public static function trend( $series, $width = 720, $height = 200 ) {
		$days   = array_keys( $series );
		$values = array_values( $series );
		$count  = count( $values );

		if ( $count < 2 ) {
			return '<p class="calcr-chart__none">Not enough days recorded yet to draw a trend.</p>';
		}

		$max = max( $values );
		$top = $max > 0 ? $max : 1;

		$pad_l = 44;
		$pad_r = 56;
		$pad_t = 16;
		$pad_b = 28;

		$plot_w = $width - $pad_l - $pad_r;
		$plot_h = $height - $pad_t - $pad_b;

		$x = function ( $i ) use ( $pad_l, $plot_w, $count ) {
			return $pad_l + ( ( $count > 1 ) ? ( $i / ( $count - 1 ) ) * $plot_w : 0 );
		};
		$y = function ( $value ) use ( $pad_t, $plot_h, $top ) {
			return $pad_t + $plot_h - ( ( $value / $top ) * $plot_h );
		};

		$line = array();
		$dots = '';

		foreach ( $values as $i => $value ) {
			$px = round( $x( $i ), 1 );
			$py = round( $y( $value ), 1 );
			$line[] = $px . ',' . $py;

			/* Every point carries a hover target, because a chart you cannot
			   interrogate is a picture. The circle is transparent until it is
			   hovered, so the line stays thin. */
			$dots .= sprintf(
				'<circle class="calcr-chart__dot" cx="%1$s" cy="%2$s" r="9"><title>%3$s</title></circle>',
				$px,
				$py,
				esc_attr( sprintf( '%s: %s uses', self::pretty_day( $days[ $i ] ), number_format_i18n( $value ) ) )
			);
		}

		$area = sprintf(
			'%s,%s %s %s,%s',
			round( $x( 0 ), 1 ),
			round( $pad_t + $plot_h, 1 ),
			implode( ' ', $line ),
			round( $x( $count - 1 ), 1 ),
			round( $pad_t + $plot_h, 1 )
		);

		/* Three gridlines, recessive, with the value axis labelled at the top
		   and the middle only: more than that is furniture. */
		$grid = '';
		foreach ( array( 0, 0.5, 1 ) as $step ) {
			$gy = round( $pad_t + ( $plot_h * $step ), 1 );
			$grid .= sprintf(
				'<line class="calcr-chart__grid" x1="%1$s" y1="%2$s" x2="%3$s" y2="%2$s"/>',
				$pad_l,
				$gy,
				$pad_l + $plot_w
			);
			$grid .= sprintf(
				'<text class="calcr-chart__axis" x="%1$s" y="%2$s" text-anchor="end" dominant-baseline="middle">%3$s</text>',
				$pad_l - 8,
				$gy,
				esc_html( number_format_i18n( round( $top * ( 1 - $step ) ) ) )
			);
		}

		$last_x = round( $x( $count - 1 ), 1 );
		$last_y = round( $y( $values[ $count - 1 ] ), 1 );

		$ends = sprintf(
			'<text class="calcr-chart__date" x="%1$s" y="%2$s" text-anchor="start">%3$s</text>'
			. '<text class="calcr-chart__date" x="%4$s" y="%2$s" text-anchor="end">%5$s</text>',
			$pad_l,
			$height - 8,
			esc_html( self::pretty_day( $days[0] ) ),
			$pad_l + $plot_w,
			esc_html( self::pretty_day( $days[ $count - 1 ] ) )
		);

		return sprintf(
			'<svg class="calcr-chart calcr-chart--trend" viewBox="0 0 %1$d %2$d" width="100%%" height="%2$d" role="img" aria-label="%3$s">'
			. '<title>%3$s</title>%4$s'
			. '<polygon class="calcr-chart__area" points="%5$s"/>'
			. '<polyline class="calcr-chart__line" points="%6$s"/>'
			. '<circle class="calcr-chart__last" cx="%7$s" cy="%8$s" r="4.5"/>'
			. '<text class="calcr-chart__value" x="%9$s" y="%8$s" dominant-baseline="middle">%10$s</text>'
			. '%11$s%12$s</svg>',
			$width,
			$height,
			esc_attr( sprintf( 'Calculations per day over the last %d days', $count ) ),
			$grid,
			esc_attr( $area ),
			esc_attr( implode( ' ', $line ) ),
			$last_x,
			$last_y,
			$last_x + 10,
			esc_html( number_format_i18n( $values[ $count - 1 ] ) ),
			$ends,
			$dots
		);
	}

	/**
	 * Ranked horizontal bars. Horizontal because the labels are calculator
	 * names, and a vertical bar chart with "Mortgage Payment Calculator" under
	 * it either truncates the name or turns it on its side.
	 *
	 * @param array $rows label => count, already ordered.
	 */
	public static function bars( $rows, $width = 720 ) {
		if ( ! $rows ) {
			return '<p class="calcr-chart__none">Nothing recorded yet.</p>';
		}

		$max      = max( $rows );
		$top      = $max > 0 ? $max : 1;
		$row_h    = 30;
		$gap      = 2;              /* the surface gap the marks need to read as separate */
		$label_w  = 260;
		$value_w  = 56;
		$bar_w    = $width - $label_w - $value_w;
		$height   = count( $rows ) * $row_h;
		$bars     = '';
		$i        = 0;

		foreach ( $rows as $label => $value ) {
			$y = ( $i * $row_h );
			$w = max( 2, ( $value / $top ) * $bar_w );

			$bar_path = self::capped_bar( $label_w, $y + $gap, $w, $row_h - ( $gap * 2 ), 4 );

			$bars .= sprintf(
				'<text class="calcr-chart__label" x="0" y="%1$s" dominant-baseline="middle">%2$s</text>'
				/* Rounded on the data end only. A rect with a corner radius
				   rounds all four, which lifts the bar off the baseline and
				   makes it read as floating rather than as measured from zero,
				   so the shape is drawn as a path instead. */
				. '<path class="calcr-chart__bar" d="%3$s"><title>%4$s</title></path>'
				. '<text class="calcr-chart__value" x="%5$s" y="%1$s" dominant-baseline="middle">%6$s</text>',
				round( $y + ( $row_h / 2 ), 1 ),
				esc_html( self::truncate( $label, 36 ) ),
				esc_attr( $bar_path ),
				esc_attr( sprintf( '%s: %s uses', $label, number_format_i18n( $value ) ) ),
				round( $label_w + $w + 8, 1 ),
				esc_html( number_format_i18n( $value ) )
			);

			$i++;
		}

		return sprintf(
			'<svg class="calcr-chart calcr-chart--bars" viewBox="0 0 %1$d %2$d" width="100%%" height="%2$d" role="img" aria-label="Most used calculators">%3$s</svg>',
			$width,
			$height,
			$bars
		);
	}

	/**
	 * A horizontal bar whose right-hand end is rounded and whose left-hand end
	 * is square, so the bar stays visually welded to the baseline it is
	 * measured from. The radius is clamped to the bar, because a short bar with
	 * a 4px radius on a 4px width is a lozenge rather than a measurement.
	 */
	private static function capped_bar( $x, $y, $width, $height, $radius ) {
		$r  = min( $radius, $width, $height / 2 );
		$x2 = $x + $width;
		$y2 = $y + $height;

		return sprintf(
			'M%1$s,%2$s H%3$s Q%4$s,%2$s %4$s,%5$s V%6$s Q%4$s,%7$s %3$s,%7$s H%1$s Z',
			round( $x, 1 ),
			round( $y, 1 ),
			round( $x2 - $r, 1 ),
			round( $x2, 1 ),
			round( $y + $r, 1 ),
			round( $y2 - $r, 1 ),
			round( $y2, 1 )
		);
	}

	private static function truncate( $text, $length ) {
		$text = (string) $text;

		return ( strlen( $text ) > $length ) ? rtrim( substr( $text, 0, $length - 1 ) ) . '…' : $text;
	}

	private static function pretty_day( $day ) {
		$time = strtotime( $day . ' 00:00:00' );

		return $time ? gmdate( 'j M', $time ) : $day;
	}
}
