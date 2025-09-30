<?php
/**
 * Plugin Name: MT WC Bricks Add to Cart
 * Plugin URI: https://mateitudor.com
 * Description: A Bricks Builder element that provides an accessible, icon-only add to cart button for WooCommerce products.
 * Version: 1.0.0
 * Author: Matei Tudor
 * Author URI: https://mateitudor.com
 * Text Domain: mt-wc-bricks-add_to_cart
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 8.1
 * License: Unlicense
 * License URI: https://unlicense.org/
 */

declare(strict_types=1);

// Prevent direct access
if (!defined('ABSPATH')) {
	exit;
}

// Define plugin constants
define('MT_WC_BRICKS_ADD_TO_CART_VERSION', '1.0.0');
define('MT_WC_BRICKS_ADD_TO_CART_PLUGIN_FILE', __FILE__);
define('MT_WC_BRICKS_ADD_TO_CART_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MT_WC_BRICKS_ADD_TO_CART_PLUGIN_URL', plugin_dir_url(__FILE__));

// Check if WooCommerce is active
function mt_wc_bricks_add_to_cart_is_woocommerce_active(): bool {
	return class_exists('WooCommerce');
}

// Check if Bricks theme is active
function mt_wc_bricks_add_to_cart_is_bricks_active(): bool {
	$current_theme = wp_get_theme();
	$parent_theme = $current_theme->parent();
	return $current_theme->get('Name') === 'Bricks' || ($parent_theme && $parent_theme->get('Name') === 'Bricks');
}

// Check if Bricks is loaded
function mt_wc_bricks_add_to_cart_is_bricks_loaded(): bool {
	return class_exists('Bricks\Element');
}

// Register the Bricks element
function mt_wc_bricks_add_to_cart_register_element(): void {
	// Check dependencies
	if (!mt_wc_bricks_add_to_cart_is_woocommerce_active() || !mt_wc_bricks_add_to_cart_is_bricks_active() || !mt_wc_bricks_add_to_cart_is_bricks_loaded()) {
		return;
	}

	$element_file = MT_WC_BRICKS_ADD_TO_CART_PLUGIN_DIR . 'includes/class-element-add-to-cart.php';

	if (file_exists($element_file)) {
		require_once $element_file;
		\Bricks\Elements::register_element($element_file);
	}
}

// Enqueue assets
function mt_wc_bricks_add_to_cart_enqueue_assets(): void {
	if (!mt_wc_bricks_add_to_cart_is_woocommerce_active() || !mt_wc_bricks_add_to_cart_is_bricks_active()) {
		return;
	}

	$css_path = MT_WC_BRICKS_ADD_TO_CART_PLUGIN_DIR . 'assets/css/add-to-cart.css';
	$js_path = MT_WC_BRICKS_ADD_TO_CART_PLUGIN_DIR . 'assets/js/add-to-cart.js';

	if (file_exists($css_path)) {
		wp_enqueue_style(
			'mt-wc-bricks-add-to-cart',
			MT_WC_BRICKS_ADD_TO_CART_PLUGIN_URL . 'assets/css/add-to-cart.css',
			[],
			filemtime($css_path)
		);
	}

	// Enqueue FontAwesome for icons
	wp_enqueue_style(
		'font-awesome',
		'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
		[],
		'6.4.0'
	);

	if (file_exists($js_path)) {
		wp_enqueue_script(
			'mt-wc-bricks-add-to-cart',
			MT_WC_BRICKS_ADD_TO_CART_PLUGIN_URL . 'assets/js/add-to-cart.js',
			[],
			filemtime($js_path),
			true
		);

		// Localize script with AJAX parameters
		wp_localize_script('mt-wc-bricks-add-to-cart', 'wc_add_to_cart_params', [
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('mt_wc_bricks_add_to_cart'),
			'debug' => defined('WP_DEBUG') && WP_DEBUG
		]);
	}
}

