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
		// Content tab - Product selection
		$this->controls['product'] = [
			'tab' => 'content',
			'label' => esc_html__('Product', 'mt-wc-bricks-add_to_cart'),
			'type' => 'select',
			'options' => $this->get_products_options(),
			'default' => 'current',
			'searchable' => true,
		];

		// Content tab - Button text
		$this->controls['button_text'] = [
			'tab' => 'content',
			'label' => esc_html__('Button Text', 'mt-wc-bricks-add_to_cart'),
			'type' => 'text',
			'default' => '',
			'placeholder' => esc_html__('Leave empty for icon-only button', 'mt-wc-bricks-add_to_cart'),
		];

		// Content tab - Icon position
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

		// Content tab - Nominal State
		$this->controls['nominal_state_heading'] = [
			'tab' => 'content',
			'label' => esc_html__('Nominal State', 'mt-wc-bricks-add_to_cart'),
			'type' => 'divider',
		];

		$this->controls['default_icon'] = [
			'tab' => 'content',
			'label' => esc_html__('Default Icon', 'mt-wc-bricks-add_to_cart'),
			'type' => 'icon',
			'default' => [
				'library' => 'fontawesome',
				'icon' => 'fas fa-shopping-cart'
			],
		];

		$this->controls['default_icon_color'] = [
			'tab' => 'content',
			'label' => esc_html__('Default Icon Color', 'mt-wc-bricks-add_to_cart'),
			'type' => 'color',
			'inline' => true,
		];

		$this->controls['button_background'] = [
			'tab' => 'content',
			'label' => esc_html__('Button Background Color', 'mt-wc-bricks-add_to_cart'),
			'type' => 'color',
			'inline' => true,
		];

		$this->controls['button_border_color'] = [
			'tab' => 'content',
			'label' => esc_html__('Button Border Color', 'mt-wc-bricks-add_to_cart'),
			'type' => 'color',
			'inline' => true,
		];

		$this->controls['button_text_color'] = [
			'tab' => 'content',
			'label' => esc_html__('Button Text Color', 'mt-wc-bricks-add_to_cart'),
			'type' => 'color',
			'inline' => true,
		];

		// Content tab - Hover State
		$this->controls['hover_state_heading'] = [
			'tab' => 'content',
			'label' => esc_html__('Hover State', 'mt-wc-bricks-add_to_cart'),
			'type' => 'divider',
		];

		$this->controls['button_background_hover'] = [
			'tab' => 'content',
			'label' => esc_html__('Button Background Color (Hover)', 'mt-wc-bricks-add_to_cart'),
			'type' => 'color',
			'inline' => true,
		];

		$this->controls['button_border_color_hover'] = [
			'tab' => 'content',
			'label' => esc_html__('Button Border Color (Hover)', 'mt-wc-bricks-add_to_cart'),
			'type' => 'color',
			'inline' => true,
		];

		$this->controls['button_text_color_hover'] = [
			'tab' => 'content',
			'label' => esc_html__('Button Text Color (Hover)', 'mt-wc-bricks-add_to_cart'),
			'type' => 'color',
			'inline' => true,
		];

		// Content tab - Error State
		$this->controls['error_state_heading'] = [
			'tab' => 'content',
			'label' => esc_html__('Error State', 'mt-wc-bricks-add_to_cart'),
			'type' => 'divider',
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

		$this->controls['error_icon_color'] = [
			'tab' => 'content',
			'label' => esc_html__('Error Icon Color', 'mt-wc-bricks-add_to_cart'),
			'type' => 'color',
			'inline' => true,
		];

		$this->controls['error_background'] = [
			'tab' => 'content',
			'label' => esc_html__('Error Background Color', 'mt-wc-bricks-add_to_cart'),
			'type' => 'color',
			'inline' => true,
		];

		// Content tab - Success State
		$this->controls['success_state_heading'] = [
			'tab' => 'content',
			'label' => esc_html__('Success State', 'mt-wc-bricks-add_to_cart'),
			'type' => 'divider',
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

		$this->controls['success_icon_color'] = [
			'tab' => 'content',
			'label' => esc_html__('Success Icon Color', 'mt-wc-bricks-add_to_cart'),
			'type' => 'color',
			'inline' => true,
		];

		$this->controls['success_background'] = [
			'tab' => 'content',
			'label' => esc_html__('Success Background Color', 'mt-wc-bricks-add_to_cart'),
			'type' => 'color',
			'inline' => true,
		];

		// Content tab - Loading State
		$this->controls['loading_state_heading'] = [
			'tab' => 'content',
			'label' => esc_html__('Loading State', 'mt-wc-bricks-add_to_cart'),
			'type' => 'divider',
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

		$this->controls['loading_icon_color'] = [
			'tab' => 'content',
			'label' => esc_html__('Loading Icon Color', 'mt-wc-bricks-add_to_cart'),
			'type' => 'color',
			'inline' => true,
		];

		// Content tab - View Cart Button
		$this->controls['view_cart_heading'] = [
			'tab' => 'content',
			'label' => esc_html__('View Cart Button', 'mt-wc-bricks-add_to_cart'),
			'type' => 'divider',
		];

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

		$this->controls['view_cart_icon_color'] = [
			'tab' => 'content',
			'label' => esc_html__('View Cart Icon Color', 'mt-wc-bricks-add_to_cart'),
			'type' => 'color',
			'inline' => true,
			'required' => ['show_view_cart', '=', true],
		];

		$this->controls['view_cart_background'] = [
			'tab' => 'content',
			'label' => esc_html__('View Cart Background Color', 'mt-wc-bricks-add_to_cart'),
			'type' => 'color',
			'inline' => true,
			'required' => ['show_view_cart', '=', true],
		];

		$this->controls['view_cart_border_color'] = [
			'tab' => 'content',
			'label' => esc_html__('View Cart Border Color', 'mt-wc-bricks-add_to_cart'),
			'type' => 'color',
			'inline' => true,
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

		// Content tab - View Cart Hover
		$this->controls['view_cart_hover_heading'] = [
			'tab' => 'content',
			'label' => esc_html__('View Cart Hover', 'mt-wc-bricks-add_to_cart'),
			'type' => 'divider',
		];

		$this->controls['view_cart_icon_color_hover'] = [
			'tab' => 'content',
			'label' => esc_html__('View Cart Icon Color (Hover)', 'mt-wc-bricks-add_to_cart'),
			'type' => 'color',
			'inline' => true,
			'required' => ['show_view_cart', '=', true],
		];

		$this->controls['view_cart_background_hover'] = [
			'tab' => 'content',
			'label' => esc_html__('View Cart Background Color (Hover)', 'mt-wc-bricks-add_to_cart'),
			'type' => 'color',
			'inline' => true,
			'required' => ['show_view_cart', '=', true],
		];

		$this->controls['view_cart_border_color_hover'] = [
			'tab' => 'content',
			'label' => esc_html__('View Cart Border Color (Hover)', 'mt-wc-bricks-add_to_cart'),
			'type' => 'color',
			'inline' => true,
			'required' => ['show_view_cart', '=', true],
		];

		// Enable Bricks built-in controls if needed
		$this->set_controls_before();
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

		// Inline CSS variables
		$button_bg = $this->get_setting($settings, 'button_background', '');
		$button_bg_hover = $this->get_setting($settings, 'button_background_hover', '');
		$button_text_color = $this->get_setting($settings, 'button_text_color', '');
		$button_text_color_hover = $this->get_setting($settings, 'button_text_color_hover', '');
		$button_border_color = $this->get_setting($settings, 'button_border_color', '');
		$button_border_color_hover = $this->get_setting($settings, 'button_border_color_hover', '');
		$default_icon_color = $this->get_setting($settings, 'default_icon_color', '');
		$loading_icon_color = $this->get_setting($settings, 'loading_icon_color', '');
		$success_icon_color = $this->get_setting($settings, 'success_icon_color', '');
		$error_icon_color = $this->get_setting($settings, 'error_icon_color', '');
		$success_bg = $this->get_setting($settings, 'success_background', '');
		$error_bg = $this->get_setting($settings, 'error_background', '');

		// View cart colors
		$view_cart_icon_color = $this->get_setting($settings, 'view_cart_icon_color', '');
		$view_cart_background = $this->get_setting($settings, 'view_cart_background', '');
		$view_cart_border_color = $this->get_setting($settings, 'view_cart_border_color', '');
		$view_cart_icon_color_hover = $this->get_setting($settings, 'view_cart_icon_color_hover', '');
		$view_cart_background_hover = $this->get_setting($settings, 'view_cart_background_hover', '');
		$view_cart_border_color_hover = $this->get_setting($settings, 'view_cart_border_color_hover', '');

		$style_vars = [];
		if (!empty($button_bg)) $style_vars[] = '--mt-wc-button-bg:' . $this->sanitize_color_value($button_bg);
		if (!empty($button_bg_hover)) $style_vars[] = '--mt-wc-button-bg-hover:' . $this->sanitize_color_value($button_bg_hover);
		if (!empty($button_text_color)) $style_vars[] = '--mt-wc-text-color:' . $this->sanitize_color_value($button_text_color);
		if (!empty($button_text_color_hover)) $style_vars[] = '--mt-wc-text-color-hover:' . $this->sanitize_color_value($button_text_color_hover);
		if (!empty($button_border_color)) $style_vars[] = '--mt-wc-border-color:' . $this->sanitize_color_value($button_border_color);
		if (!empty($button_border_color_hover)) $style_vars[] = '--mt-wc-border-color-hover:' . $this->sanitize_color_value($button_border_color_hover);
		if (!empty($default_icon_color)) $style_vars[] = '--mt-wc-icon-color-default:' . $this->sanitize_color_value($default_icon_color);
		if (!empty($default_icon_color)) $style_vars[] = '--mt-wc-icon-color:' . $this->sanitize_color_value($default_icon_color);
		if (!empty($loading_icon_color)) $style_vars[] = '--mt-wc-icon-color-loading:' . $this->sanitize_color_value($loading_icon_color);
		if (!empty($success_icon_color)) $style_vars[] = '--mt-wc-icon-color-success:' . $this->sanitize_color_value($success_icon_color);
		if (!empty($error_icon_color)) $style_vars[] = '--mt-wc-icon-color-error:' . $this->sanitize_color_value($error_icon_color);
		if (!empty($success_bg)) $style_vars[] = '--mt-wc-button-bg-success:' . $this->sanitize_color_value($success_bg);
		if (!empty($error_bg)) $style_vars[] = '--mt-wc-button-bg-error:' . $this->sanitize_color_value($error_bg);

		// View cart colors
		if (!empty($view_cart_icon_color)) $style_vars[] = '--mt-wc-view-cart-icon-color:' . $this->sanitize_color_value($view_cart_icon_color);
		if (!empty($view_cart_background)) $style_vars[] = '--mt-wc-view-cart-bg:' . $this->sanitize_color_value($view_cart_background);
		if (!empty($view_cart_border_color)) $style_vars[] = '--mt-wc-view-cart-border-color:' . $this->sanitize_color_value($view_cart_border_color);
		if (!empty($view_cart_icon_color_hover)) $style_vars[] = '--mt-wc-view-cart-icon-color-hover:' . $this->sanitize_color_value($view_cart_icon_color_hover);
		if (!empty($view_cart_background_hover)) $style_vars[] = '--mt-wc-view-cart-bg-hover:' . $this->sanitize_color_value($view_cart_background_hover);
		if (!empty($view_cart_border_color_hover)) $style_vars[] = '--mt-wc-view-cart-border-color-hover:' . $this->sanitize_color_value($view_cart_border_color_hover);

		$style_attr = !empty($style_vars) ? ' style="' . esc_attr(implode(';', $style_vars)) . '"' : '';

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
						data-show-view-cart="<?php echo esc_attr($show_view_cart ? '1' : '0'); ?>"
						aria-label="<?php echo esc_attr($display_text); ?>"
					<?php echo $style_attr; ?>
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

	// Sanitize color value for safe inline CSS variables
	private function sanitize_color_value($value): string {
		if (is_array($value)) {
			return $this->extract_color_from_bricks_array($value);
		}
		return $this->sanitize_color_string((string) $value);
	}

	// Extract hex/rgba value from Bricks color array structure
	private function extract_color_from_bricks_array(array $value): string {
		$rgba = $value['rgba'] ?? '';
		if (is_string($rgba) && $rgba !== '') {
			return $this->sanitize_color_string($rgba);
		}
		$has_full_alpha = isset($value['alpha']) ? (float) $value['alpha'] >= 1 : true;
		$hex = $has_full_alpha && !empty($value['hex']) ? $value['hex'] : '';
		if (is_string($hex) && $hex !== '') {
			return $this->sanitize_color_string($hex);
		}
		$color = $value['color'] ?? '';
		if (is_string($color) && $color !== '') {
			return $this->sanitize_color_string($color);
		}
		return '';
	}

	// Validate and return a safe CSS color string
	private function sanitize_color_string(string $value): string {
		$value = trim($value);
		if ($value === '') {
			return '';
		}
		if (preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/', $value)) {
			return $value;
		}
		if (preg_match('/^(rgb|rgba|hsl|hsla)\(/i', $value)) {
			return $value;
		}
		if (preg_match('/^[a-zA-Z]+$/', $value)) {
			return $value;
		}
		return '';
	}
}
