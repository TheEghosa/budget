<?php
/**
 * Registers each calculator as a native Elementor widget.
 *
 * Custom widgets work on Elementor Free, which is what makes this split
 * viable: the generated pages carry the full template, and anyone who wants a
 * calculator inside a page they laid out by hand can drag one in instead.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Calculatorr_Elementor {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'elementor/widgets/register', array( $this, 'register' ) );
	}

	public function register( $widgets_manager ) {
		if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
			return;
		}

		require_once CALCULATORR_PATH . 'includes/class-elementor-widget.php';

		$widgets_manager->register( new Calculatorr_Elementor_Widget() );
	}
}
