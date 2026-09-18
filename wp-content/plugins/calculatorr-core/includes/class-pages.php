<?php
/**
 * Maps calculators onto real WordPress pages.
 *
 * Each category is a top level page and each calculator is a child of it, so
 * /health/bmi-calculator/ falls out of ordinary page hierarchy instead of
 * needing custom rewrite rules that fight the rest of the site. The pages hold
 * nothing but the shortcode, which keeps the content in config where it can be
 * edited once and applied everywhere.
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
	 * Creates any missing category or calculator page and leaves existing ones
	 * alone, because overwriting a page someone has edited by hand would punish
	 * exactly the person who took the trouble.
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

		foreach ( $registry->categories() as $slug => $category ) {
			$existing = get_page_by_path( $slug, OBJECT, 'page' );

			if ( $existing ) {
				$parents[ $slug ] = $existing->ID;
				continue;
			}

			$parent_id = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_title'   => $category['name'] . ' Calculators',
					'post_name'    => $slug,
					'post_content' => '',
				)
			);

			if ( ! is_wp_error( $parent_id ) ) {
				$parents[ $slug ]  = $parent_id;
				$report['created_categories']++;
			}
		}

		foreach ( $registry->all() as $slug => $config ) {
			$category = $config['category'];

			if ( ! isset( $parents[ $category ] ) ) {
				$report['skipped']++;
				continue;
			}

			if ( get_page_by_path( $category . '/' . $slug, OBJECT, 'page' ) ) {
				$report['skipped']++;
				continue;
			}

			$page_id = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_parent'  => $parents[ $category ],
					'post_title'   => $config['title'],
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
	 * The permalink for one calculator, falling back to the constructed path
	 * when the page has not been created yet so that related links never render
	 * as dead anchors during a partial build.
	 */
	public static function url_for( $config ) {
		$slug = $config['slug'];

		if ( isset( self::$url_cache[ $slug ] ) ) {
			return self::$url_cache[ $slug ];
		}

		$path = $config['category'] . '/' . $slug;
		$page = get_page_by_path( $path, OBJECT, 'page' );
		$url  = $page ? get_permalink( $page ) : home_url( '/' . $path . '/' );

		self::$url_cache[ $slug ] = $url;

		return $url;
	}

	/**
	 * The calculator shown on the current page, or null when there is not one.
	 */
	public static function current() {
		if ( ! is_page() ) {
			return null;
		}

		$slug = get_post_meta( get_the_ID(), '_calculatorr_slug', true );

		if ( ! $slug ) {
			return null;
		}

		return Calculatorr_Registry::instance()->get( $slug );
	}
}
