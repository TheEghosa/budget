<?php
/**
 * Turns one calculator config into page markup.
 *
 * The form is rendered server side rather than by JavaScript, because real
 * inputs in the HTML source are faster to paint and safer to index than
 * controls a crawler only sees after running a script. JavaScript is left with
 * the one job it is actually needed for, which is recomputing the result.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Calculatorr_Renderer {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_shortcode( 'calculatorr', array( $this, 'shortcode' ) );
	}

	/**
	 * [calculatorr slug="bmi-calculator"]
	 */
	public function shortcode( $atts ) {
		$atts = shortcode_atts( array( 'slug' => '' ), $atts, 'calculatorr' );
		$config = Calculatorr_Registry::instance()->get( $atts['slug'] );

		if ( ! $config ) {
			return '';
		}

		wp_enqueue_style( 'calculatorr-app' );
		wp_enqueue_script( 'calculatorr-app' );

		return $this->render_page( $config );
	}

	/**
	 * The full page body: the tool, then the advertising, then the reading.
	 * That order is deliberate, since a visitor who cannot reach the calculator
	 * without scrolling past an advert tends to leave before they use either.
	 */
	public function render_page( $config ) {
		ob_start();
		?>
		<div class="calcr-page">
			<?php echo $this->render_widget( $config ); ?>
			<?php echo Calculatorr_Ads::instance()->slot( 'after_calculator' ); ?>
			<?php echo $this->render_explainer( $config ); ?>
			<?php echo Calculatorr_Ads::instance()->slot( 'in_content' ); ?>
			<?php echo $this->render_faqs( $config ); ?>
			<?php echo $this->render_related( $config ); ?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Just the calculator itself, which is what the Elementor widget drops in
	 * when someone wants to place a tool inside a page they have laid out by
	 * hand rather than use the generated page.
	 */
	public function render_widget( $config ) {
		$slug = $config['slug'];
		$result = isset( $config['default_result'] ) ? $config['default_result'] : array();

		ob_start();
		?>
		<div class="calcr" data-calcr-slug="<?php echo esc_attr( $slug ); ?>">
			<form class="calcr__form" novalidate>
				<?php
				foreach ( $config['fields'] as $field ) {
					echo $this->render_field( $slug, $field );
				}
				?>
				<div class="calcr__actions">
					<button type="button" class="calcr__btn calcr__btn--ghost" data-calcr-reset>Reset</button>
				</div>
			</form>

			<output class="calcr__result" data-calcr-result>
				<div class="calcr__primary">
					<span class="calcr__primary-label" data-calcr-primary-label><?php echo esc_html( isset( $result['label'] ) ? $result['label'] : 'Result' ); ?></span>
					<span class="calcr__primary-value" data-calcr-primary-value><?php echo esc_html( isset( $result['value'] ) ? $result['value'] : '—' ); ?></span>
				</div>

				<div class="calcr__bar" data-calcr-bar hidden></div>

				<ul class="calcr__rows" data-calcr-rows>
					<?php if ( ! empty( $result['rows'] ) ) : ?>
						<?php foreach ( $result['rows'] as $row ) : ?>
							<li class="calcr__row">
								<span class="calcr__row-label"><?php echo esc_html( $row['label'] ); ?></span>
								<span class="calcr__row-value"><?php echo esc_html( $row['value'] ); ?></span>
							</li>
						<?php endforeach; ?>
					<?php endif; ?>
				</ul>

				<p class="calcr__note" data-calcr-note<?php echo empty( $result['note'] ) ? ' hidden' : ''; ?>><?php echo esc_html( isset( $result['note'] ) ? $result['note'] : '' ); ?></p>
			</output>

			<?php if ( ! empty( $config['disclaimer'] ) ) : ?>
				<p class="calcr__disclaimer"><?php echo esc_html( $config['disclaimer'] ); ?></p>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	private function render_field( $slug, $field ) {
		$field = wp_parse_args(
			$field,
			array(
				'id'      => '',
				'label'   => '',
				'type'    => 'number',
				'default' => '',
				'prefix'  => '',
				'suffix'  => '',
				'hint'    => '',
				'options' => array(),
				'step'    => 'any',
				'min'     => null,
				'max'     => null,
				'show_when' => array(),
			)
		);

		$input_id = 'calcr-' . $slug . '-' . $field['id'];

		/* A field can declare which other field values it belongs to, so the
		   imperial inputs stay out of the way while metric is selected rather
		   than sitting there asking to be filled in twice. */
		$when_attr = empty( $field['show_when'] )
			? ''
			: " data-calcr-when='" . esc_attr( wp_json_encode( $field['show_when'] ) ) . "'";

		ob_start();

		if ( 'segmented' === $field['type'] ) {
			?>
			<div class="calcr-field"<?php echo $when_attr; ?>>
				<span class="calcr-field__label"><?php echo esc_html( $field['label'] ); ?></span>
				<div class="calcr-seg" role="group" aria-label="<?php echo esc_attr( $field['label'] ); ?>" data-calcr-seg="<?php echo esc_attr( $field['id'] ); ?>">
					<?php foreach ( $field['options'] as $value => $label ) : ?>
						<button type="button"
							class="calcr-seg__btn<?php echo ( (string) $value === (string) $field['default'] ) ? ' is-active' : ''; ?>"
							data-value="<?php echo esc_attr( $value ); ?>"
							aria-pressed="<?php echo ( (string) $value === (string) $field['default'] ) ? 'true' : 'false'; ?>"><?php echo esc_html( $label ); ?></button>
					<?php endforeach; ?>
				</div>
				<input type="hidden" name="<?php echo esc_attr( $field['id'] ); ?>" value="<?php echo esc_attr( $field['default'] ); ?>" data-calcr-input="<?php echo esc_attr( $field['id'] ); ?>">
				<?php if ( $field['hint'] ) : ?><span class="calcr-field__hint"><?php echo esc_html( $field['hint'] ); ?></span><?php endif; ?>
			</div>
			<?php
			return ob_get_clean();
		}

		if ( 'select' === $field['type'] ) {
			?>
			<div class="calcr-field"<?php echo $when_attr; ?>>
				<label class="calcr-field__label" for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
				<select id="<?php echo esc_attr( $input_id ); ?>" class="calcr-field__select" data-calcr-input="<?php echo esc_attr( $field['id'] ); ?>">
					<?php foreach ( $field['options'] as $value => $label ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>"<?php selected( (string) $value, (string) $field['default'] ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
				<?php if ( $field['hint'] ) : ?><span class="calcr-field__hint"><?php echo esc_html( $field['hint'] ); ?></span><?php endif; ?>
			</div>
			<?php
			return ob_get_clean();
		}

		if ( 'repeater' === $field['type'] ) {
			$rows = isset( $field['rows'] ) ? (int) $field['rows'] : 3;
			?>
			<div class="calcr-field calcr-field--repeater" data-calcr-repeater="<?php echo esc_attr( $field['id'] ); ?>"<?php echo $when_attr; ?>>
				<span class="calcr-field__label"><?php echo esc_html( $field['label'] ); ?></span>
				<div class="calcr-rep__list" data-calcr-rep-list>
					<?php for ( $i = 0; $i < $rows; $i++ ) : ?>
						<?php echo $this->render_repeater_row( $field, $i ); ?>
					<?php endfor; ?>
				</div>
				<button type="button" class="calcr__btn calcr__btn--ghost calcr-rep__add" data-calcr-rep-add>Add row</button>
			</div>
			<?php
			return ob_get_clean();
		}

		// Everything else is a single-line input: number, text or date.
		$type = in_array( $field['type'], array( 'number', 'text', 'date' ), true ) ? $field['type'] : 'text';
		?>
		<div class="calcr-field"<?php echo $when_attr; ?>>
			<label class="calcr-field__label" for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
			<div class="calcr-input">
				<?php if ( $field['prefix'] ) : ?><span class="calcr-input__affix"><?php echo esc_html( $field['prefix'] ); ?></span><?php endif; ?>
				<input
					id="<?php echo esc_attr( $input_id ); ?>"
					type="<?php echo esc_attr( $type ); ?>"
					class="calcr-input__control"
					value="<?php echo esc_attr( $field['default'] ); ?>"
					data-calcr-input="<?php echo esc_attr( $field['id'] ); ?>"
					<?php echo ( 'number' === $type ) ? ' inputmode="decimal" step="' . esc_attr( $field['step'] ) . '"' : ''; ?>
					<?php echo ( null !== $field['min'] ) ? ' min="' . esc_attr( $field['min'] ) . '"' : ''; ?>
					<?php echo ( null !== $field['max'] ) ? ' max="' . esc_attr( $field['max'] ) . '"' : ''; ?>>
				<?php if ( $field['suffix'] ) : ?><span class="calcr-input__affix"><?php echo esc_html( $field['suffix'] ); ?></span><?php endif; ?>
			</div>
			<?php if ( $field['hint'] ) : ?><span class="calcr-field__hint"><?php echo esc_html( $field['hint'] ); ?></span><?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	private function render_repeater_row( $field, $index ) {
		ob_start();
		?>
		<div class="calcr-rep__row" data-calcr-rep-row>
			<?php foreach ( $field['row'] as $cell ) : ?>
				<?php if ( 'select' === $cell['type'] ) : ?>
					<select class="calcr-field__select" data-calcr-rep-cell="<?php echo esc_attr( $cell['id'] ); ?>" aria-label="<?php echo esc_attr( $cell['label'] ); ?>">
						<?php foreach ( $cell['options'] as $value => $label ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>"<?php selected( (string) $value, (string) $cell['default'] ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				<?php else : ?>
					<div class="calcr-input">
						<input type="<?php echo esc_attr( $cell['type'] ); ?>" class="calcr-input__control"
							value="<?php echo esc_attr( $cell['default'] ); ?>"
							data-calcr-rep-cell="<?php echo esc_attr( $cell['id'] ); ?>"
							aria-label="<?php echo esc_attr( $cell['label'] ); ?>"
							<?php echo ( 'number' === $cell['type'] ) ? ' inputmode="decimal" step="any"' : ''; ?>>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
			<button type="button" class="calcr-rep__remove" data-calcr-rep-remove aria-label="Remove this row">&times;</button>
		</div>
		<?php
		return ob_get_clean();
	}

	private function render_explainer( $config ) {
		if ( empty( $config['explainer'] ) ) {
			return '';
		}

		ob_start();
		echo '<div class="calcr-prose">';

		foreach ( $config['explainer'] as $section ) {
			if ( ! empty( $section['heading'] ) ) {
				echo '<h2>' . esc_html( $section['heading'] ) . '</h2>';
			}
			if ( ! empty( $section['body'] ) ) {
				echo wp_kses_post( wpautop( $section['body'] ) );
			}
			if ( ! empty( $section['formula'] ) ) {
				echo '<div class="calcr-formula">' . wp_kses_post( $section['formula'] ) . '</div>';
			}
		}

		echo '</div>';
		return ob_get_clean();
	}

	private function render_faqs( $config ) {
		if ( empty( $config['faqs'] ) ) {
			return '';
		}

		ob_start();
		?>
		<section class="calcr-faq">
			<h2>Common questions</h2>
			<div class="calcr-faq__list">
				<?php foreach ( $config['faqs'] as $faq ) : ?>
					<details class="calcr-faq__item">
						<summary class="calcr-faq__q"><?php echo esc_html( $faq['q'] ); ?></summary>
						<div class="calcr-faq__a"><?php echo wp_kses_post( wpautop( $faq['a'] ) ); ?></div>
					</details>
				<?php endforeach; ?>
			</div>
		</section>
		<?php
		return ob_get_clean();
	}

	private function render_related( $config ) {
		if ( empty( $config['related'] ) ) {
			return '';
		}

		$registry = Calculatorr_Registry::instance();

		ob_start();
		?>
		<section class="calcr-related">
			<h2>Related calculators</h2>
			<div class="calcr-related__grid">
				<?php
				foreach ( $config['related'] as $slug ) :
					$related = $registry->get( $slug );
					if ( ! $related ) {
						continue;
					}
					$url = Calculatorr_Pages::url_for( $related );
					?>
					<a class="calcr-related__card" href="<?php echo esc_url( $url ); ?>">
						<span class="calcr-related__name"><?php echo esc_html( $related['title'] ); ?></span>
						<span class="calcr-related__blurb"><?php echo esc_html( $related['description'] ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		</section>
		<?php
		return ob_get_clean();
	}
}
