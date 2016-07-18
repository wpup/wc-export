<?php

/**
 * Plugin Name: WooCommerce Export.
 * Description: Export various data from WooCommerce
 * Author: Fredrik Forsmo
 * Author URI: https://frozzare.com
 * Version: 1.0.0
 * Plugin URI: https://github.com/frozzare/wc-export
 * Textdomain: wc-export
 * Domain Path: /languages/
 */

// Load `is_plugin_active` function.
if ( ! function_exists( 'is_plugin_active' ) ) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

// Load settings error functions.
if ( ! function_exists( 'add_settings_error' ) ) {
	require_once ABSPATH . 'wp-admin/includes/template.php';
}

// Load Composer autoloader.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require __DIR__ . '/vendor/autoload.php';
}

/**
 * Load WooCommerce Export plugin.
 *
 * @return \Frozzare\WooCommerce\Export\Admin|null
 */
add_action( 'plugins_loaded', function () {
	if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
		return;
	}

	return new \Frozzare\WooCommerce\Export\Admin;
} );
