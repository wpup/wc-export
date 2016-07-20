<?php

namespace Frozzare\WooCommerce\Export\Writers;

class JSON extends Writer {

	/**
	 * Get the content type.
	 *
	 * @var string
	 */
	protected function get_content_type() {
		return 'application/json';
	}

	/**
	 * Get the file extension.
	 *
	 * @var string
	 */
	protected function get_extension() {
		return 'json';
	}

	/**
	 * Render JSON file.
	 *
	 * @param array $data
	 */
	protected function render( array $data ) {
		foreach ( $data as $index => $row ) {
			if ( ! is_array( $row ) ) {
				unset( $data[$index] );
			}
		}

		echo json_encode( $data, JSON_UNESCAPED_UNICODE );
	}
}
