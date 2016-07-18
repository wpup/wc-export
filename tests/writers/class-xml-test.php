<?php

namespace Frozzare\Tests\WooCommerce\Export\Writers;

use Frozzare\WooCommerce\Export\Writers\XML;

class XML_Test extends \WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		$this->writer = new XML;
	}

	public function tearDown() {
		parent::tearDown();

		unset( $this->writer );
	}

	public function test_empty() {
		$this->writer->render( [] );
		$this->expectOutputString( '<?xml version="1.0"?><orders></orders>' );
	}

	public function test_success() {
		$this->writer->render( [
			[
				'Email' => 'hello@example.com'
			],
			null
		] );

		$this->expectOutputString( '<?xml version="1.0"?><orders><order><email>hello@example.com</email></order></orders>' );
	}
}
