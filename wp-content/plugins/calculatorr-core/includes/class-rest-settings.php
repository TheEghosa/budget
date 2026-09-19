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

		/*
		 * Content, separately from settings.
		 *
		 * The explainer and the questions are already overridable, but the
		 * overrides live in their own option rather than in the settings
		 * array, so the settings route cannot reach them and rewriting a page
		 * meant shipping a new copy of the plugin. That is fine for one page
		 * and hopeless for a hundred, which is most of what this site needs.
		 *
		 * It is a separate route rather than a new settings key because the
		 * shape is different: settings are a flat list of known keys, and this
		 * is per calculator, nested, and validated against the schema the
		 * renderer actually reads.
		 */
		register_rest_route(
			'calculatorr/v1',
			'/content/(?P<slug>[a-z0-9-]+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'read_content' ),
					'permission_callback' => array( $this, 'may_manage' ),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'write_content' ),
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

	/**
	 * The long-form content for one calculator, as it currently stands after
	 * any override has been folded in.
	 */
	public function read_content( WP_REST_Request $request ) {
		$slug   = (string) $request->get_param( 'slug' );
		$config = Calculatorr_Registry::instance()->get( $slug );

		if ( ! $config ) {
			return new WP_Error( 'calculatorr_unknown', 'No calculator with that slug.', array( 'status' => 404 ) );
		}

		return rest_ensure_response(
			array(
				'slug'      => $slug,
				'explainer' => isset( $config['explainer'] ) ? $config['explainer'] : array(),
				'faqs'      => isset( $config['faqs'] ) ? $config['faqs'] : array(),
				'words'     => self::count_words( $config ),
			)
		);
	}

	/**
	 * Replaces the explainer, the questions, or both.
	 *
	 * Everything is validated against the shape the renderer reads rather than
	 * stored as sent, because this is the one route that writes text straight
	 * onto a hundred and five public pages. A section with a heading nobody
	 * wrote or a table whose rows are a different length from its header is a
	 * broken page, and it is better to refuse it here than to render it.
	 */
	public function write_content( WP_REST_Request $request ) {
		$slug   = (string) $request->get_param( 'slug' );
		$config = Calculatorr_Registry::instance()->get( $slug );

		if ( ! $config ) {
			return new WP_Error( 'calculatorr_unknown', 'No calculator with that slug.', array( 'status' => 404 ) );
		}

		$body = $request->get_json_params();

		if ( ! is_array( $body ) ) {
			return new WP_Error( 'calculatorr_bad_body', 'Expected a JSON object.', array( 'status' => 400 ) );
		}

		$unknown = array_diff( array_keys( $body ), array( 'explainer', 'faqs' ) );

		if ( $unknown ) {
			return new WP_Error(
				'calculatorr_unknown_key',
				'This route writes explainer and faqs only. Unexpected: ' . implode( ', ', $unknown ),
				array( 'status' => 400 )
			);
		}

		$override = array();

		if ( isset( $body['explainer'] ) ) {
			$clean = self::clean_explainer( $body['explainer'] );

			if ( is_wp_error( $clean ) ) {
				return $clean;
			}

			$override['explainer'] = $clean;
		}

		if ( isset( $body['faqs'] ) ) {
			$clean = self::clean_faqs( $body['faqs'] );

			if ( is_wp_error( $clean ) ) {
				return $clean;
			}

			$override['faqs'] = $clean;
		}

		if ( ! $override ) {
			return new WP_Error( 'calculatorr_empty', 'Nothing to write.', array( 'status' => 400 ) );
		}

		/* Merged rather than replaced, so sending only the questions does not
		   wipe the explainer that took an afternoon to write. */
		$existing = Calculatorr_Settings::instance()->overrides( $slug );

		Calculatorr_Settings::instance()->save_override( $slug, array_merge( $existing, $override ) );

		$fresh = Calculatorr_Registry::instance()->get( $slug );

		return rest_ensure_response(
			array(
				'slug'     => $slug,
				'sections' => count( isset( $fresh['explainer'] ) ? $fresh['explainer'] : array() ),
				'faqs'     => count( isset( $fresh['faqs'] ) ? $fresh['faqs'] : array() ),
				'words'    => self::count_words( $fresh ),
			)
		);
	}

	private static function count_words( $config ) {
		$text = '';

		foreach ( (array) ( isset( $config['explainer'] ) ? $config['explainer'] : array() ) as $section ) {
			$text .= ' ' . ( isset( $section['body'] ) ? $section['body'] : '' );
			$text .= ' ' . ( isset( $section['example'] ) ? $section['example'] : '' );

			foreach ( (array) ( isset( $section['steps'] ) ? $section['steps'] : array() ) as $step ) {
				$text .= ' ' . $step;
			}
		}

		return str_word_count( wp_strip_all_tags( $text ) );
	}

	/**
	 * A section keeps a heading, a body, and whichever of the extras it
	 * supplied. Anything else is dropped rather than stored, because an
	 * unrecognised key is either a typo or a guess about a feature that does
	 * not exist, and neither should reach the page.
	 */
	private static function clean_explainer( $sections ) {
		if ( ! is_array( $sections ) ) {
			return new WP_Error( 'calculatorr_bad_explainer', 'explainer must be a list of sections.', array( 'status' => 400 ) );
		}

		$out = array();

		foreach ( $sections as $i => $section ) {
			if ( ! is_array( $section ) || empty( $section['heading'] ) || empty( $section['body'] ) ) {
				return new WP_Error(
					'calculatorr_bad_section',
					sprintf( 'Section %d needs a heading and a body.', (int) $i + 1 ),
					array( 'status' => 400 )
				);
			}

			$clean = array(
				'heading' => sanitize_text_field( $section['heading'] ),
				'body'    => wp_kses_post( $section['body'] ),
			);

			foreach ( array( 'formula', 'example' ) as $key ) {
				if ( ! empty( $section[ $key ] ) ) {
					$clean[ $key ] = wp_kses_post( $section[ $key ] );
				}
			}

			if ( ! empty( $section['steps'] ) && is_array( $section['steps'] ) ) {
				$clean['steps'] = array_values( array_filter( array_map( 'wp_kses_post', $section['steps'] ) ) );
			}

			if ( ! empty( $section['table'] ) ) {
				$table = self::clean_table( $section['table'], (int) $i + 1 );

				if ( is_wp_error( $table ) ) {
					return $table;
				}

				$clean['table'] = $table;
			}

			$out[] = $clean;
		}

		return $out;
	}

	/**
	 * Every row has to be as wide as the header. A short row renders as a
	 * table with a hole in it and a long one silently loses its last cell,
	 * and both are the sort of thing nobody notices until somebody reads the
	 * page.
	 */
	private static function clean_table( $table, $where ) {
		if ( ! is_array( $table ) || empty( $table['head'] ) || empty( $table['rows'] ) ) {
			return new WP_Error(
				'calculatorr_bad_table',
				sprintf( 'The table in section %d needs a head and some rows.', $where ),
				array( 'status' => 400 )
			);
		}

		$head  = array_values( array_map( 'sanitize_text_field', (array) $table['head'] ) );
		$width = count( $head );
		$rows  = array();

		foreach ( (array) $table['rows'] as $n => $row ) {
			$cells = array_values( array_map( 'wp_kses_post', (array) $row ) );

			if ( count( $cells ) !== $width ) {
				return new WP_Error(
					'calculatorr_ragged_table',
					sprintf(
						'Section %d, row %d has %d cells but the header has %d.',
						$where,
						(int) $n + 1,
						count( $cells ),
						$width
					),
					array( 'status' => 400 )
				);
			}

			$rows[] = $cells;
		}

		$clean = array( 'head' => $head, 'rows' => $rows );

		if ( ! empty( $table['caption'] ) ) {
			$clean['caption'] = sanitize_text_field( $table['caption'] );
		}

		return $clean;
	}

	private static function clean_faqs( $faqs ) {
		if ( ! is_array( $faqs ) ) {
			return new WP_Error( 'calculatorr_bad_faqs', 'faqs must be a list.', array( 'status' => 400 ) );
		}

		$out = array();

		foreach ( $faqs as $i => $faq ) {
			if ( ! is_array( $faq ) || empty( $faq['q'] ) || empty( $faq['a'] ) ) {
				return new WP_Error(
					'calculatorr_bad_faq',
					sprintf( 'Question %d needs a q and an a.', (int) $i + 1 ),
					array( 'status' => 400 )
				);
			}

			$out[] = array(
				'q' => sanitize_text_field( $faq['q'] ),
				'a' => wp_kses_post( $faq['a'] ),
			);
		}

		return $out;
	}
}
