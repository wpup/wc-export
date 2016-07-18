<?php

namespace Frozzare\WooCommerce\Export;

use Frozzare\WooCommerce\Export\Exports\Export;
use Frozzare\WooCommerce\Export\Writers\Writer;

class Admin {

	/**
	 * The construct.
	 */
	public function __construct() {
		add_action( 'init', [$this, 'export'] );
		add_action( 'admin_enqueue_scripts', [$this, 'enqueue_css'] );
		add_action( 'admin_enqueue_scripts', [$this, 'enqueue_js'] );
		add_action( 'admin_menu', [$this, 'menu'], 25 );
		add_action( 'wc_export_classes', [$this, 'export_classes'] );
		add_action( 'wc_export_writers', [$this, 'export_writers'] );
		add_action( 'wp_ajax_wc_export', [$this, 'ajax'] );
	}

	/**
	 * Output ajax fields.
	 */
	public function ajax() {
		if ( $export = $this->get_export_class() ) {
			echo json_encode( $export->get_fields() );
			exit;
		}

		echo json_encode( [
			'error' => 'No export found'
		] );

		exit;
	}

	/**
	 * Enqueue CSS.
	 */
	public function enqueue_css() {
		wp_enqueue_style(
			'jquery-ui-datepicker-style',
			plugins_url( '/../assets/css/jquery-ui.css', __FILE__ )
		);
	}

