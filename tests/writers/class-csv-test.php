<?php

namespace Frozzare\Tests\WooCommerce\Export\Writers;

use Frozzare\WooCommerce\Export\Writers\CSV;

class CSV_Test extends \WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		$this->csv = new CSV;
	}

	public function tearDown() {
		parent::tearDown();

		unset( $this->csv );
	}

	public function test_csv_empty() {
		$this->csv->render( [] );
		$this->expectOutputString( '' );
	}

	public function test_csv_success() {
		$this->csv->render( [
			[
				'Email' => 'hello@example.com'
			],
			null
		] );

		$this->expectOutputString( 'Email
"hello@example.com";
' );
	}
}
