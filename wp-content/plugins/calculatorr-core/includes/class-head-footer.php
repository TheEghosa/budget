<?php
/**
 * Site-wide header and footer code.
 *
 * AdSense needs its loader script in the head of every page, and verification
 * tags for Search Console, Bing and analytics all want the same thing. Rather
 * than making you install a separate plugin for four text boxes, they live
 * here alongside the slots the code actually fills.
 *
 * The code is stored and printed exactly as given. It has to be: an ad tag or
 * an analytics snippet is script by nature, and sanitising it would break
 * every one of them. Only an administrator can reach the screen that sets it,
 * which is the same trust model WordPress applies to the theme editor.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Calculatorr_Head_Footer {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		/* Late in the head so anything the theme declares is already out, and
		   early in the footer so a tag that expects the page to exist has it. */
		add_action( 'wp_head', array( $this, 'head' ), 99 );
		add_action( 'wp_body_open', array( $this, 'body' ), 1 );
		add_action( 'wp_footer', array( $this, 'footer' ), 99 );
	}

	private function output( $key, $label ) {
		$settings = Calculatorr_Settings::instance();

		if ( ! $settings->get( 'head_footer_enabled' ) ) {
			return;
		}

		$code = trim( (string) $settings->get( $key ) );

		if ( '' === $code ) {
			return;
		}

		/* Never on an admin screen, a feed or a REST response, where an ad
		   loader has nothing to attach to and an analytics tag would record
		   traffic that is not traffic. */
		if ( is_admin() || is_feed() ) {
			return;
		}

		echo "\n<!-- calculatorr " . esc_html( $label ) . " -->\n";
		echo $code . "\n";
	}

	public function head()   { $this->output( 'code_head', 'head' ); }
	public function body()   { $this->output( 'code_body', 'body open' ); }
	public function footer() { $this->output( 'code_footer', 'footer' ); }
}
