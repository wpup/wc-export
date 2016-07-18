<?php

namespace Frozzare\WooCommerce\Export\Writers;

class XML extends Writer {

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
	public function render( array $data ) {
		if ( $this->is_http_post() ) {
			header( 'Content-Description: File Transfer' );
			header( 'Content-Disposition: attachment; filename=' . $this->get_filename() );
			header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ), true );
		}

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

		$this->is_http_post() && exit;
	}
}
