<?php

namespace Frozzare\Tests\WooCommerce\Export\Exports;

use Frozzare\WooCommerce\Export\Exports\Customers;
use Frozzare\WooCommerce\Export\Writers\CSV;

class Customers_Test extends \WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		$this->customers = new Customers;
	}

	public function tearDown() {
		parent::tearDown();

		unset( $this->customers );
	}

	public function test_get_fields() {
		$this->assertNotEmpty( $this->customers->get_fields() );
	}

	public function test_export_failed() {
		$this->customers->export( new CSV );
		$this->expectOutputString( '' );
	}

	public function test_export_success() {
		$post_id = $this->factory->post->create( [
			'post_type'   => 'shop_order',
			'post_status' => 'wc-completed'
		] );

		$this->customers->export( new CSV );

		$this->expectOutputString( 'First name;Last name;Company;Address 1;Address 2;City;Postcode;Country;State;Telephone;Email
"Fredrik";"Example";"";"";"";"";"";"";"";"";"hello@example.com";
' );
	}

	public function test_set_fields() {
		$post_id = $this->factory->post->create( [
			'post_type'   => 'shop_order',
			'post_status' => 'wc-completed'
		] );

		$this->customers->set_fields( ['billing_first_name'] );
		$this->customers->export( new CSV );

		$this->expectOutputString( 'First name
"Fredrik";
' );
	}

	public function test_query_args() {
		$this->assertEmpty( $this->customers->query_args( [] ) );
	}
}
