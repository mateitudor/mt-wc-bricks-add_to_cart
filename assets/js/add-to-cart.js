/**
 * MT WC Bricks Add to Cart - Modern ES2022+ JavaScript
 * Follows cursorrules: modern, DRY, secure, robust, bug-free
 */

// State management for active requests
const state = {
	activeRequests: new Set(),
	debounceTimers: new Map()
};

// Utility functions
const utils = {
	// Debounce function calls
	debounce: (func, delay) => {
		return (...args) => {
			clearTimeout(state.debounceTimers.get(func));
			const timer = setTimeout(() => func.apply(this, args), delay);
			state.debounceTimers.set(func, timer);
		};
	},

	// Parse icon string from Bricks format
	parseIconString: (iconString) => {
		if (!iconString) return { library: 'fontawesome', name: 'fas fa-shopping-cart' };

		const parts = iconString.split(' ');
		return {
			library: parts[0] || 'fontawesome',
			name: parts.slice(1).join(' ')
		};
	},

	// Create icon HTML
	createIconHTML: (iconString) => {
		if (!iconString) return '<i class="fas fa-shopping-cart"></i>';

		// Handle Bricks icon format: "fontawesome fas fa-shopping-cart"
		const parts = iconString.split(' ');
		const iconClass = parts.slice(1).join(' ');

		// Ensure we only return a single icon with proper class
		return `<i class="icon ${iconClass}"></i>`;
	},

	// Report errors with context
	reportError: (error, context = {}) => {
		// Only log in debug mode
		if (typeof wc_add_to_cart_params !== 'undefined' && wc_add_to_cart_params.debug) {
			console.error('MT WC Add to Cart Error:', {
				error: error.message,
				stack: error.stack,
				context
			});
		}
	},

	// Update minicart with fragments
	updateMinicart: (data) => {
		if (data.fragments) {
			// Update cart fragments
			Object.keys(data.fragments).forEach(selector => {
				const element = document.querySelector(selector);
				if (element) {
					element.innerHTML = data.fragments[selector];
				}
			});
		}

		// Update cart count
		const cartCountElements = document.querySelectorAll('.cart-count, .cart-contents-count, .woocommerce-cart-count');
		cartCountElements.forEach(element => {
			element.textContent = data.cart_count || 0;
		});
	},

	// Trigger WooCommerce events
	triggerWooCommerceEvents: (data) => {
		// Trigger WooCommerce added_to_cart event
		document.body.dispatchEvent(new CustomEvent('added_to_cart', {
			detail: {
				fragments: data.fragments,
				cart_hash: data.cart_hash,
				product_id: data.product_id
			}
		}));

		// Trigger WooCommerce cart updated event
		document.body.dispatchEvent(new CustomEvent('wc_cart_updated', {
			detail: {
				fragments: data.fragments,
				cart_hash: data.cart_hash
			}
		}));

		// Trigger jQuery events for compatibility
		if (typeof jQuery !== 'undefined') {
			jQuery(document.body).trigger('added_to_cart', [data.fragments, data.cart_hash, jQuery()]);
			jQuery(document.body).trigger('wc_cart_updated', [data.fragments, data.cart_hash]);
		}
	},

	// Display WooCommerce notices
	displayNotices: (noticesHtml) => {
		if (!noticesHtml) return;

		// Create a temporary container to parse the new notices
		const tempContainer = document.createElement('div');
		tempContainer.innerHTML = noticesHtml;
		const newNotices = Array.from(tempContainer.children);

		// Try to find existing WooCommerce notice areas first
		const existingNoticeGroup = document.querySelector('.woocommerce-NoticeGroup-checkout, .woocommerce-notices-wrapper');
		if (existingNoticeGroup) {
			// Add new notices to existing WooCommerce notice area, avoiding duplicates
			newNotices.forEach(newNotice => {
				const noticeText = newNotice.textContent.trim();

				// Check if this notice already exists
				const existingNotices = existingNoticeGroup.querySelectorAll('.woocommerce-message');
				let isDuplicate = false;

				existingNotices.forEach(existingNotice => {
					if (existingNotice.textContent.trim() === noticeText) {
						isDuplicate = true;
					}
				});

				// Only add if it's not a duplicate
				if (!isDuplicate) {
					existingNoticeGroup.appendChild(newNotice);
				}
			});
			return;
		}

		// Create a new notice container using WooCommerce's default structure
		const noticeGroup = document.createElement('div');
		noticeGroup.className = 'woocommerce-NoticeGroup woocommerce-NoticeGroup-checkout';

		// Add new notices, avoiding duplicates
		newNotices.forEach(newNotice => {
			noticeGroup.appendChild(newNotice);
		});

		// Try to find the best place to insert the notice group
		const targetElement = document.querySelector('form.woocommerce-checkout, .woocommerce, .bricks-content, body');
		if (targetElement) {
			targetElement.insertBefore(noticeGroup, targetElement.firstChild);
		}
	}
};

