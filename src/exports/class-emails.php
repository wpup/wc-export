<?php

namespace Frozzare\WooCommerce\Export\Exports;

class Emails extends Export {

	/**
	 * Get fields that should be exported.
	 *
	 * @return array
	 */
	public function get_fields() {
		return [
			'billing_email' => __( 'Email', 'woocommerce' )
		];
	}
}
