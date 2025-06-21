# API Documentation - Online Store Mobile App

## Base URL

```
http://your-domain.com/api
```

## Authentication

L'API utilise Laravel Sanctum pour l'authentification. Les tokens sont retournés lors de l'inscription et de la connexion.

### Headers requis pour les routes protégées

```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

## Endpoints

### 🔐 Authentication

#### 1. Register User

**POST** `/auth/register`

**Body:**

```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "balance": 1000
}
```

**Response:**

```json
{
    "success": true,
    "message": "User registered successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "balance": 1000
        },
        "token": "1|abc123..."
    }
}
```

#### 2. Login User

**POST** `/auth/login`

**Body:**

```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response:**

```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "balance": 1000
        },
        "token": "1|abc123..."
    }
}
```

#### 3. Logout User

**POST** `/auth/logout`

**Headers:** `Authorization: Bearer {token}`

**Response:**

```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

#### 4. Get User Profile

**GET** `/auth/profile`

**Headers:** `Authorization: Bearer {token}`

**Response:**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "balance": 1000,
        "created_at": "2024-01-20T10:00:00.000000Z"
    }
}
```

#### 5. Update User Profile

**PUT** `/auth/profile`

**Headers:** `Authorization: Bearer {token}`

**Body:**

```json
{
    "name": "John Updated",
    "email": "john.updated@example.com",
    "current_password": "password123",
    "new_password": "newpassword123",
    "new_password_confirmation": "newpassword123"
}
```

### 📦 Products

#### 1. Get All Products

**GET** `/products?per_page=10&category_id=1&search=laptop`

**Query Parameters:**

-   `per_page` (optional): Number of products per page (default: 10)
-   `category_id` (optional): Filter by category ID
-   `search` (optional): Search in product name and description

**Response:**

```json
{
    "success": true,
    "data": {
        "products": [
            {
                "id": 1,
                "name": "Gaming Laptop",
                "description": "High-performance gaming laptop",
                "price": 1299.99,
                "image": "laptop.jpg",
                "quantity_store": 15,
                "category": {
                    "id": 1,
                    "name": "Electronics"
                },
                "supplier": {
                    "id": 1,
                    "name": "Tech Supplier"
                },
                "created_at": "2024-01-20T10:00:00.000000Z",
                "updated_at": "2024-01-20T10:00:00.000000Z"
            }
        ],
        "pagination": {
            "current_page": 1,
            "last_page": 5,
            "per_page": 10,
            "total": 50,
            "from": 1,
            "to": 10
        }
    },
    "message": "Products retrieved successfully"
}
```

#### 2. Get Product Details

**GET** `/products/{productId}`

**Response:**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Gaming Laptop",
        "description": "High-performance gaming laptop",
        "price": 1299.99,
        "image": "laptop.jpg",
        "quantity_store": 15,
        "category": {
            "id": 1,
            "name": "Electronics"
        },
        "supplier": {
            "id": 1,
            "name": "Tech Supplier"
        },
        "created_at": "2024-01-20T10:00:00.000000Z",
        "updated_at": "2024-01-20T10:00:00.000000Z",
        "stock_status": "in_stock"
    },
    "message": "Product details retrieved successfully"
}
```

#### 3. Get All Categories

**GET** `/categories`

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Electronics",
            "created_at": "2024-01-20T10:00:00.000000Z",
            "updated_at": "2024-01-20T10:00:00.000000Z"
        }
    ],
    "message": "Categories retrieved successfully"
}
```

#### 4. Get Products by Category

**GET** `/categories/{categoryId}/products?per_page=10`

**Response:** Same format as "Get All Products"

### 📋 Orders & Tracking

#### 1. Get User Orders

**GET** `/orders`

