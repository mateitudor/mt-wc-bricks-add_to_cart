<?php
// Bricks Add to Cart Element
// @package MT_WC_Bricks_Add_To_Cart

declare(strict_types=1);

// Prevent direct access
if (!defined('ABSPATH')) {
	exit;
}

// Element_Add_To_Cart class
class MT_WC_Bricks_Element_Add_To_Cart extends \Bricks\Element {
	// Element category
	public $category = 'woocommerce';

	// Element name
	public $name = 'mt-wc-add-to-cart';

	// Element icon
	public $icon = 'fas fa-shopping-cart';

	// CSS selector
	public $css_selector = '.mt-wc-add-to-cart-wrapper';

	// Get element label
	public function get_label(): string {
		return esc_html__('Add to Cart Button', 'mt-wc-bricks-add_to_cart');
	}

	// Set control groups
	public function set_control_groups(): void {
		$this->control_groups['content'] = [
			'title' => esc_html__('Content', 'mt-wc-bricks-add_to_cart'),
			'tab'   => 'content',
		];

		$this->control_groups['button'] = [
			'title' => esc_html__('Button', 'mt-wc-bricks-add_to_cart'),
			'tab'   => 'content',
		];

		$this->control_groups['colors'] = [
			'title' => esc_html__('Colors', 'mt-wc-bricks-add_to_cart'),
			'tab'   => 'style',
		];
	}

	// Set controls
	public function set_controls(): void {
		// Product selection
		$this->controls['product'] = [
			'tab'         => 'content',
			'group'       => 'content',
			'label'       => esc_html__('Product', 'mt-wc-bricks-add_to_cart'),
			'type'        => 'select',
			'options'     => $this->get_product_options(),
			'default'     => 'current',
		];

		// Button text (optional)
		$this->controls['button_text'] = [
			'tab'         => 'content',
			'group'       => 'button',
			'label'       => esc_html__('Button Text (Optional)', 'mt-wc-bricks-add_to_cart'),
			'type'        => 'text',
			'default'     => '',
			'placeholder' => esc_html__('Leave empty for icon-only button', 'mt-wc-bricks-add_to_cart'),
		];

		// Icon position (only when text is present)
		$this->controls['icon_position'] = [
			'tab'         => 'content',
			'group'       => 'button',
			'label'       => esc_html__('Icon Position', 'mt-wc-bricks-add_to_cart'),
			'type'        => 'select',
			'options'     => [
				'left'  => esc_html__('Left', 'mt-wc-bricks-add_to_cart'),
				'right' => esc_html__('Right', 'mt-wc-bricks-add_to_cart'),
			],
			'default'     => 'left',
		];

		// Default icon
		$this->controls['default_icon'] = [
			'tab'         => 'content',
			'group'       => 'button',
			'label'       => esc_html__('Default Icon', 'mt-wc-bricks-add_to_cart'),
			'type'        => 'icon',
			'default'     => 'fas fa-shopping-cart',
		];

		// Loading icon
		$this->controls['loading_icon'] = [
			'tab'         => 'content',
			'group'       => 'button',
			'label'       => esc_html__('Loading Icon', 'mt-wc-bricks-add_to_cart'),
			'type'        => 'icon',
			'default'     => 'fas fa-spinner',
		];

		// Success icon
		$this->controls['success_icon'] = [
			'tab'         => 'content',
			'group'       => 'button',
			'label'       => esc_html__('Success Icon', 'mt-wc-bricks-add_to_cart'),
			'type'        => 'icon',
			'default'     => 'fas fa-check',
		];

		// Error icon
		$this->controls['error_icon'] = [
			'tab'         => 'content',
			'group'       => 'button',
			'label'       => esc_html__('Error Icon', 'mt-wc-bricks-add_to_cart'),
			'type'        => 'icon',
			'default'     => 'fas fa-times',
		];

		// Show view cart button
		$this->controls['show_view_cart'] = [
			'tab'         => 'content',
			'group'       => 'button',
			'label'       => esc_html__('Show View Cart Button', 'mt-wc-bricks-add_to_cart'),
			'type'        => 'checkbox',
			'default'     => false,
		];

		// View cart icon
		$this->controls['view_cart_icon'] = [
			'tab'         => 'content',
			'group'       => 'button',
			'label'       => esc_html__('View Cart Icon', 'mt-wc-bricks-add_to_cart'),
			'type'        => 'icon',
			'default'     => 'fas fa-shopping-bag',
			'required'    => ['show_view_cart', '=', true],
		];

		// View cart text
		$this->controls['view_cart_text'] = [
			'tab'         => 'content',
			'group'       => 'button',
			'label'       => esc_html__('View Cart Text', 'mt-wc-bricks-add_to_cart'),
			'type'        => 'text',
			'default'     => '',
			'placeholder' => esc_html__('Leave empty for icon-only', 'mt-wc-bricks-add_to_cart'),
			'required'    => ['show_view_cart', '=', true],
		];

		// Color controls
		$this->controls['button_color'] = [
			'tab'         => 'style',
			'group'       => 'colors',
			'label'       => esc_html__('Button Color', 'mt-wc-bricks-add_to_cart'),
			'type'        => 'color',
			'default'     => '#007cba',
			'css'         => [
				[
					'selector' => '.mt-wc-add-to-cart-button',
					'property' => '--mt-wc-color-primary'
				]
			]
		];

		$this->controls['button_hover_color'] = [
			'tab'         => 'style',
			'group'       => 'colors',
			'label'       => esc_html__('Button Hover Color', 'mt-wc-bricks-add_to_cart'),
			'type'        => 'color',
			'default'     => '#005a87',
			'css'         => [
				[
					'selector' => '.mt-wc-add-to-cart-button',
					'property' => '--mt-wc-color-primary-hover'
				]
			]
		];

		$this->controls['success_color'] = [
			'tab'         => 'style',
			'group'       => 'colors',
			'label'       => esc_html__('Success Color', 'mt-wc-bricks-add_to_cart'),
			'type'        => 'color',
			'default'     => '#00a32a',
			'css'         => [
				[
					'selector' => '.mt-wc-add-to-cart-button',
					'property' => '--mt-wc-color-success'
				]
			]
		];

		$this->controls['error_color'] = [
			'tab'         => 'style',
			'group'       => 'colors',
			'label'       => esc_html__('Error Color', 'mt-wc-bricks-add_to_cart'),
			'type'        => 'color',
			'default'     => '#d63638',
			'css'         => [
				[
					'selector' => '.mt-wc-add-to-cart-button',
					'property' => '--mt-wc-color-error'
				]
			]
		];
	}

