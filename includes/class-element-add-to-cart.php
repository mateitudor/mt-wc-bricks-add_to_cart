<?php
declare(strict_types=1);

namespace MT\WC\Bricks\AddToCart;

use Bricks\Elements;
use Bricks\Helpers;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Add to Cart Element for Bricks Builder
 */
class Element_Add_To_Cart extends \Bricks\Element {

	// Element properties
	public $category = 'woocommerce';
	public $name = 'mt-wc-add-to-cart';
	public $icon = 'fas fa-shopping-bag';
	public $scripts = ['mt-wc-bricks-add-to-cart'];

	/**
	 * Get element label
	 */
	public function get_label() {
		return esc_html__('Add to Cart Button', 'mt-wc-bricks-add_to_cart');
	}

	/**
	 * Set element controls
	 */
	public function set_controls() {
		// Product selection
		$this->controls['product'] = [
			'tab' => 'content',
			'label' => esc_html__('Product', 'mt-wc-bricks-add_to_cart'),
			'type' => 'select',
			'options' => $this->get_products_options(),
			'default' => 'current',
			'searchable' => true,
		];

		// Button text
		$this->controls['button_text'] = [
			'tab' => 'content',
			'label' => esc_html__('Button Text', 'mt-wc-bricks-add_to_cart'),
			'type' => 'text',
			'default' => '',
			'placeholder' => esc_html__('Leave empty to use product default', 'mt-wc-bricks-add_to_cart'),
		];

		// Icon position
		$this->controls['icon_position'] = [
			'tab' => 'content',
			'label' => esc_html__('Icon Position', 'mt-wc-bricks-add_to_cart'),
			'type' => 'select',
			'options' => [
				'left' => esc_html__('Left', 'mt-wc-bricks-add_to_cart'),
				'right' => esc_html__('Right', 'mt-wc-bricks-add_to_cart'),
			],
			'default' => 'left',
		];

		// Icons
		$this->controls['default_icon'] = [
			'tab' => 'content',
			'label' => esc_html__('Default Icon', 'mt-wc-bricks-add_to_cart'),
			'type' => 'icon',
			'default' => [
				'library' => 'fontawesome',
				'icon' => 'fas fa-shopping-cart'
			],
		];

		$this->controls['loading_icon'] = [
			'tab' => 'content',
			'label' => esc_html__('Loading Icon', 'mt-wc-bricks-add_to_cart'),
			'type' => 'icon',
			'default' => [
				'library' => 'fontawesome',
				'icon' => 'fas fa-spinner'
			],
		];

		$this->controls['success_icon'] = [
			'tab' => 'content',
			'label' => esc_html__('Success Icon', 'mt-wc-bricks-add_to_cart'),
			'type' => 'icon',
			'default' => [
				'library' => 'fontawesome',
				'icon' => 'fas fa-check'
			],
		];

		$this->controls['error_icon'] = [
			'tab' => 'content',
			'label' => esc_html__('Error Icon', 'mt-wc-bricks-add_to_cart'),
			'type' => 'icon',
			'default' => [
				'library' => 'fontawesome',
				'icon' => 'fas fa-times'
			],
		];

		// View cart button
		$this->controls['show_view_cart'] = [
			'tab' => 'content',
			'label' => esc_html__('Show View Cart Button', 'mt-wc-bricks-add_to_cart'),
			'type' => 'checkbox',
			'default' => false,
		];

		$this->controls['view_cart_icon'] = [
			'tab' => 'content',
			'label' => esc_html__('View Cart Icon', 'mt-wc-bricks-add_to_cart'),
			'type' => 'icon',
			'default' => [
				'library' => 'fontawesome',
				'icon' => 'fas fa-shopping-bag'
			],
			'required' => ['show_view_cart', '=', true],
		];

		$this->controls['view_cart_text'] = [
			'tab' => 'content',
			'label' => esc_html__('View Cart Text', 'mt-wc-bricks-add_to_cart'),
			'type' => 'text',
			'default' => '',
			'placeholder' => esc_html__('View Cart', 'mt-wc-bricks-add_to_cart'),
			'required' => ['show_view_cart', '=', true],
		];

		// Colors
		$this->controls['button_color'] = [
			'tab' => 'style',
			'label' => esc_html__('Button Color', 'mt-wc-bricks-add_to_cart'),
			'type' => 'color',
			'default' => '#007cba',
		];

		$this->controls['button_hover_color'] = [
			'tab' => 'style',
			'label' => esc_html__('Button Hover Color', 'mt-wc-bricks-add_to_cart'),
			'type' => 'color',
			'default' => '#005a87',
		];

		$this->controls['success_color'] = [
			'tab' => 'style',
			'label' => esc_html__('Success Color', 'mt-wc-bricks-add_to_cart'),
			'type' => 'color',
			'default' => '#00a32a',
		];

		$this->controls['error_color'] = [
			'tab' => 'style',
			'label' => esc_html__('Error Color', 'mt-wc-bricks-add_to_cart'),
			'type' => 'color',
			'default' => '#d63638',
		];
	}