// WooCommerce AJAX handler
function mt_wc_bricks_add_to_cart_ajax_handler() {
	// Check if WooCommerce is active
	if (!mt_wc_bricks_add_to_cart_is_woocommerce_active()) {
		wp_send_json_error(['message' => 'WooCommerce is not active']);
		return;
	}

	// Debug logging
	if (defined('WP_DEBUG') && WP_DEBUG) {
		error_log('AJAX Handler called with POST data: ' . print_r($_POST, true));
	}

	// Verify nonce
	$nonce = sanitize_text_field($_POST['nonce'] ?? '');
	if (!wp_verify_nonce($nonce, 'mt_wc_bricks_add_to_cart')) {
		if (defined('WP_DEBUG') && WP_DEBUG) {
			error_log('AJAX nonce verification failed');
		}
		wp_send_json_error(['message' => 'Security check failed']);
		return;
	}

	$product_id = intval(sanitize_text_field($_POST['product_id'] ?? 0));
	$quantity = intval(sanitize_text_field($_POST['quantity'] ?? 1));

	if (defined('WP_DEBUG') && WP_DEBUG) {
		error_log("AJAX: product_id={$product_id}, quantity={$quantity}");
	}

	// Validate product
	if (!$product_id) {
		if (defined('WP_DEBUG') && WP_DEBUG) {
			error_log('AJAX: Invalid product ID');
		}
		wp_send_json_error(['message' => 'Invalid product ID']);
		return;
	}

	$product = wc_get_product($product_id);
	if (defined('WP_DEBUG') && WP_DEBUG) {
		error_log("AJAX: Product object: " . (is_object($product) ? get_class($product) : 'not object'));
		error_log("AJAX: Product purchasable: " . ($product ? ($product->is_purchasable() ? 'yes' : 'no') : 'no product'));
	}

	if (!$product || !$product->is_purchasable()) {
		if (defined('WP_DEBUG') && WP_DEBUG) {
			error_log('AJAX: Product not available or not purchasable');
		}
		wp_send_json_error(['message' => 'Product not available']);
		return;
	}

	// Check stock
	if (defined('WP_DEBUG') && WP_DEBUG) {
		error_log("AJAX: Product in stock: " . ($product->is_in_stock() ? 'yes' : 'no'));
	}

	if (!$product->is_in_stock()) {
		if (defined('WP_DEBUG') && WP_DEBUG) {
			error_log('AJAX: Product out of stock - sending error response');
		}
		wp_send_json_error(['message' => 'Product out of stock']);
		return;
	}

	// Validate quantity
	$min_quantity = $product->get_min_purchase_quantity();
	$max_quantity = $product->get_max_purchase_quantity();

	if (defined('WP_DEBUG') && WP_DEBUG) {
		error_log("AJAX: Quantity validation - min: {$min_quantity}, max: {$max_quantity}, requested: {$quantity}");
	}

	if ($quantity < $min_quantity) {
		if (defined('WP_DEBUG') && WP_DEBUG) {
			error_log('AJAX: Quantity too low - sending error response');
		}
		wp_send_json_error(['message' => 'Quantity too low']);
		return;
	}

	if ($max_quantity > 0 && $quantity > $max_quantity) {
		if (defined('WP_DEBUG') && WP_DEBUG) {
			error_log('AJAX: Quantity too high - sending error response');
		}
		wp_send_json_error(['message' => 'Quantity too high']);
		return;
	}

	// Add to cart
	if (defined('WP_DEBUG') && WP_DEBUG) {
		error_log('AJAX: Attempting to add to cart');
	}

	$cart_item_key = WC()->cart->add_to_cart($product_id, $quantity);

	if (defined('WP_DEBUG') && WP_DEBUG) {
		error_log("AJAX: Cart item key: " . ($cart_item_key ? $cart_item_key : 'false'));
	}

	if ($cart_item_key) {
		// Add WooCommerce success notice
		$product_name = $product->get_name();

		// Get current cart quantity for this product
		$cart_item = WC()->cart->get_cart_item($cart_item_key);
		$current_quantity = $cart_item ? $cart_item['quantity'] : $quantity;

		// Create appropriate message based on quantity
		if ($current_quantity > $quantity) {
			// Product was already in cart, quantity was updated
			$notice_message = sprintf(
				'<a href="%s" class="button wc-forward wp-element-button">%s</a> %s',
				esc_url(wc_get_cart_url()),
				__('View cart', 'woocommerce'),
				sprintf(__('"%s" quantity updated to %d in your cart.', 'woocommerce'), $product_name, $current_quantity)
			);
		} else {
			// New product added to cart
			$notice_message = sprintf(
				'<a href="%s" class="button wc-forward wp-element-button">%s</a> %s',
				esc_url(wc_get_cart_url()),
				__('View cart', 'woocommerce'),
				sprintf(__('"%s" has been added to your cart.', 'woocommerce'), $product_name)
			);
		}

		// Clear any existing notices for this product to avoid duplicates
		$existing_notices = wc_get_notices('success');
		foreach ($existing_notices as $key => $notice) {
			if (strpos($notice['notice'], $product_name) !== false) {
				unset($existing_notices[$key]);
			}
		}
		wc_set_notices(['success' => $existing_notices]);

		// Add the new notice
		wc_add_notice($notice_message, 'success');

		// Trigger WooCommerce events for minicart and notifications
		do_action('woocommerce_add_to_cart', $cart_item_key, $product_id, $quantity, 0, []);
		do_action('woocommerce_ajax_added_to_cart', $product_id, $quantity, 0);

		// Get cart fragments for minicart update
		$cart_fragments = apply_filters('woocommerce_add_to_cart_fragments', []);

		// Get notices for frontend display without clearing them
		$notices_html = '';
		if (function_exists('wc_get_notices')) {
			$notices = wc_get_notices('success');
			if (!empty($notices)) {
				ob_start();
				foreach ($notices as $notice) {
					wc_print_notice($notice['notice'], 'success', $notice['data'], false);
				}
				$notices_html = ob_get_clean();
			}
		}

		if (defined('WP_DEBUG') && WP_DEBUG) {
			error_log('AJAX: Success - sending success response with fragments and notices');
		}

		wp_send_json_success([
			'message' => 'Product added to cart',
			'cart_item_key' => $cart_item_key,
			'cart_count' => WC()->cart->get_cart_contents_count(),
			'cart_hash' => WC()->cart->get_cart_hash(),
			'fragments' => $cart_fragments,
			'notices' => $notices_html
		]);
	} else {
		if (defined('WP_DEBUG') && WP_DEBUG) {
			error_log('AJAX: Failed to add to cart - sending error response');
		}
		wp_send_json_error([
			'message' => 'Failed to add product to cart'
		]);
	}
}

