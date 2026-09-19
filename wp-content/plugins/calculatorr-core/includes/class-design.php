<?php
/**
 * The design layer: the palette, the type and the spacing as stored values
 * rather than as lines in a stylesheet.
 *
 * Every colour in the site already comes from one custom property, so the whole
 * look can be changed by redeclaring a handful of those properties after the
 * stylesheets have loaded. That is what this class does. The practical effect
 * is that changing the brand colour, the fonts or the container width is a
 * saved setting rather than an edited file, so it needs no packaging step, no
 * upload, and no waiting: it applies to all 118 pages on the next request.
 *
 * Only what defines the brand is exposed. The rest of the palette derives from
 * these, and the long tail is served by the custom CSS box, because a settings
 * screen with sixty colour pickers on it is not a design system, it is a
 * stylesheet with extra steps.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Calculatorr_Design {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		/* Priority 99 so this lands after the enqueued stylesheets and wins on
		   source order without needing !important anywhere. */
		add_action( 'wp_head', array( $this, 'output' ), 99 );
	}

	/**
	 * What can be changed, what it maps to, and what it is by default.
	 *
	 * The defaults repeat the values in tokens.css deliberately: a field is
	 * only written into the page when it differs from its default, so an
	 * untouched install emits nothing at all and the stylesheet stays the
	 * single source of truth until somebody actually changes something.
	 */
	public static function schema() {
		return array(
			'light' => array(
				'label'  => 'Light mode colours',
				'fields' => array(
					'accent'      => array( 'Accent', '--calcr-accent', 'color', '#0E6E63' ),
					'accent_soft' => array( 'Accent tint', '--calcr-accent-soft', 'color', '#E8F2F0' ),
					'paper'       => array( 'Page background', '--calcr-paper', 'color', '#FBFAF8' ),
					'surface'     => array( 'Card background', '--calcr-surface', 'color', '#FFFFFF' ),
					'ink'         => array( 'Text', '--calcr-ink', 'color', '#14181D' ),
					'muted'       => array( 'Secondary text', '--calcr-muted', 'color', '#5A6472' ),
					'line'        => array( 'Borders', '--calcr-line', 'color', '#E4E2DC' ),
					'warn'        => array( 'Highlight', '--calcr-warn', 'color', '#B45E0C' ),
					'footer_bg'   => array( 'Footer band', '--calcr-footer-bg', 'color', '#14181D' ),
				),
			),
			'dark'  => array(
				'label'  => 'Dark mode colours',
				'fields' => array(
					'accent'      => array( 'Accent', '--calcr-accent', 'color', '#4FB8A8' ),
					'accent_soft' => array( 'Accent tint', '--calcr-accent-soft', 'color', '#16302C' ),
					'paper'       => array( 'Page background', '--calcr-paper', 'color', '#14181D' ),
					'surface'     => array( 'Card background', '--calcr-surface', 'color', '#1C2128' ),
					'ink'         => array( 'Text', '--calcr-ink', 'color', '#F2F4F5' ),
					'muted'       => array( 'Secondary text', '--calcr-muted', 'color', '#9BA5B0' ),
					'line'        => array( 'Borders', '--calcr-line', 'color', '#2C333B' ),
					'warn'        => array( 'Highlight', '--calcr-warn', 'color', '#E29A4E' ),
					'footer_bg'   => array( 'Footer band', '--calcr-footer-bg', 'color', '#0E1116' ),
				),
			),
			'type'  => array(
				'label'  => 'Typography and spacing',
				'fields' => array(
					'font_display' => array( 'Heading font', '--calcr-font-display', 'font', "'Space Grotesk', ui-sans-serif, system-ui, sans-serif" ),
					'font_body'    => array( 'Body font', '--calcr-font-body', 'font', "'Source Sans 3', ui-sans-serif, system-ui, sans-serif" ),
					'container'    => array( 'Content width', '--calcr-container', 'length', '1280px' ),
					'header'       => array( 'Header height', '--calcr-header-height', 'length', '70px' ),
					'radius'       => array( 'Corner radius', '--calcr-radius', 'length', '9px' ),
					'touch'        => array( 'Minimum control height', '--calcr-touch', 'length', '46px' ),
				),
			),
		);
	}

	public static function defaults() {
		$out = array( 'fonts_url' => self::default_fonts_url(), 'custom_css' => '' );

		foreach ( self::schema() as $group => $spec ) {
			foreach ( $spec['fields'] as $key => $field ) {
				$out[ $group . '_' . $key ] = $field[3];
			}
		}

		return $out;
	}

	public static function default_fonts_url() {
		return 'https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Source+Sans+3:wght@400;600&display=swap';
	}

	public static function get( $key = null ) {
		$stored = (array) Calculatorr_Settings::instance()->get( 'design' );
		$all    = array_merge( self::defaults(), $stored );

		return null === $key ? $all : ( isset( $all[ $key ] ) ? $all[ $key ] : null );
	}

	/**
	 * Cleans a submitted design array.
	 *
	 * Each field is checked against the shape it is meant to be rather than
	 * merely escaped, because these values are written into a stylesheet where
	 * a malformed one does not fail loudly, it silently breaks the rule it sits
	 * in and takes the rest of the block with it.
	 */
	public static function sanitise( $values ) {
		$clean = array();

		foreach ( self::schema() as $group => $spec ) {
			foreach ( $spec['fields'] as $key => $field ) {
				$name = $group . '_' . $key;

				if ( ! isset( $values[ $name ] ) ) {
					continue;
				}

				$value = self::clean_value( $field[2], $values[ $name ] );

				if ( null !== $value ) {
					$clean[ $name ] = $value;
				}
			}
		}

		if ( isset( $values['fonts_url'] ) ) {
			$url = trim( (string) $values['fonts_url'] );
			/* Empty is meaningful: it means do not load a webfont at all. */
			$clean['fonts_url'] = ( '' === $url ) ? '' : esc_url_raw( $url, array( 'https' ) );
		}

		if ( isset( $values['custom_css'] ) ) {
			$clean['custom_css'] = self::clean_css( $values['custom_css'] );
		}

		return $clean;
	}

	private static function clean_value( $type, $raw ) {
		$raw = trim( (string) $raw );

		if ( '' === $raw ) {
			return null;
		}

		if ( 'color' === $type ) {
			if ( preg_match( '/^#(?:[0-9a-f]{3}|[0-9a-f]{6}|[0-9a-f]{8})$/i', $raw ) ) {
				return $raw;
			}

			/* rgb(), rgba(), hsl() and hsla() in either the comma or the space
			   syntax, with nothing in the brackets but numbers and separators,
			   which rules out a url() or a var() smuggled in as a colour. */
			if ( preg_match( '/^(?:rgb|hsl)a?\(\s*[0-9.,%\/\s+-]+\)$/i', $raw ) ) {
				return $raw;
			}

			return null;
		}

		if ( 'length' === $type ) {
			return preg_match( '/^[0-9]+(?:\.[0-9]+)?(?:px|rem|em|%|vw|vh|ch)$/', $raw ) ? $raw : null;
		}

		if ( 'font' === $type ) {
			/* A font stack is names, quotes, commas and hyphens. Anything with
			   a bracket in it is trying to be a function call. */
			return preg_match( '/^[A-Za-z0-9 ,\'"_\-]+$/', $raw ) ? $raw : null;
		}

		return null;
	}

	/**
	 * Custom CSS is free text, which is the point of it, so the only thing
	 * removed is the ability to stop being CSS: a closing style tag, or any
	 * tag at all, would let a stylesheet become a script. Administrators can
	 * already install plugins, so the trust level is the same as the theme
	 * editor WordPress ships with; escaping out of the element is not.
	 */
	public static function clean_css( $css ) {
		$css = (string) $css;
		$css = str_replace( array( '<', '>' ), '', $css );

		return trim( wp_unslash( $css ) );
	}

	/**
	 * Only the properties that differ from the stylesheet are written out, so
	 * an install nobody has touched adds nothing to the page.
	 */
	public function overrides() {
		$design   = self::get();
		$defaults = self::defaults();
		$out      = array( 'light' => array(), 'dark' => array(), 'root' => array() );

		foreach ( self::schema() as $group => $spec ) {
			foreach ( $spec['fields'] as $key => $field ) {
				$name = $group . '_' . $key;

				if ( ! isset( $design[ $name ] ) || $design[ $name ] === $defaults[ $name ] ) {
					continue;
				}

				$bucket = in_array( $group, array( 'light', 'dark' ), true ) ? $group : 'root';
				$out[ $bucket ][ $field[1] ] = $design[ $name ];
			}
		}

		return $out;
	}

	public function output() {
		if ( is_admin() || is_feed() ) {
			return;
		}

		$overrides = $this->overrides();
		$css       = '';

		if ( $overrides['root'] ) {
			$css .= ':root{' . $this->declarations( $overrides['root'] ) . '}';
		}

		if ( $overrides['light'] ) {
			$css .= ':root{' . $this->declarations( $overrides['light'] ) . '}';
		}

		if ( $overrides['dark'] ) {
			$dark = $this->declarations( $overrides['dark'] );

			/* Written twice, matching tokens.css: once for the visitor whose
			   system asks for dark and has not overridden it, and once for the
			   visitor who pressed the switch. */
			$css .= '@media (prefers-color-scheme:dark){:root:not([data-calcr-theme="light"]){' . $dark . '}}';
			$css .= ':root[data-calcr-theme="dark"]{' . $dark . '}';
		}

		$custom = self::get( 'custom_css' );

		if ( '' !== trim( (string) $custom ) ) {
			$css .= "\n" . $custom;
		}

		if ( '' === $css ) {
			return;
		}

		echo "\n<style id=\"calculatorr-design\">" . $css . "</style>\n";
	}

	private function declarations( $pairs ) {
		$parts = array();

		foreach ( $pairs as $property => $value ) {
			$parts[] = $property . ':' . $value;
		}

		return implode( ';', $parts ) . ';';
	}
}
