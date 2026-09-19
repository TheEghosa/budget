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

	/**
	 * Calculators part-way through rendering, so one cannot re-enter itself
	 * through a shortcode in its own explainer copy.
	 *
	 * @var array
	 */
	private $rendering = array();

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_shortcode( 'calculatorr', array( $this, 'shortcode' ) );
		add_shortcode( 'calculatorr_category', array( $this, 'category_shortcode' ) );
	}

	/**
	 * [calculatorr slug="bmi-calculator"]
	 */
	public function shortcode( $atts ) {
		$atts = shortcode_atts( array( 'slug' => '' ), $atts, 'calculatorr' );
		$config = Calculatorr_Registry::instance()->get_live( $atts['slug'] );

		if ( ! $config ) {
			return '';
		}

		/*
		 * Explainer copy runs its shortcodes, so a page can carry another
		 * calculator inside its prose. That also means a calculator could name
		 * itself, directly or through a chain of two, and recurse until PHP
		 * ran out of stack, so a calculator already being rendered is refused
		 * rather than entered again.
		 */
		if ( isset( $this->rendering[ $config['slug'] ] ) ) {
			return '';
		}

		$this->rendering[ $config['slug'] ] = true;

		wp_enqueue_style( 'calculatorr-app' );
		wp_enqueue_script( 'calculatorr-app' );

		$settings = Calculatorr_Settings::instance();
		$config_js = array();

		if ( $settings->get( 'log_enabled' ) ) {
			$config_js['logUrl'] = rest_url( 'calculatorr/v1/log' );
		}

		if ( $settings->get( 'usage_enabled' ) ) {
			$config_js['usageUrl'] = rest_url( 'calculatorr/v1/usage' );
		}

		if ( $config_js ) {
			wp_localize_script( 'calculatorr-app', 'CalculatorrConfig', $config_js );
		}

		$html = $this->render_page( $config );

		unset( $this->rendering[ $config['slug'] ] );

		return $html;
	}

	/**
	 * [calculatorr_category key="home-diy"]
	 *
	 * The hub page for one use case. It exists to pass authority down to the
	 * calculators beneath it, so every entry is a real link with descriptive
	 * text rather than a card whose only anchor is the word "calculate".
	 */
	public function category_shortcode( $atts ) {
		$atts     = shortcode_atts( array( 'key' => '' ), $atts, 'calculatorr_category' );
		$registry = Calculatorr_Registry::instance();
		$category = $registry->category( $atts['key'] );

		if ( ! $category ) {
			return '';
		}

		wp_enqueue_style( 'calculatorr-app' );

		$calculators = $registry->in_category( $atts['key'], false );

		ob_start();
		?>
		<div class="calcr-hub">
			<p class="calcr-hub__intro"><?php echo esc_html( $category['meta_description'] ); ?></p>

			<div class="calcr-hub__grid">
				<?php foreach ( $calculators as $config ) : ?>
					<a class="calcr-hub__card" href="<?php echo esc_url( Calculatorr_Pages::url_for( $config ) ); ?>">
						<span class="calcr-hub__name"><?php echo esc_html( $config['h1'] ); ?></span>
						<span class="calcr-hub__blurb"><?php echo esc_html( $config['description'] ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>

			<?php echo Calculatorr_Ads::instance()->slot( 'after_calculator' ); ?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * The full page body: the tool, then the advertising, then the reading.
	 * That order is deliberate, since a visitor who cannot reach the calculator
	 * without scrolling past an advert tends to leave before they use either.
	 */
	public function render_page( $config ) {
		$settings = Calculatorr_Settings::instance();
		$ads = Calculatorr_Ads::instance();

		ob_start();
		?>
		<div class="calcr-page">
			<?php echo $this->render_breadcrumbs( $config ); ?>

			<?php if ( $settings->get( 'render_heading' ) || Calculatorr_Site_Chrome::heading_is_ours() ) : ?>
				<h1 class="calcr-page__title"><?php echo esc_html( $config['h1'] ); ?></h1>
				<p class="calcr-page__intro"><?php echo esc_html( $config['description'] ); ?></p>
			<?php endif; ?>

			<div class="calcr-layout">
				<div class="calcr-layout__main">
					<?php echo $this->render_widget( $config ); ?>
					<?php echo $ads->slot( 'after_calculator', $config ); ?>
					<?php echo $this->render_contents( $config ); ?>
					<?php echo $this->render_explainer( $config ); ?>
					<?php echo $ads->slot( 'in_content', $config ); ?>
					<?php echo $this->render_faqs( $config ); ?>
					<?php echo $this->render_sources( $config ); ?>
					<?php echo $this->render_related( $config ); ?>
				</div>

				<aside class="calcr-layout__side calcr-sidebar">
					<?php
					$sidebar = $ads->slot( 'sidebar', $config );
					echo $sidebar;

					/* The sidebar slot falls back to a house promo listing the
					   same category, so printing the more-in-category block
					   underneath it would name those calculators twice in one
					   column. The promo wins, because it carries the
					   descriptions as well as the links. */
					if ( false === strpos( $sidebar, 'calcr-ad--house' ) ) {
						echo $this->render_popular( $config );
					}
					?>
				</aside>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * The visible breadcrumb trail.
	 *
	 * The BreadcrumbList markup was already being emitted, but structured data
	 * describes a trail rather than creating one: without the links on the page
	 * there is nothing for a visitor to climb back up, and Google has said it
	 * uses the visible trail as well as the markup.
	 */
	public function render_breadcrumbs( $config ) {
		$category = Calculatorr_Registry::instance()->category( $config['category'] );

		ob_start();
		?>
		<nav class="calcr-breadcrumb" aria-label="Breadcrumb">
			<ol>
				<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
				<?php if ( $category ) : ?>
					<li><a href="<?php echo esc_url( Calculatorr_Pages::url_for_category( $category ) ); ?>"><?php echo esc_html( $category['h1'] ); ?></a></li>
				<?php endif; ?>
				<li><span aria-current="page"><?php echo esc_html( $config['h1'] ); ?></span></li>
			</ol>
		</nav>
		<?php
		return ob_get_clean();
	}

	/**
	 * Jump links to the sections below.
	 *
	 * Worth having for its own sake on a long page, and it is also what gives
	 * Google the anchors it uses to build the sub-links that sometimes appear
	 * under a result.
	 */
	private function render_contents( $config ) {
		$items = array();

		foreach ( $config['explainer'] as $i => $section ) {
			if ( ! empty( $section['heading'] ) ) {
				$items[ 'section-' . $i ] = $section['heading'];
			}
		}

		if ( ! empty( $config['faqs'] ) ) {
			$items['faq'] = 'Common questions';
		}

		/* Two entries is a list, not a table of contents. */
		if ( count( $items ) < 3 ) {
			return '';
		}

		ob_start();
		?>
		<nav class="calcr-toc" aria-label="On this page">
			<span class="calcr-toc__label">On this page</span>
			<ol>
				<?php foreach ( $items as $anchor => $label ) : ?>
					<li><a href="#<?php echo esc_attr( $anchor ); ?>"><?php echo esc_html( $label ); ?></a></li>
				<?php endforeach; ?>
			</ol>
		</nav>
		<?php
		return ob_get_clean();
	}

	/**
	 * The other calculators in the same category.
	 *
	 * This is the internal link that actually does work: a hub page passes
	 * authority down, and these pass it sideways between pages that are
	 * topically adjacent, which is what makes a category read as a cluster
	 * rather than as a pile of unrelated tools.
	 */
	private function render_popular( $config ) {
		$registry = Calculatorr_Registry::instance();
		$category = $registry->category( $config['category'] );
		$siblings = $registry->in_category( $config['category'], false );

		unset( $siblings[ $config['slug'] ] );

		if ( ! $siblings ) {
			return '';
		}

		$siblings = array_slice( $siblings, 0, 8, true );

		ob_start();
		?>
		<div class="calcr-popular">
			<h2 class="calcr-popular__title">More in <?php echo esc_html( $category ? $category['name'] : 'this category' ); ?></h2>
			<ul>
				<?php foreach ( $siblings as $sibling ) : ?>
					<li><a href="<?php echo esc_url( Calculatorr_Pages::url_for( $sibling ) ); ?>"><?php echo esc_html( $sibling['h1'] ); ?></a></li>
				<?php endforeach; ?>
			</ul>
			<?php if ( $category ) : ?>
				<a class="calcr-popular__all" href="<?php echo esc_url( Calculatorr_Pages::url_for_category( $category ) ); ?>">
					All <?php echo esc_html( strtolower( $category['name'] ) ); ?> calculators
				</a>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Where the figures came from.
	 *
	 * Constants like the weight of a cubic yard of gravel or the coefficients
	 * in a body fat formula are somebody else's published work, and saying
	 * whose is both honest and the clearest trust signal a page of this kind
	 * can carry.
	 */
	private function render_sources( $config ) {
		if ( empty( $config['sources'] ) ) {
			return '';
		}

		ob_start();
		?>
		<section class="calcr-sources">
			<h2>Where these figures come from</h2>
			<ul>
				<?php foreach ( $config['sources'] as $source ) : ?>
					<li>
						<?php if ( ! empty( $source['url'] ) ) : ?>
							<a href="<?php echo esc_url( $source['url'] ); ?>" rel="nofollow noopener" target="_blank"><?php echo esc_html( $source['name'] ); ?></a>
						<?php else : ?>
							<?php echo esc_html( $source['name'] ); ?>
						<?php endif; ?>
						<?php if ( ! empty( $source['detail'] ) ) : ?>
							<span class="calcr-sources__detail"><?php echo esc_html( $source['detail'] ); ?></span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</section>
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

		/*
		 * With empty start on, the fields open blank, so the panel has nothing
		 * to report yet and says so rather than showing a worked example the
		 * visitor did not ask for and would have to clear. The prompt is
		 * rendered on the server, not written in by script, so the panel is
		 * never briefly wrong while JavaScript loads.
		 */
		$empty_start = (bool) Calculatorr_Settings::instance()->get( 'empty_start' );
		$prompt      = 'Fill in the fields above and your answer appears here.';
		$shown       = $empty_start ? array( 'label' => isset( $result['label'] ) ? $result['label'] : 'Result' ) : $result;

		ob_start();
		?>
		<div class="calcr<?php echo $empty_start ? ' calcr--awaiting' : ''; ?>" data-calcr-slug="<?php echo esc_attr( $slug ); ?>"<?php echo $empty_start ? ' data-calcr-empty-start="1" data-calcr-prompt="' . esc_attr( $prompt ) . '"' : ''; ?>>
			<form class="calcr__form" novalidate>
				<?php
				foreach ( $config['fields'] as $field ) {
					echo $this->render_field( $slug, $field );
				}
				?>
				<div class="calcr__actions">
					<button type="button" class="calcr__btn calcr__btn--ghost" data-calcr-reset disabled>
						<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true" focusable="false"><path d="M3 12a9 9 0 1 0 3-6.7"></path><polyline points="3 4 3 10 9 10"></polyline></svg>
						Reset
					</button>

					<button type="button" class="calcr__btn calcr__btn--ghost" data-calcr-copy<?php echo $empty_start ? ' disabled' : ''; ?>>
						<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="9" y="9" width="12" height="12" rx="2"></rect><path d="M5 15H4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v1"></path></svg>
						<span data-calcr-copy-label>Copy result</span>
					</button>

					<?php if ( Calculatorr_Settings::instance()->get( 'share_enabled' ) ) : ?>
					<div class="calcr__share">
						<button type="button" class="calcr__btn calcr__btn--primary" data-calcr-share-toggle aria-expanded="false" aria-haspopup="true"<?php echo $empty_start ? ' disabled' : ''; ?>>
							<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.6" y1="10.5" x2="15.4" y2="6.5"></line><line x1="8.6" y1="13.5" x2="15.4" y2="17.5"></line></svg>
							Share
						</button>

						<div class="calcr__share-panel" data-calcr-share-panel hidden>
							<p class="calcr__share-title">Share this result</p>

							<div class="calcr__share-formats" role="group" aria-label="Image shape">
								<button type="button" class="calcr__share-format is-active" data-calcr-format="square" aria-pressed="true">Square</button>
								<button type="button" class="calcr__share-format" data-calcr-format="landscape" aria-pressed="false">Wide</button>
							</div>

							<div class="calcr__share-preview" data-calcr-preview></div>

							<button type="button" class="calcr__share-option" data-calcr-share-native>
								<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M4 12v7a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-7"></path><polyline points="8 8 12 4 16 8"></polyline><line x1="12" y1="4" x2="12" y2="15"></line></svg>
								<span data-calcr-label>Share with the image</span>
							</button>

							<button type="button" class="calcr__share-option" data-calcr-share-tweet>
								<svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M17.53 3h3.05l-6.66 7.61L21.75 21h-6.13l-4.8-6.28L5.32 21H2.27l7.12-8.14L2.25 3h6.29l4.34 5.74zm-1.07 16.17h1.69L7.62 4.73H5.81z"></path></svg>
								<span data-calcr-label>Post on X</span>
							</button>

							<button type="button" class="calcr__share-option" data-calcr-share-link>
								<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true" focusable="false"><path d="M10 13a5 5 0 0 0 7.07 0l2.12-2.12a5 5 0 0 0-7.07-7.07L10.7 5.2"></path><path d="M14 11a5 5 0 0 0-7.07 0L4.8 13.12a5 5 0 0 0 7.07 7.07l1.4-1.4"></path></svg>
								<span data-calcr-label>Copy link</span>
							</button>

							<button type="button" class="calcr__share-option" data-calcr-share-download>
								<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><path d="m3 17 5-4 4 3 3-2 6 5"></path></svg>
								<span data-calcr-label>Download image</span>
							</button>

							<p class="calcr__share-note">The link reopens this calculator with your figures already filled in.</p>
						</div>
					</div>
					<?php endif; ?>
				</div>
			</form>

			<output class="calcr__result" data-calcr-result aria-live="polite">
				<div class="calcr__primary">
					<span class="calcr__primary-label" data-calcr-primary-label><?php echo esc_html( isset( $shown['label'] ) ? $shown['label'] : 'Result' ); ?></span>
					<span class="calcr__primary-value" data-calcr-primary-value><?php echo esc_html( isset( $shown['value'] ) ? $shown['value'] : '—' ); ?></span>
					<?php
					/* The line under the number that says what it is an answer
					   to: "for 180 months on a $48,000 loan". Hidden until a
					   formula supplies one, because most do not. */
					$sub = isset( $shown['sub'] ) ? $shown['sub'] : '';
					?>
					<span class="calcr__primary-sub" data-calcr-primary-sub<?php echo '' === $sub ? ' hidden' : ''; ?>><?php echo esc_html( $sub ); ?></span>
				</div>

				<div class="calcr__bar" data-calcr-bar hidden></div>

				<ul class="calcr__rows" data-calcr-rows>
					<?php if ( ! empty( $shown['rows'] ) ) : ?>
						<?php foreach ( $shown['rows'] as $row ) : ?>
							<li class="calcr__row">
								<span class="calcr__row-label"><?php echo esc_html( $row['label'] ); ?></span>
								<span class="calcr__row-value"><?php echo esc_html( $row['value'] ); ?></span>
							</li>
						<?php endforeach; ?>
					<?php endif; ?>
				</ul>

				<?php $note = $empty_start ? $prompt : ( isset( $shown['note'] ) ? $shown['note'] : '' ); ?>
				<p class="calcr__note<?php echo $empty_start ? ' calcr__note--prompt' : ''; ?>" data-calcr-note<?php echo '' === $note ? ' hidden' : ''; ?>><?php echo esc_html( $note ); ?></p>
			</output>

			<?php if ( ! empty( $config['disclaimer'] ) ) : ?>
				<p class="calcr__disclaimer"><?php echo esc_html( $config['disclaimer'] ); ?></p>
			<?php endif; ?>

			<?php
			/*
			 * On a phone the inputs push the answer below the fold, so it
			 * follows the visitor up the page. It carries the live region
			 * while it is showing and the result panel gives its up, because
			 * two live regions announcing the same number is how a screen
			 * reader ends up reading every keystroke twice.
			 */
			?>
			<div class="calcr__sticky" data-calcr-sticky hidden>
				<div class="calcr__sticky-text">
					<span class="calcr__sticky-label" data-calcr-sticky-label><?php echo esc_html( isset( $shown['label'] ) ? $shown['label'] : 'Result' ); ?></span>
					<span class="calcr__sticky-value" data-calcr-sticky-value><?php echo esc_html( isset( $shown['value'] ) ? $shown['value'] : '—' ); ?></span>
				</div>
				<button type="button" class="calcr__sticky-btn" data-calcr-breakdown>
					Breakdown
					<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M12 5v14"></path><polyline points="6 13 12 19 18 13"></polyline></svg>
				</button>
			</div>
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
				'optional' => null,
				'show_when' => array(),
			)
		);

		$input_id = 'calcr-' . $slug . '-' . $field['id'];

		/*
		 * With empty start switched on, a field opens blank and shows its usual
		 * figure as a placeholder, so nobody has to clear somebody else's
		 * numbers before entering their own.
		 *
		 * The runtime then needs to know which blanks it is allowed to treat as
		 * nothing. A config can say so outright, and where it does not, a
		 * default of zero or nothing is taken as the config author saying this
		 * one can be left alone: a waste allowance, an extra discount, a count
		 * of holidays. Anything with a real figure behind it is the visitor's
		 * to supply before an answer means anything.
		 */
		$empty_start = (bool) Calculatorr_Settings::instance()->get( 'empty_start' );
		$optional    = null === $field['optional']
			? in_array( (string) $field['default'], array( '', '0' ), true )
			: (bool) $field['optional'];

		$starts_blank = $empty_start && in_array( $field['type'], array( 'number', 'text', 'date' ), true );
		$placeholder  = ( $starts_blank && '' !== (string) $field['default'] )
			? ' placeholder="' . esc_attr( $field['default'] ) . '"'
			: '';
		$required     = ( $starts_blank && ! $optional ) ? ' data-calcr-required="1"' : '';

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
					value="<?php echo $starts_blank ? '' : esc_attr( $field['default'] ); ?>"
					data-calcr-input="<?php echo esc_attr( $field['id'] ); ?>"<?php echo $placeholder . $required; ?>
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
		/* A repeater row is entirely the visitor's data, so it follows the same
		   rule as a single field: blank, with the usual figure as a hint. */
		$blank = (bool) Calculatorr_Settings::instance()->get( 'empty_start' );

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
							value="<?php echo $blank ? '' : esc_attr( $cell['default'] ); ?>"
							<?php echo ( $blank && '' !== (string) $cell['default'] ) ? ' placeholder="' . esc_attr( $cell['default'] ) . '"' : ''; ?>
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

		foreach ( $config['explainer'] as $i => $section ) {
			if ( ! empty( $section['heading'] ) ) {
				printf(
					'<h2 id="section-%d">%s</h2>',
					(int) $i,
					esc_html( $section['heading'] )
				);
			}

			if ( ! empty( $section['body'] ) ) {
				echo do_shortcode( wp_kses_post( wpautop( $section['body'] ) ) );
			}

			if ( ! empty( $section['formula'] ) ) {
				echo '<div class="calcr-formula">' . wp_kses_post( $section['formula'] ) . '</div>';
			}

			/* Numbered steps, because "how to calculate" is how people phrase
			   the query and an ordered list is what answers it. */
			if ( ! empty( $section['steps'] ) ) {
				echo '<ol class="calcr-steps">';
				foreach ( $section['steps'] as $step ) {
					echo '<li>' . wp_kses_post( $step ) . '</li>';
				}
				echo '</ol>';
			}

			/* A worked example with real numbers, which is the part people
			   scroll to and the part most calculator pages leave out. */
			if ( ! empty( $section['example'] ) ) {
				echo '<div class="calcr-example">';
				echo '<span class="calcr-example__label">Worked example</span>';
				echo do_shortcode( wp_kses_post( wpautop( $section['example'] ) ) );
				echo '</div>';
			}

			/* A reference table of common values. These earn featured
			   snippets and they are genuinely the thing a tradesperson or a
			   student wants to glance at without entering anything. */
			if ( ! empty( $section['table'] ) ) {
				$table = $section['table'];
				echo '<div class="calcr-reftable">';
				if ( ! empty( $table['caption'] ) ) {
					echo '<p class="calcr-reftable__caption">' . esc_html( $table['caption'] ) . '</p>';
				}
				echo '<table><thead><tr>';
				foreach ( $table['head'] as $cell ) {
					echo '<th>' . esc_html( $cell ) . '</th>';
				}
				echo '</tr></thead><tbody>';
				foreach ( $table['rows'] as $row ) {
					echo '<tr>';
					foreach ( $row as $cell ) {
						echo '<td>' . esc_html( $cell ) . '</td>';
					}
					echo '</tr>';
				}
				echo '</tbody></table></div>';
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
		<section class="calcr-faq" id="faq">
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
