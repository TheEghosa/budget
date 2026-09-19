<?php
/**
 * Calculators defined as JSON rather than as PHP.
 *
 * The first hundred and five calculators each ship as a PHP config file plus a
 * function in formulas.js, which means adding one means shipping a new copy of
 * the plugin. That is a fine arrangement for a handful of tools and a bad one
 * for a site whose whole point is to keep adding them, because every addition
 * then waits on somebody uploading a zip.
 *
 * A JSON calculator carries everything in one document: the fields, the copy,
 * the questions and the formula itself. It can be written straight over the
 * REST route, so a new tool goes live without a deploy, and it is read back
 * into exactly the same config array the PHP files produce, so nothing
 * downstream, the renderer, the schema, the sitemap or the category pages, can
 * tell the two apart.
 *
 * Definitions come from two places and files win. A definition committed to
 * calculators/json/ is what the plugin ships; the database copy of the same
 * slug stands aside for it, which is how a calculator published today becomes
 * an ordinary part of the next build without anybody having to remember to
 * delete anything.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Calculatorr_Json_Calculators {

	const OPTION = 'calculatorr_json_calculators';

	private static $instance = null;

	/** @var array<string,array>|null Merged definitions, keyed by slug. */
	private $definitions = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * The merged set, files over database.
	 *
	 * Read once per request and held, because the registry asks for it on
	 * every load and the file scan is the expensive half.
	 */
	public function all() {
		if ( null === $this->definitions ) {
			$this->definitions = array_merge( $this->from_db(), $this->from_files() );
			ksort( $this->definitions );
		}

		return $this->definitions;
	}

	public function get( $slug ) {
		$all = $this->all();
		return isset( $all[ $slug ] ) ? $all[ $slug ] : null;
	}

	public function has( $slug ) {
		return null !== $this->get( $slug );
	}

	/**
	 * Where one definition came from, which the REST route reports back so
	 * nobody is left wondering why a write appeared to do nothing.
	 *
	 * @return string One of 'file', 'db' or ''.
	 */
	public function source_of( $slug ) {
		$files = $this->from_files();

		if ( isset( $files[ $slug ] ) ) {
			return 'file';
		}

		$db = $this->from_db();

		return isset( $db[ $slug ] ) ? 'db' : '';
	}

	/**
	 * The formula body for one slug, which is what the renderer prints beside
	 * the card for the sandbox worker to run.
	 */
	public function formula( $slug ) {
		$definition = $this->get( $slug );
		return ( $definition && isset( $definition['formula'] ) ) ? $definition['formula'] : '';
	}

	private function from_files() {
		$out   = array();
		$files = glob( CALCULATORR_PATH . 'calculators/json/*.json' );

		if ( empty( $files ) ) {
			return $out;
		}

		foreach ( $files as $file ) {
			$raw = json_decode( file_get_contents( $file ), true );

			if ( ! is_array( $raw ) ) {
				continue;
			}

			$clean = self::validate( $raw );

			/*
			 * A shipped file that no longer validates is skipped rather than
			 * rendered half-formed, and it is logged, because a calculator
			 * quietly vanishing from the site after an upgrade is the kind of
			 * failure nobody notices for a month.
			 */
			if ( is_wp_error( $clean ) ) {
				if ( class_exists( 'Calculatorr_Error_Log' ) ) {
					Calculatorr_Error_Log::instance()->add(
						basename( $file ),
						'Shipped JSON calculator rejected: ' . $clean->get_error_message()
					);
				}
				continue;
			}

			$out[ $clean['slug'] ] = $clean;
		}

		return $out;
	}

	private function from_db() {
		$stored = get_option( self::OPTION, array() );

		if ( ! is_array( $stored ) ) {
			return array();
		}

		$out = array();

		foreach ( $stored as $slug => $definition ) {
			if ( is_array( $definition ) && ! empty( $definition['slug'] ) ) {
				$out[ $definition['slug'] ] = $definition;
			}
		}

		return $out;
	}

	/**
	 * Writes one definition to the database.
	 *
	 * Validation happens here rather than only in the route, so there is no way
	 * to reach the option row with something the renderer cannot draw.
	 *
	 * @return array|WP_Error The stored definition.
	 */
	public function save( $raw ) {
		$clean = self::validate( $raw );

		if ( is_wp_error( $clean ) ) {
			return $clean;
		}

		$stored = get_option( self::OPTION, array() );
		$stored = is_array( $stored ) ? $stored : array();

		$stored[ $clean['slug'] ] = $clean;

		update_option( self::OPTION, $stored, false );

		$this->definitions = null;

		return $clean;
	}

	/**
	 * @return bool Whether there was a database copy to remove. A definition
	 *              that only exists as a shipped file is not deletable from
	 *              here, and saying so is more useful than reporting success.
	 */
	public function remove( $slug ) {
		$stored = get_option( self::OPTION, array() );

		if ( ! is_array( $stored ) || ! isset( $stored[ $slug ] ) ) {
			return false;
		}

		unset( $stored[ $slug ] );

		update_option( self::OPTION, $stored, false );

		$this->definitions = null;

		return true;
	}

	/* ---------- Validation ---------- */

	/** Everything a definition is allowed to carry. */
	private static function keys() {
		return array(
			'slug', 'title', 'category', 'description', 'keyword', 'h1',
			'meta_title', 'meta_description', 'fields', 'formula',
			'explainer', 'faqs', 'related', 'sources', 'disclaimer',
			'default_result',
		);
	}

	/**
	 * Turns whatever arrived into a definition the rest of the plugin can use,
	 * or explains why it cannot.
	 *
	 * The rule throughout is that an unrecognised key is refused rather than
	 * dropped. Dropping it silently means somebody writes a definition with a
	 * feature that does not exist, sees it published, and never finds out that
	 * half of what they wrote went nowhere.
	 *
	 * @return array|WP_Error
	 */
	public static function validate( $raw ) {
		if ( ! is_array( $raw ) ) {
			return self::fail( 'Expected a JSON object.' );
		}

		$unknown = array_diff( array_keys( $raw ), self::keys() );

		if ( $unknown ) {
			return self::fail( 'Unexpected keys: ' . implode( ', ', $unknown ) . '.' );
		}

		$slug = isset( $raw['slug'] ) ? (string) $raw['slug'] : '';

		if ( ! preg_match( '/^[a-z0-9]+(-[a-z0-9]+)*$/', $slug ) ) {
			return self::fail( 'slug must be lowercase words joined by single hyphens.' );
		}

		/*
		 * A JSON definition cannot take over a slug that ships as PHP. The two
		 * would both load, the file would win in the registry and the JSON copy
		 * would sit there looking published while doing nothing, which is worse
		 * than being told no.
		 */
		if ( file_exists( CALCULATORR_PATH . 'calculators/' . $slug . '.php' ) ) {
			return self::fail( sprintf( '%s already ships as a PHP calculator.', $slug ) );
		}

		foreach ( array( 'title', 'description' ) as $required ) {
			if ( empty( $raw[ $required ] ) ) {
				return self::fail( $required . ' is required.' );
			}
		}

		/* Asked for statically, because the registry is part-way through its
		   own constructor when it loads these and an instance() call from here
		   would build a second one and recurse. */
		$categories = Calculatorr_Registry::category_keys();
		$category   = isset( $raw['category'] ) ? (string) $raw['category'] : '';

		if ( ! in_array( $category, $categories, true ) ) {
			return self::fail( 'category must be one of: ' . implode( ', ', $categories ) . '.' );
		}

		$fields = self::clean_fields( isset( $raw['fields'] ) ? $raw['fields'] : array() );

		if ( is_wp_error( $fields ) ) {
			return $fields;
		}

		$formula = self::clean_formula( isset( $raw['formula'] ) ? $raw['formula'] : '' );

		if ( is_wp_error( $formula ) ) {
			return $formula;
		}

		$clean = array(
			'slug'        => $slug,
			'title'       => sanitize_text_field( $raw['title'] ),
			'category'    => $category,
			'description' => sanitize_text_field( $raw['description'] ),
			'fields'      => $fields,
			'formula'     => $formula,
		);

		foreach ( array( 'keyword', 'h1', 'meta_title', 'meta_description', 'disclaimer' ) as $key ) {
			if ( ! empty( $raw[ $key ] ) ) {
				$clean[ $key ] = sanitize_text_field( $raw[ $key ] );
			}
		}

		if ( isset( $raw['explainer'] ) ) {
			$explainer = Calculatorr_Content::clean_explainer( $raw['explainer'] );

			if ( is_wp_error( $explainer ) ) {
				return $explainer;
			}

			$clean['explainer'] = $explainer;
		}

		if ( isset( $raw['faqs'] ) ) {
			$faqs = Calculatorr_Content::clean_faqs( $raw['faqs'] );

			if ( is_wp_error( $faqs ) ) {
				return $faqs;
			}

			$clean['faqs'] = $faqs;
		}

		foreach ( array( 'related', 'sources' ) as $key ) {
			if ( ! empty( $raw[ $key ] ) ) {
				if ( ! is_array( $raw[ $key ] ) ) {
					return self::fail( $key . ' must be a list.' );
				}

				$clean[ $key ] = 'related' === $key
					? array_values( array_map( 'sanitize_key', $raw[ $key ] ) )
					: array_values( array_map( array( __CLASS__, 'clean_source' ), $raw[ $key ] ) );
			}
		}

		$result = self::clean_result( isset( $raw['default_result'] ) ? $raw['default_result'] : array() );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$clean['default_result'] = $result;

		return $clean;
	}

	private static function clean_source( $source ) {
		if ( is_array( $source ) ) {
			return array(
				'name' => sanitize_text_field( isset( $source['name'] ) ? $source['name'] : '' ),
				'url'  => esc_url_raw( isset( $source['url'] ) ? $source['url'] : '' ),
			);
		}

		return sanitize_text_field( $source );
	}

	/** The field types the renderer can actually draw. */
	private static function types() {
		return array( 'number', 'text', 'date', 'select', 'segmented', 'repeater' );
	}

	private static function clean_fields( $fields ) {
		if ( ! is_array( $fields ) || ! $fields ) {
			return self::fail( 'fields must be a non-empty list.' );
		}

		$out  = array();
		$seen = array();

		foreach ( $fields as $i => $field ) {
			$where = sprintf( 'Field %d', (int) $i + 1 );

			if ( ! is_array( $field ) || empty( $field['id'] ) || empty( $field['label'] ) ) {
				return self::fail( $where . ' needs an id and a label.' );
			}

			$id = (string) $field['id'];

			if ( ! preg_match( '/^[A-Za-z][A-Za-z0-9_]*$/', $id ) ) {
				return self::fail( $where . ': id must start with a letter and hold letters, digits or underscores only.' );
			}

			$type = isset( $field['type'] ) ? (string) $field['type'] : 'number';

			if ( ! in_array( $type, self::types(), true ) ) {
				return self::fail( $where . ': type must be one of ' . implode( ', ', self::types() ) . '.' );
			}

			/*
			 * Two fields may deliberately share an id, which is how the metric
			 * and imperial versions of the same measurement feed one name into
			 * the formula. What they may not do is share it while both being
			 * visible at once, because then one silently wins and the other is
			 * furniture.
			 */
			$when = isset( $field['show_when'] ) ? $field['show_when'] : array();

			if ( ! is_array( $when ) ) {
				return self::fail( $where . ': show_when must be an object of field ids to values.' );
			}

			if ( isset( $seen[ $id ] ) && ( ! $when || $seen[ $id ] === wp_json_encode( $when ) ) ) {
				return self::fail( $where . ': id "' . $id . '" is already used by a field that shows at the same time.' );
			}

			$seen[ $id ] = wp_json_encode( $when );

			$clean = array(
				'id'    => $id,
				'label' => sanitize_text_field( $field['label'] ),
				'type'  => $type,
			);

			foreach ( array( 'prefix', 'suffix', 'hint' ) as $key ) {
				if ( ! empty( $field[ $key ] ) ) {
					$clean[ $key ] = sanitize_text_field( $field[ $key ] );
				}
			}

			foreach ( array( 'min', 'max', 'step' ) as $key ) {
				if ( isset( $field[ $key ] ) && '' !== $field[ $key ] ) {
					$clean[ $key ] = sanitize_text_field( $field[ $key ] );
				}
			}

			if ( isset( $field['optional'] ) ) {
				$clean['optional'] = (bool) $field['optional'];
			}

			if ( $when ) {
				$clean['show_when'] = array_map( 'sanitize_text_field', $when );
			}

			if ( in_array( $type, array( 'select', 'segmented' ), true ) ) {
				if ( empty( $field['options'] ) || ! is_array( $field['options'] ) ) {
					return self::fail( $where . ': a ' . $type . ' field needs options.' );
				}

				$options = array();

				foreach ( $field['options'] as $value => $label ) {
					$options[ sanitize_text_field( $value ) ] = sanitize_text_field( $label );
				}

				$clean['options'] = $options;
				$default          = isset( $field['default'] ) ? (string) $field['default'] : '';

				if ( ! array_key_exists( $default, $options ) ) {
					return self::fail( $where . ': default "' . $default . '" is not one of its options.' );
				}

				$clean['default'] = $default;
			} elseif ( 'repeater' === $type ) {
				$row = self::clean_row( isset( $field['row'] ) ? $field['row'] : array(), $where );

				if ( is_wp_error( $row ) ) {
					return $row;
				}

				$clean['row']     = $row;
				$clean['rows']    = isset( $field['rows'] ) ? max( 1, min( 12, (int) $field['rows'] ) ) : 3;
				$clean['default'] = '';
			} else {
				$clean['default'] = isset( $field['default'] ) ? sanitize_text_field( $field['default'] ) : '';
			}

			$out[] = $clean;
		}

		return $out;
	}

	private static function clean_row( $row, $where ) {
		if ( ! is_array( $row ) || ! $row ) {
			return self::fail( $where . ': a repeater needs a row of cells.' );
		}

		$out = array();

		foreach ( $row as $cell ) {
			if ( ! is_array( $cell ) || empty( $cell['id'] ) || empty( $cell['label'] ) ) {
				return self::fail( $where . ': every repeater cell needs an id and a label.' );
			}

			$type = isset( $cell['type'] ) ? (string) $cell['type'] : 'number';

			if ( ! in_array( $type, array( 'number', 'text', 'select' ), true ) ) {
				return self::fail( $where . ': a repeater cell must be number, text or select.' );
			}

			$clean = array(
				'id'      => sanitize_key( $cell['id'] ),
				'label'   => sanitize_text_field( $cell['label'] ),
				'type'    => $type,
				'default' => isset( $cell['default'] ) ? sanitize_text_field( $cell['default'] ) : '',
			);

			if ( 'select' === $type ) {
				if ( empty( $cell['options'] ) || ! is_array( $cell['options'] ) ) {
					return self::fail( $where . ': a select cell needs options.' );
				}

				$options = array();

				foreach ( $cell['options'] as $value => $label ) {
					$options[ sanitize_text_field( $value ) ] = sanitize_text_field( $label );
				}

				$clean['options'] = $options;
			}

			$out[] = $clean;
		}

		return $out;
	}

	/**
	 * The formula body.
	 *
	 * It is stored as written, because it is JavaScript and mangling it would
	 * change what it computes. What is checked is that it is a plausible size
	 * and that it does not name any of the ways out of the worker.
	 *
	 * That name check is a second line rather than the first. The sandbox is
	 * what actually stops a formula reaching the network, since it has already
	 * had fetch and the rest taken away from it before the body is compiled,
	 * and a name check can always be written around by anyone determined to.
	 * It stays because the realistic failure here is not an attacker, it is a
	 * formula written against the wrong mental model, and telling its author
	 * that XMLHttpRequest is not available is more use than letting it fail
	 * silently in front of a visitor.
	 */
	private static function clean_formula( $formula ) {
		$formula = (string) $formula;

		if ( '' === trim( $formula ) ) {
			return self::fail( 'formula is required.' );
		}

		if ( strlen( $formula ) > 20000 ) {
			return self::fail( 'formula is longer than 20,000 characters, which is far past anything on the site.' );
		}

		if ( false === strpos( $formula, 'return' ) ) {
			return self::fail( 'formula never returns anything, so it could not produce a result.' );
		}

		$banned = array(
			'importScripts', 'fetch', 'XMLHttpRequest', 'WebSocket', 'EventSource',
			'postMessage', 'indexedDB', 'localStorage', 'sessionStorage',
			'document', 'window', 'globalThis', 'eval',
		);

		foreach ( $banned as $name ) {
			if ( preg_match( '/\b' . preg_quote( $name, '/' ) . '\b/', $formula ) ) {
				return self::fail( sprintf( 'formula names %s, which the sandbox does not provide.', $name ) );
			}
		}

		return $formula;
	}

	/**
	 * The worked answer the server renders before any script has run.
	 *
	 * This is not decoration. It is what a crawler indexes and what a visitor
	 * reads in the moment before the sandbox answers, so it has to be the real
	 * result for the field defaults. The authoring tool computes it by running
	 * the formula, which is the only way it cannot drift from what the browser
	 * will show a second later.
	 */
	private static function clean_result( $result ) {
		if ( ! is_array( $result ) || empty( $result['value'] ) ) {
			return self::fail( 'default_result needs at least a label and a value. Run tools/json-calculator.js to compute it from the formula.' );
		}

		$clean = array(
			'label' => sanitize_text_field( isset( $result['label'] ) ? $result['label'] : 'Result' ),
			'value' => sanitize_text_field( $result['value'] ),
			'rows'  => array(),
		);

		foreach ( array( 'sub', 'note' ) as $key ) {
			if ( ! empty( $result[ $key ] ) ) {
				$clean[ $key ] = sanitize_text_field( $result[ $key ] );
			}
		}

		foreach ( (array) ( isset( $result['rows'] ) ? $result['rows'] : array() ) as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}

			$clean['rows'][] = array(
				'label' => sanitize_text_field( isset( $row['label'] ) ? $row['label'] : '' ),
				'value' => sanitize_text_field( isset( $row['value'] ) ? $row['value'] : '' ),
			);
		}

		return $clean;
	}

	private static function fail( $message ) {
		return new WP_Error( 'calculatorr_bad_definition', $message, array( 'status' => 400 ) );
	}
}
