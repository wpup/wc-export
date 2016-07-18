<?php

namespace Frozzare\WooCommerce\Export\Writers;

class CSV extends Writer {

	/**
	 * Get the file extension.
	 *
	 * @var string
	 */
	protected function get_extension() {
		return 'csv';
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
			header( 'Content-Type: text/csv; charset=' . get_option( 'blog_charset' ), true );
		}

		// Output CSV headers.
		if ( isset( $data[0] ) ) {
			echo implode( ";", array_keys( $data[0] ) ) . "\n";
		}

		// Output each row as a line.
		foreach ( $data as $row ) {
			$line = '';

			if ( ! is_array( $row ) ) {
				continue;
			}

			foreach ( $row as $field => $value ) {
				$line .= '"' . $value . '";';
			}

			echo $line . "\n";
		}

		$this->is_http_post() && exit;
	}
}
