<?php

namespace Frozzare\WooCommerce\Export\Exports;

class Customers extends Export {

	/**
	 * Get fields that should be exported.
	 *
	 * @return array
	 */
	public function get_fields() {
		return [
			'billing_first_name' => __( 'First name', 'woocommerce' ),
			'billing_last_name'  => __( 'Last name', 'woocommerce' ),
			'billing_company'    => __( 'Company', 'woocommerce' ),
			'billing_address_1'  => __( 'Address 1', 'woocommerce' ),
			'billing_address_2'  => __( 'Address 2', 'woocommerce' ),
			'billing_city'       => __( 'City', 'woocommerce' ),
			'billing_postcode'   => __( 'Postcode', 'woocommerce' ),
			'billing_country'    => __( 'Country', 'woocommerce' ),
			'billing_state'      => __( 'State', 'woocommerce' ),
			'billing_phone'      => __( 'Telephone', 'woocommerce' ),
			'billing_email'      => __( 'Email', 'woocommerce' )
		];
	}
}
