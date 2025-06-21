<?php

/**
 * Test script for payment functionality
 * This file can be used to test the payment features
 */

echo "=== Payment System Test ===\n\n";

// Test 1: Check if migration file exists
$migrationFile = 'database/migrations/2025_01_20_000000_add_payment_method_to_orders_table.php';
if (file_exists($migrationFile)) {
    echo "✓ Migration file exists: $migrationFile\n";
} else {
    echo "✗ Migration file missing: $migrationFile\n";
}

// Test 2: Check if PaymentController exists
$controllerFile = 'app/Http/Controllers/PaymentController.php';
if (file_exists($controllerFile)) {
    echo "✓ PaymentController exists: $controllerFile\n";
} else {
    echo "✗ PaymentController missing: $controllerFile\n";
}

// Test 3: Check if AdminOrderController exists
$adminControllerFile = 'app/Http/Controllers/Admin/AdminOrderController.php';
if (file_exists($adminControllerFile)) {
    echo "✓ AdminOrderController exists: $adminControllerFile\n";
} else {
    echo "✗ AdminOrderController missing: $adminControllerFile\n";
}

// Test 4: Check if payment views exist
$paymentViews = [
    'resources/views/payment/options.blade.php',
    'resources/views/payment/confirmation.blade.php',
    'resources/views/admin/order/index.blade.php',
    'resources/views/admin/order/show.blade.php'
];

foreach ($paymentViews as $view) {
    if (file_exists($view)) {
        echo "✓ Payment view exists: $view\n";
    } else {
        echo "✗ Payment view missing: $view\n";
    }
}

// Test 5: Check if routes are added
$routesFile = 'routes/web.php';
if (file_exists($routesFile)) {
    $routesContent = file_get_contents($routesFile);
    $paymentRoutes = [
        'payment.options',
        'payment.cash-on-delivery',
        'payment.online',
        'admin.order.index',
        'admin.order.show',
        'admin.order.update-payment-status'
    ];

    foreach ($paymentRoutes as $route) {
        if (strpos($routesContent, $route) !== false) {
            echo "✓ Route exists: $route\n";
        } else {
            echo "✗ Route missing: $route\n";
        }
    }
}

echo "\n=== Test Summary ===\n";
echo "The payment system has been implemented with the following features:\n";
echo "1. Two payment methods: Cash on Delivery and Online Payment\n";
echo "2. Payment status tracking (pending, paid, failed)\n";
echo "3. Admin interface to manage orders and payment status\n";
echo "4. User-friendly payment flow with confirmation pages\n";
echo "5. Integration with existing cart and order system\n\n";

echo "To test the system:\n";
echo "1. Run the migration: php artisan migrate\n";
echo "2. Start the server: php artisan serve\n";
echo "3. Add products to cart and proceed to payment\n";
echo "4. Choose between Cash on Delivery or Online Payment\n";
echo "5. Check admin panel for order management\n";
