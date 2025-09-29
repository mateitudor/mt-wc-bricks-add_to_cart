<?php
/**
 * Uninstall script for MT WC Bricks Add to Cart
 *
 * @package MT_WC_Bricks_Add_To_Cart
 */

// If uninstall not called from WordPress, then exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
	exit;
}

// Clean up any plugin-specific data if needed
// Note: This plugin doesn't store any data in the database,
// so no cleanup is necessary

// Clear any cached data
if (function_exists('wp_cache_flush')) {
	wp_cache_flush();
}
