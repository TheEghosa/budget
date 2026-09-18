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

/**
 * Boot every piece of the plugin on plugins_loaded so that Elementor, which
 * registers its own widget hooks late, is already present when we look for it.
 */
function calculatorr_boot() {
	Calculatorr_Registry::instance();
}
add_action( 'plugins_loaded', 'calculatorr_boot' );

