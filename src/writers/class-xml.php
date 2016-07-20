<?php

namespace Frozzare\WooCommerce\Export\Writers;

class XML extends Writer {

	/**
	 * Get the content type.
	 *
	 * @var string
	 */
	protected function get_content_type() {
		return 'text/xml';
	}

	/**
	 * Get the file extension.
	 *
	 * @var string
	 */
	protected function get_extension() {
		return 'xml';
	}

	/**
	 * Render XML file.
	 *
	 * @param array $data
	 */
	protected function render( array $data ) {
		echo '<?xml version="1.0"?>';
		echo '<orders>';

		// Output each row as a line.
		foreach ( $data as $row ) {
			$line = '<order>';

			if ( ! is_array( $row ) ) {
				continue;
			}

			foreach ( $row as $field => $value ) {
				$tag = sanitize_title_with_dashes( $field );
				$tag = str_replace( '-', '_', $tag );

				$line .= sprintf( '<%s>%s</%s>', $tag, $value, $tag );
			}

			echo $line . '</order>';
		}

		echo '</orders>';
	}
}
