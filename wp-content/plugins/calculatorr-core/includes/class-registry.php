<?php
/**
 * Loads and serves the calculator configs.
 *
 * Every calculator on the site is a single PHP file under calculators/ that
 * returns an array. Nothing else in the plugin needs to change when a new one
 * is added, which is the whole point of keeping the registry this thin.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Calculatorr_Registry {

	private static $instance = null;

	/** @var array<string,array> Config arrays keyed by slug. */
	private $calculators = array();

	/** @var array<string,array> Category metadata keyed by category slug. */
	private $categories = array();

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->categories = $this->default_categories();
		$this->load_calculators();
	}

	/**
	 * The ten use cases. Order here is the order they appear in navigation and
	 * on the homepage grid, so it is deliberate rather than alphabetical.
	 */
	private function default_categories() {
		$categories = array(
			'finance'   => array( 'name' => 'Finance & Money',      'blurb' => 'Savings, interest, retirement and take-home pay' ),
			'loans'     => array( 'name' => 'Loans & Mortgages',    'blurb' => 'Repayments, affordability and payoff plans' ),
			'health'    => array( 'name' => 'Health & Fitness',     'blurb' => 'BMI, calories, macros and training zones' ),
			'math'      => array( 'name' => 'Math & Algebra',       'blurb' => 'Percentages, fractions, averages and solvers' ),
			'geometry'  => array( 'name' => 'Geometry & Shapes',    'blurb' => 'Area, volume, triangles and surface area' ),
			'convert'   => array( 'name' => 'Unit Conversion',      'blurb' => 'Length, weight, temperature and cooking' ),
			'business'  => array( 'name' => 'Business & Sales',     'blurb' => 'Margin, markup, break-even, tax and tips' ),
			'home-diy'  => array( 'name' => 'Home & DIY',           'blurb' => 'Paint, flooring, concrete, roofing and fencing' ),
			'time'      => array( 'name' => 'Date, Time & Age',     'blurb' => 'Age, durations, business days and time zones' ),
			'education' => array( 'name' => 'Education',            'blurb' => 'GPA, grades, reading time and word counts' ),
		);

		return apply_filters( 'calculatorr_categories', $categories );
	}

	/**
	 * Reads every config file once per request. The glob is cheap and the files
	 * are small, so caching it would add a stale-data problem without buying
	 * anything measurable.
	 */
	private function load_calculators() {
		$files = glob( CALCULATORR_PATH . 'calculators/*.php' );

		if ( empty( $files ) ) {
			return;
		}

		foreach ( $files as $file ) {
			$config = include $file;

			if ( ! is_array( $config ) || empty( $config['slug'] ) ) {
				continue;
			}

			$config = wp_parse_args(
				$config,
				array(
					'category'    => 'math',
					'title'       => '',
					'description' => '',
					'fields'      => array(),
					'explainer'   => array(),
					'faqs'        => array(),
					'related'     => array(),
					'disclaimer'  => '',
				)
			);

			$this->calculators[ $config['slug'] ] = $config;
		}
	}

	public function all() {
		return $this->calculators;
	}

	public function get( $slug ) {
		return isset( $this->calculators[ $slug ] ) ? $this->calculators[ $slug ] : null;
	}

	public function categories() {
		return $this->categories;
	}

	public function category( $slug ) {
		return isset( $this->categories[ $slug ] ) ? $this->categories[ $slug ] : null;
	}

	/**
	 * @return array<string,array> Calculators belonging to one category.
	 */
	public function in_category( $category ) {
		$matches = array();

		foreach ( $this->calculators as $slug => $config ) {
			if ( $config['category'] === $category ) {
				$matches[ $slug ] = $config;
			}
		}

		return $matches;
	}
}
