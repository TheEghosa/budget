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
			'load_fonts'          => 1,
			'seo_enabled'         => 1,
			'schema_enabled'      => 1,
			'share_enabled'       => 1,
			'log_enabled'         => 1,
			'log_limit'           => 200,
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

		foreach ( array( 'ads_enabled', 'load_fonts', 'seo_enabled', 'schema_enabled', 'share_enabled', 'log_enabled' ) as $flag ) {
			$clean[ $flag ] = empty( $clean[ $flag ] ) ? 0 : 1;
		}

		$clean['log_limit'] = max( 20, min( 2000, (int) $clean['log_limit'] ) );

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
