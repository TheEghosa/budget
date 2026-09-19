<?php
/**
 * Reaches into the generated artwork.
 *
 * design-assets.php is a plain array produced by tools/build-assets.py, and
 * everything that draws something goes through here rather than reading that
 * array directly, for two reasons. The file is only read once per request
 * however many tiles a page prints, and the fallback lives in one place: a
 * calculator with no drawing of its own borrows its category's, because there
 * are ten category icons and a hundred and five calculators, and a tile that
 * silently renders as nothing leaves a hole in every card it appears on.
 *
 * Nothing here escapes its output. The artwork is generated from files in the
 * repository rather than from anything a visitor or an editor can supply, so
 * it is markup by design, and running it through esc_html would print the
 * source of an SVG instead of drawing it.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Calculatorr_Art {

	private static $assets = null;

	private static function assets() {
		if ( null === self::$assets ) {
			$path = __DIR__ . '/design-assets.php';
			$data = file_exists( $path ) ? require $path : array();
			self::$assets = is_array( $data ) ? $data : array();
		}

		return self::$assets;
	}

	private static function pick( $group, $name ) {
		$assets = self::assets();

		return isset( $assets[ $group ][ $name ] ) ? $assets[ $group ][ $name ] : '';
	}

	public static function icon( $name ) {
		return self::pick( 'icons', $name );
	}

	public static function motif( $name ) {
		return self::pick( 'motifs', $name );
	}

	/**
	 * The artwork keys drop the word everybody's slug ends in, so
	 * boat-loan-calculator is drawn as boat-loan. Stripping it here rather
	 * than renaming a hundred and five configs keeps the slugs matching the
	 * URLs, which is the thing that must not move.
	 */
	private static function art_key( $slug ) {
		return preg_replace( '/-(calculator|converter)$/', '', (string) $slug );
	}

	/**
	 * The icon for one calculator: its own if it has been drawn, otherwise the
	 * one for the category it sits in. Nine of the hundred and five have their
	 * own; the rest wear their category's, which is why the fallback is the
	 * normal path here rather than the error case.
	 */
	public static function icon_for( $config ) {
		$category = isset( $config['category'] ) ? $config['category'] : '';
		$own      = self::icon( 'tool-' . self::art_key( isset( $config['slug'] ) ? $config['slug'] : '' ) );

		return $own ? $own : self::category_icon( $category );
	}

	/**
	 * The wide drawing behind a card's thumbnail. Unlike the icon this has no
	 * fallback: a card without a motif shows its tint and its tile, which is a
	 * quieter card rather than a broken one, and eleven borrowed drawings
	 * repeated across a hundred cards would be worse than none.
	 */
	public static function motif_for( $config ) {
		return self::motif( self::art_key( isset( $config['slug'] ) ? $config['slug'] : '' ) );
	}

	/*
	 * Four categories are filed under one name and drawn under another: the
	 * registry calls them finance, convert, home-diy and education while the
	 * artwork calls them financial, conversion, construction and grade. The
	 * names are both in use elsewhere, so rather than renaming either the
	 * mapping lives here, which is the one place that has to know both.
	 */
	private static $icon_aliases = array(
		'finance'   => 'financial',
		'convert'   => 'conversion',
		'home-diy'  => 'construction',
		'education' => 'grade',
	);

	public static function category_icon( $slug ) {
		$slug = isset( self::$icon_aliases[ $slug ] ) ? self::$icon_aliases[ $slug ] : $slug;

		return self::icon( 'category-' . $slug );
	}

	/**
	 * A rounded brand-coloured square with an icon in it, at whatever size the
	 * caller needs. The radius and the glyph are derived from the size rather
	 * than passed in, because every tile in the design holds the same ratios
	 * and three numbers at every call site is three chances to get one wrong.
	 */
	public static function tile( $svg, $size = 52, $class = 'calcr-tile' ) {
		if ( ! $svg ) {
			return '';
		}

		return sprintf(
			'<span class="%1$s" style="--tile:%2$dpx;--tile-radius:%3$dpx;--tile-glyph:%4$dpx" aria-hidden="true">%5$s</span>',
			esc_attr( $class ),
			(int) $size,
			(int) round( $size * 0.29 ),
			(int) round( $size * 0.5 ),
			$svg
		);
	}

	/**
	 * The logo mark.
	 *
	 * One drawing serves both themes. The generated file carries a separate
	 * light and dark copy, but they differ only in the two colours that are
	 * already tokens, so keeping two would mean printing both and hiding one,
	 * and the hidden one would still be downloaded and would drift the first
	 * time somebody edited only the copy they were looking at.
	 */
	public static function mark( $size = 36 ) {
		return sprintf(
			'<svg class="calcr-mark" width="%1$d" height="%1$d" viewBox="0 0 36 36" aria-hidden="true" focusable="false">'
			. '<rect width="36" height="36" rx="10" fill="var(--calcr-accent)"/>'
			. '<rect x="9" y="12" width="18" height="4" rx="2" fill="var(--c-tile-glyph)"/>'
			. '<rect x="9" y="20" width="10" height="4" rx="2" fill="var(--c-tile-glyph)"/>'
			. '<circle cx="24.5" cy="22" r="2.6" fill="var(--c-tile-glyph)"/></svg>',
			(int) $size
		);
	}

	/**
	 * The full lockup: mark plus wordmark, as one link.
	 *
	 * The wordmark is live text rather than part of the drawing, so it is
	 * selectable, it scales with the visitor's font size, and the trailing
	 * "rr" can take the brand colour without shipping a second file.
	 */
	public static function lockup( $url = '/', $size = 36, $class = 'calcr-lockup' ) {
		return sprintf(
			'<a class="%1$s" href="%2$s" rel="home"><span class="calcr-lockup__mark">%3$s</span>'
			. '<span class="calcr-lockup__word">calculato<span>rr</span></span></a>',
			esc_attr( $class ),
			esc_url( $url ),
			self::mark( $size )
		);
	}
}
