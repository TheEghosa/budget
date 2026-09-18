<?php
/**
 * Structured data, emitted as a single connected graph.
 *
 * One @graph rather than several separate blocks, because the nodes can then
 * reference each other by @id instead of repeating the page URL in every one,
 * and because a parser that chokes on one block does not lose the rest.
 *
 * Note on FAQ markup: Google narrowed FAQ rich results to authoritative
 * government and health sites in 2023, so the FAQPage node here is correct and
 * machine-readable but should not be expected to produce the expanded snippet
 * it once did. It is emitted because other engines and assistants still read
 * it, not because it guarantees anything in Google.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Calculatorr_Schema {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'wp_head', array( $this, 'output' ), 20 );
	}

	private function org_id() {
		return home_url( '/#organization' );
	}

	private function site_id() {
		return home_url( '/#website' );
	}

	private function organization() {
		$node = array(
			'@type' => 'Organization',
			'@id'   => $this->org_id(),
			'name'  => get_bloginfo( 'name' ),
			'url'   => home_url( '/' ),
		);

		$logo = CALCULATORR_PATH . 'assets/images/logo-mark.svg';

		if ( file_exists( $logo ) ) {
			$node['logo'] = array(
				'@type' => 'ImageObject',
				'url'   => CALCULATORR_URL . 'assets/images/logo-mark.svg',
			);
		}

		return $node;
	}

	private function website() {
		return array(
			'@type'     => 'WebSite',
			'@id'       => $this->site_id(),
			'url'       => home_url( '/' ),
			'name'      => get_bloginfo( 'name' ),
			'publisher' => array( '@id' => $this->org_id() ),
			'inLanguage' => get_bloginfo( 'language' ),
			'potentialAction' => array(
				'@type'       => 'SearchAction',
				'target'      => array(
					'@type'       => 'EntryPoint',
					'urlTemplate' => home_url( '/?s={search_term_string}' ),
				),
				'query-input' => 'required name=search_term_string',
			),
		);
	}

	private function breadcrumbs( $trail ) {
		$items = array();
		$position = 1;

		foreach ( $trail as $crumb ) {
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => $position++,
				'name'     => $crumb['name'],
				'item'     => $crumb['url'],
			);
		}

		return array(
			'@type'           => 'BreadcrumbList',
			'@id'             => $trail[ count( $trail ) - 1 ]['url'] . '#breadcrumb',
			'itemListElement' => $items,
		);
	}

	private function calculator_nodes( $config ) {
		$registry = Calculatorr_Registry::instance();
		$category = $registry->category( $config['category'] );
		$url      = Calculatorr_Pages::url_for( $config );
		$nodes    = array();

		$nodes[] = $this->breadcrumbs(
			array(
				array( 'name' => 'Home', 'url' => home_url( '/' ) ),
				array( 'name' => $category ? $category['h1'] : $config['category'], 'url' => Calculatorr_Pages::url_for_category( $category ) ),
				array( 'name' => $config['h1'], 'url' => $url ),
			)
		);

		$nodes[] = array(
			'@type'      => 'WebPage',
			'@id'        => $url . '#webpage',
			'url'        => $url,
			'name'       => $config['meta_title'],
			'description' => $config['meta_description'],
			'isPartOf'   => array( '@id' => $this->site_id() ),
			'breadcrumb' => array( '@id' => $url . '#breadcrumb' ),
			'inLanguage' => get_bloginfo( 'language' ),
		);

		/* SoftwareApplication rather than WebApplication: both are valid, but
		   Google documents the former and treats the latter as a subtype, so
		   the more widely parsed one is the safer choice. */
		$nodes[] = array(
			'@type'               => 'SoftwareApplication',
			'@id'                 => $url . '#tool',
			'name'                => $config['h1'],
			'url'                 => $url,
			'description'         => $config['meta_description'],
			'applicationCategory' => 'UtilitiesApplication',
			'operatingSystem'     => 'Any',
			'browserRequirements' => 'Requires JavaScript',
			'isAccessibleForFree' => true,
			'publisher'           => array( '@id' => $this->org_id() ),
			'offers'              => array(
				'@type'         => 'Offer',
				'price'         => '0',
				'priceCurrency' => 'USD',
			),
		);

		if ( ! empty( $config['faqs'] ) ) {
			$questions = array();

			foreach ( $config['faqs'] as $faq ) {
				$questions[] = array(
					'@type'          => 'Question',
					'name'           => wp_strip_all_tags( $faq['q'] ),
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => wp_strip_all_tags( $faq['a'] ),
					),
				);
			}

			$nodes[] = array(
				'@type'      => 'FAQPage',
				'@id'        => $url . '#faq',
				'mainEntity' => $questions,
			);
		}

		return $nodes;
	}

	private function category_nodes( $category ) {
		$registry = Calculatorr_Registry::instance();
		$url      = Calculatorr_Pages::url_for_category( $category );
		$items    = array();
		$position = 1;

		foreach ( $registry->in_category( $category['key'] ) as $config ) {
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => $position++,
				'name'     => $config['h1'],
				'url'      => Calculatorr_Pages::url_for( $config ),
			);
		}

		return array(
			$this->breadcrumbs(
				array(
					array( 'name' => 'Home', 'url' => home_url( '/' ) ),
					array( 'name' => $category['h1'], 'url' => $url ),
				)
			),
			array(
				'@type'       => 'CollectionPage',
				'@id'         => $url . '#webpage',
				'url'         => $url,
				'name'        => $category['meta_title'],
				'description' => $category['meta_description'],
				'isPartOf'    => array( '@id' => $this->site_id() ),
				'breadcrumb'  => array( '@id' => $url . '#breadcrumb' ),
				'inLanguage'  => get_bloginfo( 'language' ),
				'mainEntity'  => array(
					'@type'           => 'ItemList',
					'numberOfItems'   => count( $items ),
					'itemListElement' => $items,
				),
			),
		);
	}

	public function output() {
		$graph = array( $this->organization(), $this->website() );

		$config = Calculatorr_Pages::current();

		if ( $config ) {
			$graph = array_merge( $graph, $this->calculator_nodes( $config ) );
		} else {
			$category = Calculatorr_Pages::current_category();

			if ( $category ) {
				$graph = array_merge( $graph, $this->category_nodes( $category ) );
			} elseif ( ! is_front_page() ) {
				/* Organization and WebSite belong on the home page and on our
				   own pages. Emitting them across somebody else's blog posts
				   would just duplicate whatever their theme already outputs. */
				return;
			}
		}

		$payload = array(
			'@context' => 'https://schema.org',
			'@graph'   => $graph,
		);

		echo "\n" . '<script type="application/ld+json">' . wp_json_encode( $payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	}
}
