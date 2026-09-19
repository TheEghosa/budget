<?php
/**
 * Counts what actually gets used.
 *
 * A page view is not usage: somebody can land on the mortgage page from a
 * search, read the explainer and leave without touching a field. What this
 * records is a calculation being run, once per calculator per page load, which
 * is the number that answers "which of these hundred and five tools are worth
 * the next hour of work".
 *
 * It is kept in its own table rather than in an option. A day of traffic writes
 * one row per calculator and each hit is a single small update, where an option
 * would mean reading, unserialising, rewriting and saving the whole history on
 * every calculation. The table is small by design: one row per calculator per
 * day, pruned to a rolling window.
 *
 * This is a house counter, not analytics. It cannot tell you where a visitor
 * came from, it does not set a cookie, and a determined person could inflate
 * it. Treat it as a ranking of your own tools, which is what it is for.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Calculatorr_Usage {

	const WINDOW = 90;

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

	public static function table() {
		global $wpdb;

		return isset( $wpdb ) ? $wpdb->prefix . 'calculatorr_usage' : '';
	}

	/** Whether there is a database to talk to at all. */
	public static function available() {
		global $wpdb;

		return isset( $wpdb ) && is_object( $wpdb );
	}

	/**
	 * Creates or updates the table. Safe to call repeatedly, which is why it
	 * runs on activation and again the first time the admin screen loads: an
	 * install that upgraded rather than activated would otherwise never get it.
	 */
	public static function install() {
		global $wpdb;

		if ( ! self::available() ) {
			return false;
		}

		$table = self::table();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		/* day plus slug is the primary key, so a hit is one upsert and the
		   table can never grow a second row for the same calculator and day. */
		$sql = "CREATE TABLE {$table} (
			day date NOT NULL,
			slug varchar(64) NOT NULL,
			hits bigint(20) unsigned NOT NULL DEFAULT 0,
			PRIMARY KEY  (day,slug),
			KEY slug (slug)
		) " . $wpdb->get_charset_collate() . ';';

		dbDelta( $sql );

		return true;
	}

	public static function table_exists() {
		global $wpdb;

		if ( ! self::available() ) {
			return false;
		}

		$table = self::table();

		return (bool) $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) );
	}

	public function register() {
		register_rest_route(
			'calculatorr/v1',
			'/usage',
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'record' ),
				/*
				 * Open on purpose: the visitors being counted are never logged
				 * in, and a cached page cannot carry a fresh nonce. What keeps
				 * it safe is that it writes nothing but a counter, and only
				 * against a slug the registry already knows, so it cannot be
				 * used to create rows or store anything of its own.
				 */
				'permission_callback' => '__return_true',
				'args' => array(
					'slug' => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_key',
					),
				),
			)
		);
	}

	public function record( WP_REST_Request $request ) {
		if ( ! Calculatorr_Settings::instance()->get( 'usage_enabled' ) ) {
			return rest_ensure_response( array( 'recorded' => false, 'reason' => 'disabled' ) );
		}

		$slug = (string) $request->get_param( 'slug' );

		if ( ! Calculatorr_Registry::instance()->get( $slug ) ) {
			return rest_ensure_response( array( 'recorded' => false, 'reason' => 'unknown' ) );
		}

		return rest_ensure_response( array( 'recorded' => self::add( $slug ) ) );
	}

	public static function add( $slug, $day = null, $by = 1 ) {
		global $wpdb;

		if ( ! self::available() ) {
			return false;
		}

		$table = self::table();
		$day   = $day ? $day : current_time( 'Y-m-d' );

		$sql = $wpdb->prepare(
			"INSERT INTO {$table} (day, slug, hits) VALUES (%s, %s, %d)
			 ON DUPLICATE KEY UPDATE hits = hits + %d",
			$day,
			$slug,
			$by,
			$by
		);

		return false !== $wpdb->query( $sql );
	}

	/**
	 * Rows older than the window are dropped. Kept deliberately short, because
	 * the question this answers is which tools are earning their place now, and
	 * three months of that is plenty to see a trend.
	 */
	public static function prune() {
		global $wpdb;

		if ( ! self::available() ) {
			return 0;
		}

		$table  = self::table();
		$cutoff = gmdate( 'Y-m-d', time() - ( self::WINDOW * DAY_IN_SECONDS ) );

		return (int) $wpdb->query( $wpdb->prepare( "DELETE FROM {$table} WHERE day < %s", $cutoff ) );
	}

	/**
	 * Daily totals across every calculator, as an ordered day => hits map with
	 * the quiet days present as zero rather than missing, because a line with
	 * gaps in it reads as a drop that never happened.
	 */
	public static function daily( $days = 30 ) {
		global $wpdb;

		$series = self::empty_days( $days );

		if ( ! self::available() ) {
			return $series;
		}

		$table = self::table();
		$from  = array_key_first( $series );

		$rows = $wpdb->get_results(
			$wpdb->prepare( "SELECT day, SUM(hits) AS hits FROM {$table} WHERE day >= %s GROUP BY day", $from ),
			ARRAY_A
		);

		foreach ( (array) $rows as $row ) {
			if ( isset( $series[ $row['day'] ] ) ) {
				$series[ $row['day'] ] = (int) $row['hits'];
			}
		}

		return $series;
	}

	/** Totals per calculator over the window, highest first. */
	public static function by_slug( $days = 30 ) {
		global $wpdb;

		if ( ! self::available() ) {
			return array();
		}

		$table = self::table();
		$from  = gmdate( 'Y-m-d', time() - ( ( $days - 1 ) * DAY_IN_SECONDS ) );

		$rows = $wpdb->get_results(
			$wpdb->prepare( "SELECT slug, SUM(hits) AS hits FROM {$table} WHERE day >= %s GROUP BY slug ORDER BY hits DESC", $from ),
			ARRAY_A
		);

		$out = array();

		foreach ( (array) $rows as $row ) {
			$out[ $row['slug'] ] = (int) $row['hits'];
		}

		return $out;
	}

	/** A day => hits map per calculator, for the sparklines in the table. */
	public static function sparklines( $days = 30 ) {
		global $wpdb;

		if ( ! self::available() ) {
			return array();
		}

		$table = self::table();
		$blank = self::empty_days( $days );
		$from  = array_key_first( $blank );

		$rows = $wpdb->get_results(
			$wpdb->prepare( "SELECT day, slug, hits FROM {$table} WHERE day >= %s", $from ),
			ARRAY_A
		);

		$out = array();

		foreach ( (array) $rows as $row ) {
			if ( ! isset( $out[ $row['slug'] ] ) ) {
				$out[ $row['slug'] ] = $blank;
			}

			if ( isset( $out[ $row['slug'] ][ $row['day'] ] ) ) {
				$out[ $row['slug'] ][ $row['day'] ] = (int) $row['hits'];
			}
		}

		return $out;
	}

	/**
	 * Comparison of the last N days against the N before them, which is the
	 * honest way to say "up" or "down" without reading a single day as a trend.
	 */
	public static function trend( $days = 7 ) {
		$series = array_values( self::daily( $days * 2 ) );

		if ( count( $series ) < $days * 2 ) {
			return array( 'now' => 0, 'before' => 0, 'change' => null );
		}

		$before = array_sum( array_slice( $series, 0, $days ) );
		$now    = array_sum( array_slice( $series, $days ) );

		return array(
			'now'    => $now,
			'before' => $before,
			/* No baseline means no percentage. Reporting a jump from nothing as
			   an infinite rise is how dashboards start lying. */
			'change' => $before > 0 ? ( ( $now - $before ) / $before ) * 100 : null,
		);
	}

	public static function empty_days( $days ) {
		$out = array();

		for ( $i = $days - 1; $i >= 0; $i-- ) {
			$out[ gmdate( 'Y-m-d', time() - ( $i * DAY_IN_SECONDS ) ) ] = 0;
		}

		return $out;
	}
}
