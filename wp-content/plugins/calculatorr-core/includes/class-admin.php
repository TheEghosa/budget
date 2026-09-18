<?php
/**
 * The one admin screen: a button that creates the pages.
 *
 * A hundred calculators means a hundred pages, and creating those by hand in
 * the editor is the part of this build most likely to be abandoned halfway
 * through, so it is a single action here instead.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Calculatorr_Admin {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_post_calculatorr_sync', array( $this, 'handle_sync' ) );
	}

	public function menu() {
		add_menu_page(
			'Calculatorr',
			'Calculatorr',
			'manage_options',
			'calculatorr',
			array( $this, 'screen' ),
			'dashicons-calculator',
			58
		);
	}

	public function screen() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$registry   = Calculatorr_Registry::instance();
		$categories = $registry->categories();
		$all        = $registry->all();
		$report     = get_transient( 'calculatorr_sync_report' );

		if ( $report ) {
			delete_transient( 'calculatorr_sync_report' );
		}
		?>
		<div class="wrap">
			<h1>Calculatorr</h1>

			<?php if ( $report ) : ?>
				<div class="notice notice-success is-dismissible">
					<p>
						Created <?php echo (int) $report['created_categories']; ?> category pages and
						<?php echo (int) $report['created_calculators']; ?> calculator pages.
						<?php echo (int) $report['skipped']; ?> were already there and were left alone.
					</p>
				</div>
			<?php endif; ?>

			<p>
				<?php echo count( $all ); ?> calculators are registered across
				<?php echo count( $categories ); ?> categories. Running the sync creates a page for
				any calculator that does not have one yet, and never touches a page that already
				exists, so it is safe to run as often as you like.
			</p>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="calculatorr_sync">
				<?php wp_nonce_field( 'calculatorr_sync' ); ?>
				<?php submit_button( 'Sync calculator pages' ); ?>
			</form>

			<h2>Registered calculators</h2>
			<table class="widefat striped">
				<thead>
					<tr><th>Calculator</th><th>Category</th><th>URL</th><th>Page</th></tr>
				</thead>
				<tbody>
					<?php foreach ( $all as $slug => $config ) : ?>
						<?php
						$path = $config['category'] . '/' . $slug;
						$page = get_page_by_path( $path, OBJECT, 'page' );
						?>
						<tr>
							<td><strong><?php echo esc_html( $config['title'] ); ?></strong></td>
							<td><?php echo esc_html( isset( $categories[ $config['category'] ] ) ? $categories[ $config['category'] ]['name'] : $config['category'] ); ?></td>
							<td><code>/<?php echo esc_html( $path ); ?>/</code></td>
							<td>
								<?php if ( $page ) : ?>
									<a href="<?php echo esc_url( get_edit_post_link( $page->ID ) ); ?>">Edit</a>
								<?php else : ?>
									<em>not created</em>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	public function handle_sync() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'You do not have permission to do that.' );
		}

		check_admin_referer( 'calculatorr_sync' );

		$report = Calculatorr_Pages::sync();
		set_transient( 'calculatorr_sync_report', $report, 60 );

		wp_safe_redirect( admin_url( 'admin.php?page=calculatorr' ) );
		exit;
	}
}
