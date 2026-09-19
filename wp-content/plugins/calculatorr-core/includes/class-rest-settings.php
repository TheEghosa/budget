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

		/*
		 * A whole calculator, not just its copy.
		 *
		 * The content route above can rewrite the prose on a page that already
		 * exists. This one creates the page: fields, formula, explainer,
		 * questions and all. It is the difference between editing the site and
		 * extending it, and it is what takes a new calculator off the path of
		 * building a zip, uploading it and reactivating the plugin.
		 *
		 * The formula is stored as written and never runs on the server. It
		 * runs in the browser inside the sandbox worker, which has no DOM and
		 * no network, so what reaches a visitor is a function that can do
		 * arithmetic and nothing else.
		 */
		register_rest_route(
			'calculatorr/v1',
			'/calculator/(?P<slug>[a-z0-9-]+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'read_calculator' ),
					'permission_callback' => array( $this, 'may_manage' ),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'write_calculator' ),
					'permission_callback' => array( $this, 'may_manage' ),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_calculator' ),
					'permission_callback' => array( $this, 'may_manage' ),
				),
			)
		);

		register_rest_route(
			'calculatorr/v1',
			'/calculators',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'list_calculators' ),
				'permission_callback' => array( $this, 'may_manage' ),
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
				'words'     => Calculatorr_Content::count_words( $config ),
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
			$clean = Calculatorr_Content::clean_explainer( $body['explainer'] );

			if ( is_wp_error( $clean ) ) {
				return $clean;
			}

			$override['explainer'] = $clean;
		}

		if ( isset( $body['faqs'] ) ) {
			$clean = Calculatorr_Content::clean_faqs( $body['faqs'] );

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
				'words'    => Calculatorr_Content::count_words( $fresh ),
			)
		);
	}

	/* ---------- Whole calculators ---------- */

	public function read_calculator( WP_REST_Request $request ) {
		$slug  = (string) $request->get_param( 'slug' );
		$store = Calculatorr_Json_Calculators::instance();
		$definition = $store->get( $slug );

		if ( ! $definition ) {
			return new WP_Error( 'calculatorr_unknown', 'No JSON calculator with that slug.', array( 'status' => 404 ) );
		}

		return rest_ensure_response( array_merge( $definition, array( 'stored_in' => $store->source_of( $slug ) ) ) );
	}

	public function list_calculators() {
		$store = Calculatorr_Json_Calculators::instance();
		$out   = array();

		foreach ( $store->all() as $slug => $definition ) {
			$out[] = array(
				'slug'      => $slug,
				'title'     => $definition['title'],
				'category'  => $definition['category'],
				'fields'    => count( $definition['fields'] ),
				'stored_in' => $store->source_of( $slug ),
			);
		}

		return rest_ensure_response(
			array(
				'json'    => $out,
				'shipped' => count( Calculatorr_Registry::instance()->all() ) - count( $out ),
			)
		);
	}

	/**
	 * Creates or replaces one calculator.
	 *
	 * Replaced rather than merged, unlike the content route. A definition is
	 * one document describing one tool, and merging a partial one would leave
	 * a calculator whose fields came from today and whose formula came from
	 * last week, which is a worse failure than being asked to send the whole
	 * thing again.
	 */
	public function write_calculator( WP_REST_Request $request ) {
		$slug = (string) $request->get_param( 'slug' );
		$body = $request->get_json_params();

		if ( ! is_array( $body ) ) {
			return new WP_Error( 'calculatorr_bad_body', 'Expected a JSON object.', array( 'status' => 400 ) );
		}

		/* The slug in the URL is the one that counts, so a definition whose
		   body disagrees with it is refused rather than quietly filed under
		   whichever of the two happened to be read last. */
		if ( ! empty( $body['slug'] ) && $body['slug'] !== $slug ) {
			return new WP_Error(
				'calculatorr_slug_mismatch',
				sprintf( 'The URL says %s and the body says %s.', $slug, $body['slug'] ),
				array( 'status' => 400 )
			);
		}

		$body['slug'] = $slug;

		$store  = Calculatorr_Json_Calculators::instance();
		$stored = $store->save( $body );

		if ( is_wp_error( $stored ) ) {
			return $stored;
		}

		Calculatorr_Registry::instance()->reload();

		/* A definition with no page behind it is a calculator nobody can
		   reach, so the page is created here rather than waiting for the next
		   activation. sync() only ever adds what is missing, so a page
		   somebody has since tuned by hand is left exactly as it is. */
		Calculatorr_Pages::sync();

		$config = Calculatorr_Registry::instance()->get( $slug );

		/*
		 * A file of the same name wins, so a write that lands behind one has
		 * changed the database and changed nothing a visitor will see. Saying
		 * so is the whole point: the alternative is an author who believes a
		 * calculator has been updated for as long as it takes somebody to
		 * notice that it has not.
		 */
		$live = $store->source_of( $slug );

		return rest_ensure_response(
			array(
				'slug'      => $slug,
				'title'     => $stored['title'],
				'fields'    => count( $stored['fields'] ),
				'sections'  => count( isset( $stored['explainer'] ) ? $stored['explainer'] : array() ),
				'faqs'      => count( isset( $stored['faqs'] ) ? $stored['faqs'] : array() ),
				'words'     => Calculatorr_Content::count_words( $stored ),
				'url'       => $config ? Calculatorr_Pages::url_for( $config ) : '',
				'stored_in' => $live,
				'note'      => 'file' === $live
					? 'Saved to the database, but this slug also ships as a file and the file is what the site serves.'
					: '',
			)
		);
	}

	public function delete_calculator( WP_REST_Request $request ) {
		$slug  = (string) $request->get_param( 'slug' );
		$store = Calculatorr_Json_Calculators::instance();

		if ( ! $store->remove( $slug ) ) {
			return new WP_Error(
				'calculatorr_not_stored',
				'There is no database copy of that calculator to remove.',
				array( 'status' => 404 )
			);
		}

		Calculatorr_Registry::instance()->reload();

		/* The page itself is left alone. Deleting a published URL is not
		   something a calculator definition should be able to do on its own,
		   and the page is where any hand editing would have gone. */
		return rest_ensure_response(
			array(
				'slug'    => $slug,
				'deleted' => true,
				'note'    => 'The definition is gone. Its page is still published and will render nothing until a calculator of that slug exists again, so delete or unpublish it in WordPress if it is not coming back.',
			)
		);
	}
}
