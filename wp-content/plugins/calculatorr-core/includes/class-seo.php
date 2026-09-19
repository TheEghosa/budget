<?php
/**
 * Every on-page SEO element, written by the plugin rather than left to a theme.
 *
 * Titles, descriptions, canonicals and social cards are generated from the same
 * config that builds the calculator, so the keyword a page targets and the
 * keyword its title tag claims can never drift apart. If a dedicated SEO plugin
 * is active, everything here stands down instead, because two plugins writing
 * the same tags produces duplicates that are worse than either alone.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Calculatorr_SEO {

	private static $instance = null;

	/** @var bool Whether another plugin already owns the meta tags. */
	private $other_plugin = false;
	private $deferring = false;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->other_plugin = $this->another_seo_plugin_is_active();
		$this->deferring    = $this->other_plugin
			|| ! Calculatorr_Settings::instance()->get( 'seo_enabled' );

		if ( $this->deferring ) {
			/* Still feed the other plugin good defaults, since it will use the
			   post title and excerpt if nothing better is offered. */
			add_filter( 'wpseo_title', array( $this, 'filter_title_string' ) );
			add_filter( 'wpseo_metadesc', array( $this, 'filter_description_string' ) );
			add_filter( 'rank_math/frontend/title', array( $this, 'filter_title_string' ) );
			add_filter( 'rank_math/frontend/description', array( $this, 'filter_description_string' ) );
			return;
		}

		add_filter( 'pre_get_document_title', array( $this, 'document_title' ), 20 );
		add_action( 'wp_head', array( $this, 'head_tags' ), 1 );
	}

	public function is_deferring() {
		return $this->deferring;
	}

	/**
	 * Whether a dedicated SEO plugin is publishing the head.
	 *
	 * This is deliberately not the same question as is_deferring(): the plugin
	 * also stands down when its own SEO output has simply been switched off,
	 * and in that case nothing else is emitting Organization or WebSite, so
	 * the schema still should.
	 */
	public function other_plugin_active() {
		return $this->other_plugin;
	}

	private function another_seo_plugin_is_active() {
		$owned = defined( 'WPSEO_VERSION' )
			|| defined( 'RANK_MATH_VERSION' )
			|| defined( 'AIOSEO_VERSION' )
			|| defined( 'SEOPRESS_VERSION' )
			|| class_exists( 'The_SEO_Framework\\Load' );

		return (bool) apply_filters( 'calculatorr_defer_seo', $owned );
	}

	/**
	 * The page this request is about, as one shape whether it is a calculator,
	 * a category hub or something else entirely.
	 *
	 * @return array{type:string,title:string,description:string,url:string}|null
	 */
	private function context() {
		$calculator = Calculatorr_Pages::current();

		if ( $calculator ) {
			return array(
				'type'        => 'calculator',
				'title'       => $calculator['meta_title'],
				'description' => $calculator['meta_description'],
				'url'         => Calculatorr_Pages::url_for( $calculator ),
				'config'      => $calculator,
			);
		}

		$category = Calculatorr_Pages::current_category();

		if ( $category ) {
			return array(
				'type'        => 'category',
				'title'       => $category['meta_title'],
				'description' => $category['meta_description'],
				'url'         => home_url( '/' . $category['slug'] . '/' ),
				'category'    => $category,
			);
		}

		/*
		 * The front page is somebody's editable page rather than one of ours,
		 * so the title it already produces is left alone. What it does not
		 * produce is a description or a social card, and a homepage sharing as
		 * a blank rectangle with no summary is the one page where that is most
		 * expensive.
		 */
		if ( is_front_page() ) {
			$front = (int) get_option( 'page_on_front' );

			return array(
				'type'        => 'home',
				/* Built from the page rather than read back from
				   wp_get_document_title(), because this method runs inside the
				   pre_get_document_title filter and asking for the title from
				   in there calls straight back into itself. */
				'title'       => $front ? get_the_title( $front ) : get_bloginfo( 'name', 'display' ),
				'description' => $this->home_description(),
				'url'         => home_url( '/' ),
			);
		}

		return null;
	}

	/**
	 * The homepage summary, from settings when one has been written and from
	 * the live calculator count when it has not, so the count in the sentence
	 * cannot drift away from the number of calculators actually installed.
	 */
	private function home_description() {
		$written = trim( (string) Calculatorr_Settings::instance()->get( 'home_description' ) );

		if ( '' !== $written ) {
			return self::trim_description( $written );
		}

		$count = count( Calculatorr_Registry::instance()->all( false ) );

		return self::trim_description(
			sprintf(
				/* translators: %d: number of calculators on the site. */
				'Free online calculators for money, health, maths, DIY and more. %d tools that show the formula they used, run in your browser and need no sign-up.',
				$count
			)
		);
	}

	public function document_title( $title ) {
		$context = $this->context();

		if ( ! $context || 'home' === $context['type'] ) {
			return $title;
		}

		return $context['title'];
	}

	public function filter_title_string( $title ) {
		$context = $this->context();

		if ( ! $context || 'home' === $context['type'] ) {
			return $title;
		}

		return $context['title'];
	}

	public function filter_description_string( $description ) {
		$context = $this->context();
		return $context ? $context['description'] : $description;
	}

	public function head_tags() {
		$context = $this->context();

		if ( ! $context ) {
			return;
		}

		$image = CALCULATORR_URL . 'assets/images/social-card.png';

		$tags = array(
			array( 'name' => 'description', 'content' => $context['description'] ),
			array( 'property' => 'og:type', 'content' => 'website' ),
			array( 'property' => 'og:site_name', 'content' => get_bloginfo( 'name' ) ),
			array( 'property' => 'og:title', 'content' => $context['title'] ),
			array( 'property' => 'og:description', 'content' => $context['description'] ),
			array( 'property' => 'og:url', 'content' => $context['url'] ),
			array( 'property' => 'og:locale', 'content' => get_locale() ),
			array( 'name' => 'twitter:card', 'content' => 'summary_large_image' ),
			array( 'name' => 'twitter:title', 'content' => $context['title'] ),
			array( 'name' => 'twitter:description', 'content' => $context['description'] ),
		);

		echo "\n<!-- calculatorr -->\n";
		echo '<link rel="canonical" href="' . esc_url( $context['url'] ) . '">' . "\n";

		foreach ( $tags as $tag ) {
			$key = isset( $tag['property'] ) ? 'property' : 'name';
			printf(
				'<meta %1$s="%2$s" content="%3$s">' . "\n",
				$key,
				esc_attr( $tag[ $key ] ),
				esc_attr( $tag['content'] )
			);
		}

		/* The social image is only advertised once it actually exists, because
		   a card pointing at a 404 renders worse than no card at all. */
		if ( file_exists( CALCULATORR_PATH . 'assets/images/social-card.png' ) ) {
			echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
			echo '<meta name="twitter:image" content="' . esc_url( $image ) . '">' . "\n";
		}

		echo "<!-- /calculatorr -->\n";
	}

	/**
	 * Builds a description from a template when a config does not supply one.
	 * Kept near the length Google actually renders, since anything longer is
	 * truncated and anything much shorter wastes the space.
	 */
	public static function trim_description( $text, $limit = 158 ) {
		$text = trim( wp_strip_all_tags( $text ) );

		if ( strlen( $text ) <= $limit ) {
			return $text;
		}

		$cut = substr( $text, 0, $limit );
		$last = strrpos( $cut, ' ' );

		return rtrim( false === $last ? $cut : substr( $cut, 0, $last ), ' ,.;:' ) . '.';
	}
}
