<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderApiController extends Controller
{
    /**
     * Get all orders for the authenticated user
     */
    public function getUserOrders(): JsonResponse
    {
        try {
            $user = Auth::user();
            $orders = Order::with(['items.product', 'items.product.category'])
                ->where('user_id', $user->getId())
                ->orderBy('created_at', 'desc')
                ->get();

            $formattedOrders = $orders->map(function ($order) {
                return [
                    'id' => $order->getId(),
                    'total' => $order->getTotal(),
                    'payment_method' => $order->getPaymentMethod(),
                    'payment_status' => $order->getPaymentStatus(),
                    'order_status' => $this->getOrderStatus($order),
                    'created_at' => $order->getCreatedAt(),
                    'updated_at' => $order->getUpdatedAt(),
                    'items' => $order->getItems()->map(function ($item) {
                        return [
                            'id' => $item->getId(),
                            'product_name' => $item->getProduct() ? $item->getProduct()->getName() : 'Product not found',
                            'product_image' => $item->getProduct() ? $item->getProduct()->getImage() : null,
                            'price' => $item->getPrice(),
                            'quantity' => $item->getQuantity(),
                            'subtotal' => $item->getPrice() * $item->getQuantity(),
                            'category' => $item->getProduct() && $item->getProduct()->category ?
                                $item->getProduct()->category->name : null
                        ];
                    }),
                    'items_count' => $order->getItems()->count(),
                    'estimated_delivery' => $this->getEstimatedDelivery($order)
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedOrders,
                'message' => 'Orders retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving orders: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific order details
     */
    public function getOrderDetails($orderId): JsonResponse
    {
        try {
            $user = Auth::user();
            $order = Order::with(['items.product', 'items.product.category'])
                ->where('id', $orderId)
                ->where('user_id', $user->getId())
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            $orderData = [
                'id' => $order->getId(),
                'total' => $order->getTotal(),
                'payment_method' => $order->getPaymentMethod(),
                'payment_status' => $order->getPaymentStatus(),
                'order_status' => $this->getOrderStatus($order),
                'created_at' => $order->getCreatedAt(),
                'updated_at' => $order->getUpdatedAt(),
                'items' => $order->getItems()->map(function ($item) {
                    return [
                        'id' => $item->getId(),
                        'product_name' => $item->getProduct() ? $item->getProduct()->getName() : 'Product not found',
                        'product_image' => $item->getProduct() ? $item->getProduct()->getImage() : null,
                        'price' => $item->getPrice(),
                        'quantity' => $item->getQuantity(),
                        'subtotal' => $item->getPrice() * $item->getQuantity(),
                        'category' => $item->getProduct() && $item->getProduct()->category ?
                            $item->getProduct()->category->name : null
                    ];
                }),
                'items_count' => $order->getItems()->count(),
                'estimated_delivery' => $this->getEstimatedDelivery($order),
                'delivery_tracking' => $this->getDeliveryTracking($order)
            ];

            return response()->json([
                'success' => true,
                'data' => $orderData,
                'message' => 'Order details retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving order details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Track order status by order ID
     */
    public function trackOrder($orderId): JsonResponse
    {
        try {
            $user = Auth::user();
            $order = Order::where('id', $orderId)
                ->where('user_id', $user->getId())
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            $trackingData = [
                'order_id' => $order->getId(),
                'order_status' => $this->getOrderStatus($order),
                'payment_status' => $order->getPaymentStatus(),
                'payment_method' => $order->getPaymentMethod(),
                'created_at' => $order->getCreatedAt(),
                'estimated_delivery' => $this->getEstimatedDelivery($order),
                'delivery_tracking' => $this->getDeliveryTracking($order),
                'last_updated' => $order->getUpdatedAt()
            ];

            return response()->json([
                'success' => true,
                'data' => $trackingData,
                'message' => 'Order tracking information retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error tracking order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get order status based on payment and delivery logic
     */
    private function getOrderStatus($order): string
    {
        $paymentStatus = $order->getPaymentStatus();
        $paymentMethod = $order->getPaymentMethod();
        $createdAt = $order->getCreatedAt();
        $now = now();

        // Calculate days since order
        $daysSinceOrder = $createdAt->diffInDays($now);

        if ($paymentStatus === 'failed') {
            return 'payment_failed';
        }

        if ($paymentMethod === 'cash_on_delivery') {
            if ($paymentStatus === 'pending') {
                if ($daysSinceOrder < 1) {
                    return 'processing';
                } elseif ($daysSinceOrder < 3) {
                    return 'preparing_for_delivery';
                } elseif ($daysSinceOrder < 7) {
                    return 'out_for_delivery';
                } else {
                    return 'delivered';
                }
            }
        } else { // online payment
            if ($paymentStatus === 'paid') {
                if ($daysSinceOrder < 1) {
                    return 'processing';
                } elseif ($daysSinceOrder < 3) {
                    return 'preparing_for_delivery';
                } elseif ($daysSinceOrder < 7) {
                    return 'out_for_delivery';
                } else {
                    return 'delivered';
                }
            }
        }

        return 'processing';
    }

    /**
     * Get estimated delivery date
     */
    private function getEstimatedDelivery($order): string
    {
        $createdAt = $order->getCreatedAt();
        $estimatedDate = $createdAt->addDays(7);

        return $estimatedDate->format('Y-m-d H:i:s');
    }

    /**
     * Get delivery tracking information
     */
    private function getDeliveryTracking($order): array
    {
        $orderStatus = $this->getOrderStatus($order);
        $createdAt = $order->getCreatedAt();
        $now = now();

        $trackingSteps = [
            'order_placed' => [
                'status' => 'completed',
                'title' => 'Order Placed',
                'description' => 'Your order has been successfully placed',
                'timestamp' => $createdAt->format('Y-m-d H:i:s'),
                'icon' => 'check-circle'
            ],
            'processing' => [
                'status' => $orderStatus === 'processing' ? 'current' : (in_array($orderStatus, ['preparing_for_delivery', 'out_for_delivery', 'delivered']) ? 'completed' : 'pending'),
                'title' => 'Processing',
                'description' => 'We are processing your order',
                'timestamp' => $createdAt->addHours(2)->format('Y-m-d H:i:s'),
                'icon' => 'gear'
            ],
            'preparing_for_delivery' => [
                'status' => $orderStatus === 'preparing_for_delivery' ? 'current' : (in_array($orderStatus, ['out_for_delivery', 'delivered']) ? 'completed' : 'pending'),
                'title' => 'Preparing for Delivery',
                'description' => 'Your order is being prepared for delivery',
                'timestamp' => $createdAt->addDays(1)->format('Y-m-d H:i:s'),
                'icon' => 'box'
            ],
            'out_for_delivery' => [
                'status' => $orderStatus === 'out_for_delivery' ? 'current' : ($orderStatus === 'delivered' ? 'completed' : 'pending'),
                'title' => 'Out for Delivery',
                'description' => 'Your order is on its way',
                'timestamp' => $createdAt->addDays(3)->format('Y-m-d H:i:s'),
                'icon' => 'truck'
            ],
            'delivered' => [
                'status' => $orderStatus === 'delivered' ? 'completed' : 'pending',
                'title' => 'Delivered',
                'description' => 'Your order has been delivered',
                'timestamp' => $createdAt->addDays(7)->format('Y-m-d H:i:s'),
                'icon' => 'house-check'
            ]
        ];

        return $trackingSteps;
    }
}
