<?php
/**
 * The sanitisers that decide what explainer copy and questions are allowed to
 * look like.
 *
 * They used to sit inside the REST settings class, which was fine while the
 * content route was the only thing writing this text. A calculator defined
 * entirely in JSON arrives with an explainer and a set of questions of its own,
 * and it has to be held to exactly the same rules, so the rules moved here
 * rather than being copied into a second class where the two could drift.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Calculatorr_Content {

	public static function count_words( $config ) {
		$text = '';

		foreach ( (array) ( isset( $config['explainer'] ) ? $config['explainer'] : array() ) as $section ) {
			$text .= ' ' . ( isset( $section['body'] ) ? $section['body'] : '' );
			$text .= ' ' . ( isset( $section['example'] ) ? $section['example'] : '' );

			foreach ( (array) ( isset( $section['steps'] ) ? $section['steps'] : array() ) as $step ) {
				$text .= ' ' . $step;
			}
		}

		return str_word_count( wp_strip_all_tags( $text ) );
	}

	/**
	 * A section keeps a heading, a body, and whichever of the extras it
	 * supplied. Anything else is dropped rather than stored, because an
	 * unrecognised key is either a typo or a guess about a feature that does
	 * not exist, and neither should reach the page.
	 */
	public static function clean_explainer( $sections ) {
		if ( ! is_array( $sections ) ) {
			return new WP_Error( 'calculatorr_bad_explainer', 'explainer must be a list of sections.', array( 'status' => 400 ) );
		}

		$out = array();

		foreach ( $sections as $i => $section ) {
			if ( ! is_array( $section ) || empty( $section['heading'] ) || empty( $section['body'] ) ) {
				return new WP_Error(
					'calculatorr_bad_section',
					sprintf( 'Section %d needs a heading and a body.', (int) $i + 1 ),
					array( 'status' => 400 )
				);
			}

			$clean = array(
				'heading' => sanitize_text_field( $section['heading'] ),
				'body'    => wp_kses_post( $section['body'] ),
			);

			foreach ( array( 'formula', 'example' ) as $key ) {
				if ( ! empty( $section[ $key ] ) ) {
					$clean[ $key ] = wp_kses_post( $section[ $key ] );
				}
			}

			if ( ! empty( $section['steps'] ) && is_array( $section['steps'] ) ) {
				$clean['steps'] = array_values( array_filter( array_map( 'wp_kses_post', $section['steps'] ) ) );
			}

			if ( ! empty( $section['table'] ) ) {
				$table = self::clean_table( $section['table'], (int) $i + 1 );

				if ( is_wp_error( $table ) ) {
					return $table;
				}

				$clean['table'] = $table;
			}

			$out[] = $clean;
		}

		return $out;
	}

	/**
	 * Every row has to be as wide as the header. A short row renders as a
	 * table with a hole in it and a long one silently loses its last cell,
	 * and both are the sort of thing nobody notices until somebody reads the
	 * page.
	 */
	public static function clean_table( $table, $where ) {
		if ( ! is_array( $table ) || empty( $table['head'] ) || empty( $table['rows'] ) ) {
			return new WP_Error(
				'calculatorr_bad_table',
				sprintf( 'The table in section %d needs a head and some rows.', $where ),
				array( 'status' => 400 )
			);
		}

		$head  = array_values( array_map( 'sanitize_text_field', (array) $table['head'] ) );
		$width = count( $head );
		$rows  = array();

		foreach ( (array) $table['rows'] as $n => $row ) {
			$cells = array_values( array_map( 'wp_kses_post', (array) $row ) );

			if ( count( $cells ) !== $width ) {
				return new WP_Error(
					'calculatorr_ragged_table',
					sprintf(
						'Section %d, row %d has %d cells but the header has %d.',
						$where,
						(int) $n + 1,
						count( $cells ),
						$width
					),
					array( 'status' => 400 )
				);
			}

			$rows[] = $cells;
		}

		$clean = array( 'head' => $head, 'rows' => $rows );

		if ( ! empty( $table['caption'] ) ) {
			$clean['caption'] = sanitize_text_field( $table['caption'] );
		}

		return $clean;
	}

	public static function clean_faqs( $faqs ) {
		if ( ! is_array( $faqs ) ) {
			return new WP_Error( 'calculatorr_bad_faqs', 'faqs must be a list.', array( 'status' => 400 ) );
		}

		$out = array();

		foreach ( $faqs as $i => $faq ) {
			if ( ! is_array( $faq ) || empty( $faq['q'] ) || empty( $faq['a'] ) ) {
				return new WP_Error(
					'calculatorr_bad_faq',
					sprintf( 'Question %d needs a q and an a.', (int) $i + 1 ),
					array( 'status' => 400 )
				);
			}

			$out[] = array(
				'q' => sanitize_text_field( $faq['q'] ),
				'a' => wp_kses_post( $faq['a'] ),
			);
		}

		return $out;
	}
}