	// Render the element
	public function render(): void {
		$settings = $this->settings;
		$product = $this->get_product($settings);

		// Validate product
		if (!$product || !is_a($product, 'WC_Product') || !$product->is_purchasable()) {
			$this->render_element_placeholder([
				'title' => esc_html__('No valid product found.', 'mt-wc-bricks-add_to_cart'),
			]);
			return;
		}

		$this->set_attribute('_root', 'class', 'mt-wc-add-to-cart-wrapper');
		$this->set_attribute('_root', 'data-product-id', $product->get_id());

		// Get settings
		$button_text = $this->get_setting($settings, 'button_text', '');
		$icon_position = $this->get_setting($settings, 'icon_position', 'left');
		$default_icon = $this->get_icon_value($settings, 'default_icon', 'fontawesome fas fa-shopping-cart');
		$loading_icon = $this->get_icon_value($settings, 'loading_icon', 'fontawesome fas fa-spinner');
		$success_icon = $this->get_icon_value($settings, 'success_icon', 'fontawesome fas fa-check');
		$error_icon = $this->get_icon_value($settings, 'error_icon', 'fontawesome fas fa-times');
		$show_view_cart = $this->get_setting($settings, 'show_view_cart', false);
		$view_cart_icon = $this->get_icon_value($settings, 'view_cart_icon', 'fontawesome fas fa-shopping-bag');
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
						<?php echo $this->build_bricks_icon('icon', $default_icon); ?>

						<?php if ($button_text) : ?>
							<span class="button-text"><?php echo esc_html($button_text); ?></span>
						<?php endif; ?>
					</button>

					<!-- Debug info -->
					<?php if (defined('WP_DEBUG') && WP_DEBUG) : ?>
						<div style="font-size: 12px; color: #666; margin-top: 5px; background: #f0f0f0; padding: 10px; border-radius: 4px;">
							<strong>Icon Debug:</strong><br>
							Default: <?php echo esc_html($default_icon); ?><br>
							Loading: <?php echo esc_html($loading_icon); ?><br>
							Success: <?php echo esc_html($success_icon); ?><br>
							Error: <?php echo esc_html($error_icon); ?><br>
							View Cart: <?php echo esc_html($view_cart_icon); ?><br>
							<strong>Raw Settings:</strong><br>
							<?php
							$icon_settings = [
								'default_icon' => $settings['default_icon'] ?? 'not set',
								'loading_icon' => $settings['loading_icon'] ?? 'not set',
								'success_icon' => $settings['success_icon'] ?? 'not set',
								'error_icon' => $settings['error_icon'] ?? 'not set'
							];
							foreach ($icon_settings as $key => $value) {
								echo $key . ': ' . (is_array($value) ? json_encode($value) : $value) . '<br>';
							}
							?>
						</div>
					<?php endif; ?>

					<?php if ($show_view_cart) : ?>
						<a
							href="<?php echo esc_url(wc_get_cart_url()); ?>"
							class="mt-wc-view-cart-button button"
							style="display: none;"
							aria-label="<?php echo esc_attr($view_cart_text ?: __('View Cart', 'woocommerce')); ?>"
						>
							<?php echo $this->build_bricks_icon('icon', $view_cart_icon); ?>

							<?php if ($view_cart_text) : ?>
								<span class="button-text"><?php echo esc_html($view_cart_text); ?></span>
							<?php endif; ?>
						</a>
					<?php endif; ?>
				</div>
			<?php else : ?>
				<p class="stock out-of-stock"><?php esc_html_e('This product is currently out of stock.', 'woocommerce'); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}