// Load text domain
function mt_wc_bricks_add_to_cart_load_textdomain(): void {
	load_plugin_textdomain(
		'mt-wc-bricks-add_to_cart',
		false,
		dirname(plugin_basename(__FILE__)) . '/languages'
	);
}

// Plugin activation hook
function mt_wc_bricks_add_to_cart_activate(): void {
	// Check dependencies
	if (!mt_wc_bricks_add_to_cart_is_woocommerce_active()) {
		deactivate_plugins(plugin_basename(__FILE__));
		wp_die(
			esc_html__('MT WC Bricks Add to Cart requires WooCommerce to be installed and active.', 'mt-wc-bricks-add_to_cart'),
			esc_html__('Plugin Activation Error', 'mt-wc-bricks-add_to_cart'),
			['back_link' => true]
		);
	}

	if (!mt_wc_bricks_add_to_cart_is_bricks_active()) {
		deactivate_plugins(plugin_basename(__FILE__));
		wp_die(
			esc_html__('MT WC Bricks Add to Cart requires Bricks Builder theme to be installed and active.', 'mt-wc-bricks-add_to_cart'),
			esc_html__('Plugin Activation Error', 'mt-wc-bricks-add_to_cart'),
			['back_link' => true]
		);
	}

	// Flush rewrite rules
	flush_rewrite_rules();
}

// Plugin deactivation hook
function mt_wc_bricks_add_to_cart_deactivate(): void {
	// Flush rewrite rules
	flush_rewrite_rules();
}

// Hook into WordPress
add_action('init', 'mt_wc_bricks_add_to_cart_load_textdomain');
add_action('init', 'mt_wc_bricks_add_to_cart_register_element', 11);
add_action('wp_enqueue_scripts', 'mt_wc_bricks_add_to_cart_enqueue_assets');
add_action('wp_ajax_mt_wc_bricks_add_to_cart', 'mt_wc_bricks_add_to_cart_ajax_handler');
add_action('wp_ajax_nopriv_mt_wc_bricks_add_to_cart', 'mt_wc_bricks_add_to_cart_ajax_handler');

// Activation and deactivation hooks
register_activation_hook(__FILE__, 'mt_wc_bricks_add_to_cart_activate');
register_deactivation_hook(__FILE__, 'mt_wc_bricks_add_to_cart_deactivate');
