<?php

namespace Frozzare\WooCommerce\Export\Writers;

class CSV extends Writer {

	/**
	 * Get the content type.
	 *
	 * @var string
	 */
	protected function get_content_type() {
		return 'text/csv';
	}

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
	protected function render( array $data ) {
		// Output CSV headers.
		if ( isset( $data[0] ) ) {
			echo implode( ';', array_keys( $data[0] ) ) . "\n";
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
	}
}
