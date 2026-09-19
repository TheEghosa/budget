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

		if ( Calculatorr_Settings::instance()->get( 'theme_switch' ) ) {
			add_action( 'wp_head', array( $this, 'early_theme_script' ), 1 );
			add_filter( 'wp_nav_menu_items', array( $this, 'append_theme_switch' ), 10, 2 );
		}
	}

	/**
	 * Applies a stored light or dark choice before anything is painted.
	 *
	 * This is printed inline at the very top of the head rather than enqueued,
	 * because a file has to be fetched and a visitor who chose dark would
	 * otherwise watch the page flash white first. It is a handful of bytes and
	 * it touches one attribute.
	 */
	public function early_theme_script() {
		?>
<script>(function(){try{var t=window.localStorage.getItem('calcr-theme');if('dark'===t||'light'===t){var r=document.documentElement;r.setAttribute('data-theme',t);r.setAttribute('data-calcr-theme',t);}}catch(e){}}());</script>
		<?php
	}

	/**
	 * Adds the light and dark switch to the end of the header navigation.
	 *
	 * It goes in the menu markup rather than being injected by script after
	 * load, so it cannot arrive late or shift the header once it does. The
	 * button starts with no pressed state: which mode is active depends on the
	 * visitor's system preference until they choose, and the server has no way
	 * of knowing that, so the script settles it on load.
	 *
	 * @param string   $items Menu markup so far.
	 * @param stdClass $args  Menu arguments, which carry the theme location.
	 * @return string
	 */
	public function append_theme_switch( $items, $args ) {
		$location = isset( $args->theme_location ) ? $args->theme_location : '';

		if ( 'menu-1' !== $location ) {
			return $items;
		}

		$moon = '<svg class="calcr-icon calcr-icon--moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"></path></svg>';
		$sun  = '<svg class="calcr-icon calcr-icon--sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="4.2"></circle><path d="M12 2.5v2M12 19.5v2M4.2 4.2l1.4 1.4M18.4 18.4l1.4 1.4M2.5 12h2M19.5 12h2M4.2 19.8l1.4-1.4M18.4 5.6l1.4-1.4"></path></svg>';

		return $items . sprintf(
			'<li class="calcr-theme-switch"><button type="button" class="calcr-theme-switch__btn" data-calcr-theme-toggle aria-pressed="false"><span class="calcr-theme-switch__icons">%1$s%2$s</span><span class="calcr-theme-switch__label" data-calcr-theme-label>%3$s</span></button></li>',
			$moon,
			$sun,
			esc_html__( 'Dark', 'calculatorr' )
		);
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

		if ( Calculatorr_Settings::instance()->get( 'theme_switch' ) ) {
			wp_enqueue_script(
				'calculatorr-site',
				CALCULATORR_URL . 'assets/js/site.js',
				array(),
				CALCULATORR_VERSION,
				true
			);
		}
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
