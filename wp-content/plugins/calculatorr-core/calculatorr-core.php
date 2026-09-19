<?php
/**
 * Plugin Name:       Calculatorr Core
 * Plugin URI:        https://calculatorr.org
 * Description:       Powers every calculator on calculatorr.org. Each calculator is one config file, so adding the hundred and first is a config file rather than a new template.
 * Version:           1.1.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            calculatorr.org
 * License:           GPL-2.0-or-later
 * Text Domain:       calculatorr
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CALCULATORR_VERSION', '1.1.0' );
define( 'CALCULATORR_FILE', __FILE__ );
define( 'CALCULATORR_PATH', plugin_dir_path( __FILE__ ) );
define( 'CALCULATORR_URL', plugin_dir_url( __FILE__ ) );

require_once CALCULATORR_PATH . 'includes/class-settings.php';
require_once CALCULATORR_PATH . 'includes/class-error-log.php';
require_once CALCULATORR_PATH . 'includes/class-registry.php';
require_once CALCULATORR_PATH . 'includes/class-renderer.php';
require_once CALCULATORR_PATH . 'includes/class-schema.php';
require_once CALCULATORR_PATH . 'includes/class-seo.php';
require_once CALCULATORR_PATH . 'includes/class-ads.php';
require_once CALCULATORR_PATH . 'includes/class-head-footer.php';
require_once CALCULATORR_PATH . 'includes/class-pages.php';
require_once CALCULATORR_PATH . 'includes/class-site-chrome.php';
require_once CALCULATORR_PATH . 'includes/class-rest-settings.php';
require_once CALCULATORR_PATH . 'includes/class-admin.php';
require_once CALCULATORR_PATH . 'includes/class-elementor.php';

/**
 * Boot every piece of the plugin on plugins_loaded so that Elementor, which
 * registers its own widget hooks late, is already present when we look for it.
 */
function calculatorr_boot() {
	Calculatorr_Settings::instance();
	Calculatorr_Error_Log::instance();
	Calculatorr_Registry::instance();
	Calculatorr_Renderer::instance();
	Calculatorr_Schema::instance();
	Calculatorr_SEO::instance();
	Calculatorr_Ads::instance();
	Calculatorr_Head_Footer::instance();
	Calculatorr_Site_Chrome::instance();
	Calculatorr_Rest_Settings::instance();
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
		array( 'calculatorr-tokens', 'calculatorr-fonts' ),
		CALCULATORR_VERSION
	);
	/**
	 * The design depends on these two faces, and the shareable snapshot is
	 * drawn in them too, so a missing font is visible rather than cosmetic.
	 * Sites that self-host fonts or already load them can switch this off with
	 * the calculatorr_load_fonts filter.
	 */
	if ( apply_filters( 'calculatorr_load_fonts', (bool) Calculatorr_Settings::instance()->get( 'load_fonts' ) ) ) {
		wp_register_style(
			'calculatorr-fonts',
			'https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Source+Sans+3:wght@400;600&display=swap',
			array(),
			null
		);
	}

	wp_register_script(
		'calculatorr-share',
		CALCULATORR_URL . 'assets/js/share.js',
		array(),
		CALCULATORR_VERSION,
		true
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
		array( 'calculatorr-formulas', 'calculatorr-share' ),
		CALCULATORR_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'calculatorr_register_assets' );

register_activation_hook( __FILE__, array( 'Calculatorr_Pages', 'on_activate' ) );