	// Get product based on settings
	private function get_product($settings) {
		$product_selection = $this->get_setting($settings, 'product', 'current');

		if ($product_selection === 'current') {
			// First try to get product from Bricks query loop context
			if (class_exists('\Bricks\Query') && \Bricks\Query::is_any_looping()) {
				$loop_object = \Bricks\Query::get_loop_object();

				if ($loop_object) {
					// Handle different loop object types
					if (is_a($loop_object, 'WP_Post')) {
						$product = wc_get_product($loop_object->ID);
						if ($product && is_a($product, 'WC_Product')) {
							return $product;
						}
					} elseif (is_a($loop_object, 'WC_Product')) {
						return $loop_object;
					}
				}
			}

			// Fallback to global product (for single product pages)
			global $product;
			if ($product && is_a($product, 'WC_Product')) {
				return $product;
			}

			// Fallback to current post
			$post_id = get_the_ID();
			if ($post_id) {
				return wc_get_product($post_id);
			}
			return null;
		}

		if (is_numeric($product_selection)) {
			return wc_get_product((int) $product_selection);
		}

		return null;
	}

	// Get setting value with fallback
	private function get_setting($settings, string $key, $default = '') {
		return !empty($settings[$key]) ? $settings[$key] : $default;
	}

	// Get icon value from settings (handles Bricks icon control format)
	private function get_icon_value($settings, string $key, string $default = '') {
		$value = $this->get_setting($settings, $key, $default);

		// Handle Bricks icon control format - it returns arrays with 'library' and 'icon' properties
		if (is_array($value)) {
			$library = $value['library'] ?? 'fontawesome';
			$icon = $value['icon'] ?? '';

			// Convert library names to proper format
			if ($library === 'fontawesomeSolid') {
				$library = 'fontawesome';
			}

			// Return in format: "library icon-class"
			return $library . ' ' . $icon;
		}

		// If it's a string, return as is
		if (is_string($value) && !empty($value)) {
			return $value;
		}

		// Return default with proper format
		$default_icons = [
			'default_icon' => 'fontawesome fas fa-shopping-cart',
			'loading_icon' => 'fontawesome fas fa-spinner',
			'success_icon' => 'fontawesome fas fa-check',
			'error_icon' => 'fontawesome fas fa-times',
			'view_cart_icon' => 'fontawesome fas fa-shopping-bag'
		];

		return $default_icons[$key] ?? $default;
	}

	// Build Bricks icon HTML with proper integration
	private function build_bricks_icon(string $class, string $icon_name): string {
		// Parse icon name (e.g., "fontawesome fas fa-shopping-cart")
		$icon_parts = explode(' ', $icon_name);
		$library = $icon_parts[0] ?? 'fontawesome';
		$icon_class = implode(' ', array_slice($icon_parts, 1));

		// Try Bricks icon system first
		if (class_exists('\Bricks\Helpers') && method_exists('\Bricks\Helpers', 'get_icon_html')) {
			try {
				$icon_data = [
					'library' => $library,
					'name' => $icon_class
				];

				$icon_html = \Bricks\Helpers::get_icon_html($icon_data, [
					'class' => 'icon ' . esc_attr($class),
					'aria-hidden' => 'true'
				]);

				if ($icon_html) {
					return $icon_html;
				}
			} catch (Exception $e) {
				// Log error in debug mode
				if (defined('WP_DEBUG') && WP_DEBUG) {
					error_log('Bricks icon error: ' . $e->getMessage());
				}
			}
		}

		// Fallback to simple icon with proper accessibility
		return sprintf(
			'<i class="icon %s %s" aria-hidden="true"></i>',
			esc_attr($class),
			esc_attr($icon_class)
		);
	}

	// Get product options for select control
	private function get_product_options(): array {
		$options = [
			'current' => esc_html__('Current Product', 'mt-wc-bricks-add_to_cart'),
		];

		// Get recent products
		$products = wc_get_products([
			'limit' => 20,
			'status' => 'publish',
		]);

		foreach ($products as $product) {
			$options[$product->get_id()] = $product->get_name();
		}

		return $options;
	}
}
