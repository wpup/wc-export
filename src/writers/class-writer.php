<?php

namespace Frozzare\WooCommerce\Export\Writers;

abstract class Writer {

	/**
	 * Get the content type.
	 *
	 * @var string
	 */
	abstract protected function get_content_type();

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
	 * Output headers.
	 */
	protected function headers() {
		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $this->get_filename() );
		header( 'Content-Type: ' . $this->get_content_type() . '; charset=' . get_option( 'blog_charset' ), true );
	}

	/**
	 * Check if it's http post.
	 *
	 * @return bool
	 */
	protected function is_http_post() {
		return isset( $_SERVER['REQUEST_METHOD'] ) && strtolower( $_SERVER['REQUEST_METHOD'] ) === 'post';
	}

	/**
	 * Write export data.
	 */
	public function write( array $data ) {
		$this->is_http_post() && $this->headers();

		$this->render( $data );

		$this->is_http_post() && exit;
	}

	/**
	 * Render file.
	 *
	 * @param array $data
	 */
	abstract protected function render( array $data );
}
