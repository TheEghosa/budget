<?php
/**
 * The three advertising slots, kept as filters rather than hard-coded markup.
 *
 * Nothing ships with ad code in it, so the slots render an empty reserved box
 * until someone hooks real creative onto them. Reserving the height up front
 * matters because a slot that grows when an advert loads shifts the page under
 * the reader's cursor, which Core Web Vitals measures and penalises.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Calculatorr_Ads {

	private static $instance = null;

	/**
	 * Slot name to reserved height in pixels. The desktop sizes come from the
	 * design: a leaderboard under the result, a rectangle mid-article and a
	 * sticky tower in the sidebar.
	 */
	private $slots = array(
		'after_calculator' => 90,
		'in_content'       => 280,
		'sidebar'          => 600,
	);

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {}

	public function slot( $name ) {
		if ( ! isset( $this->slots[ $name ] ) ) {
			return '';
		}

		/**
		 * Return ad markup here to fill the slot. Returning an empty string
		 * leaves the reserved space blank, which is the default so that a fresh
		 * install never renders an empty grey box to real visitors.
		 */
		$settings = Calculatorr_Settings::instance();

		if ( ! $settings->get( 'ads_enabled' ) ) {
			return '';
		}

		$stored = (string) $settings->get( 'ad_' . $name );
		$markup = apply_filters( 'calculatorr_ad_slot', $stored, $name );

		if ( '' === $markup && ! apply_filters( 'calculatorr_show_empty_ad_slots', false, $name ) ) {
			return '';
		}

		$height = (int) apply_filters( 'calculatorr_ad_slot_height', $this->slots[ $name ], $name );

		return sprintf(
			'<div class="calcr-ad calcr-ad--%1$s" style="min-height:%2$dpx" data-calcr-ad="%1$s">%3$s</div>',
			esc_attr( $name ),
			$height,
			$markup
		);
	}
}
