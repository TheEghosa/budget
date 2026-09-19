<?php
/**
 * Stored settings, and the overrides that let a calculator be changed without
 * touching a config file.
 *
 * Everything here lives in two options rather than one row per calculator,
 * because a hundred and five autoloaded options would be a hundred and five
 * queries the front end does not need.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Calculatorr_Settings {

	const OPTION   = 'calculatorr_settings';
	const OVERRIDE = 'calculatorr_overrides';

	private static $instance = null;
	private $settings = null;
	private $overrides = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {}

	public function defaults() {
		return array(
			'ad_after_calculator' => '',
			'ad_in_content'       => '',
			'ad_sidebar'          => '',
			'ads_enabled'         => 0,
			'house_ads_enabled'   => 1,
			'head_footer_enabled' => 1,
			'code_head'           => '',
			'code_body'           => '',
			'code_footer'         => '',
			'site_chrome'         => 1,
			'theme_switch'        => 1,
			'empty_start'         => 1,
			'home_description'    => '',
			'load_fonts'          => 1,
			'seo_enabled'         => 1,
			'schema_enabled'      => 1,
			'share_enabled'       => 1,
			'log_enabled'         => 1,
			'render_heading'      => 0,
			'log_limit'           => 200,
			'design'              => array(),
			'disabled'            => array(),
		);
	}

	public function all() {
		if ( null === $this->settings ) {
			$this->settings = wp_parse_args( get_option( self::OPTION, array() ), $this->defaults() );
		}
		return $this->settings;
	}

	public function get( $key ) {
		$all = $this->all();
		return isset( $all[ $key ] ) ? $all[ $key ] : null;
	}

	public function save( $values ) {
		$clean = wp_parse_args( $values, $this->all() );
		$clean['disabled'] = array_values( array_unique( array_map( 'sanitize_key', (array) $clean['disabled'] ) ) );

		foreach ( array( 'ads_enabled', 'house_ads_enabled', 'head_footer_enabled', 'site_chrome', 'theme_switch', 'empty_start', 'load_fonts', 'seo_enabled', 'schema_enabled', 'share_enabled', 'log_enabled', 'render_heading' ) as $flag ) {
			$clean[ $flag ] = empty( $clean[ $flag ] ) ? 0 : 1;
		}

		$clean['log_limit'] = max( 20, min( 2000, (int) $clean['log_limit'] ) );

		/* The design values are written straight into a stylesheet, so they are
		   checked against the shape each one is meant to be rather than merely
		   escaped: a malformed colour does not fail loudly, it quietly breaks the
		   rule it sits in and takes the rest of the block with it. */
		$clean['design'] = Calculatorr_Design::sanitise( (array) $clean['design'] );

		update_option( self::OPTION, $clean );
		$this->settings = $clean;
	}

	public function is_disabled( $slug ) {
		return in_array( $slug, (array) $this->get( 'disabled' ), true );
	}

	/**
	 * Per-calculator overrides for the fields an editor might reasonably want
	 * to change without a deploy: the headline, the title tag and the
	 * description. Anything structural stays in the config file, because a
	 * formula is code and belongs under version control.
	 */
	public function overrides( $slug = null ) {
		if ( null === $this->overrides ) {
			$this->overrides = get_option( self::OVERRIDE, array() );
			if ( ! is_array( $this->overrides ) ) {
				$this->overrides = array();
			}
		}

		if ( null === $slug ) {
			return $this->overrides;
		}

		return isset( $this->overrides[ $slug ] ) ? $this->overrides[ $slug ] : array();
	}

	public function save_override( $slug, $values ) {
		$all = $this->overrides();
		$clean = array();

		foreach ( array( 'h1', 'meta_title', 'meta_description', 'description' ) as $key ) {
			if ( isset( $values[ $key ] ) && '' !== trim( $values[ $key ] ) ) {
				$clean[ $key ] = sanitize_text_field( $values[ $key ] );
			}
		}

		if ( $clean ) {
			$all[ $slug ] = $clean;
		} else {
			unset( $all[ $slug ] );
		}

		update_option( self::OVERRIDE, $all );
		$this->overrides = $all;
	}

	/**
	 * Applies any override on top of a config. Called by the registry so
	 * nothing downstream has to know overrides exist.
	 */
	public function apply( $config ) {
		$override = $this->overrides( $config['slug'] );

		return $override ? array_merge( $config, $override ) : $config;
	}
}
