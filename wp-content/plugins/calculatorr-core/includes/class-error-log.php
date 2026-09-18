<?php
/**
 * A small error log for the things that break quietly.
 *
 * Two sources feed it. PHP problems raised inside the plugin's own code, and
 * JavaScript errors reported from the browser, which is the half that
 * normally goes unseen: a formula that throws on a visitor's device leaves no
 * trace on the server at all, and the visitor simply sees a stale answer and
 * leaves.
 *
 * Entries are capped and stored in a single option, so the log can never grow
 * into a problem of its own.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Calculatorr_Error_Log {

	const OPTION = 'calculatorr_error_log';

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_route' ) );
	}

	public function register_route() {
		register_rest_route(
			'calculatorr/v1',
			'/log',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'handle_report' ),
				/* Deliberately public: the whole point is to hear from
				   visitors, who are never logged in. The endpoint writes only
				   to a capped log and returns nothing useful, and the payload
				   is sanitised and length-limited on the way in. */
				'permission_callback' => '__return_true',
				'args'                => array(
					'slug'    => array( 'type' => 'string', 'required' => false ),
					'message' => array( 'type' => 'string', 'required' => true ),
					'context' => array( 'type' => 'string', 'required' => false ),
				),
			)
		);
	}

	public function handle_report( $request ) {
		if ( ! Calculatorr_Settings::instance()->get( 'log_enabled' ) ) {
			return rest_ensure_response( array( 'logged' => false ) );
		}

		$this->add(
			'js',
			sanitize_text_field( (string) $request->get_param( 'message' ) ),
			array(
				'slug'    => sanitize_key( (string) $request->get_param( 'slug' ) ),
				'context' => sanitize_text_field( (string) $request->get_param( 'context' ) ),
			)
		);

		return rest_ensure_response( array( 'logged' => true ) );
	}

	/**
	 * @param string $source  'js' or 'php'.
	 * @param string $message What went wrong.
	 * @param array  $meta    Anything useful for reproducing it.
	 */
	public function add( $source, $message, $meta = array() ) {
		$log = $this->entries();

		$entry = array(
			'time'    => time(),
			'source'  => 'php' === $source ? 'php' : 'js',
			'message' => mb_substr( (string) $message, 0, 500 ),
			'slug'    => isset( $meta['slug'] ) ? $meta['slug'] : '',
			'context' => isset( $meta['context'] ) ? mb_substr( (string) $meta['context'], 0, 300 ) : '',
			'count'   => 1,
		);

		/* The same broken formula reports on every keystroke, so identical
		   consecutive errors are counted rather than repeated. Without this
		   one bad calculator would fill the log within a minute. */
		foreach ( $log as $i => $existing ) {
			if ( $existing['message'] === $entry['message'] && $existing['slug'] === $entry['slug'] ) {
				$log[ $i ]['count'] = (int) $existing['count'] + 1;
				$log[ $i ]['time']  = $entry['time'];
				$this->store( $log );
				return;
			}
		}

		array_unshift( $log, $entry );
		$this->store( $log );
	}

	public function entries() {
		$log = get_option( self::OPTION, array() );
		return is_array( $log ) ? $log : array();
	}

	private function store( $log ) {
		$limit = (int) Calculatorr_Settings::instance()->get( 'log_limit' );
		update_option( self::OPTION, array_slice( $log, 0, $limit ), false );
	}

	public function clear() {
		update_option( self::OPTION, array(), false );
	}

	public function count() {
		return count( $this->entries() );
	}

	/**
	 * How many distinct calculators are currently reporting problems, which is
	 * the figure that actually tells you whether something is wrong.
	 */
	public function affected_slugs() {
		$slugs = array();

		foreach ( $this->entries() as $entry ) {
			if ( $entry['slug'] ) {
				$slugs[ $entry['slug'] ] = true;
			}
		}

		return array_keys( $slugs );
	}
}
