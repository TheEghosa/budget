<?php
/**
 * Dresses the theme's header and footer to match the calculatorr design.
 *
 * Elementor Free has no Theme Builder, so the header and footer belong to the
 * theme and cannot be rebuilt as templates. What the plugin can do is load one
 * stylesheet on every front-end view and take ownership of the page heading,
 * which is the difference between a site that looks designed and a site that
 * looks like a plugin dropped into a blank theme.
 *
 * It also resolves two duplications that only show up once the plugin is live
 * inside a real theme: a second H1 printed above the breadcrumb, and a second
 * canonical tag printed by WordPress core.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Calculatorr_Site_Chrome {

	private static $instance = null;

	/**
	 * Set once the theme has been told not to print the page title, so the
	 * renderer knows the H1 is now its job. The theme asks for the title
	 * before it prints the content, so this is always resolved by the time a
	 * shortcode runs.
	 *
	 * @var bool
	 */
	private $heading_suppressed = false;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		if ( ! Calculatorr_Settings::instance()->get( 'site_chrome' ) ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ), 20 );
		add_filter( 'hello_elementor_page_title', array( $this, 'page_title' ) );
		add_action( 'template_redirect', array( $this, 'drop_duplicate_canonical' ) );
	}

	/**
	 * The chrome stylesheet loads everywhere, including on pages with no
	 * calculator on them, because the header and footer are on every page.
	 * The calculator runtime stays where it was: only where it is used.
	 */
	public function enqueue() {
		if ( is_admin() ) {
			return;
		}

		wp_enqueue_style( 'calculatorr-tokens' );

		if ( wp_style_is( 'calculatorr-fonts', 'registered' ) ) {
			wp_enqueue_style( 'calculatorr-fonts' );
		}

		wp_enqueue_style(
			'calculatorr-site',
			CALCULATORR_URL . 'assets/css/site.css',
			array( 'calculatorr-tokens' ),
			CALCULATORR_VERSION
		);
	}

	/**
	 * Hello Elementor prints the page title in a block above the content. On a
	 * calculator page that lands an H1 above the breadcrumb, which reverses the
	 * order the design sets, and on the homepage it repeats a heading the page
	 * already carries. Both cases are answered by declining the title and
	 * letting the page render its own.
	 *
	 * @param bool $show Whether the theme intends to print the title.
	 * @return bool
	 */
	public function page_title( $show ) {
		if ( ! $show || ! $this->plugin_owns_heading() ) {
			return $show;
		}

		$this->heading_suppressed = true;
		return false;
	}

	/**
	 * True once the theme has agreed not to print a heading, which is the
	 * renderer's cue to print one itself. Without this the page would be left
	 * with no H1 at all, which is worse than the duplicate it replaces.
	 */
	public static function heading_is_ours() {
		return self::instance()->heading_suppressed;
	}

	/**
	 * A page is the plugin's to head up when it is a calculator, a category hub
	 * or the front page carrying the homepage block, which is identified by the
	 * wrapper class the homepage markup is built around.
	 */
	private function plugin_owns_heading() {
		if ( Calculatorr_Pages::current() || Calculatorr_Pages::current_category() ) {
			return true;
		}

		if ( ! is_front_page() && ! is_page() ) {
			return false;
		}

		$post = get_post();

		return $post && false !== strpos( (string) $post->post_content, 'class="ch-home"' );
	}

	/**
	 * WordPress core prints its own canonical link, and so does this plugin's
	 * SEO module. They agree on the URL, so nothing breaks, but two canonical
	 * tags on one page is the kind of thing an audit flags and a crawler has
	 * to pick between. Core's is the one that goes, because the plugin's is
	 * the one that knows about the calculator's own URL.
	 */
	public function drop_duplicate_canonical() {
		$seo = Calculatorr_SEO::instance();

		if ( $seo->is_deferring() || ! Calculatorr_Settings::instance()->get( 'seo_enabled' ) ) {
			return;
		}

		if ( ! Calculatorr_Pages::current() && ! Calculatorr_Pages::current_category() ) {
			return;
		}

		remove_action( 'wp_head', 'rel_canonical' );
	}
}
