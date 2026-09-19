<?php
/**
 * Reads and writes the plugin's settings over the REST API.
 *
 * The admin screens remain the place a person changes these. This route exists
 * so the settings can also be inspected and adjusted from outside the browser,
 * which is what makes it possible to check a live install's configuration, or
 * fix one, without asking somebody to click through six tabs and read values
 * back by hand.
 *
 * It is administrator-only and it writes through the same Calculatorr_Settings
 * sanitiser the admin forms use, so a value cannot reach the option row by this
 * route that could not reach it through the settings page.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Calculatorr_Rest_Settings {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}

	public function register() {
		register_rest_route(
			'calculatorr/v1',
			'/settings',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'read' ),
					'permission_callback' => array( $this, 'may_manage' ),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'write' ),
					'permission_callback' => array( $this, 'may_manage' ),
				),
			)
		);

		register_rest_route(
			'calculatorr/v1',
			'/status',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'status' ),
				'permission_callback' => array( $this, 'may_manage' ),
			)
		);
	}

	public function may_manage() {
		return current_user_can( 'manage_options' );
	}

	public function read() {
		return rest_ensure_response( Calculatorr_Settings::instance()->all() );
	}

	/**
	 * Only the keys that already exist are accepted, so a typo in a request
	 * cannot quietly add a setting nothing reads.
	 */
	public function write( WP_REST_Request $request ) {
		$settings = Calculatorr_Settings::instance();
		$current  = $settings->all();
		$body     = $request->get_json_params();

		if ( ! is_array( $body ) ) {
			return new WP_Error( 'calculatorr_bad_body', 'Expected a JSON object of settings.', array( 'status' => 400 ) );
		}

		$unknown = array_diff( array_keys( $body ), array_keys( $settings->defaults() ) );

		if ( $unknown ) {
			return new WP_Error(
				'calculatorr_unknown_setting',
				'Unknown setting: ' . implode( ', ', $unknown ),
				array( 'status' => 400 )
			);
		}

		/* Design is a nested set, so a request naming one colour means change
		   that colour, not replace the palette with a single entry. Sending an
		   empty object for it is the way to clear the lot back to defaults. */
		if ( isset( $body['design'] ) && is_array( $body['design'] ) && $body['design'] ) {
			$body['design'] = array_merge( (array) $current['design'], $body['design'] );
		}

		$settings->save( array_merge( $current, $body ) );

		return rest_ensure_response( $settings->all() );
	}

	/**
	 * A one-request summary of what the install actually has, which is quicker
	 * to read than the dashboard when all you want to know is whether the
	 * pages, the theme and the plugin agree with each other.
	 */
	public function status() {
		$registry = Calculatorr_Registry::instance();
		$all      = $registry->all();
		$live     = 0;

		foreach ( array_keys( $all ) as $slug ) {
			if ( $registry->get_live( $slug ) ) {
				$live++;
			}
		}

		/* A calculator with no page behind it still renders a link, so the
		   only way to notice one is missing is to look the path up. */
		$missing = array();

		foreach ( $all as $slug => $config ) {
			if ( ! get_page_by_path( Calculatorr_Pages::path_for( $config ), OBJECT, 'page' ) ) {
				$missing[] = $slug;
			}
		}

		return rest_ensure_response(
			array(
				'version'     => CALCULATORR_VERSION,
				'calculators' => count( $all ),
				'live'        => $live,
				'categories'  => count( $registry->categories() ),
				'missing'     => $missing,
				'theme'       => get_stylesheet(),
				'seo_active'  => ! Calculatorr_SEO::instance()->is_deferring(),
				'front_page'  => (int) get_option( 'page_on_front' ),
			)
		);
	}
}
