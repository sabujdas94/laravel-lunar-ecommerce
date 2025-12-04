# API Documentation

## Base URL
```
http://your-domain.com/api
```

---

## Products API

### Get Product by URL/Slug
Get detailed product information by its URL slug.

**Endpoint:** `GET /product/{slug}`

**Query Parameters:**
- `lang_id` (optional, integer) - Language ID for translations (default: 1)

**Example Request:**
```bash
GET /api/product/mechanical-keyboard?lang_id=1
```

**Success Response (200):**
```json
{
  "url": "mechanical-keyboard",
  "name": "Mechanical Keyboard",
  "description": "High-quality mechanical keyboard...",
  "specification": "Full detailed specifications...",
  "price": "$129.99",
  "comparePrice": "$149.99",
  "collections": [
    {
      "name": "Electronics",
      "slug": "electronics"
    }
  ],
  "tags": ["gaming", "rgb", "wireless"],
  "variant_options": [
    {
      "name": "Size",
      "values": [
        {"id": 1, "name": "Small"},
        {"id": 2, "name": "Medium"},
        {"id": 3, "name": "Large"}
      ]
    },
    {
      "name": "Color",
      "values": [
        {"id": 18, "name": "Black"},
        {"id": 19, "name": "White"}
      ]
    }
  ],
  "variant_combinations": [
    {
      "id": 118,
      "sku": "KB-SM-BLK",
      "price": "$129.99",
      "stock": 50,
      "backorder": 10,
      "option_value_ids": [1, 18],
      "thumbnail": "https://..."
    }
  ],
  "thumbnail": "https://...",
  "images": [
    {"path": "https://..."}
  ]
}
```

**Error Response (404):**
```json
{
  "message": "Product not found."
}
```

---

### List Products
Get paginated list of products.

**Endpoint:** `GET /products`

**Query Parameters:**
- `lang_id` (optional, integer) - Language ID (default: 1)
- `per_page` (optional, integer) - Items per page (default: 12)

**Example Request:**
```bash
GET /api/products?lang_id=1&per_page=20
```

---

## Cart API

### Create Cart
Create a new shopping cart or retrieve an existing one. Cart uses UUID for identification.

**Endpoint:** `POST /cart`

**Request Body (optional):**
```json
{
  "cart_id": "9d3f5c8a-1b2e-4f3d-8a9b-1c2d3e4f5a6b"
}
```

**Success Response (201 for new, 200 for existing):**
```json
{
  "id": "9d3f5c8a-1b2e-4f3d-8a9b-1c2d3e4f5a6b",
  "currency_code": "USD",
  "sub_total": "$0.00",
  "tax_total": "$0.00",
  "total": "$0.00",
  "lines": [],
  "lines_count": 0
}
```

---

### Get Cart
Retrieve cart details with all items using UUID.

**Endpoint:** `GET /cart`

**Query Parameters:**
- `cart_id` (required, string/UUID) - Cart UUID

**Example Request:**
```bash
GET /api/cart?cart_id=9d3f5c8a-1b2e-4f3d-8a9b-1c2d3e4f5a6b
```

**Success Response (200):**
```json
{
  "id": "9d3f5c8a-1b2e-4f3d-8a9b-1c2d3e4f5a6b",
  "currency_code": "USD",
  "sub_total": "$129.99",
  "tax_total": "$13.00",
  "total": "$142.99",
  "lines": [
    {
      "id": 456,
      "variant_id": 118,
      "quantity": 2,
      "unit_price": "$129.99",
      "sub_total": "$259.98",
      "total": "$285.98",
      "product": {
        "name": "Mechanical Keyboard",
        "sku": "KB-SM-BLK",
        "thumbnail": "https://..."
      }
    }
  ],
  "lines_count": 1
}
```

**Error Response (400):**
```json
{
  "message": "cart_id is required."
}
```

**Error Response (404):**
```json
{
  "message": "Cart not found."
}
```

---

### Add Item to Cart
Add a product variant to the cart.

**Endpoint:** `POST /cart/items`

**Request Body:**
```json
{
  "cart_id": "9d3f5c8a-1b2e-4f3d-8a9b-1c2d3e4f5a6b",
  "variant_id": 118,
  "quantity": 2
}
```