// Button state management
const setIcon = (button, iconString) => {
	if (!iconString) return;
	const iconElement = button.querySelector('.icon');
	if (!iconElement) return;
	iconElement.className = 'icon';
	const parts = iconString.split(' ');
	const iconClassOnly = parts.slice(1).join(' ');
	if (iconClassOnly) iconElement.classList.add(...iconClassOnly.split(' '));
};

const buttonState = {
	// Set button state with proper transitions
	setState: (button, state, iconClass = null) => {
		// Remove all state classes
		button.classList.remove('is-loading', 'is-success', 'is-error');

		// Add new state class
		if (state !== 'default') {
			button.classList.add(`is-${state}`);
		}

		// Update icon if provided
		if (iconClass) setIcon(button, iconClass);

		// Update accessibility attributes
		const ariaLabels = {
			loading: 'Adding to cart...',
			success: 'Added to cart!',
			error: 'Error adding to cart',
			default: button.dataset.defaultText || 'Add to cart'
		};

		button.setAttribute('aria-label', ariaLabels[state] || ariaLabels.default);
		button.disabled = state === 'loading';
	},

	// Show view cart button with animation
	showViewCart: (button) => {
		// Respect toggle
		const allow = button?.dataset?.showViewCart === '1';
		if (!allow) return;
		const viewCartButton = button.closest('.mt-wc-buttons-container')?.querySelector('.mt-wc-view-cart-button');
		if (viewCartButton) {
			viewCartButton.classList.add('show');
		}
	},

	// Hide view cart button
	hideViewCart: (button) => {
		const viewCartButton = button.closest('.mt-wc-buttons-container')?.querySelector('.mt-wc-view-cart-button');
		if (viewCartButton) {
			viewCartButton.classList.remove('show');
		}
	}
};

// AJAX handler with modern fetch API
const ajaxHandler = {
	// Make AJAX request with proper error handling
	async addToCart(productId, quantity) {
		const controller = new AbortController();
		const timeoutId = setTimeout(() => controller.abort(), 10000); // 10s timeout

		try {
			// Debug logging only in debug mode
			if (wc_add_to_cart_params.debug) {
				console.log('Making AJAX request:', {
					url: wc_add_to_cart_params.ajax_url,
					productId,
					quantity,
					nonce: wc_add_to_cart_params.nonce
				});
			}

			const response = await fetch(wc_add_to_cart_params.ajax_url, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: new URLSearchParams({
					action: 'mt_wc_bricks_add_to_cart',
					product_id: productId,
					quantity: quantity,
					nonce: wc_add_to_cart_params.nonce
				}),
				signal: controller.signal
			});

			clearTimeout(timeoutId);

			if (wc_add_to_cart_params.debug) {
				console.log('AJAX response status:', response.status);
			}

			if (!response.ok) {
				throw new Error(`HTTP ${response.status}: ${response.statusText}`);
			}

			const data = await response.json();
			if (wc_add_to_cart_params.debug) {
				console.log('AJAX response data:', data);
			}
			return data;

		} catch (error) {
			clearTimeout(timeoutId);

			if (error.name === 'AbortError') {
				throw new Error('Request timeout - please try again');
			}

			throw error;
		}
	}
};

