<?php
/**
 * Structured data for calculator pages.
 *
 * Three graphs go out together: the breadcrumb so the category shows in the
 * result snippet, the FAQ so the questions can win their own place, and a
 * WebApplication node describing the tool itself. They are emitted as one
 * @graph rather than three separate blocks, since a single graph lets the
 * nodes reference each other without repeating the page URL each time.
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

	public function output() {
		$config = Calculatorr_Pages::current();

		if ( ! $config ) {
			return;
		}

		$registry = Calculatorr_Registry::instance();
		$category = $registry->category( $config['category'] );
		$url      = Calculatorr_Pages::url_for( $config );

		$graph = array();

		$graph[] = array(
			'@type'           => 'BreadcrumbList',
			'itemListElement' => array(
				array(
					'@type'    => 'ListItem',
					'position' => 1,
					'name'     => 'Home',
					'item'     => home_url( '/' ),
				),
				array(
					'@type'    => 'ListItem',
					'position' => 2,
					'name'     => $category ? $category['name'] : $config['category'],
					'item'     => home_url( '/' . $config['category'] . '/' ),
				),
				array(
					'@type'    => 'ListItem',
					'position' => 3,
					'name'     => $config['title'],
					'item'     => $url,
				),
			),
		);

		$graph[] = array(
			'@type'           => 'WebApplication',
			'name'            => $config['title'],
			'url'             => $url,
			'description'     => $config['description'],
			'applicationCategory' => 'UtilitiesApplication',
			'operatingSystem' => 'Any',
			'browserRequirements' => 'Requires JavaScript',
			'offers'          => array(
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
					'name'           => $faq['q'],
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => wp_strip_all_tags( $faq['a'] ),
					),
				);
			}

			$graph[] = array(
				'@type'      => 'FAQPage',
				'mainEntity' => $questions,
			);
		}

		$payload = array(
			'@context' => 'https://schema.org',
			'@graph'   => $graph,
		);

		echo "\n<script type=\"application/ld+json\">" . wp_json_encode( $payload ) . "</script>\n";
	}
}