**Validation Rules:**
- `cart_id` - required, string (UUID)
- `variant_id` - required, integer, must exist in product_variants table
- `quantity` - optional, integer, minimum 1 (default: 1)

**Success Response (200):**
```json
{
  "id": "9d3f5c8a-1b2e-4f3d-8a9b-1c2d3e4f5a6b",
  "currency_code": "USD",
  "sub_total": "$259.98",
  "tax_total": "$26.00",
  "total": "$285.98",
  "lines": [...],
  "lines_count": 1
}
```

**Notes:**
- If the item already exists in cart, quantities will be added together
- Cart totals are automatically recalculated

---

### Update Cart Item Quantity
Update the quantity of an item in the cart.

**Endpoint:** `PATCH /cart/items/{lineId}`

**URL Parameters:**
- `lineId` (required, integer) - Cart line ID

**Request Body:**
```json
{
  "cart_id": "9d3f5c8a-1b2e-4f3d-8a9b-1c2d3e4f5a6b",
  "quantity": 5
}
```

**Validation Rules:**
- `cart_id` - required, string (UUID)
- `quantity` - required, integer, minimum 1

**Success Response (200):**
```json
{
  "id": "9d3f5c8a-1b2e-4f3d-8a9b-1c2d3e4f5a6b",
  "currency_code": "USD",
  "sub_total": "$649.95",
  "tax_total": "$65.00",
  "total": "$714.95",
  "lines": [...],
  "lines_count": 1
}
```

**Error Response (404):**
```json
{
  "message": "Cart item not found."
}
```

---

### Remove Item from Cart
Remove a specific item from the cart.

**Endpoint:** `DELETE /cart/items/{lineId}`

**URL Parameters:**
- `lineId` (required, integer) - Cart line ID

**Request Body:**
```json
{
  "cart_id": "9d3f5c8a-1b2e-4f3d-8a9b-1c2d3e4f5a6b"
}
```

**Validation Rules:**
- `cart_id` - required, string (UUID)

**Success Response (200):**
```json
{
  "id": "9d3f5c8a-1b2e-4f3d-8a9b-1c2d3e4f5a6b",
  "currency_code": "USD",
  "sub_total": "$0.00",
  "tax_total": "$0.00",
  "total": "$0.00",
  "lines": [],
  "lines_count": 0
}
```

---

### Clear Cart
Remove all items from the cart.

**Endpoint:** `DELETE /cart`

**Request Body:**
```json
{
  "cart_id": "9d3f5c8a-1b2e-4f3d-8a9b-1c2d3e4f5a6b"
}
```

**Validation Rules:**
- `cart_id` - required, string (UUID)

**Success Response (200):**
```json
{
  "id": "9d3f5c8a-1b2e-4f3d-8a9b-1c2d3e4f5a6b",
  "currency_code": "USD",
  "sub_total": "$0.00",
  "tax_total": "$0.00",
  "total": "$0.00",
  "lines": [],
  "lines_count": 0
}
```

---

## Common Response Codes

- `200 OK` - Request successful
- `201 Created` - Resource created successfully
- `400 Bad Request` - Invalid request parameters
- `404 Not Found` - Resource not found
- `422 Unprocessable Entity` - Validation failed

---

## Example Usage Flow

### 1. Browse Products
```bash
GET /api/products?per_page=12
```

### 2. View Product Details
```bash
GET /api/product/mechanical-keyboard
```

### 3. Create Cart
```bash
POST /api/cart
# Response: { "id": 123, ... }
```

### 4. Add Item to Cart
```bash
POST /api/cart/items
Content-Type: application/json

{
  "cart_id": 123,
  "variant_id": 118,
  "quantity": 2
}
```

### 5. Update Quantity
```bash
PATCH /api/cart/items/456
Content-Type: application/json

{
  "cart_id": 123,
  "quantity": 5
}
```

### 6. Get Cart
```bash
GET /api/cart?cart_id=123
```

### 7. Remove Item
```bash
DELETE /api/cart/items/456
Content-Type: application/json

{
  "cart_id": 123
}
```

---

## Notes

- Store the `cart_id` returned from cart creation on the client side
- Pass `cart_id` with all subsequent cart operations
- Cart calculations (totals, taxes) are automatically performed on each operation
- Language support available through `lang_id` parameter
- All price values are pre-formatted with currency symbols
