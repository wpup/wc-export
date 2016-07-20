<?php

namespace Frozzare\Tests\WooCommerce\Export\Writers;

use Frozzare\WooCommerce\Export\Writers\JSON;

class JSON_Test extends \WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		$this->writer = new JSON;
	}

	public function tearDown() {
		parent::tearDown();

		unset( $this->writer );
	}

	public function test_empty() {
		$this->writer->write( [] );
		$this->expectOutputString( '[]' );
	}

	public function test_success() {
		$this->writer->write( [
			[
				'Email' => 'hello@example.com'
			],
			null
		] );

		$this->expectOutputString( '[{"Email":"hello@example.com"}]' );
	}
}