// Main event handler
const handleAddToCart = async (event) => {
	event.preventDefault();

	const button = event.target.closest('.mt-wc-add-to-cart-button');
	if (!button) return;

	// Prevent multiple clicks
	if (button.classList.contains('is-loading')) return;

	// Get button data
	const productId = button.dataset.productId;
	const quantity = parseInt(button.dataset.quantity) || 1;
	const defaultIcon = button.dataset.defaultIcon;
	const loadingIcon = button.dataset.loadingIcon;
	const successIcon = button.dataset.successIcon;
	const errorIcon = button.dataset.errorIcon;

	// Debug icon values (only in debug mode)
	if (typeof wc_add_to_cart_params !== 'undefined' && wc_add_to_cart_params.debug) {
		console.log('Icon Debug:', {
			defaultIcon,
			loadingIcon,
			successIcon,
			errorIcon,
			button: button
		});
	}

	// Validate required data
	if (!productId) {
		utils.reportError(new Error('Product ID not found'), { button });
		return;
	}

	// Add to active requests
	state.activeRequests.add(button);

	try {
		// Set loading state
		buttonState.setState(button, 'loading', loadingIcon);

		// Make AJAX request
		const result = await ajaxHandler.addToCart(productId, quantity);

		if (result.success) {
			// Success state
			buttonState.setState(button, 'success', successIcon);
			buttonState.showViewCart(button);

			// Update minicart and trigger WooCommerce events
			utils.updateMinicart(result.data);
			utils.triggerWooCommerceEvents(result.data);
			utils.displayNotices(result.data.notices);

			// Reset after delay
			setTimeout(() => {
				buttonState.setState(button, 'default', defaultIcon);
				buttonState.hideViewCart(button);
			}, 3000);

		} else {
			// Error state
			buttonState.setState(button, 'error', errorIcon);

			// Reset after delay
			setTimeout(() => {
				buttonState.setState(button, 'default', defaultIcon);
			}, 2000);
		}

	} catch (error) {
		// Handle errors
		utils.reportError(error, { productId, quantity });
		buttonState.setState(button, 'error', errorIcon);

		// Reset after delay
		setTimeout(() => {
			buttonState.setState(button, 'default', defaultIcon);
		}, 2000);

	} finally {
		// Remove from active requests
		state.activeRequests.delete(button);
	}
};

// Debounced event handler
const debouncedHandler = utils.debounce(handleAddToCart, 100);

	// Initialize when DOM is ready
const init = () => {
	// Find all add to cart buttons that don't already have event listeners
	const buttons = document.querySelectorAll('.mt-wc-add-to-cart-button:not([data-mt-wc-initialized])');
	const allButtons = document.querySelectorAll('.mt-wc-add-to-cart-button');

	// Debug logging only in debug mode
	if (typeof wc_add_to_cart_params !== 'undefined' && wc_add_to_cart_params.debug) {
		console.log('MT WC Add to Cart: Found', allButtons.length, 'total buttons');
		console.log('MT WC Add to Cart: Found', buttons.length, 'uninitialized buttons');
		console.log('All buttons:', allButtons);
	}

	if (buttons.length === 0) return;

	if (typeof wc_add_to_cart_params !== 'undefined' && wc_add_to_cart_params.debug) {
		console.log('MT WC Add to Cart: Initializing', buttons.length, 'buttons');
	}

	// Add event listeners
	buttons.forEach(button => {
		// Remove any existing event listeners first
		button.removeEventListener('click', debouncedHandler);

		// Add new event listener
		button.addEventListener('click', debouncedHandler);

		// Mark as initialized to prevent duplicate listeners
		button.setAttribute('data-mt-wc-initialized', 'true');

		// Store original aria-label for restoration
		if (!button.dataset.defaultText) {
			button.dataset.defaultText = button.getAttribute('aria-label') || 'Add to cart';
		}

		if (typeof wc_add_to_cart_params !== 'undefined' && wc_add_to_cart_params.debug) {
			console.log('MT WC Add to Cart: Button initialized', button);
		}
	});

	// Cleanup on page unload (only add once)
	if (!window.mtWcCleanupAdded) {
		window.addEventListener('beforeunload', () => {
			state.activeRequests.clear();
			state.debounceTimers.forEach(timer => clearTimeout(timer));
			state.debounceTimers.clear();
		});
		window.mtWcCleanupAdded = true;
	}
};

