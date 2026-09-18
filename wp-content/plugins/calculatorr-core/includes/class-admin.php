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

	public function screen() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

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
		?>
		<div class="calcr-stats">
			<?php
			$this->stat( 'Active calculators', $active, count( $all ) . ' registered' );
			$this->stat( 'Switched off', count( $disabled ), $disabled ? 'not shown to visitors' : 'all live' );
			$this->stat( 'Categories', count( $categories ), 'hub pages' );
			$this->stat( 'Pages created', $pages, $pages < count( $all ) ? ( count( $all ) - $pages ) . ' still to create' : 'all present' );
			$this->stat( 'Logged errors', $log->count(), $log->count() ? count( $log->affected_slugs() ) . ' calculators affected' : 'nothing reported' );
			?>
		</div>

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
			return isset( $override[ $key ] ) ? $override[ $key ] : $config[ $key ];
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
				</table>

				<?php submit_button( 'Save' ); ?>
				<a class="button" href="<?php echo esc_url( $this->url( 'calculators' ) ); ?>">Back to the list</a>
			</form>
		</div>
		<?php
	}

	/* ---------------------------------------------------------------- */

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
						Show advertising slots
					</label>
				</p>

				<?php foreach ( $slots as $key => $slot ) : ?>
					<h3><?php echo esc_html( $slot[0] ); ?></h3>
					<p class="description"><?php echo esc_html( $slot[1] ); ?></p>
					<textarea name="<?php echo esc_attr( $key ); ?>" rows="4" class="large-text code"><?php echo esc_textarea( $settings->get( $key ) ); ?></textarea>
				<?php endforeach; ?>

				<?php submit_button( 'Save advertising' ); ?>
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

		if ( 'ads' === $section ) {
			$values['ads_enabled'] = isset( $_POST['ads_enabled'] ) ? 1 : 0;
			foreach ( array( 'ad_after_calculator', 'ad_in_content', 'ad_sidebar' ) as $key ) {
				/* Ad code is markup and script by nature, so it is stored as
				   given. Only an administrator can reach this screen, and
				   sanitising it would break every ad network there is. */
				$values[ $key ] = isset( $_POST[ $key ] ) ? trim( wp_unslash( $_POST[ $key ] ) ) : '';
			}
			$settings->save( $values );
			$this->finish( 'Advertising saved.', 'ads' );
		}

		foreach ( array( 'seo_enabled', 'schema_enabled', 'share_enabled', 'load_fonts', 'log_enabled', 'render_heading' ) as $flag ) {
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

	public function handle_override() {
		$this->guard( 'calculatorr_save_override' );

		$slug = isset( $_POST['slug'] ) ? sanitize_key( wp_unslash( $_POST['slug'] ) ) : '';
		$config = Calculatorr_Registry::instance()->get( $slug );

		if ( ! $config ) {
			$this->finish( 'That calculator does not exist.', 'calculators' );
		}

		$values = array();
		foreach ( array( 'h1', 'meta_title', 'meta_description', 'description' ) as $key ) {
			$given = isset( $_POST[ $key ] ) ? trim( wp_unslash( $_POST[ $key ] ) ) : '';
			/* Storing a value identical to the config would be a pointless
			   override that then hides future config changes, so it is left out. */
			if ( '' !== $given && $given !== $config[ $key ] ) {
				$values[ $key ] = $given;
			}
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