**Headers:** `Authorization: Bearer {token}`

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "total": 1299.99,
            "payment_method": "online",
            "payment_status": "paid",
            "order_status": "processing",
            "created_at": "2024-01-20T10:00:00.000000Z",
            "updated_at": "2024-01-20T10:00:00.000000Z",
            "items": [
                {
                    "id": 1,
                    "product_name": "Gaming Laptop",
                    "product_image": "laptop.jpg",
                    "price": 1299.99,
                    "quantity": 1,
                    "subtotal": 1299.99,
                    "category": "Electronics"
                }
            ],
            "items_count": 1,
            "estimated_delivery": "2024-01-27T10:00:00.000000Z"
        }
    ],
    "message": "Orders retrieved successfully"
}
```

#### 2. Get Order Details

**GET** `/orders/{orderId}`

**Headers:** `Authorization: Bearer {token}`

**Response:**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "total": 1299.99,
        "payment_method": "online",
        "payment_status": "paid",
        "order_status": "processing",
        "created_at": "2024-01-20T10:00:00.000000Z",
        "updated_at": "2024-01-20T10:00:00.000000Z",
        "items": [
            {
                "id": 1,
                "product_name": "Gaming Laptop",
                "product_image": "laptop.jpg",
                "price": 1299.99,
                "quantity": 1,
                "subtotal": 1299.99,
                "category": "Electronics"
            }
        ],
        "items_count": 1,
        "estimated_delivery": "2024-01-27T10:00:00.000000Z",
        "delivery_tracking": {
            "order_placed": {
                "status": "completed",
                "title": "Order Placed",
                "description": "Your order has been successfully placed",
                "timestamp": "2024-01-20T10:00:00.000000Z",
                "icon": "check-circle"
            },
            "processing": {
                "status": "current",
                "title": "Processing",
                "description": "We are processing your order",
                "timestamp": "2024-01-20T12:00:00.000000Z",
                "icon": "gear"
            },
            "preparing_for_delivery": {
                "status": "pending",
                "title": "Preparing for Delivery",
                "description": "Your order is being prepared for delivery",
                "timestamp": "2024-01-21T10:00:00.000000Z",
                "icon": "box"
            },
            "out_for_delivery": {
                "status": "pending",
                "title": "Out for Delivery",
                "description": "Your order is on its way",
                "timestamp": "2024-01-23T10:00:00.000000Z",
                "icon": "truck"
            },
            "delivered": {
                "status": "pending",
                "title": "Delivered",
                "description": "Your order has been delivered",
                "timestamp": "2024-01-27T10:00:00.000000Z",
                "icon": "house-check"
            }
        }
    },
    "message": "Order details retrieved successfully"
}
```

#### 3. Track Order Status

**GET** `/orders/{orderId}/track`

**Headers:** `Authorization: Bearer {token}`

**Response:**

```json
{
    "success": true,
    "data": {
        "order_id": 1,
        "order_status": "processing",
        "payment_status": "paid",
        "payment_method": "online",
        "created_at": "2024-01-20T10:00:00.000000Z",
        "estimated_delivery": "2024-01-27T10:00:00.000000Z",
        "delivery_tracking": {
            // Same as in order details
        },
        "last_updated": "2024-01-20T10:00:00.000000Z"
    },
    "message": "Order tracking information retrieved successfully"
}
```

## Order Statuses

### Order Status Values:

-   `processing` - Order is being processed
-   `preparing_for_delivery` - Order is being prepared for delivery
-   `out_for_delivery` - Order is on its way
-   `delivered` - Order has been delivered
-   `payment_failed` - Payment failed

### Payment Status Values:

-   `pending` - Payment is pending (for cash on delivery)
-   `paid` - Payment completed
-   `failed` - Payment failed

### Payment Method Values:

-   `cash_on_delivery` - Pay when delivered
-   `online` - Online payment

### Stock Status Values:

-   `in_stock` - Product available
-   `low_stock` - Limited quantity available
-   `out_of_stock` - Product not available

## Error Responses

### Validation Error (422)

```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password field is required."]
    }
}
```

### Not Found Error (404)

```json
{
    "success": false,
    "message": "Order not found"
}
```

### Unauthorized Error (401)

```json
{
    "success": false,
    "message": "Invalid credentials"
}
```

### Server Error (500)

```json
{
    "success": false,
    "message": "Error retrieving orders: Database connection failed"
}
```

## Rate Limiting

L'API implémente un rate limiting pour prévenir l'abus. Les limites sont :

-   60 requêtes par minute pour les routes publiques
-   100 requêtes par minute pour les routes authentifiées

## CORS

L'API supporte les requêtes CORS pour permettre l'accès depuis les applications mobiles et web.

## Exemple d'utilisation avec cURL

### Login

```bash
curl -X POST http://your-domain.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"password123"}'
```

### Get Orders (avec token)

```bash
curl -X GET http://your-domain.com/api/orders \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

### Track Order

```bash
curl -X GET http://your-domain.com/api/orders/1/track \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```
