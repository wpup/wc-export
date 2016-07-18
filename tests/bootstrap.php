<?php

// Load Composer autoload.
require __DIR__ . '/../vendor/autoload.php';

// Load test data file.
WP_Test_Suite::load_files( __DIR__ . '/class-wc-order.php' );

// Run the WordPress test suite.
WP_Test_Suite::run();
