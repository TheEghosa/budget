<?php
/**
 * Minimal WordPress stubs so the plugin can be exercised without a WordPress
 * install. Enough of the API is faked to load the registry, render every
 * calculator and emit the head tags, which is what the QA pass needs.
 *
 * This is a test harness, not a WordPress emulator. Anything it does not
 * stub is something the tests do not currently reach.
 */

define( 'ABSPATH', dirname( __DIR__ ) . '/' );
define( 'CALCULATORR_VERSION', 'test' );
define( 'CALCULATORR_FILE', dirname( __DIR__ ) . '/calculatorr-core.php' );
define( 'CALCULATORR_PATH', dirname( __DIR__ ) . '/' );
define( 'CALCULATORR_URL', 'https://calculatorr.org/wp-content/plugins/calculatorr-core/' );
define( 'OBJECT', 'OBJECT' );

$GLOBALS['calcr_test_state'] = array(
	'current_slug'     => null,
	'current_category' => null,
	'enqueued'         => array(),
);

function add_action( $h, $c, $p = 10, $a = 1 ) {}
function add_filter( $h, $c, $p = 10, $a = 1 ) {}
function add_shortcode( $t, $c ) { $GLOBALS['calcr_shortcodes'][ $t ] = $c; }
function apply_filters( $hook, $value ) { return $value; }
function do_action( $hook ) {}
function is_admin() { return false; }
function current_user_can( $c ) { return true; }
function add_menu_page() {}
function register_activation_hook() {}

function wp_enqueue_style( $h ) { $GLOBALS['calcr_test_state']['enqueued'][] = $h; }
function wp_enqueue_script( $h ) { $GLOBALS['calcr_test_state']['enqueued'][] = $h; }
function wp_register_style() {}
function wp_register_script() {}

function wp_parse_args( $args, $defaults = array() ) {
	return array_merge( $defaults, array_filter( (array) $args, function ( $v ) { return null !== $v; } ) );
}

function shortcode_atts( $pairs, $atts, $shortcode = '' ) {
	$out = array();
	foreach ( $pairs as $name => $default ) {
		$out[ $name ] = array_key_exists( $name, (array) $atts ) ? $atts[ $name ] : $default;
	}
	return $out;
}

