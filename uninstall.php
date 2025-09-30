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

// Clear any cached data
if (function_exists('wp_cache_flush')) {
	wp_cache_flush();
}
