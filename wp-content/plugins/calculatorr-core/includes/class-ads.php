<?php
/**
 * The three advertising slots, and what fills them before there is any
 * advertising to put in.
 *
 * A slot resolves in one of three ways. If an administrator has pasted ad code
 * for it and advertising is switched on, that runs. Otherwise, if house promos
 * are enabled, the slot shows other calculators on the site. Failing both, it
 * renders nothing at all.
 *
 * The house promo exists because an empty slot is wasted space and a grey
 * placeholder is worse than wasted. AdSense approval needs content and traffic
 * first, so there is a period where the slots have nothing to show, and
 * filling them with real internal links makes that period useful instead of
 * merely tolerable.
 *
 * Deliberately links rather than a banner image: an image passes no anchor
 * text, cannot be read by a crawler, and costs a request. Six text links to
 * related tools do more for the site than a picture of them would.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Calculatorr_Ads {

	private static $instance = null;

	/**
	 * Slot name to the height it reserves, taken from the design. Reserving it
	 * whether or not anything loads is what stops a late advert shifting the
	 * page under the reader's cursor.
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

	/**
	 * @param string     $name   Slot name.
	 * @param array|null $config The calculator this slot sits on, so a house
	 *                           promo can pick genuinely related tools rather
	 *                           than the same list on every page.
	 */
	public function slot( $name, $config = null ) {
		if ( ! isset( $this->slots[ $name ] ) ) {
			return '';
		}

		$settings = Calculatorr_Settings::instance();
		$markup = '';
		$kind = '';

		if ( $settings->get( 'ads_enabled' ) ) {
			$markup = apply_filters( 'calculatorr_ad_slot', (string) $settings->get( 'ad_' . $name ), $name );
			$kind = 'paid';
		}

		if ( '' === trim( $markup ) && $settings->get( 'house_ads_enabled' ) ) {
			$markup = $this->house_promo( $name, $config );
			$kind = 'house';
		}

		if ( '' === trim( $markup ) ) {
			return '';
		}

		$height = (int) apply_filters( 'calculatorr_ad_slot_height', $this->slots[ $name ], $name );

		return sprintf(
			'<div class="calcr-ad calcr-ad--%1$s calcr-ad--%2$s" style="min-height:%3$dpx" data-calcr-ad="%1$s">%4$s</div>',
			esc_attr( $name ),
			esc_attr( $kind ),
			$height,
			$markup
		);
	}

	/**
	 * Which calculators to promote: the ones this page already names as
	 * related, then its category siblings, then anything. Never itself.
	 */
	private function promo_targets( $config, $limit ) {
		$registry = Calculatorr_Registry::instance();
		$picked = array();

		if ( $config ) {
			foreach ( (array) $config['related'] as $slug ) {
				$related = $registry->get_live( $slug );
				if ( $related && $related['slug'] !== $config['slug'] ) {
					$picked[ $related['slug'] ] = $related;
				}
			}

			foreach ( $registry->in_category( $config['category'], false ) as $slug => $sibling ) {
				if ( $slug !== $config['slug'] ) {
					$picked[ $slug ] = $sibling;
				}
			}
		}

		if ( count( $picked ) < $limit ) {
			foreach ( $registry->all( false ) as $slug => $any ) {
				if ( ! $config || $slug !== $config['slug'] ) {
					$picked[ $slug ] = $any;
				}
			}
		}

		return array_slice( $picked, 0, $limit, true );
	}

	private function house_promo( $name, $config ) {
		/* Each slot is a different shape, so each gets a different number of
		   links rather than the same block squeezed three ways. */
		$limits = array( 'after_calculator' => 4, 'in_content' => 4, 'sidebar' => 7 );
		$targets = $this->promo_targets( $config, $limits[ $name ] );

		if ( ! $targets ) {
			return '';
		}

		$category = $config ? Calculatorr_Registry::instance()->category( $config['category'] ) : null;
		$heading = $category ? 'More ' . strtolower( $category['name'] ) . ' tools' : 'More calculators';

		ob_start();
		?>
		<div class="calcr-house">
			<div class="calcr-house__head">
				<span class="calcr-house__mark" aria-hidden="true"></span>
				<span class="calcr-house__title"><?php echo esc_html( $heading ); ?></span>
			</div>
			<ul class="calcr-house__list">
				<?php foreach ( $targets as $target ) : ?>
					<li>
						<a href="<?php echo esc_url( Calculatorr_Pages::url_for( $target ) ); ?>">
							<span class="calcr-house__name"><?php echo esc_html( $target['h1'] ); ?></span>
							<span class="calcr-house__blurb"><?php echo esc_html( $target['description'] ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php if ( $category ) : ?>
				<?php $in_category = count( Calculatorr_Registry::instance()->in_category( $config['category'], false ) ); ?>
				<a class="calcr-house__all" href="<?php echo esc_url( Calculatorr_Pages::url_for_category( $category ) ); ?>">
					See all <?php echo (int) $in_category; ?>
				</a>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}
}
