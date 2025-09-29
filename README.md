# MT WC Bricks Add to Cart

A Bricks Builder element that provides an accessible, icon-only add to cart button for WooCommerce products.

## Description

This plugin extends Bricks Builder with a custom element that enables you to add accessible, icon-only add to cart buttons to your WooCommerce products. The button includes loading states, success feedback, error handling, and full accessibility support.

## Features

- **Accessible Design**: Full keyboard navigation, screen reader support, and ARIA attributes
- **Icon-Only Button**: Clean, minimal design with optional text
- **Loading States**: Visual feedback during AJAX requests
- **Success Feedback**: Confirmation when items are added to cart
- **Error Handling**: Graceful error states with user feedback
- **Quantity Support**: Optional quantity input with validation
- **View Cart Link**: Optional link to view cart after adding items
- **Responsive Design**: Works on all device sizes
- **Performance**: Optimized JavaScript with modern fetch API
- **Accessibility**: WCAG 2.2 AA compliant with proper focus management

## Requirements

- WordPress 5.0 or higher
- PHP 8.1 or higher
- WooCommerce 5.0 or higher
- Bricks Builder theme (version 1.0 or higher)

## Installation

1. Upload the plugin files to `/wp-content/plugins/mt-wc-bricks-add_to_cart/` directory
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Ensure WooCommerce and Bricks Builder are installed and active
4. The "Add to Cart Button" element will be available in the Bricks Builder elements panel

## Usage

1. Add the "Add to Cart Button" element to your Bricks Builder page
2. Configure the product selection (current product or specific product)
3. Customize button appearance, text, and behavior
4. Set up quantity input options if needed
5. Configure success and error states
6. Publish your page

### Element Configuration

#### Content Tab
- **Product**: Select which product to add to cart (current product or specific product)
- **Show Quantity Input**: Enable/disable quantity input
- **Default Quantity**: Set the default quantity value

#### Button Tab
- **Button Text**: Customize the button text (leave empty for icon-only)
- **Show Icon**: Enable/disable the cart icon
- **Icon Position**: Position icon left or right of text
- **Success Timeout**: How long to show success state (in milliseconds)
- **Show View Cart Link**: Enable/disable view cart link after adding
- **View Cart Text**: Customize the view cart link text

### Accessibility Features

- **Keyboard Navigation**: Full keyboard support with Enter and Space key activation
- **Screen Reader Support**: Proper ARIA labels and live regions for status updates
- **Focus Management**: Clear focus indicators and logical tab order
- **High Contrast**: Support for high contrast mode
- **Reduced Motion**: Respects user's motion preferences
- **Touch Targets**: Minimum 44px touch targets for mobile devices

### CSS Classes

- `.mt-wc-add-to-cart-wrapper` - Main wrapper element
- `.product_actions` - Container for quantity and button
- `.add-to-cart-container` - Container for button and view cart link
- `.add-to-cart` - The add to cart button
- `.view-cart` - The view cart link (hidden initially)
- `.icon` - SVG icons with state classes (default, loading, success, error)

### JavaScript Events

The plugin triggers and listens for several events:

- `wc_fragment_refresh` - Triggers WooCommerce cart fragments refresh
- Custom events for status updates and error handling

## Customization

### CSS Custom Properties

The plugin uses CSS custom properties for easy theming:

```css
.mt-wc-add-to-cart-wrapper {
	--button-bg: #007cba;
	--button-hover-bg: #005a87;
	--success-bg: #28a745;
	--error-bg: #dc3545;
	--text-color: white;
	--border-radius: 4px;
	--transition-duration: 0.2s;
}
```

### JavaScript API

The plugin exposes a global `mtWcAddToCart` object with methods:

```javascript
// Re-initialize the plugin
mtWcAddToCart.init();

// Announce status to screen readers
mtWcAddToCart.announceStatus('Item added to cart');

// Trigger fragments refresh
mtWcAddToCart.triggerFragmentsRefresh();
```

## Browser Support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- Internet Explorer 11+ (with reduced functionality)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Performance

- Lazy loading for JavaScript functionality
- Optimized CSS with minimal footprint
- Efficient event handling with proper cleanup
- Modern fetch API for AJAX requests
- Minimal DOM manipulation

## Accessibility

- WCAG 2.2 AA compliant
- Proper semantic HTML structure
- ARIA labels and live regions
- Keyboard navigation support
- Screen reader compatibility
- High contrast mode support
- Focus management

## Changelog

### 1.0.0
- Initial release
- Accessible add to cart button
- Loading, success, and error states
- Quantity input support
- View cart link functionality
- Full keyboard navigation
- Screen reader support
- Responsive design

## Support

For support, feature requests, or bug reports, please visit the [plugin page](https://mateitudor.com) or contact the developer.

## License

This plugin is licensed under the Unlicense. See the [LICENSE](https://unlicense.org/) file for details.

## Credits

Developed by [Matei Tudor](https://mateitudor.com) for use with Bricks Builder and WooCommerce.
