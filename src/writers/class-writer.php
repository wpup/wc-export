<?php

namespace Frozzare\WooCommerce\Export\Writers;

abstract class Writer {

	/**
	 * Check if it's http post.
	 *
	 * @return bool
	 */
	protected function is_http_post() {
		return isset( $_SERVER['REQUEST_METHOD'] ) && strtolower( $_SERVER['REQUEST_METHOD'] ) === 'post';
	}

	/**
	 * Get the file extension.
	 *
	 * @var string
	 */
	abstract protected function get_extension();

	/**
	 * Get filename.
	 *
	 * @return string
	 */
	protected function get_filename() {
		return 'wc-export-' . date( 'ymdHis', current_time( 'timestamp' ) ) . '.' . $this->get_extension();
	}

	/**
	 * Render file.
	 *
	 * @param array $data
	 */
	abstract public function render( array $data );
}
