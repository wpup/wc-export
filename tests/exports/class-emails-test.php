<?php

namespace Frozzare\Tests\WooCommerce\Export\Exports;

use Frozzare\WooCommerce\Export\Exports\Emails;
use Frozzare\WooCommerce\Export\Writers\CSV;

class Emails_Test extends \WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		$this->emails = new Emails;
	}

	public function tearDown() {
		parent::tearDown();

		unset( $this->emails );
	}

	public function test_get_fields() {
		$this->assertNotEmpty( $this->emails->get_fields() );
	}

	public function test_export_failed() {
		$this->emails->export( new CSV );
		$this->expectOutputString( '' );
	}

	public function test_export_success() {
		$post_id = $this->factory->post->create( [
			'post_type'   => 'shop_order',
			'post_status' => 'wc-completed'
		] );

		$this->emails->export( new CSV );

		$this->expectOutputString( 'Email
"hello@example.com";
' );
	}

	public function test_query_args() {
		$this->assertEmpty( $this->emails->query_args( [] ) );
	}
}
