<?php
/**
 * Maps calculators onto real WordPress pages at keyword-led URLs.
 *
 * Each category is a top level page whose slug is the phrase people actually
 * search for, such as construction-calculators-online, and each calculator is a
 * child of it at its own exact-match slug. The nesting means the hierarchy does
 * the URL work, so no custom rewrite rules are needed and nothing fights the
 * rest of the site.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Calculatorr_Pages {

	/** @var array<string,string> Resolved permalinks, keyed by calculator slug. */
	private static $url_cache = array();

	public static function on_activate() {
		self::sync();
		flush_rewrite_rules();
	}

	/**
	 * Creates any missing category or calculator page and never edits one that
	 * already exists, because overwriting a page somebody has tuned by hand
	 * would punish exactly the person who took the trouble.
	 *
	 * @return array{created_categories:int,created_calculators:int,skipped:int}
	 */
	public static function sync() {
		$registry = Calculatorr_Registry::instance();
		$report   = array(
			'created_categories'  => 0,
			'created_calculators' => 0,
			'skipped'             => 0,
		);

		$parents = array();

		foreach ( $registry->categories() as $key => $category ) {
			$existing = get_page_by_path( $category['slug'], OBJECT, 'page' );

			if ( $existing ) {
				$parents[ $key ] = $existing->ID;
				update_post_meta( $existing->ID, '_calculatorr_category', $key );
				continue;
			}

			$parent_id = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_title'   => $category['h1'],
					'post_name'    => $category['slug'],
					'post_content' => '[calculatorr_category key="' . $key . '"]',
				)
			);

			if ( ! is_wp_error( $parent_id ) ) {
				$parents[ $key ] = $parent_id;
				update_post_meta( $parent_id, '_calculatorr_category', $key );
				$report['created_categories']++;
			}
		}

		foreach ( $registry->all() as $slug => $config ) {
			$category = $config['category'];

			if ( ! isset( $parents[ $category ] ) ) {
				$report['skipped']++;
				continue;
			}

			if ( get_page_by_path( self::path_for( $config ), OBJECT, 'page' ) ) {
				$report['skipped']++;
				continue;
			}

			$page_id = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_parent'  => $parents[ $category ],
					'post_title'   => $config['h1'],
					'post_name'    => $slug,
					'post_content' => '[calculatorr slug="' . $slug . '"]',
				)
			);

			if ( ! is_wp_error( $page_id ) ) {
				update_post_meta( $page_id, '_calculatorr_slug', $slug );
				$report['created_calculators']++;
			}
		}

		return $report;
	}

	/**
	 * The path a calculator lives at, relative to the site root and without
	 * surrounding slashes: <category-slug>/<calculator-slug>.
	 */
	public static function path_for( $config ) {
		$category = Calculatorr_Registry::instance()->category( $config['category'] );
		$prefix   = $category ? $category['slug'] : $config['category'];

		return $prefix . '/' . $config['slug'];
	}

	/**
	 * The permalink for one calculator, falling back to the constructed path
	 * when its page has not been created yet, so related links never render as
	 * dead anchors during a partial build.
	 */
	public static function url_for( $config ) {
		$slug = $config['slug'];

		if ( isset( self::$url_cache[ $slug ] ) ) {
			return self::$url_cache[ $slug ];
		}

		$path = self::path_for( $config );
		$page = get_page_by_path( $path, OBJECT, 'page' );
		$url  = $page ? get_permalink( $page ) : home_url( '/' . $path . '/' );

		self::$url_cache[ $slug ] = $url;

		return $url;
	}

	public static function url_for_category( $category ) {
		return home_url( '/' . $category['slug'] . '/' );
	}

	/**
	 * The calculator shown on the current page, or null when there is not one.
	 */
	public static function current() {
		if ( ! is_page() ) {
			return null;
		}

		$slug = get_post_meta( get_the_ID(), '_calculatorr_slug', true );

		return $slug ? Calculatorr_Registry::instance()->get( $slug ) : null;
	}

	/**
	 * The category hub the current page represents, with its key folded in so
	 * callers can look up its calculators without a second query.
	 */
	public static function current_category() {
		if ( ! is_page() ) {
			return null;
		}

		$key = get_post_meta( get_the_ID(), '_calculatorr_category', true );

		if ( ! $key ) {
			return null;
		}

		$category = Calculatorr_Registry::instance()->category( $key );

		return $category ? array( 'key' => $key ) + $category : null;
	}
}
