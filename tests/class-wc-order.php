<?php

class WC_Order {

	public function __construct( $post ) {
		$this->post = $post;
	}

	public function __get( $key ) {
		switch ( $key ) {
			case 'billing_first_name':
				return 'Fredrik';
			case 'billing_last_name':
				return 'Example';
			case 'billing_email':
				return 'hello@example.com';
			default:
				break;
		}

		if ( isset( $this->post->$key ) ) {
			return $this->post->$key;
		}
	}
}
