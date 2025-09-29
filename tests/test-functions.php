<?php
// Unit tests for MT WC Bricks Add to Cart plugin
// @package MT_WC_Bricks_Add_To_Cart

declare(strict_types=1);

// Prevent direct access
if (!defined('ABSPATH')) {
	exit;
}

// Simple test runner for core functions
class MT_WC_Bricks_Add_To_Cart_Tests {
	private array $tests = [];
	private int $passed = 0;
	private int $failed = 0;

	// Add a test
	public function add_test(string $name, callable $test): void {
		$this->tests[] = ['name' => $name, 'test' => $test];
	}

	// Run all tests
	public function run_tests(): void {
		foreach ($this->tests as $test) {
			try {
				$result = $test['test']();
				if ($result) {
					$this->passed++;
					error_log("[PASS] {$test['name']}");
				} else {
					$this->failed++;
					error_log("[FAIL] {$test['name']}");
				}
			} catch (Exception $e) {
				$this->failed++;
				error_log("[ERROR] {$test['name']}: " . $e->getMessage());
			}
		}

		error_log(sprintf("Tests completed: %d passed, %d failed", $this->passed, $this->failed));
	}

	// Test WooCommerce detection
	public function test_woocommerce_detection(): bool {
		return function_exists('mt_wc_bricks_add_to_cart_is_woocommerce_active');
	}

	// Test Bricks theme detection
	public function test_bricks_theme_detection(): bool {
		return function_exists('mt_wc_bricks_add_to_cart_is_bricks_active');
	}

	// Test asset version function
	public function test_asset_version_function(): bool {
		if (!function_exists('mt_wc_bricks_add_to_cart_get_asset_version')) {
			return false;
		}

		// Test with non-existent file (should return constant version)
		$version = mt_wc_bricks_add_to_cart_get_asset_version('non-existent-file.css');
		return $version === MT_WC_BRICKS_ADD_TO_CART_VERSION;
	}

	// Test error logging function
	public function test_error_logging_function(): bool {
		if (!function_exists('mt_wc_bricks_add_to_cart_log_error')) {
			return false;
		}

		// Test that function doesn't throw errors
		mt_wc_bricks_add_to_cart_log_error('Test error message', ['test' => true]);
		return true;
	}

	// Test element class exists
	public function test_element_class_exists(): bool {
		return class_exists('MT_WC_Bricks_Element_Add_To_Cart');
	}

	// Run all tests
	public function run(): void {
		$this->add_test('WooCommerce detection function exists', [$this, 'test_woocommerce_detection']);
		$this->add_test('Bricks theme detection function exists', [$this, 'test_bricks_theme_detection']);
		$this->add_test('Asset version function works', [$this, 'test_asset_version_function']);
		$this->add_test('Error logging function works', [$this, 'test_error_logging_function']);
		$this->add_test('Element class exists', [$this, 'test_element_class_exists']);

		$this->run_tests();
	}
}

// Run tests if WP_DEBUG is enabled
if (defined('WP_DEBUG') && WP_DEBUG && is_admin()) {
	add_action('admin_init', function() {
		$tests = new MT_WC_Bricks_Add_To_Cart_Tests();
		$tests->run();
	});
}
