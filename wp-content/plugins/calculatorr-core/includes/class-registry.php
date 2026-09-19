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
		$this->categories = self::default_categories();
		$this->load_calculators();
	}

	/**
	 * The ten use cases. Order here is the order they appear in navigation and
	 * on the homepage grid, so it is deliberate rather than alphabetical.
	 */
	private static function default_categories() {
		$categories = array(
			'finance'   => array(
				'name'  => 'Finance & Retirement',
				'slug'  => 'financial-calculators-online',
				'h1'    => 'Financial Calculators',
				'blurb' => 'Saving, investing, pensions and pay',
				'meta_title'       => 'Financial Calculators Online - Free Money & Pay Tools',
				'meta_description' => 'Free online financial calculators for pay, savings, retirement and investing. Work out take-home pay, 401(k) growth, dividends and future value in seconds.',
			),
			'loans'     => array(
				'name'  => 'Loans & Debt',
				'slug'  => 'loan-calculators-online',
				'h1'    => 'Loan Calculators',
				'blurb' => 'Repayments, amortisation and payoff',
				'meta_title'       => 'Loan Calculators Online - Free Repayment & Payoff Tools',
				'meta_description' => 'Free online loan calculators for amortisation, mortgage payoff, HELOC, personal and boat loans. See repayments, interest and payoff dates instantly.',
			),
			'health'    => array(
				'name'  => 'Health & Body',
				'slug'  => 'health-calculators-online',
				'h1'    => 'Health & Body Calculators',
				'blurb' => 'Body composition, fertility, training and intake',
				'meta_title'       => 'Health Calculators Online - Free Body & Fitness Tools',
				'meta_description' => 'Free online health calculators for body fat, macros, water intake, ovulation, BAC and one rep max. Every formula is named so you can check the working.',
			),
			'math'      => array(
				'name'  => 'Math & Numbers',
				'slug'  => 'math-calculators-online',
				'h1'    => 'Math Calculators',
				'blurb' => 'Percentages, fractions, algebra, calculus and statistics',
				'meta_title'       => 'Math Calculators Online - Free Step-by-Step Solvers',
				'meta_description' => 'Free online math calculators for percentages, fractions, ratios, algebra, calculus and statistics. Each one shows the working, not just the answer.',
			),
			'geometry'  => array(
				'name'  => 'Geometry & Shapes',
				'slug'  => 'geometry-calculators-online',
				'h1'    => 'Geometry Calculators',
				'blurb' => 'Area, volume, triangles and distance',
				'meta_title'       => 'Geometry Calculators Online - Area, Volume & Triangles',
				'meta_description' => 'Free online geometry calculators for area, volume, circumference, triangles and distance. Enter your measurements and get the formula alongside the answer.',
			),
			'convert'   => array(
				'name'  => 'Unit Conversion',
				'slug'  => 'unit-conversion-calculators-online',
				'h1'    => 'Unit Conversion Calculators',
				'blurb' => 'Length, weight, temperature and sizing',
				'meta_title'       => 'Unit Conversion Calculators - Free Online Converters',
				'meta_description' => 'Free online unit converters for length, weight, temperature and sizing. Convert mm to inches, feet to meters, Celsius to Fahrenheit and more, instantly.',
			),
			'business'  => array(
				'name'  => 'Business & Shopping',
				'slug'  => 'business-calculators-online',
				'h1'    => 'Business & Shopping Calculators',
				'blurb' => 'Margin, markup, tax, fees and everyday retail maths',
				'meta_title'       => 'Business Calculators Online - Margin, Markup & Tax',
				'meta_description' => 'Free online business calculators for profit margin, markup, sales tax, VAT, GST, eBay fees and CPM. Built for sellers and shoppers alike.',
			),
			'home-diy'  => array(
				'name'  => 'Home & DIY',
				'slug'  => 'construction-calculators-online',
				'h1'    => 'Construction & DIY Calculators',
				'blurb' => 'Materials estimating for building, landscaping and decorating',
				'meta_title'       => 'Construction Calculators Online - Free Material Estimators',
				'meta_description' => 'Free online construction calculators for concrete, gravel, mulch, tile, paint, decking and stairs. Work out how much material to order before you buy.',
			),
			'time'      => array(
				'name'  => 'Date, Time & Work',
				'slug'  => 'time-and-date-calculators-online',
				'h1'    => 'Time & Date Calculators',
				'blurb' => 'Dates, durations, shift hours and paid time',
				'meta_title'       => 'Time & Date Calculators Online - Hours, Duration, Payroll',
				'meta_description' => 'Free online time and date calculators for hours worked, overtime, time cards, business days, durations and military time. Built for payroll and planning.',
			),
			'education' => array(
				'name'  => 'School & Study',
				'slug'  => 'grade-calculators-online',
				'h1'    => 'Grade & Study Calculators',
				'blurb' => 'Grades, scores and coursework',
				'meta_title'       => 'Grade Calculators Online - GPA, AP Score & Coursework',
				'meta_description' => 'Free online grade calculators for GPA, cumulative GPA, AP scores and coursework chemistry. Weighted by credits, with the scale shown on every page.',
			),
		);

		return apply_filters( 'calculatorr_categories', $categories );
	}

	/**
	 * The category keys, without needing a registry.
	 *
	 * The JSON definitions are validated while the registry is still inside
	 * its own constructor, so anything they need has to be reachable without
	 * asking for the instance that does not exist yet.
	 */
	public static function category_keys() {
		return array_keys( self::default_categories() );
	}

	/**
	 * Category lookup by its public URL slug, which is what the rewrite and the
	 * breadcrumb both resolve against.
	 */
	public function category_by_slug( $slug ) {
		foreach ( $this->categories as $key => $category ) {
			if ( $category['slug'] === $slug ) {
				return array( 'key' => $key ) + $category;
			}
		}

		return null;
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

		/* Default results live in one generated file rather than inside each
		   config, because they are computed from the formulas by the build
		   step. Keeping them together is what stops the server-rendered answer
		   drifting away from the one JavaScript produces, which happened once
		   when a rounding bug was fixed in the formulas alone. */
		$defaults_file = CALCULATORR_PATH . 'calculators/_defaults.php';
		$defaults = file_exists( $defaults_file ) ? include $defaults_file : array();

		foreach ( $files as $file ) {
			/* Files beginning with an underscore are build output, not
			   calculators. */
			if ( '_' === basename( $file )[0] ) {
				continue;
			}

			$config = include $file;

			if ( ! is_array( $config ) || empty( $config['slug'] ) ) {
				continue;
			}

			$this->adopt( $config, $defaults );
		}

		/*
		 * JSON definitions come in behind the PHP files and skip any slug a
		 * file already holds, which is how a calculator published to the
		 * database today becomes an ordinary shipped file on the next build
		 * without anybody having to remember to delete the database copy.
		 *
		 * They go through exactly the same defaults and the same admin
		 * overrides below, so by the time anything else in the plugin sees a
		 * config there is nothing left to tell the two apart.
		 */
		if ( class_exists( 'Calculatorr_Json_Calculators' ) ) {
			foreach ( Calculatorr_Json_Calculators::instance()->all() as $slug => $definition ) {
				if ( isset( $this->calculators[ $slug ] ) ) {
					continue;
				}

				$this->adopt( $definition, $defaults );
			}
		}

		ksort( $this->calculators );
	}

	/**
	 * Fills one config out and files it, whichever source it arrived from.
	 */
	private function adopt( $config, $defaults ) {
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
				'meta_title'       => '',
				'meta_description' => '',
				'h1'               => '',
				'keyword'          => '',
				'sources'          => array(),
			)
		);

		/* The exact keyword drives the H1 and the title tag, so it is
		   derived from the title once here rather than repeated in every
		   config file and eventually falling out of step with it. */
		if ( '' === $config['keyword'] ) {
			$config['keyword'] = $config['title'];
		}
		if ( '' === $config['h1'] ) {
			$config['h1'] = $config['title'];
		}
		if ( '' === $config['meta_title'] ) {
			$config['meta_title'] = $config['title'] . ' - Free Online Tool';
		}
		if ( '' === $config['meta_description'] ) {
			$config['meta_description'] = $config['description'];
		}

		if ( isset( $defaults[ $config['slug'] ] ) ) {
			$config['default_result'] = $defaults[ $config['slug'] ];
		}

		/* Admin overrides are applied here so nothing downstream has to
		   know they exist. */
		if ( class_exists( 'Calculatorr_Settings' ) ) {
			$config = Calculatorr_Settings::instance()->apply( $config );
		}

		$this->calculators[ $config['slug'] ] = $config;
	}

	/**
	 * @param bool $include_disabled Admin screens want everything. The front
	 *                               end wants only what is switched on.
	 */
	public function all( $include_disabled = true ) {
		if ( $include_disabled || ! class_exists( 'Calculatorr_Settings' ) ) {
			return $this->calculators;
		}

		$settings = Calculatorr_Settings::instance();

		return array_filter( $this->calculators, function ( $config ) use ( $settings ) {
			return ! $settings->is_disabled( $config['slug'] );
		} );
	}

	public function get( $slug ) {
		return isset( $this->calculators[ $slug ] ) ? $this->calculators[ $slug ] : null;
	}

	/**
	 * Rebuilds the merged configs.
	 *
	 * Overrides are folded in when the registry loads, which means anything
	 * already holding a config keeps the old text after a save. The admin gets
	 * away with it because it redirects, but a save followed by a read in the
	 * same request would otherwise return the previous copy, so saving an
	 * override calls this.
	 */
	public function reload() {
		$this->calculators = array();
		$this->load_calculators();
	}

	/**
	 * Null for a calculator an administrator has switched off, so the renderer
	 * shows nothing rather than a half-working tool.
	 */
	public function get_live( $slug ) {
		if ( class_exists( 'Calculatorr_Settings' ) && Calculatorr_Settings::instance()->is_disabled( $slug ) ) {
			return null;
		}

		return $this->get( $slug );
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
	public function in_category( $category, $include_disabled = true ) {
		$matches = array();

		foreach ( $this->all( $include_disabled ) as $slug => $config ) {
			if ( $config['category'] === $category ) {
				$matches[ $slug ] = $config;
			}
		}

		return $matches;
	}
}
