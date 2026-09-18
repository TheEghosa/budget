<?php
/**
 * Plugin Name:       Calculatorr Core
 * Plugin URI:        https://calculatorr.com
 * Description:       Powers every calculator on calculatorr.com. Each calculator is one config file, so adding the hundred and first is a config file rather than a new template.
 * Version:           0.1.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            calculatorr.com
 * License:           GPL-2.0-or-later
 * Text Domain:       calculatorr
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CALCULATORR_VERSION', '0.1.0' );
define( 'CALCULATORR_FILE', __FILE__ );
define( 'CALCULATORR_PATH', plugin_dir_path( __FILE__ ) );
define( 'CALCULATORR_URL', plugin_dir_url( __FILE__ ) );

require_once CALCULATORR_PATH . 'includes/class-registry.php';
require_once CALCULATORR_PATH . 'includes/class-renderer.php';
require_once CALCULATORR_PATH . 'includes/class-schema.php';
require_once CALCULATORR_PATH . 'includes/class-seo.php';
require_once CALCULATORR_PATH . 'includes/class-ads.php';
require_once CALCULATORR_PATH . 'includes/class-pages.php';
require_once CALCULATORR_PATH . 'includes/class-admin.php';
require_once CALCULATORR_PATH . 'includes/class-elementor.php';

/**
 * Boot every piece of the plugin on plugins_loaded so that Elementor, which
 * registers its own widget hooks late, is already present when we look for it.
 */
function calculatorr_boot() {
	Calculatorr_Registry::instance();
	Calculatorr_Renderer::instance();
	Calculatorr_Schema::instance();
	Calculatorr_SEO::instance();
	Calculatorr_Ads::instance();
	Calculatorr_Elementor::instance();

	if ( is_admin() ) {
		Calculatorr_Admin::instance();
	}
}
add_action( 'plugins_loaded', 'calculatorr_boot' );

/**
 * Front-end assets are registered here but only enqueued by the renderer, so a
 * page without a calculator on it never downloads the runtime.
 */
function calculatorr_register_assets() {
	wp_register_style(
		'calculatorr-tokens',
		CALCULATORR_URL . 'assets/css/tokens.css',
		array(),
		CALCULATORR_VERSION
	);
	wp_register_style(
		'calculatorr-app',
		CALCULATORR_URL . 'assets/css/calculator.css',
		array( 'calculatorr-tokens' ),
		CALCULATORR_VERSION
	);
	wp_register_script(
		'calculatorr-formulas',
		CALCULATORR_URL . 'assets/js/formulas.js',
		array(),
		CALCULATORR_VERSION,
		true
	);
	wp_register_script(
		'calculatorr-app',
		CALCULATORR_URL . 'assets/js/calculator.js',
		array( 'calculatorr-formulas' ),
		CALCULATORR_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'calculatorr_register_assets' );

register_activation_hook( __FILE__, array( 'Calculatorr_Pages', 'on_activate' ) );