	/**
	 * Enqueue JavaScript.
	 */
	public function enqueue_js() {
		// WordPress will override window.wc_export on plugins page,
		// so don't include wc_export script.
		if ( strpos( $_SERVER['REQUEST_URI'], 'plugins.php' ) !== false ) {
			return;
		}

		wp_enqueue_script(
			'wc-export-scripts',
			plugins_url( '/../assets/js/scripts.js', __FILE__ ),
			['jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'wp-util'],
			'',
			true
		);
	}

	/**
	 * Export data.
	 */
	public function export() {
		// Check for nonce value.
		if ( ! isset( $_POST['wc_export_nonce'] ) ) {
			return;
		}

		// Check if our nonce is vailed.
		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['wc_export_nonce'] ), 'wc_export_data' ) ) {
			return;
		}

		// Get export class.
		if ( ! ( $export = $this->get_export_class() ) ) {
			return;
		}

		// Set include fields if any.
		if ( ! empty( $_POST['wc_export_include_fields'] ) ) {
			$export->set_fields( $_POST['wc_export_include_fields'] );
		}

		// Get export writer class.
		if ( ! ( $writer = $this->get_export_writer() ) ) {
			return;
		}

		$export->export( $writer );
	}

	/**
	 * Add export classes.
	 *
	 * @param  array $exports
	 *
	 * @return array
	 */
	public function export_classes( array $exports ) {
		return array_merge( $exports, [
			'Customers' => '\\Frozzare\\WooCommerce\\Export\\Exports\\Customers',
			'Emails'    => '\\Frozzare\\WooCommerce\\Export\\Exports\\Emails'
		] );
	}

	/**
	 * Add export writers.
	 *
	 * @param  array $writers
	 *
	 * @return array
	 */
	public function export_writers( array $writers ) {
		return array_merge( $writers, [
			'CSV' => '\\Frozzare\\WooCommerce\\Export\\Writers\\CSV'
		] );
	}

	/**
	 * Get export class.
	 *
	 * @return null|object
	 */
	protected function get_export_class() {
		if ( ! isset( $_POST['wc_export_class'] ) ) {
			return;
		}

		$class = sanitize_text_field( $_POST['wc_export_class'] );
		$class = base64_decode( $class );

		if ( class_exists( $class ) ) {
			$class = new $class( [
				'date_query' => [
					[
						'after'     => sanitize_text_field( $_POST['wc_export_end_date'] ),
						'before'    => sanitize_text_field( $_POST['wc_export_start_date'] ),
						'inclusive' => true
					]
				],
				'post_status' => array_map( 'sanitize_text_field', $_POST['wc_export_order_status'] )
			] );

			// Check so the class is a valid export class.
			if ( $class instanceof Export ) {
				return $class;
			}
		}
	}

	/**
	 * Get export writer.
	 *
	 * @return null|object
	 */
	protected function get_export_writer() {
		if ( ! isset( $_POST['wc_export_writer'] ) ) {
			return;
		}

		$class = sanitize_text_field( $_POST['wc_export_writer'] );
		$class = base64_decode( $class );

		if ( class_exists( $class ) ) {
			$class = new $class;

			// Check so the class is a valid writer class.
			if ( $class instanceof Writer ) {
				return $class;
			}
		}
	}

	/**
	 * Get export classes.
	 *
	 * @return array
	 */
	public function get_export_classes() {
		return apply_filters( 'wc_export_classes', [] );
	}

	/**
	 * Get export writers.
	 *
	 * @return array
	 */
	public function get_export_writers() {
		return apply_filters( 'wc_export_writers', [] );
	}

	/**
	 * Add export menu.
	 */
	public function menu() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		add_submenu_page(
			'woocommerce',
			__( 'Export', 'wc_export' ),
			__( 'Export', 'wc_export' ),
			'view_woocommerce_reports',
			'wc-export',
			[$this, 'render']
		);
	}

	/**
	 * Render export page.
	 */
	public function render() {
		$exports = $this->get_export_classes();
		$export  = array_values( $exports )[0];
		$writers = $this->get_export_writers();
	?>
		<div class="wrap woocommerce">
			<h1><?php _e( 'Export', 'wc-export' ); ?></h1>

			<?php
			// Display errors if any.
			if ( $errors = get_transient( 'settings_errors' ) ) {
				// Create a list of errors.
				$message = '<div id="wc-export-message" class="error"><ul>';
				foreach ( $errors as $error ) {
					$message .= '<li>' . $error['message'] . '</li>';
				}
				$message .= '</ul></div>';

				// Output error.
				echo $message;

				// Clear and the transient and unhook any other notices so we don't see duplicate messages.
				delete_transient( 'settings_errors' );
			}
			?>

			<form method="post" action="" enctype="multipart/form-data">
		        <?php wp_nonce_field( 'wc_export_data', 'wc_export_nonce' ); ?>

		        <table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<label for="wc-export-writer"><?php _e( 'Output format', 'wc-export' ); ?></label>
							</th>
							<td>
								<select name="wc_export_writer" id="wc-export-writer">
									<?php foreach ( $writers as $name => $class ): ?>
										<option value="<?php echo base64_encode( $class ); ?>"><?php echo $name; ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="wc-export-start-date"><?php _e( 'Start date', 'wc-export' ); ?></label>
							</th>
							<td>
								<input type="text" name="wc_export_start_date" id="wc-export-start-date">
								<p class="description">
									<?php _e( 'Leave empty for no start date', 'wc-export'  ); ?>
								</p>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="wc-export-end-date"><?php _e( 'End date', 'wc-export' ); ?></label>
							</th>
							<td>
								<input type="text" name="wc_export_end_date" id="wc-export-end-date">
								<p class="description">
									<?php _e( 'Leave empty for no end date', 'wc-export'  ); ?>
								</p>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="wc-export-post-status"><?php _e( 'Order status', 'wc-export' ); ?></label>
							</th>
							<td>
								<ul id="wc-export-order-status">
									<?php foreach ( wc_get_order_statuses() as $status => $name ): ?>
										<li>
											<label for="wc-export-order-status-<?php echo $status; ?>">
												<input type="checkbox" id="wc-export-order-status-<?php echo $status; ?>" name="wc_export_order_status[]" value="<?php echo $status; ?>" <?php checked( $status, 'wc-completed' ); ?>>
												<?php echo $name; ?>
											</label>
										</li>
									<?php endforeach; ?>
								</ul>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="wc-export-class"><?php _e( 'Expoter', 'wc-export' ); ?></label>
							</th>
							<td>
								<select name="wc_export_class" id="wc-export-class">
									<?php foreach ( $exports as $name => $class ): ?>
										<option value="<?php echo base64_encode( $class ); ?>"><?php echo $name; ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="wc-export-include-fields"><?php _e( 'Include fields', 'wc-export' ); ?></label>
							</th>
							<td>
								<p class="description">
									<?php _e( 'All fields will be included if none is selected.', 'wc-export'  ); ?>
								</p>
								<ul id="wc-export-include-fields">
								<?php if ( class_exists( $export ) ): ?>
									<?php foreach ( ( new $export )->get_fields() as $field => $name ): ?>
										<li>
											<label for="wc-export-include-field-<?php echo $field; ?>">
												<input type="checkbox" id="wc-export-include-field-<?php echo $field; ?>" name="wc_export_include_fields[]" value="<?php echo $field; ?>">
												<?php echo $name; ?>
											</label>
										</li>
									<?php endforeach; ?>
								<?php endif; ?>
								</ul>
							</td>
						</tr>
					</tbody>
				</table>

		        <p class="submit">
		            <input type="submit" class="button-primary" value="Export" />
		        </p>
		    </form>
		 </div>

		 <script type="text/template" id="tmpl-wc-export-include-fields">
		 	<ul id="wc-export-include-fields">
		 		<% _.each(fields, function(field, name) { %>
		 			<li>
		 				<label for="wc-export-include-field-<%= field %>">
							<input type="checkbox" id="wc-export-include-field-<%= field %>" name="wc_export_include_fields[]" value="<%= field %>">
							<%= name %>
		 				</label>
		 			</li>
		 		<% }) %>
		 	</ul>
		 </script>
	<?php
	}
}