	/**
	 * Render the element
	 */
	public function render() {
		$settings = $this->settings;
		$product = $this->get_product($settings);

		// Validate product
		if (!$product || !is_a($product, 'WC_Product') || !$product->is_purchasable()) {
			$this->set_attribute('_root', 'class', 'mt-wc-add-to-cart-wrapper');
			echo '<div class="mt-wc-add-to-cart-wrapper">';
			echo '<p>' . esc_html__('No valid product found.', 'mt-wc-bricks-add_to_cart') . '</p>';
			echo '</div>';
			return;
		}

		// Get settings
		$button_text = $this->get_setting($settings, 'button_text', '');
		$icon_position = $this->get_setting($settings, 'icon_position', 'left');
		// Prepare icons in both formats: array (for Bricks renderer) and string (for JS data attributes)
		$default_icon_arr = $this->getIconArray($settings, 'default_icon');
		$loading_icon_arr = $this->getIconArray($settings, 'loading_icon');
		$success_icon_arr = $this->getIconArray($settings, 'success_icon');
		$error_icon_arr = $this->getIconArray($settings, 'error_icon');

		$default_icon = $this->getIconString($settings, 'default_icon');
		$loading_icon = $this->getIconString($settings, 'loading_icon');
		$success_icon = $this->getIconString($settings, 'success_icon');
		$error_icon = $this->getIconString($settings, 'error_icon');
		$show_view_cart = $this->get_setting($settings, 'show_view_cart', false);
		$view_cart_icon_arr = $this->getIconArray($settings, 'view_cart_icon');
		$view_cart_icon = $this->getIconString($settings, 'view_cart_icon');
		$view_cart_text = $this->get_setting($settings, 'view_cart_text', '');

		// Get fallback text
		$fallback_text = $product->single_add_to_cart_text() ?: __('Add to cart', 'woocommerce');
		$display_text = $button_text ?: $fallback_text;

		// Build button classes
		$button_classes = ['mt-wc-add-to-cart-button', 'button', 'alt'];

		// Add icon position class only if text is present
		if ($button_text && $icon_position) {
			$button_classes[] = 'icon-position_' . $icon_position;
		}

		// Set root attributes
		$this->set_attribute('_root', 'class', 'mt-wc-add-to-cart-wrapper');
		$this->set_attribute('_root', 'data-product-id', $product->get_id());

		// Render the element
		?>
		<div class="mt-wc-add-to-cart-wrapper">
			<?php if ($product->is_in_stock()) : ?>
				<div class="mt-wc-buttons-container">
					<button
						type="button"
						class="<?php echo esc_attr(implode(' ', $button_classes)); ?>"
						data-product-id="<?php echo esc_attr($product->get_id()); ?>"
						data-quantity="1"
						data-default-icon="<?php echo esc_attr($default_icon); ?>"
						data-loading-icon="<?php echo esc_attr($loading_icon); ?>"
						data-success-icon="<?php echo esc_attr($success_icon); ?>"
						data-error-icon="<?php echo esc_attr($error_icon); ?>"
						aria-label="<?php echo esc_attr($display_text); ?>"
					>
						<?php echo self::render_icon($default_icon_arr, ['icon']); ?>

						<?php if ($button_text) : ?>
							<span class="button-text"><?php echo esc_html($button_text); ?></span>
						<?php endif; ?>
					</button>

					<?php if ($show_view_cart) : ?>
						<a
							href="<?php echo esc_url(wc_get_cart_url()); ?>"
							class="mt-wc-view-cart-button button"
							aria-label="<?php echo esc_attr($view_cart_text ?: __('View Cart', 'mt-wc-bricks-add_to_cart')); ?>"
						>
							<?php echo self::render_icon($view_cart_icon_arr, ['icon']); ?>

							<?php if ($view_cart_text) : ?>
								<span class="button-text"><?php echo esc_html($view_cart_text); ?></span>
							<?php endif; ?>
						</a>
					<?php endif; ?>
				</div>
			<?php else : ?>
				<p class="mt-wc-out-of-stock">
					<?php echo esc_html($product->get_stock_status() === 'onbackorder' ? __('Available on backorder', 'woocommerce') : __('Out of stock', 'woocommerce')); ?>
				</p>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Get product based on settings
	 */
	private function get_product($settings) {
		$product_setting = $this->get_setting($settings, 'product', 'current');

		// Handle query loop context
		if (class_exists('\Bricks\Query') && \Bricks\Query::is_any_looping()) {
			$loop_object = \Bricks\Query::get_loop_object();
			if ($loop_object && is_a($loop_object, 'WC_Product')) {
				return $loop_object;
			}

			// Derive product from loop object when it is a post
			if ($loop_object instanceof \WP_Post) {
				$product = wc_get_product($loop_object->ID);
				if ($product && is_a($product, 'WC_Product')) {
					return $product;
				}
			}

			$post = get_post();
			if ($post) {
				$product = wc_get_product($post->ID);
				if ($product && is_a($product, 'WC_Product')) {
					return $product;
				}
			}
		}

		// Handle specific product selection
		if ($product_setting && $product_setting !== 'current') {
			$product = wc_get_product($product_setting);
			if ($product && is_a($product, 'WC_Product')) {
				return $product;
			}
		}

		// Fallback to global product
		global $product;
		if ($product && is_a($product, 'WC_Product')) {
			return $product;
		}

		return null;
	}

	/**
	 * Get setting value with default
	 */
	private function get_setting($settings, string $key, $default = '') {
		if (isset($settings[$key]) && !empty($settings[$key])) {
			return $settings[$key];
		}
		return $default;
	}

	/**
	 * Get icon value from settings
	 */
	private function get_icon_value($settings, string $key) {
		$value = $this->get_setting($settings, $key, []);

		if (is_array($value) && isset($value['library']) && isset($value['icon'])) {
			$library = $value['library'];
			$icon = $value['icon'];

			// Handle FontAwesome library variations
			if ($library === 'fontawesomeSolid') {
				$library = 'fontawesome';
			}

			return $library . ' ' . $icon;
		}

		// Fallback to string value
		if (is_string($value) && !empty($value)) {
			return $value;
		}

		// Return default based on key
		$defaults = [
			'default_icon' => 'fontawesome fas fa-shopping-cart',
			'loading_icon' => 'fontawesome fas fa-spinner',
			'success_icon' => 'fontawesome fas fa-check',
			'error_icon' => 'fontawesome fas fa-times',
			'view_cart_icon' => 'fontawesome fas fa-shopping-bag',
		];

		return $defaults[$key] ?? 'fontawesome fas fa-shopping-cart';
	}

	// Convert control value to Bricks icon array
	private function getIconArray($settings, string $key): array {
		$value = $this->get_setting($settings, $key, []);
		if (is_array($value) && isset($value['library']) && isset($value['icon'])) {
			$library = $value['library'] === 'fontawesomeSolid' ? 'fontawesome' : $value['library'];
			return [
				'library' => $library,
				'icon' => $value['icon'],
			];
		}
		// Defaults
		$defaults = [
			'default_icon' => ['library' => 'fontawesome', 'icon' => 'fas fa-shopping-cart'],
			'loading_icon' => ['library' => 'fontawesome', 'icon' => 'fas fa-spinner'],
			'success_icon' => ['library' => 'fontawesome', 'icon' => 'fas fa-check'],
			'error_icon' => ['library' => 'fontawesome', 'icon' => 'fas fa-times'],
			'view_cart_icon' => ['library' => 'fontawesome', 'icon' => 'fas fa-shopping-bag'],
		];
		return $defaults[$key] ?? ['library' => 'fontawesome', 'icon' => 'fas fa-shopping-cart'];
	}

	// Convert control value to "library classes" string for JS data-* attributes
	private function getIconString($settings, string $key): string {
		$icon = $this->getIconArray($settings, $key);
		return trim(($icon['library'] ?? 'fontawesome') . ' ' . ($icon['icon'] ?? 'fas fa-shopping-cart'));
	}

	/**
	 * Render icon using Bricks icon system
	 * Note: Method name avoids clashing with Bricks\Element::render_icon (static)
	 */
	private function renderElementIcon(string $icon_value) {
		// Parse icon value
		$parts = explode(' ', $icon_value, 2);
		$library = $parts[0] ?? 'fontawesome';
		$icon = $parts[1] ?? 'fas fa-shopping-cart';

		// Use Bricks icon system
		if (class_exists('\Bricks\Helpers')) {
			$icon_data = [
				'library' => $library,
				'name' => $icon
			];

			$icon_html = \Bricks\Helpers::get_icon_html($icon_data, [
				'class' => 'icon',
				'aria-hidden' => 'true'
			]);

			if ($icon_html) {
				return $icon_html;
			}
		}

		// Fallback to simple icon
		return sprintf(
			'<i class="icon %s" aria-hidden="true"></i>',
			esc_attr($icon)
		);
	}

	/**
	 * Get products for select control
	 */
	private function get_products_options() {
		$options = [
			'current' => esc_html__('Current Product', 'mt-wc-bricks-add_to_cart'),
		];

		$products = wc_get_products([
			'limit' => 100,
			'status' => 'publish',
		]);

		foreach ($products as $product) {
			$options[$product->get_id()] = $product->get_name();
		}

		return $options;
	}
}
