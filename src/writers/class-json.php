<?php

namespace Frozzare\WooCommerce\Export\Writers;

class JSON extends Writer {

	/**
	 * Get the file extension.
	 *
	 * @var string
	 */
	protected function get_extension() {
		return 'json';
	}

	/**
	 * Render CSV file.
	 *
	 * @param array $data
	 */
	public function render( array $data ) {
		if ( $this->is_http_post() ) {
			header( 'Content-Description: File Transfer' );
			header( 'Content-Disposition: attachment; filename=' . $this->get_filename() );
			header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ), true );
		}

		foreach ( $data as $index => $row ) {
			if ( ! is_array( $row ) ) {
				unset( $data[$index] );
			}
		}

		echo json_encode( $data, JSON_UNESCAPED_UNICODE );

		$this->is_http_post() && exit;
	}
}
