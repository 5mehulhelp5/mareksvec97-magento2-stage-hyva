// This prevents callbacks from being overwritten in the event of multiple executions.
window.stripeCallbacks = window.stripeCallbacks || [];

/**
 * The Stripe JS library is used in multiple locations, so centralize the loading of it.
 *
 * @param callback
 */
window.loadStripe = function (callback) {
    const scriptId = 'stripe-js';
    const stripeScript = document.getElementById(scriptId);

    if (typeof Stripe !== 'undefined') {
        callback();
        return;
    }

    window.stripeCallbacks.push(callback);

    if (stripeScript) {
        return;
    }

    const script = document.createElement('script');
    script.src = 'https://js.stripe.com/v3/';
    script.id = scriptId;
    script.onload = () => {
        window.stripeCallbacks.forEach((callback) => callback());
        // Clear the callbacks after execution to prevent duplicate executions
        window.stripeCallbacks = [];
    };
    script.onerror = () => {
        console.error(new Error('Stripe SDK could not be loaded'));
    };

    document.head.append(script);
};

/**
 * Stripe utility functions
 */
window.stripeUtils = window.stripeUtils || {};

/**
 * Used by Express Checkout wallets at various pages.
 * Calculates the total amount from line items.
 *
 * @param {Array} lineItems - Array of line items with amount property
 * @returns {number} Total amount in cents
 */
window.stripeUtils.addLineItemsAmounts = function(lineItems) {
    if (!lineItems || !Array.isArray(lineItems)) {
        return 0;
    }
    return lineItems.reduce((total, item) => total + (item.amount || 0), 0);
};