function esc_url_raw( $u, $p = array() ) { return preg_match( '#^https://#', (string) $u ) ? $u : ''; }
function esc_textarea( $t ) { return htmlspecialchars( (string) $t, ENT_QUOTES, 'UTF-8' ); }
function wp_unslash( $v ) { return is_string( $v ) ? stripslashes( $v ) : $v; }
function number_format_i18n( $n, $d = 0 ) { return number_format( (float) $n, (int) $d ); }
function current_time( $f ) { return gmdate( $f ); }
if ( ! defined( 'DAY_IN_SECONDS' ) ) { define( 'DAY_IN_SECONDS', 86400 ); }
function admin_url( $p = '' ) { return 'https://calculatorr.org/wp-admin/' . $p; }
function submit_button( $t = '', $c = '', $n = '', $w = true ) {}
function wp_nonce_field( $a = -1, $n = '_wpnonce', $r = true, $e = true ) {}
function checked( $a, $b = true, $echo = true ) { $r = ( (string) $a === (string) $b ) ? ' checked' : ''; if ( $echo ) { echo $r; } return $r; }
function add_submenu_page() {}
function do_shortcode( $c ) { return preg_replace_callback( '/\[calculatorr slug="([a-z0-9\-]+)"\]/', function ( $m ) { return $GLOBALS['calcr_shortcodes']['calculatorr']( array( 'slug' => $m[1] ) ); }, (string) $c ); }
function wp_editor( $content, $id, $settings = array() ) { echo '<textarea name="' . ( $settings['textarea_name'] ?? $id ) . '">' . htmlspecialchars( (string) $content ) . '</textarea>'; }
function esc_html__( $t, $d = '' ) { return $t; }
function __( $t, $d = '' ) { return $t; }
function _n( $single, $plural, $n, $d = '' ) { return 1 === (int) $n ? $single : $plural; }
function esc_html( $t )  { return htmlspecialchars( (string) $t, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $t )  { return htmlspecialchars( (string) $t, ENT_QUOTES, 'UTF-8' ); }
function esc_url( $t )   { return htmlspecialchars( (string) $t, ENT_QUOTES, 'UTF-8' ); }
function wp_kses_post( $t ) { return $t; }
function wpautop( $t )   { return '<p>' . str_replace( "\n\n", "</p><p>", (string) $t ) . '</p>'; }
function wp_strip_all_tags( $t ) { return strip_tags( (string) $t ); }
function selected( $a, $b, $echo = true ) { $r = ( (string) $a === (string) $b ) ? ' selected' : ''; if ( $echo ) { echo $r; } return $r; }
function wp_json_encode( $d, $f = 0 ) { return json_encode( $d, $f ); }

function home_url( $path = '/' ) { return 'https://calculatorr.org' . $path; }
function get_bloginfo( $what = 'name', $filter = 'raw' ) { return 'name' === $what ? 'calculatorr.org' : 'en-US'; }
function get_locale() { return 'en_US'; }
function is_front_page() { return false; }
function is_feed() { return false; }

function is_page() { return null !== $GLOBALS['calcr_test_state']['current_slug'] || null !== $GLOBALS['calcr_test_state']['current_category']; }
function get_the_ID() { return 1; }

function get_post_meta( $id, $key, $single = true ) {
	if ( '_calculatorr_slug' === $key )     { return $GLOBALS['calcr_test_state']['current_slug'] ?: ''; }
	if ( '_calculatorr_category' === $key ) { return $GLOBALS['calcr_test_state']['current_category'] ?: ''; }
	return '';
}

function get_page_by_path( $path, $output = OBJECT, $type = 'page' ) { return null; }
function get_permalink( $p ) { return home_url( '/' ); }
function update_post_meta() {}
function wp_insert_post() { return 1; }
function is_wp_error( $t ) { return false; }
function flush_rewrite_rules() {}
function get_transient( $k ) { return false; }
function set_transient() {}
function delete_transient() {}

function get_option( $k, $d = false ) { return isset( $GLOBALS['calcr_options'][ $k ] ) ? $GLOBALS['calcr_options'][ $k ] : $d; }
function update_option( $k, $v, $a = true ) { $GLOBALS['calcr_options'][ $k ] = $v; return true; }
function sanitize_key( $k ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $k ) ); }
function sanitize_text_field( $t ) { return trim( strip_tags( (string) $t ) ); }
function register_rest_route() {}
function remove_action( $h, $c, $p = 10 ) {}
function remove_filter( $h, $c, $p = 10 ) {}
function wp_style_is( $h, $l = 'enqueued' ) { return true; }
function get_stylesheet() { return 'hello-elementor'; }
function get_post( $p = null ) { return null; }
function get_the_title( $p = 0 ) { return 'Free Online Calculators for Everything'; }
function rest_url( $p = '' ) { return home_url( '/wp-json/' . $p ); }
function wp_localize_script() {}
function mb_substr_compat( $s, $a, $b ) { return substr( $s, $a, $b ); }

$GLOBALS['calcr_options'] = array();

require_once CALCULATORR_PATH . 'includes/class-chart.php';
require_once CALCULATORR_PATH . 'includes/class-usage.php';
require_once CALCULATORR_PATH . 'includes/class-design.php';
require_once CALCULATORR_PATH . 'includes/class-settings.php';
require_once CALCULATORR_PATH . 'includes/class-error-log.php';
require_once CALCULATORR_PATH . 'includes/class-registry.php';
require_once CALCULATORR_PATH . 'includes/class-art.php';
require_once CALCULATORR_PATH . 'includes/class-renderer.php';
require_once CALCULATORR_PATH . 'includes/class-schema.php';
require_once CALCULATORR_PATH . 'includes/class-ads.php';
require_once CALCULATORR_PATH . 'includes/class-pages.php';
require_once CALCULATORR_PATH . 'includes/class-seo.php';
require_once CALCULATORR_PATH . 'includes/class-head-footer.php';
require_once CALCULATORR_PATH . 'includes/class-site-chrome.php';
require_once CALCULATORR_PATH . 'includes/class-admin.php';
