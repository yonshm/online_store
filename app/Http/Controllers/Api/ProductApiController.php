<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductApiController extends Controller
{
    /**
     * Get all products with pagination
     */
    public function getProducts(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 10);
            $categoryId = $request->input('category_id');
            $search = $request->input('search');

            $query = Product::with(['category', 'supplier']);

            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }

            if ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            }

            $products = $query->paginate($perPage);

            $formattedProducts = $products->getCollection()->map(function ($product) {
                return [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'description' => $product->getDescription(),
                    'price' => $product->getPrice(),
                    'image' => $product->getImage(),
                    'quantity_store' => $product->getQuantity_store(),
                    'category' => $product->category ? [
                        'id' => $product->category->id,
                        'name' => $product->category->name
                    ] : null,
                    'supplier' => $product->supplier ? [
                        'id' => $product->supplier->id,
                        'name' => $product->supplier->raison_social
                    ] : null,
                    'created_at' => $product->getCreatedAt(),
                    'updated_at' => $product->getUpdatedAt()
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'products' => $formattedProducts,
                    'pagination' => [
                        'current_page' => $products->currentPage(),
                        'last_page' => $products->lastPage(),
                        'per_page' => $products->perPage(),
                        'total' => $products->total(),
                        'from' => $products->firstItem(),
                        'to' => $products->lastItem()
                    ]
                ],
                'message' => 'Products retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving products: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific product details
     */
    public function getProductDetails($productId): JsonResponse
    {
        try {
            $product = Product::with(['category', 'supplier'])->find($productId);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            $productData = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'price' => $product->getPrice(),
                'image' => $product->getImage(),
                'quantity_store' => $product->getQuantity_store(),
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'name' => $product->category->name
                ] : null,
                'supplier' => $product->supplier ? [
                    'id' => $product->supplier->id,
                    'name' => $product->supplier->raison_social
                ] : null,
                'created_at' => $product->getCreatedAt(),
                'updated_at' => $product->getUpdatedAt(),
                'stock_status' => $this->getStockStatus($product->getQuantity_store())
            ];

            return response()->json([
                'success' => true,
                'data' => $productData,
                'message' => 'Product details retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving product details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all categories
     */
    public function getCategories(): JsonResponse
    {
        try {
            $categories = Category::all();

            $formattedCategories = $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'created_at' => $category->created_at,
                    'updated_at' => $category->updated_at
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedCategories,
                'message' => 'Categories retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving categories: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get products by category
     */
    public function getProductsByCategory($categoryId, Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 10);

            $products = Product::with(['category', 'supplier'])
                ->where('category_id', $categoryId)
                ->paginate($perPage);

            $formattedProducts = $products->getCollection()->map(function ($product) {
                return [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'description' => $product->getDescription(),
                    'price' => $product->getPrice(),
                    'image' => $product->getImage(),
                    'quantity_store' => $product->getQuantity_store(),
                    'category' => $product->category ? [
                        'id' => $product->category->id,
                        'name' => $product->category->name
                    ] : null,
                    'supplier' => $product->supplier ? [
                        'id' => $product->supplier->id,
                        'name' => $product->supplier->raison_social
                    ] : null,
                    'created_at' => $product->getCreatedAt(),
                    'updated_at' => $product->getUpdatedAt(),
                    'stock_status' => $this->getStockStatus($product->getQuantity_store())
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'products' => $formattedProducts,
                    'pagination' => [
                        'current_page' => $products->currentPage(),
                        'last_page' => $products->lastPage(),
                        'per_page' => $products->perPage(),
                        'total' => $products->total(),
                        'from' => $products->firstItem(),
                        'to' => $products->lastItem()
                    ]
                ],
                'message' => 'Products by category retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving products by category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get stock status based on quantity
     */
    private function getStockStatus($quantity): string
    {
        if ($quantity == 0) {
            return 'out_of_stock';
        } elseif ($quantity < 10) {
            return 'low_stock';
        } else {
            return 'in_stock';
        }
    }
}
