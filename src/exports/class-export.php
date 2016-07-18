<?php

namespace Frozzare\WooCommerce\Export\Exports;

use Frozzare\WooCommerce\Export\Writers\Writer;
use WC_Order;
use WP_Query;

abstract class Export {

	/**
	 * WP Query args.
	 *
	 * @var array
	 */
	protected $args = [];

	/**
	 * The construct.
	 *
	 * @param array $args
	 */
	public function __construct( array $args = [] ) {
		$this->args = $args;
	}

	/**
	 * Add error message.
	 *
	 * @param string $name
	 * @param string $message
	 */
	protected function add_error( $name, $message ) {
		$name = sanitize_title_with_dashes( $name );

		add_settings_error(
			'wc-export-' . $name,
			'wc-export-' . $name,
			'No orders found so cannot export anything',
			'error'
		);

		set_transient( 'settings_errors', get_settings_errors(), 30 );
	}

	/**
	 * Export order data.
	 *
	 * @param \Frozzare\WooCommerce\Export\Writer $writer
	 */
	public function export( Writer $writer ) {
		$orders = $this->get_orders();

		// Get all fields that should be exported.
		$fields = empty( $this->fields ) ? $this->get_fields() : $this->fields;
		$data   = [];

		// Export fields.
		foreach ( $orders as $order ) {
			$row = [];

			foreach ( $fields as $field => $name ) {
				$row[$name] = $order->$field;
			}

			$data[] = $row;
		}

		// Add error if no data.
		if ( empty( $data ) ) {
			$this->add_error( 'no-orders', 'No orders found so cannot export anything' );
			return;
		}

		// Let the writer render the data.
		$writer->render( $data );
	}

	/**
	 * Get fields that should be exported.
	 *
	 * @return array
	 */
	public function get_fields() {
		return $this->fields;
	}

	/**
	 * Get orders.
	 *
	 * @return array
	 */
	protected function get_orders() {
		$args = array_merge( [
			'date_query'     => [],
			'post_status'    => 'wc-completed',
			'post_type'      => 'shop_order',
			'posts_per_page' => -1
		], $this->args );

		// Post status needs to be array for in array check later on.
		if ( ! is_array( $args['post_status'] ) ) {
			$args['post_status'] = [$args['post_status']];
		}

		// Modify WP Query args from the exporter.
		$args = $this->query_args( $args );

		// Get all order posts.
		$orders = ( new WP_Query( $args ) )->get_posts();

		// Create WooCommerce orders instances.
		$orders = array_map( function( $order ) {
			return new WC_Order( $order );
		}, $orders );

		// Check if order should be exported.
		return array_filter( $orders, function ( $order ) use ( $args ) {
			return apply_filters( 'wc_export_order', true, $order ) === true
				&& in_array( $order->post_status, $args['post_status'] );
		} );
	}

	/**
	 * Modify WP Query args.
	 *
	 * @param  array  $args
	 *
	 * @return array
	 */
	public function query_args( array $args ) {
		return $args;
	}

	/**
	 * Set fields that should be exported.
	 *
	 * @param array $fields
	 */
	public function set_fields( array $fields ) {
		$old_fields = $this->get_fields();
		$new_fields = [];

		foreach ( $fields as $key ) {
			if ( isset( $old_fields[$key] ) ) {
				$new_fields[$key] = $old_fields[$key];
			}
		}

		$this->fields = $new_fields;
	}
}
