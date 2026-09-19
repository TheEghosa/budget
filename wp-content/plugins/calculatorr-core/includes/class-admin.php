<?php
/**
 * The control panel.
 *
 * Five screens behind one menu item: a dashboard that answers "is anything
 * broken", a calculator list that can switch any of them off or retitle it
 * live, the advertising slots, the plugin settings, and the error log.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Calculatorr_Admin {

	private static $instance = null;

	private $tabs = array(
		'dashboard'   => 'Dashboard',
		'calculators' => 'Calculators',
		'design'      => 'Design',
		'ads'         => 'Advertising',
		'settings'    => 'Settings',
		'log'         => 'Error log',
	);

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
		add_action( 'admin_post_calculatorr_sync', array( $this, 'handle_sync' ) );
		add_action( 'admin_post_calculatorr_save_settings', array( $this, 'handle_settings' ) );
		add_action( 'admin_post_calculatorr_save_calculators', array( $this, 'handle_calculators' ) );
		add_action( 'admin_post_calculatorr_save_override', array( $this, 'handle_override' ) );
		add_action( 'admin_post_calculatorr_clear_log', array( $this, 'handle_clear_log' ) );
	}

	public function menu() {
		$errors = Calculatorr_Error_Log::instance()->count();
		$label  = $errors ? sprintf( 'Calculatorr <span class="update-plugins count-%d"><span class="update-count">%d</span></span>', $errors, $errors ) : 'Calculatorr';

		add_menu_page( 'Calculatorr', $label, 'manage_options', 'calculatorr', array( $this, 'screen' ), 'dashicons-calculator', 58 );
	}

	public function assets( $hook ) {
		if ( false === strpos( $hook, 'calculatorr' ) ) {
			return;
		}

		wp_enqueue_style( 'calculatorr-admin', CALCULATORR_URL . 'assets/css/admin.css', array(), CALCULATORR_VERSION );
	}

	private function tab() {
		$tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'dashboard';
		return isset( $this->tabs[ $tab ] ) ? $tab : 'dashboard';
	}

	private function url( $tab ) {
		return admin_url( 'admin.php?page=calculatorr&tab=' . $tab );
	}

	/**
	 * An upgrade does not fire the activation hook, so an install that already
	 * had the plugin would otherwise never get the usage table. Checked once a
	 * day rather than on every admin page, because SHOW TABLES on every request
	 * to earn nothing is a query nobody asked for.
	 */
	private function ensure_usage_table() {
		if ( get_transient( 'calculatorr_usage_table' ) ) {
			return;
		}

		if ( ! Calculatorr_Usage::table_exists() ) {
			Calculatorr_Usage::install();
		}

		Calculatorr_Usage::prune();
		set_transient( 'calculatorr_usage_table', 1, DAY_IN_SECONDS );
	}

	public function screen() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$this->ensure_usage_table();

		$tab = $this->tab();
		$notice = get_transient( 'calculatorr_notice' );

		if ( $notice ) {
			delete_transient( 'calculatorr_notice' );
		}
		?>
		<div class="wrap calcr-admin">
			<h1 class="calcr-admin__title">
				<span class="calcr-admin__mark" aria-hidden="true"></span>
				Calculatorr
			</h1>

			<?php if ( $notice ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php echo esc_html( $notice ); ?></p></div>
			<?php endif; ?>

			<nav class="nav-tab-wrapper calcr-admin__tabs">
				<?php foreach ( $this->tabs as $key => $label ) : ?>
					<a class="nav-tab <?php echo $tab === $key ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( $this->url( $key ) ); ?>">
						<?php echo esc_html( $label ); ?>
						<?php if ( 'log' === $key && Calculatorr_Error_Log::instance()->count() ) : ?>
							<span class="calcr-admin__badge"><?php echo (int) Calculatorr_Error_Log::instance()->count(); ?></span>
						<?php endif; ?>
					</a>
				<?php endforeach; ?>
			</nav>

			<?php $this->{ 'render_' . $tab }(); ?>
		</div>
		<?php
	}

	/* ---------------------------------------------------------------- */

	private function render_dashboard() {
		$registry = Calculatorr_Registry::instance();
		$settings = Calculatorr_Settings::instance();
		$log      = Calculatorr_Error_Log::instance();

		$all        = $registry->all();
		$categories = $registry->categories();
		$disabled   = (array) $settings->get( 'disabled' );
		$active     = count( $all ) - count( array_intersect( array_keys( $all ), $disabled ) );

		$pages = 0;
		foreach ( $all as $slug => $config ) {
			if ( get_page_by_path( Calculatorr_Pages::path_for( $config ), OBJECT, 'page' ) ) {
				$pages++;
			}
		}

		$checks = $this->health_checks( $all, $pages );

		$tracking = (bool) $settings->get( 'usage_enabled' );
		$daily    = $tracking ? Calculatorr_Usage::daily( 30 ) : array();
		$ranked   = $tracking ? Calculatorr_Usage::by_slug( 30 ) : array();
		$sparks   = $tracking ? Calculatorr_Usage::sparklines( 30 ) : array();
		$trend    = $tracking ? Calculatorr_Usage::trend( 7 ) : array( 'now' => 0, 'before' => 0, 'change' => null );

		$month = array_sum( $daily );
		$used  = count( array_filter( $ranked ) );

		$delta = null;
		if ( null !== $trend['change'] ) {
			/* The sign is a separate string: number_format_i18n returns text,
			   and multiplying that by minus one turns the formatted figure back
			   into a bare number and throws the separators away. */
			$size  = abs( $trend['change'] );
			$delta = sprintf(
				'%s%s%% on the previous seven days',
				$trend['change'] >= 0 ? '+' : '-',
				number_format_i18n( $size, $size < 10 ? 1 : 0 )
			);
		}
		?>
		<div class="calcr-stats">
			<?php
			$this->stat( 'Calculations, 30 days', number_format_i18n( $month ), $tracking ? $used . ' of ' . count( $all ) . ' calculators used' : 'counting is switched off' );
			$this->stat( 'Last 7 days', number_format_i18n( $trend['now'] ), $delta ? $delta : 'no earlier week to compare' );
			$this->stat( 'Active calculators', $active, count( $all ) . ' registered' );
			$this->stat( 'Pages created', $pages, $pages < count( $all ) ? ( count( $all ) - $pages ) . ' still to create' : 'all present' );
			$this->stat( 'Logged errors', $log->count(), $log->count() ? count( $log->affected_slugs() ) . ' calculators affected' : 'nothing reported' );
			?>
		</div>

		<?php if ( ! $tracking ) : ?>
			<div class="calcr-panel calcr-panel--quiet">
				<h2>Usage counting is off</h2>
				<p>
					Turn it on under Settings and this screen fills in as people use the calculators. It records
					one hit per calculator per page load when a calculation actually runs, stores no visitor
					data and sets no cookie.
				</p>
			</div>
		<?php else : ?>
			<div class="calcr-panel">
				<h2>Calculations per day</h2>
				<p class="calcr-panel__note">
					A page view is not a use. This counts a calculation actually running, once per calculator per
					page load, so the line reflects people using the tools rather than arriving at them.
				</p>
				<?php echo Calculatorr_Chart::trend( $daily ); ?>
			</div>

			<div class="calcr-panel">
				<h2>Most used, last 30 days</h2>
				<?php
				$top = array();
				foreach ( array_slice( $ranked, 0, 12, true ) as $slug => $hits ) {
					$config = $registry->get( $slug );
					$top[ $config ? $config['h1'] : $slug ] = $hits;
				}
				echo Calculatorr_Chart::bars( $top );
				?>
			</div>

			<div class="calcr-panel">
				<h2>Every calculator</h2>
				<p class="calcr-panel__note">
					The same numbers as a table, sorted by use. The ones at the bottom are either genuinely
					niche or not being found, and the difference between those two is a question for Search
					Console rather than for this screen.
				</p>
				<table class="widefat striped calcr-usage">
					<thead>
						<tr>
							<th scope="col">Calculator</th>
							<th scope="col">Category</th>
							<th scope="col" class="calcr-usage__num">30 days</th>
							<th scope="col" class="calcr-usage__num">7 days</th>
							<th scope="col">Shape</th>
							<th scope="col"></th>
						</tr>
					</thead>
					<tbody>
					<?php
					/* Everything is listed, including the calculators with no
					   hits, because a zero is the most actionable number here
					   and hiding it would be flattering rather than useful. */
					$ordered = $ranked;
					foreach ( array_keys( $all ) as $slug ) {
						if ( ! isset( $ordered[ $slug ] ) ) {
							$ordered[ $slug ] = 0;
						}
					}

					foreach ( $ordered as $slug => $hits ) :
						$config = $registry->get( $slug );

						if ( ! $config ) {
							continue;
						}

						$category = $registry->category( $config['category'] );
						$spark    = isset( $sparks[ $slug ] ) ? $sparks[ $slug ] : Calculatorr_Usage::empty_days( 30 );
						$week     = array_sum( array_slice( $spark, -7 ) );
						?>
						<tr<?php echo $hits ? '' : ' class="calcr-usage__idle"'; ?>>
							<td><strong><?php echo esc_html( $config['h1'] ); ?></strong></td>
							<td><?php echo esc_html( $category ? $category['h1'] : $config['category'] ); ?></td>
							<td class="calcr-usage__num"><?php echo esc_html( number_format_i18n( $hits ) ); ?></td>
							<td class="calcr-usage__num"><?php echo esc_html( number_format_i18n( $week ) ); ?></td>
							<td class="calcr-usage__spark"><?php echo Calculatorr_Chart::sparkline( $spark ); ?></td>
							<td><a href="<?php echo esc_url( $this->url( 'calculators' ) . '&edit=' . rawurlencode( $slug ) ); ?>">Edit</a></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php endif; ?>

		<div class="calcr-panel">
			<h2>Health</h2>
			<table class="widefat calcr-checks">
				<tbody>
				<?php foreach ( $checks as $check ) : ?>
					<tr>
						<td class="calcr-checks__state calcr-checks__state--<?php echo esc_attr( $check['state'] ); ?>">
							<?php echo esc_html( 'ok' === $check['state'] ? 'OK' : ( 'warn' === $check['state'] ? 'Check' : 'Action' ) ); ?>
						</td>
						<td><strong><?php echo esc_html( $check['label'] ); ?></strong></td>
						<td><?php echo esc_html( $check['detail'] ); ?></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<div class="calcr-panel">
			<h2>Create the pages</h2>
			<p>
				Creates a WordPress page for every calculator that does not have one yet, under its category
				parent, so the URLs come from ordinary page hierarchy. It never touches a page that already
				exists, so it is safe to run as often as you like.
			</p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="calculatorr_sync">
				<?php wp_nonce_field( 'calculatorr_sync' ); ?>
				<?php submit_button( $pages ? 'Create the missing pages' : 'Create all pages', 'primary', 'submit', false ); ?>
			</form>
		</div>

		<div class="calcr-panel">
			<h2>By category</h2>
			<table class="widefat striped">
				<thead><tr><th>Category</th><th>URL</th><th>Calculators</th><th>Hub page</th></tr></thead>
				<tbody>
				<?php foreach ( $categories as $key => $category ) :
					$hub = get_page_by_path( $category['slug'], OBJECT, 'page' ); ?>
					<tr>
						<td><strong><?php echo esc_html( $category['name'] ); ?></strong></td>
						<td><code>/<?php echo esc_html( $category['slug'] ); ?>/</code></td>
						<td><?php echo count( $registry->in_category( $key ) ); ?></td>
						<td><?php echo $hub ? '<a href="' . esc_url( get_edit_post_link( $hub->ID ) ) . '">Edit</a>' : '<em>not created</em>'; ?></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	private function health_checks( $all, $pages ) {
		$settings = Calculatorr_Settings::instance();
		$checks   = array();

		$checks[] = array(
			'label'  => 'Calculator pages',
			'state'  => $pages >= count( $all ) ? 'ok' : ( $pages ? 'warn' : 'act' ),
			'detail' => $pages >= count( $all )
				? 'Every calculator has a page.'
				: sprintf( '%d of %d created. Run the sync below to create the rest.', $pages, count( $all ) ),
		);

		$defaults = file_exists( CALCULATORR_PATH . 'calculators/_defaults.php' );
		$checks[] = array(
			'label'  => 'Precomputed results',
			'state'  => $defaults ? 'ok' : 'warn',
			'detail' => $defaults
				? 'Present, so each page renders an answer before JavaScript runs.'
				: 'Missing. Pages will load with a blank result until the script runs, which weakens what search engines index.',
		);

		$seo_conflict = Calculatorr_SEO::instance()->is_deferring();
		$checks[] = array(
			'label'  => 'Meta tags',
			'state'  => 'ok',
			'detail' => $seo_conflict
				? 'Another SEO plugin is active, so Calculatorr is standing down and feeding it titles and descriptions instead.'
				: 'Calculatorr is writing the title, description, canonical and social tags.',
		);

		$checks[] = array(
			'label'  => 'Advertising',
			'state'  => $settings->get( 'ads_enabled' ) ? 'ok' : 'warn',
			'detail' => $settings->get( 'ads_enabled' )
				? 'Slots are live. Reserved heights keep the page from shifting as units load.'
				: 'Switched off, so no slots render at all. Turn it on once you have ad code to paste in.',
		);

		$errors = Calculatorr_Error_Log::instance();
		$checks[] = array(
			'label'  => 'Errors',
			'state'  => $errors->count() ? 'act' : 'ok',
			'detail' => $errors->count()
				? sprintf( '%d entries across %d calculators. Check the error log tab.', $errors->count(), count( $errors->affected_slugs() ) )
				: 'Nothing reported from the browser or from PHP.',
		);

		return $checks;
	}

	private function stat( $label, $value, $detail ) {
		?>
		<div class="calcr-stat">
			<span class="calcr-stat__value"><?php echo esc_html( $value ); ?></span>
			<span class="calcr-stat__label"><?php echo esc_html( $label ); ?></span>
			<span class="calcr-stat__detail"><?php echo esc_html( $detail ); ?></span>
		</div>
		<?php
	}

	/* ---------------------------------------------------------------- */

	private function render_calculators() {
		$registry = Calculatorr_Registry::instance();
		$settings = Calculatorr_Settings::instance();
		$disabled = (array) $settings->get( 'disabled' );
		$editing  = isset( $_GET['edit'] ) ? sanitize_key( wp_unslash( $_GET['edit'] ) ) : '';

		if ( $editing && $registry->get( $editing ) ) {
			$this->render_override_form( $registry->get( $editing ) );
			return;
		}

		$categories = $registry->categories();
		$filter = isset( $_GET['cat'] ) ? sanitize_key( wp_unslash( $_GET['cat'] ) ) : '';
		$list = $filter && isset( $categories[ $filter ] ) ? $registry->in_category( $filter ) : $registry->all();
		?>
		<div class="calcr-panel">
			<h2>Every calculator</h2>
			<p>
				Unticking a calculator hides it from visitors without deleting anything, which is the safe way
				to pull one that is wrong while you fix it. The title and description can be edited here too,
				and those changes take effect immediately without a deploy.
			</p>

			<p class="calcr-filter">
				<a class="<?php echo $filter ? '' : 'is-active'; ?>" href="<?php echo esc_url( $this->url( 'calculators' ) ); ?>">All (<?php echo count( $registry->all() ); ?>)</a>
				<?php foreach ( $categories as $key => $category ) : ?>
					<a class="<?php echo $filter === $key ? 'is-active' : ''; ?>" href="<?php echo esc_url( $this->url( 'calculators' ) . '&cat=' . $key ); ?>">
						<?php echo esc_html( $category['name'] ); ?> (<?php echo count( $registry->in_category( $key ) ); ?>)
					</a>
				<?php endforeach; ?>
			</p>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="calculatorr_save_calculators">
				<input type="hidden" name="scope" value="<?php echo esc_attr( $filter ); ?>">
				<?php wp_nonce_field( 'calculatorr_save_calculators' ); ?>

				<table class="widefat striped calcr-table">
					<thead>
						<tr>
							<th class="check-column">On</th>
							<th>Calculator</th>
							<th>URL</th>
							<th>Page</th>
							<th>Edited</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ( $list as $slug => $config ) :
						$path = Calculatorr_Pages::path_for( $config );
						$page = get_page_by_path( $path, OBJECT, 'page' );
						$override = $settings->overrides( $slug ); ?>
						<tr>
							<td class="check-column">
								<input type="checkbox" name="enabled[]" value="<?php echo esc_attr( $slug ); ?>"
									<?php checked( ! in_array( $slug, $disabled, true ) ); ?>>
							</td>
							<td><strong><?php echo esc_html( $config['h1'] ); ?></strong></td>
							<td><code>/<?php echo esc_html( $path ); ?>/</code></td>
							<td><?php echo $page ? '<a href="' . esc_url( get_permalink( $page ) ) . '" target="_blank" rel="noopener">View</a>' : '<em>none</em>'; ?></td>
							<td><?php echo $override ? esc_html( implode( ', ', array_keys( $override ) ) ) : '&mdash;'; ?></td>
							<td><a href="<?php echo esc_url( $this->url( 'calculators' ) . '&edit=' . $slug ); ?>">Edit text</a></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>

				<?php submit_button( 'Save which calculators are live' ); ?>
			</form>
		</div>
		<?php
	}

	private function render_override_form( $config ) {
		$override = Calculatorr_Settings::instance()->overrides( $config['slug'] );
		$value = function ( $key ) use ( $override, $config ) {
			if ( isset( $override[ $key ] ) ) {
				return $override[ $key ];
			}

			return isset( $config[ $key ] ) ? $config[ $key ] : '';
		};

		/* The list fields need the same fallback but must always come back as
		   an array, because a config that has never carried one returns null
		   and foreach over null is a warning on every page load. */
		$value_list = function ( $key ) use ( $override, $config ) {
			if ( isset( $override[ $key ] ) ) {
				return (array) $override[ $key ];
			}

			return isset( $config[ $key ] ) ? (array) $config[ $key ] : array();
		};
		?>
		<div class="calcr-panel">
			<h2>Edit <?php echo esc_html( $config['h1'] ); ?></h2>
			<p>
				These override the config file without changing it. Clearing a field returns that item to
				whatever the file says, so nothing is ever lost by experimenting here.
			</p>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="calculatorr_save_override">
				<input type="hidden" name="slug" value="<?php echo esc_attr( $config['slug'] ); ?>">
				<?php wp_nonce_field( 'calculatorr_save_override' ); ?>

				<table class="form-table">
					<tr>
						<th><label for="ov-h1">Heading (H1)</label></th>
						<td><input id="ov-h1" name="h1" type="text" class="regular-text" value="<?php echo esc_attr( $value( 'h1' ) ); ?>"></td>
					</tr>
					<tr>
						<th><label for="ov-title">Title tag</label></th>
						<td>
							<input id="ov-title" name="meta_title" type="text" class="large-text" value="<?php echo esc_attr( $value( 'meta_title' ) ); ?>">
							<p class="description">Keep it to 60 characters or Google truncates it. Currently <?php echo strlen( $value( 'meta_title' ) ); ?>.</p>
						</td>
					</tr>
					<tr>
						<th><label for="ov-desc">Meta description</label></th>
						<td>
							<textarea id="ov-desc" name="meta_description" rows="3" class="large-text"><?php echo esc_textarea( $value( 'meta_description' ) ); ?></textarea>
							<p class="description">Between 120 and 160 characters. Currently <?php echo strlen( $value( 'meta_description' ) ); ?>.</p>
						</td>
					</tr>
					<tr>
						<th><label for="ov-blurb">Short description</label></th>
						<td>
							<input id="ov-blurb" name="description" type="text" class="large-text" value="<?php echo esc_attr( $value( 'description' ) ); ?>">
							<p class="description">Shown on the category hub and in related links.</p>
						</td>
					</tr>
					<tr>
						<th><label for="ov-keyword">Primary keyword</label></th>
						<td>
							<input id="ov-keyword" name="keyword" type="text" class="regular-text" value="<?php echo esc_attr( $value( 'keyword' ) ); ?>">
							<p class="description">
								The phrase this page is built to win. It is the fallback for the heading and the
								title tag, so changing it here changes what the page claims to be about.
							</p>
						</td>
					</tr>
					<tr>
						<th><label for="ov-keywords">Secondary keywords</label></th>
						<td>
							<?php $tags = (array) $value_list( 'keywords' ); ?>
							<input id="ov-keywords" name="keywords" type="text" class="large-text" value="<?php echo esc_attr( implode( ', ', $tags ) ); ?>">
							<p class="description">
								Comma separated. These are for your own targeting and reporting rather than for
								the page: a meta keywords tag has been ignored by every search engine that
								matters for well over a decade, so writing them into one would be theatre.
								Where they earn their keep is telling you what this page is supposed to cover
								when you come back to write the copy.
							</p>
							<?php if ( $tags ) : ?>
								<p class="calcr-tags">
									<?php foreach ( $tags as $tag ) : ?>
										<span class="calcr-tag"><?php echo esc_html( $tag ); ?></span>
									<?php endforeach; ?>
								</p>
							<?php endif; ?>
						</td>
					</tr>
				</table>

				<h3>Long description</h3>
				<p class="description calcr-panel__note">
					The sections below the calculator. This is where the depth that earns a ranking lives, so
					it is the part worth spending time on. Leave a heading empty to drop that section. Anything
					richer that the config file carries for a section, a numbered list of steps or a reference
					table, is kept as it is and is not shown here, so editing the text cannot quietly delete it.
				</p>

				<?php
				$sections = (array) $value_list( 'explainer' );
				/* Two spare slots so adding a section needs no button and no
				   JavaScript: the empty ones are simply dropped on save. */
				$sections[] = array();
				$sections[] = array();

				foreach ( $sections as $i => $section ) :
					$carried = $section;
					unset( $carried['heading'], $carried['body'], $carried['formula'] );
					?>
					<div class="calcr-section">
						<p>
							<label>
								<strong>Section <?php echo (int) ( $i + 1 ); ?> heading</strong>
								<input type="text" class="large-text" name="explainer[<?php echo (int) $i; ?>][heading]"
									value="<?php echo esc_attr( isset( $section['heading'] ) ? $section['heading'] : '' ); ?>">
							</label>
						</p>
						<p>
							<label>
								Body
								<textarea name="explainer[<?php echo (int) $i; ?>][body]" rows="5" class="large-text"><?php echo esc_textarea( isset( $section['body'] ) ? $section['body'] : '' ); ?></textarea>
							</label>
						</p>
						<p>
							<label>
								Formula <span class="description">(optional, shown in a box)</span>
								<input type="text" class="large-text code" name="explainer[<?php echo (int) $i; ?>][formula]"
									value="<?php echo esc_attr( isset( $section['formula'] ) ? $section['formula'] : '' ); ?>">
							</label>
						</p>
						<?php if ( $carried ) : ?>
							<p class="description">
								Also carries: <?php echo esc_html( implode( ', ', array_keys( $carried ) ) ); ?>. Kept as it is.
							</p>
							<input type="hidden" name="explainer[<?php echo (int) $i; ?>][carried]"
								value="<?php echo esc_attr( wp_json_encode( $carried ) ); ?>">
						<?php endif; ?>
					</div>
				<?php endforeach; ?>

				<h3>Common questions</h3>
				<p class="description calcr-panel__note">
					These are published as FAQ structured data as well as on the page, so a question with a
					straight answer under it can win the answer box. Leave a question empty to drop the pair.
				</p>

				<?php
				$faqs = (array) $value_list( 'faqs' );
				$faqs[] = array();
				$faqs[] = array();

				foreach ( $faqs as $i => $faq ) : ?>
					<div class="calcr-section calcr-section--faq">
						<p>
							<label>
								<strong>Question <?php echo (int) ( $i + 1 ); ?></strong>
								<input type="text" class="large-text" name="faqs[<?php echo (int) $i; ?>][q]"
									value="<?php echo esc_attr( isset( $faq['q'] ) ? $faq['q'] : '' ); ?>">
							</label>
						</p>
						<p>
							<label>
								Answer
								<textarea name="faqs[<?php echo (int) $i; ?>][a]" rows="4" class="large-text"><?php echo esc_textarea( isset( $faq['a'] ) ? $faq['a'] : '' ); ?></textarea>
							</label>
						</p>
					</div>
				<?php endforeach; ?>

				<?php submit_button( 'Save' ); ?>
				<a class="button" href="<?php echo esc_url( $this->url( 'calculators' ) ); ?>">Back to the list</a>
			</form>
		</div>
		<?php
	}

	/* ---------------------------------------------------------------- */

	/**
	 * The design screen.
	 *
	 * Everything on it is a stored value that is written into the page as a
	 * custom property override, which is why a change here reaches all 118
	 * pages on the next request with no packaging step and no upload. Fields
	 * left at their default emit nothing at all, so an untouched install runs
	 * exactly as the stylesheet ships.
	 */
	private function render_design() {
		$design = Calculatorr_Design::get();
		$defaults = Calculatorr_Design::defaults();
		?>
		<div class="calcr-panel">
			<h2>Design</h2>
			<p>
				The palette, the type and the spacing, as settings rather than as lines in a stylesheet. Every
				colour on the site comes from one named property, so changing it here changes it everywhere:
				the calculators, the category hubs, the homepage, the header and the footer, in one save.
			</p>
			<p>
				Only the values that define the brand are here, because the rest of the palette is derived from
				them. Anything this screen does not cover belongs in the custom CSS box at the bottom, which is
				loaded after everything else and therefore wins.
			</p>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="calculatorr_save_settings">
				<input type="hidden" name="section" value="design">
				<?php wp_nonce_field( 'calculatorr_save_settings' ); ?>

				<?php foreach ( Calculatorr_Design::schema() as $group => $spec ) : ?>
					<h3><?php echo esc_html( $spec['label'] ); ?></h3>
					<table class="form-table calcr-design__table" role="presentation">
						<tbody>
						<?php foreach ( $spec['fields'] as $key => $field ) : ?>
							<?php
							$name    = $group . '_' . $key;
							$value   = isset( $design[ $name ] ) ? $design[ $name ] : $field[3];
							$changed = $value !== $defaults[ $name ];
							?>
							<tr>
								<th scope="row"><label for="calcr-design-<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $field[0] ); ?></label></th>
								<td>
									<?php if ( 'color' === $field[2] ) : ?>
										<input type="color" value="<?php echo esc_attr( preg_match( '/^#[0-9a-f]{6}$/i', $value ) ? $value : '#000000' ); ?>"
											data-calcr-colour-for="calcr-design-<?php echo esc_attr( $name ); ?>" aria-hidden="true" tabindex="-1">
									<?php endif; ?>
									<input type="text" id="calcr-design-<?php echo esc_attr( $name ); ?>"
										name="design[<?php echo esc_attr( $name ); ?>]"
										value="<?php echo esc_attr( $value ); ?>"
										class="regular-text code" spellcheck="false">
									<?php if ( $changed ) : ?>
										<span class="description">Default <code><?php echo esc_html( $defaults[ $name ] ); ?></code></span>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				<?php endforeach; ?>

				<h3>Webfont</h3>
				<p class="description">
					The stylesheet the fonts are loaded from. Changing the font names above without changing this
					gives you a font the browser has not been sent, so the two move together. Leave it empty to
					load no webfont at all and use whatever the visitor already has.
				</p>
				<input type="url" name="design[fonts_url]" value="<?php echo esc_attr( $design['fonts_url'] ); ?>" class="large-text code" spellcheck="false">

				<h3>Custom CSS</h3>
				<p class="description">
					Loaded after every other stylesheet, so it wins without needing !important. Use it for anything
					this screen does not cover. It is stored as written apart from angle brackets, which are
					removed because a stylesheet that can close its own tag stops being a stylesheet.
				</p>
				<textarea name="design[custom_css]" rows="10" class="large-text code" spellcheck="false"><?php echo esc_textarea( $design['custom_css'] ); ?></textarea>

				<p class="submit">
					<button type="submit" class="button button-primary">Save design</button>
					<button type="submit" name="reset_design" value="1" class="button">Reset to defaults</button>
				</p>
			</form>
		</div>

		<script>
		/* The colour swatch is a convenience beside the text field rather than
		   a replacement for it, because the text field also accepts rgb() and
		   hsl(), which a native colour input cannot express. */
		document.querySelectorAll( '[data-calcr-colour-for]' ).forEach( function ( swatch ) {
			var field = document.getElementById( swatch.getAttribute( 'data-calcr-colour-for' ) );
			if ( ! field ) { return; }
			swatch.addEventListener( 'input', function () { field.value = swatch.value; } );
			field.addEventListener( 'input', function () {
				if ( /^#[0-9a-f]{6}$/i.test( field.value.trim() ) ) { swatch.value = field.value.trim(); }
			} );
		} );
		</script>
		<?php
	}

	private function render_ads() {
		$settings = Calculatorr_Settings::instance();
		$slots = array(
			'ad_after_calculator' => array( 'Below the result', '728 x 90 on desktop, 300 x 250 on mobile. The first slot a visitor meets, and it sits under the answer rather than above it.' ),
			'ad_in_content'       => array( 'Mid-article', '336 x 280. After the explanation, where the reader is browsing rather than working.' ),
			'ad_sidebar'          => array( 'Sidebar', '300 x 600, sticky on desktop and removed entirely below 1024px rather than stacked into the content.' ),
		);
		?>
		<div class="calcr-panel">
			<h2>Advertising slots</h2>
			<p>
				Paste your ad code into a slot to fill it. Each slot reserves its height whether or not it has
				code in it, so a unit loading late cannot shift the page under the reader&rsquo;s cursor, which is
				what Core Web Vitals measures and penalises.
			</p>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="calculatorr_save_settings">
				<input type="hidden" name="section" value="ads">
				<?php wp_nonce_field( 'calculatorr_save_settings' ); ?>

				<p>
					<label>
						<input type="checkbox" name="ads_enabled" value="1" <?php checked( $settings->get( 'ads_enabled' ) ); ?>>
						<strong>Run the ad code below</strong>
					</label>
					<span class="description">Leave this off until AdSense has approved the site. Nothing breaks either way.</span>
				</p>

				<p>
					<label>
						<input type="checkbox" name="house_ads_enabled" value="1" <?php checked( $settings->get( 'house_ads_enabled' ) ); ?>>
						<strong>Fill empty slots with your own calculators</strong>
					</label>
					<span class="description">
						A slot with no ad code in it promotes related tools instead of sitting empty. They are
						real links rather than a banner image, so they pass anchor text and help the pages rank,
						which a picture would not. Paid ad code always takes precedence where you have pasted it.
					</span>
				</p>

				<?php foreach ( $slots as $key => $slot ) : ?>
					<h3><?php echo esc_html( $slot[0] ); ?></h3>
					<p class="description"><?php echo esc_html( $slot[1] ); ?></p>
					<textarea name="<?php echo esc_attr( $key ); ?>" rows="4" class="large-text code"><?php echo esc_textarea( $settings->get( $key ) ); ?></textarea>
				<?php endforeach; ?>

				<hr>

				<h2>Site-wide code</h2>
				<p>
					The slots above are where an ad unit is drawn. These three are where the code that makes
					units work at all has to live, which is a different job: AdSense serves nothing until its
					loader script is in the head of every page, and the same goes for Search Console
					verification and most analytics.
				</p>
				<p>
					It is printed exactly as you paste it, because an ad tag is script by nature and sanitising
					it would break every network there is. Only administrators can reach this screen, which is
					the same trust WordPress already extends to the theme editor. Paste from the source, not
					from a forum post.
				</p>

				<p>
					<label>
						<input type="checkbox" name="head_footer_enabled" value="1" <?php checked( $settings->get( 'head_footer_enabled' ) ); ?>>
						<strong>Output this code on the front end</strong>
					</label>
					<span class="description">Never runs in the admin or on feeds, where an ad loader has nothing to attach to.</span>
				</p>

				<?php
				$code_fields = array(
					'code_head'   => array( 'Head', 'Goes just before &lt;/head&gt; on every page. The AdSense loader belongs here, along with Search Console and Bing verification tags.' ),
					'code_body'   => array( 'After the opening body tag', 'For anything that has to run before the content, such as Google Tag Manager&rsquo;s noscript fallback.' ),
					'code_footer' => array( 'Footer', 'Goes just before &lt;/body&gt;. Anything that does not need to block rendering is better here, because a script in the head delays the page for every visitor.' ),
				);

				foreach ( $code_fields as $key => $field ) : ?>
					<h3><?php echo esc_html( $field[0] ); ?></h3>
					<p class="description"><?php echo wp_kses_post( $field[1] ); ?></p>
					<textarea name="<?php echo esc_attr( $key ); ?>" rows="5" class="large-text code" spellcheck="false"><?php echo esc_textarea( $settings->get( $key ) ); ?></textarea>
				<?php endforeach; ?>

				<p class="description">
					AdSense also wants an <code>ads.txt</code> file at the root of the domain with your publisher
					ID in it. That is a real file rather than a script, so it cannot live in a text box; AdSense
					gives you the exact line once your account exists.
				</p>

				<?php submit_button( 'Save advertising and code' ); ?>
			</form>
		</div>
		<?php
	}

private function render_settings() {
		$settings = Calculatorr_Settings::instance();
		$toggles = array(
			'seo_enabled'    => array( 'Write meta tags', 'Title, description, canonical and social tags. Turn off if another SEO plugin should own them, though Calculatorr detects the common ones and stands down automatically.' ),
			'schema_enabled' => array( 'Output structured data', 'Organization, WebSite, breadcrumbs, the tool itself and the FAQs, as one connected JSON-LD graph.' ),
			'share_enabled'  => array( 'Show the share button', 'The share panel with the rendered snapshot, the X post and the copyable link.' ),
			'load_fonts'     => array( 'Load Space Grotesk and Source Sans 3', 'Turn off if your theme already loads them or if you self-host, which is faster and better for privacy.' ),
			'site_chrome' => array( 'Style the theme header and footer', 'Loads one small stylesheet on every page so the site header, navigation and footer carry the calculatorr design. Turn it off if you build the header and footer yourself.' ),
			'theme_switch' => array( 'Offer a light and dark switch', 'Adds a control to the end of the header menu so a visitor can choose, and remembers the choice. With this off the palette still follows the visitor\'s system setting, they just cannot override it.' ),
			'empty_start' => array( 'Start calculators empty', 'Fields open blank with the usual figure shown as a placeholder, so nobody has to clear somebody else\'s numbers before entering their own. Turn it off to prefill every field with a worked example instead.' ),
			'usage_enabled' => array( 'Count which calculators get used', 'Records one hit per calculator per page load when a calculation actually runs, which is what the dashboard charts. It stores no visitor data and sets no cookie, so it needs no consent banner of its own.' ),
			'render_heading' => array( 'Print the heading and intro', 'Most themes already output the page title as the H1, so this is off by default to avoid two of them. Turn it on if your theme does not, or if the theme heading does not match the calculator title.' ),
			'log_enabled'    => array( 'Collect errors', 'Records JavaScript errors reported by visitors and PHP problems inside the plugin. Worth leaving on: a formula that breaks on a phone leaves no trace on the server otherwise.' ),
		);
		?>
		<div class="calcr-panel">
			<h2>Settings</h2>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="calculatorr_save_settings">
				<input type="hidden" name="section" value="settings">
				<?php wp_nonce_field( 'calculatorr_save_settings' ); ?>

				<table class="form-table">
					<?php foreach ( $toggles as $key => $toggle ) : ?>
						<tr>
							<th scope="row"><?php echo esc_html( $toggle[0] ); ?></th>
							<td>
								<label>
									<input type="checkbox" name="<?php echo esc_attr( $key ); ?>" value="1" <?php checked( $settings->get( $key ) ); ?>>
									Enabled
								</label>
								<p class="description"><?php echo esc_html( $toggle[1] ); ?></p>
							</td>
						</tr>
					<?php endforeach; ?>
					<tr>
						<th scope="row"><label for="log-limit">Error log size</label></th>
						<td>
							<input id="log-limit" name="log_limit" type="number" min="20" max="2000" value="<?php echo (int) $settings->get( 'log_limit' ); ?>" class="small-text">
							<p class="description">Entries kept before the oldest are discarded. Identical errors are counted rather than repeated, so this goes a long way.</p>
						</td>
					</tr>
				</table>

				<?php submit_button( 'Save settings' ); ?>
			</form>
		</div>
		<?php
	}

	private function render_log() {
		$log = Calculatorr_Error_Log::instance();
		$entries = $log->entries();
		?>
		<div class="calcr-panel">
			<h2>Error log</h2>
			<p>
				JavaScript errors reported by visitors&rsquo; browsers, and PHP problems raised inside the plugin.
				The browser half is the one that normally goes unseen, because a formula that throws on somebody&rsquo;s
				phone leaves no trace on the server and they simply see a stale answer and leave.
			</p>

			<?php if ( ! $entries ) : ?>
				<p class="calcr-empty">Nothing logged. That is the result you want.</p>
			<?php else : ?>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-bottom:16px">
					<input type="hidden" name="action" value="calculatorr_clear_log">
					<?php wp_nonce_field( 'calculatorr_clear_log' ); ?>
					<?php submit_button( 'Clear the log', 'secondary', 'submit', false ); ?>
				</form>

				<table class="widefat striped">
					<thead><tr><th>When</th><th>Source</th><th>Calculator</th><th>Message</th><th>Seen</th></tr></thead>
					<tbody>
					<?php foreach ( $entries as $entry ) : ?>
						<tr>
							<td><?php echo esc_html( human_time_diff( $entry['time'] ) . ' ago' ); ?></td>
							<td><code><?php echo esc_html( $entry['source'] ); ?></code></td>
							<td><?php echo $entry['slug'] ? '<code>' . esc_html( $entry['slug'] ) . '</code>' : '&mdash;'; ?></td>
							<td>
								<?php echo esc_html( $entry['message'] ); ?>
								<?php if ( $entry['context'] ) : ?>
									<br><span class="description"><?php echo esc_html( $entry['context'] ); ?></span>
								<?php endif; ?>
							</td>
							<td><?php echo (int) $entry['count']; ?>x</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
		<?php
	}

	/* ---------------------------------------------------------------- */

	private function finish( $message, $tab ) {
		set_transient( 'calculatorr_notice', $message, 60 );
		wp_safe_redirect( $this->url( $tab ) );
		exit;
	}

	private function guard( $nonce ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'You do not have permission to do that.' );
		}
		check_admin_referer( $nonce );
	}

	public function handle_sync() {
		$this->guard( 'calculatorr_sync' );
		$report = Calculatorr_Pages::sync();
		$this->finish(
			sprintf(
				'Created %d category pages and %d calculator pages. %d already existed and were left alone.',
				$report['created_categories'], $report['created_calculators'], $report['skipped']
			),
			'dashboard'
		);
	}

	public function handle_settings() {
		$this->guard( 'calculatorr_save_settings' );

		$section = isset( $_POST['section'] ) ? sanitize_key( wp_unslash( $_POST['section'] ) ) : 'settings';
		$settings = Calculatorr_Settings::instance();
		$values = $settings->all();

		if ( 'design' === $section ) {
			if ( isset( $_POST['reset_design'] ) ) {
				$values['design'] = array();
				$settings->save( $values );
				$this->finish( 'Design reset to the shipped defaults.', 'design' );
			}

			/* Sanitising happens in Calculatorr_Settings::save via
			   Calculatorr_Design::sanitise, so a value that is not the shape it
			   claims to be is dropped rather than written into a stylesheet. */
			$submitted = isset( $_POST['design'] ) ? (array) wp_unslash( $_POST['design'] ) : array();
			$values['design'] = $submitted;
			$settings->save( $values );
			$this->finish( 'Design saved.', 'design' );
		}

		if ( 'ads' === $section ) {
			$values['ads_enabled'] = isset( $_POST['ads_enabled'] ) ? 1 : 0;
			$values['house_ads_enabled'] = isset( $_POST['house_ads_enabled'] ) ? 1 : 0;
			$values['head_footer_enabled'] = isset( $_POST['head_footer_enabled'] ) ? 1 : 0;

			/* Stored verbatim, as the screen explains. */
			foreach ( array( 'code_head', 'code_body', 'code_footer' ) as $key ) {
				$values[ $key ] = isset( $_POST[ $key ] ) ? trim( wp_unslash( $_POST[ $key ] ) ) : '';
			}

			foreach ( array( 'ad_after_calculator', 'ad_in_content', 'ad_sidebar' ) as $key ) {
				/* Ad code is markup and script by nature, so it is stored as
				   given. Only an administrator can reach this screen, and
				   sanitising it would break every ad network there is. */
				$values[ $key ] = isset( $_POST[ $key ] ) ? trim( wp_unslash( $_POST[ $key ] ) ) : '';
			}
			$settings->save( $values );
			$this->finish( 'Advertising and site-wide code saved.', 'ads' );
		}

		foreach ( array( 'seo_enabled', 'schema_enabled', 'share_enabled', 'site_chrome', 'theme_switch', 'empty_start', 'load_fonts', 'log_enabled', 'usage_enabled', 'render_heading' ) as $flag ) {
			$values[ $flag ] = isset( $_POST[ $flag ] ) ? 1 : 0;
		}
		$values['log_limit'] = isset( $_POST['log_limit'] ) ? (int) $_POST['log_limit'] : 200;

		$settings->save( $values );
		$this->finish( 'Settings saved.', 'settings' );
	}

	public function handle_calculators() {
		$this->guard( 'calculatorr_save_calculators' );

		$registry = Calculatorr_Registry::instance();
		$settings = Calculatorr_Settings::instance();
		$scope    = isset( $_POST['scope'] ) ? sanitize_key( wp_unslash( $_POST['scope'] ) ) : '';
		$enabled  = isset( $_POST['enabled'] ) ? array_map( 'sanitize_key', (array) wp_unslash( $_POST['enabled'] ) ) : array();

		/* Only the calculators shown on the submitted screen are reconsidered,
		   so filtering to one category and saving cannot silently switch off
		   everything that was not on screen. */
		$inScope = $scope ? array_keys( $registry->in_category( $scope ) ) : array_keys( $registry->all() );
		$disabled = array_diff( (array) $settings->get( 'disabled' ), $inScope );

		foreach ( $inScope as $slug ) {
			if ( ! in_array( $slug, $enabled, true ) ) {
				$disabled[] = $slug;
			}
		}

		$values = $settings->all();
		$values['disabled'] = array_values( array_unique( $disabled ) );
		$settings->save( $values );

		$this->finish( sprintf( '%d calculators live, %d switched off.', count( $registry->all() ) - count( $values['disabled'] ), count( $values['disabled'] ) ), 'calculators' );
	}

	/**
	 * Turns the posted explainer rows into config-shaped sections.
	 *
	 * A section with no heading is dropped, which is how a row is deleted
	 * without a delete button. The carried field holds whatever the config had
	 * that this screen does not show, a numbered step list or a reference
	 * table, so editing the prose cannot quietly throw those away.
	 */
	public static function clean_sections( $rows ) {
		$out = array();

		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}

			$heading = isset( $row['heading'] ) ? trim( (string) $row['heading'] ) : '';

			if ( '' === $heading ) {
				continue;
			}

			$section = array( 'heading' => wp_kses_post( $heading ) );
			$body    = isset( $row['body'] ) ? trim( (string) $row['body'] ) : '';

			if ( '' !== $body ) {
				$section['body'] = wp_kses_post( $body );
			}

			$formula = isset( $row['formula'] ) ? trim( (string) $row['formula'] ) : '';

			if ( '' !== $formula ) {
				$section['formula'] = wp_kses_post( $formula );
			}

			if ( ! empty( $row['carried'] ) ) {
				$carried = json_decode( (string) $row['carried'], true );

				if ( is_array( $carried ) ) {
					$section = array_merge( $section, $carried );
				}
			}

			$out[] = $section;
		}

		return $out;
	}

	/** Question and answer pairs; a pair with no question is dropped. */
	public static function clean_faqs( $rows ) {
		$out = array();

		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}

			$question = isset( $row['q'] ) ? trim( (string) $row['q'] ) : '';
			$answer   = isset( $row['a'] ) ? trim( (string) $row['a'] ) : '';

			if ( '' === $question || '' === $answer ) {
				continue;
			}

			$out[] = array(
				'q' => wp_kses_post( $question ),
				'a' => wp_kses_post( $answer ),
			);
		}

		return $out;
	}

	public function handle_override() {
		$this->guard( 'calculatorr_save_override' );

		$slug = isset( $_POST['slug'] ) ? sanitize_key( wp_unslash( $_POST['slug'] ) ) : '';
		$config = Calculatorr_Registry::instance()->get( $slug );

		if ( ! $config ) {
			$this->finish( 'That calculator does not exist.', 'calculators' );
		}

		$values = array();
		foreach ( array( 'h1', 'meta_title', 'meta_description', 'description', 'keyword' ) as $key ) {
			$given = isset( $_POST[ $key ] ) ? trim( wp_unslash( $_POST[ $key ] ) ) : '';
			$current = isset( $config[ $key ] ) ? $config[ $key ] : '';
			/* Storing a value identical to the config would be a pointless
			   override that then hides future config changes, so it is left out. */
			if ( '' !== $given && $given !== $current ) {
				$values[ $key ] = $given;
			}
		}

		if ( isset( $_POST['keywords'] ) ) {
			$tags = array_filter( array_map( 'trim', explode( ',', (string) wp_unslash( $_POST['keywords'] ) ) ) );

			if ( $tags ) {
				$values['keywords'] = array_values( array_unique( $tags ) );
			}
		}

		$explainer = self::clean_sections(
			isset( $_POST['explainer'] ) ? (array) wp_unslash( $_POST['explainer'] ) : array()
		);

		/* An empty result means every section was cleared, which is a real
		   instruction and not the same as never having touched the field, so
		   it is stored as an empty array rather than dropped. */
		if ( isset( $_POST['explainer'] ) ) {
			$values['explainer'] = $explainer;
		}

		if ( isset( $_POST['faqs'] ) ) {
			$values['faqs'] = self::clean_faqs( (array) wp_unslash( $_POST['faqs'] ) );
		}

		Calculatorr_Settings::instance()->save_override( $slug, $values );
		$this->finish( $values ? 'Saved. The change is live immediately.' : 'Cleared, so the config file is back in charge.', 'calculators' );
	}

	public function handle_clear_log() {
		$this->guard( 'calculatorr_clear_log' );
		Calculatorr_Error_Log::instance()->clear();
		$this->finish( 'Error log cleared.', 'log' );
	}
}