// Initialize when DOM is ready
if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', init);
} else {
	init();
}

// Force reinitialize function for buttons that might not be working
const forceReinit = () => {
	const allButtons = document.querySelectorAll('.mt-wc-add-to-cart-button');

	if (typeof wc_add_to_cart_params !== 'undefined' && wc_add_to_cart_params.debug) {
		console.log('MT WC Add to Cart: Force reinitializing', allButtons.length, 'buttons');
		console.log('Force reinit - All buttons:', allButtons);
	}

	allButtons.forEach(button => {
		// Remove existing event listeners
		button.removeEventListener('click', debouncedHandler);

		// Add new event listener
		button.addEventListener('click', debouncedHandler);

		// Ensure it's marked as initialized
		button.setAttribute('data-mt-wc-initialized', 'true');

		if (typeof wc_add_to_cart_params !== 'undefined' && wc_add_to_cart_params.debug) {
			console.log('MT WC Add to Cart: Button force reinitialized', button);
		}
	});
};

// Reinitialize when Bricks loads new content (query loops, AJAX, etc.)
document.addEventListener('bricks/ajax/nodes_added', () => {
	if (typeof wc_add_to_cart_params !== 'undefined' && wc_add_to_cart_params.debug) {
		console.log('MT WC Add to Cart: Bricks nodes added event fired');
	}
	// Small delay to ensure DOM is updated
	setTimeout(() => {
		if (typeof wc_add_to_cart_params !== 'undefined' && wc_add_to_cart_params.debug) {
			console.log('MT WC Add to Cart: Reinitializing after Bricks nodes added');
		}
		init();
		// Also force reinit all buttons to ensure they work
		forceReinit();
	}, 100);
});

// Also listen for general DOM changes in case other plugins add content
const observer = new MutationObserver((mutations) => {
	let shouldReinit = false;

	mutations.forEach((mutation) => {
		if (mutation.type === 'childList') {
			// Check if any added nodes contain our buttons
			mutation.addedNodes.forEach((node) => {
				if (node.nodeType === Node.ELEMENT_NODE) {
					if (node.classList?.contains('mt-wc-add-to-cart-button') ||
						node.querySelector?.('.mt-wc-add-to-cart-button')) {
						shouldReinit = true;
					}
				}
			});
		}
	});

	if (shouldReinit) {
		if (typeof wc_add_to_cart_params !== 'undefined' && wc_add_to_cart_params.debug) {
			console.log('MT WC Add to Cart: MutationObserver detected new buttons, reinitializing');
		}
		// Debounce reinitialization to avoid multiple calls
		clearTimeout(observer.timeout);
		observer.timeout = setTimeout(() => {
			if (typeof wc_add_to_cart_params !== 'undefined' && wc_add_to_cart_params.debug) {
				console.log('MT WC Add to Cart: MutationObserver timeout - reinitializing');
			}
			init();
		}, 100);
	}
});

// Start observing when DOM is ready
if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', () => {
		observer.observe(document.body, {
			childList: true,
			subtree: true
		});
	});
} else {
	observer.observe(document.body, {
		childList: true,
		subtree: true
	});
}

// Export for testing (if needed)
if (typeof module !== 'undefined' && module.exports) {
	module.exports = { utils, buttonState, ajaxHandler };
}

// Global functions for debugging
window.mtWcAddToCart = {
	init,
	forceReinit,
	utils,
	buttonState,
	ajaxHandler
};
