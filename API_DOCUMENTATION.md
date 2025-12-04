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

## Checkout API

### Set Shipping Address
Add or update shipping address for cart checkout.

**Endpoint:** `POST /checkout/shipping-address`

**Request Body:**
```json
{
  "cart_id": "9d3f5c8a-1b2e-4f3d-8a9b-1c2d3e4f5a6b",
  "first_name": "John",
  "last_name": "Doe",
  "line_one": "123 Main Street",
  "line_two": "Apt 4B",
  "line_three": null,
  "city": "New York",
  "state": "NY",
  "postcode": "10001",
  "country_id": 1,
  "company_name": null,
  "delivery_instructions": "Leave at door",
  "contact_email": "john@example.com",
  "contact_phone": "+1234567890"
}
```

**Validation Rules:**
- `cart_id` - required, string (UUID)
- `first_name` - required, string, max 255
- `last_name` - required, string, max 255
- `line_one` - required, string, max 255
- `line_two` - optional, string, max 255
- `line_three` - optional, string, max 255
- `city` - required, string, max 255
- `state` - optional, string, max 255
- `postcode` - required, string, max 255
- `country_id` - required, integer, must exist in countries table
- `company_name` - optional, string, max 255
- `delivery_instructions` - optional, string, max 500
- `contact_email` - required, email, max 255
- `contact_phone` - optional, string, max 255

**Success Response (200):**
```json
{
  "message": "Shipping address set successfully.",
  "address": {
    "id": 1,
    "first_name": "John",
    "last_name": "Doe",
    "company_name": null,
    "line_one": "123 Main Street",
    "line_two": "Apt 4B",
    "line_three": null,
    "city": "New York",
    "state": "NY",
    "postcode": "10001",
    "country": "United States",
    "country_id": 1,
    "contact_email": "john@example.com",
    "contact_phone": "+1234567890",
    "delivery_instructions": "Leave at door"
  }
}
```

---

### Set Billing Address
Add or update billing address for cart checkout.

**Endpoint:** `POST /checkout/billing-address`

**Request Body (Option 1 - Same as Shipping):**
```json
{
  "cart_id": "9d3f5c8a-1b2e-4f3d-8a9b-1c2d3e4f5a6b",
  "same_as_shipping": true
}
```

**Request Body (Option 2 - Different Address):**
```json
{
  "cart_id": "9d3f5c8a-1b2e-4f3d-8a9b-1c2d3e4f5a6b",
  "same_as_shipping": false,
  "first_name": "Jane",
  "last_name": "Smith",
  "line_one": "456 Oak Avenue",
  "city": "Los Angeles",
  "state": "CA",
  "postcode": "90001",
  "country_id": 1,
  "contact_email": "jane@example.com",
  "contact_phone": "+0987654321"
}
```

**Validation Rules:**
- `cart_id` - required, string (UUID)
- `same_as_shipping` - optional, boolean
- `first_name` - required if `same_as_shipping` is false
- `last_name` - required if `same_as_shipping` is false
- `line_one` - required if `same_as_shipping` is false
- `city` - required if `same_as_shipping` is false
- `postcode` - required if `same_as_shipping` is false
- `country_id` - required if `same_as_shipping` is false
- `contact_email` - required if `same_as_shipping` is false

**Success Response (200):**
```json
{
  "message": "Billing address set successfully.",
  "address": {
    "id": 2,
    "first_name": "Jane",
    "last_name": "Smith",
    ...
  }
}
```

**Error Response (400):**
```json
{
  "message": "Shipping address must be set first."
}
```

---

### Get Checkout Summary
Get complete checkout summary including cart items and addresses.

**Endpoint:** `GET /checkout/summary`

**Query Parameters:**
- `cart_id` (required, string/UUID) - Cart UUID

**Example Request:**
```bash
GET /api/checkout/summary?cart_id=9d3f5c8a-1b2e-4f3d-8a9b-1c2d3e4f5a6b
```

**Success Response (200):**
```json
{
  "id": "9d3f5c8a-1b2e-4f3d-8a9b-1c2d3e4f5a6b",
  "currency_code": "USD",
  "sub_total": "$259.98",
  "tax_total": "$26.00",
  "shipping_total": "$10.00",
  "total": "$295.98",
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
  "lines_count": 1,
  "shipping_address": {
    "id": 1,
    "first_name": "John",
    "last_name": "Doe",
    "line_one": "123 Main Street",
    "city": "New York",
    "state": "NY",
    "postcode": "10001",
    "country": "United States",
    "country_id": 1,
    "contact_email": "john@example.com",
    "contact_phone": "+1234567890",
    "delivery_instructions": "Leave at door"
  },
  "billing_address": {
    "id": 2,
    "first_name": "Jane",
    "last_name": "Smith",
    ...
  }
}
```

---

### Complete Checkout
Complete checkout and create order from cart.

**Endpoint:** `POST /checkout/complete`

**Request Body:**
```json
{
  "cart_id": "9d3f5c8a-1b2e-4f3d-8a9b-1c2d3e4f5a6b",
  "payment_method": "card"
}
```

**Validation Rules:**
- `cart_id` - required, string (UUID)
- `payment_method` - optional, string, one of: card, cash, bank_transfer

**Success Response (201):**
```json
{
  "message": "Order created successfully.",
  "order": {
    "id": 789,
    "reference": "00000789",
    "status": "awaiting-payment",
    "status_label": "Awaiting Payment",
    "customer_reference": null,
    "currency_code": "USD",
    "sub_total": "$259.98",
    "discount_total": "$0.00",
    "tax_total": "$26.00",
    "shipping_total": "$10.00",
    "total": "$295.98",
    "placed_at": "2025-12-04T10:30:00+00:00",
    "notes": null,
    "lines": [
      {
        "id": 890,
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
    "lines_count": 1,
    "shipping_address": { ... },
    "billing_address": { ... }
  }
}
```

**Error Responses:**

Cart is empty (400):
```json
{
  "message": "Cart is empty."
}
```

Missing shipping address (400):
```json
{
  "message": "Shipping address is required."
}
```

Missing billing address (400):
```json
{
  "message": "Billing address is required."
}
```

Validation failed (422):
```json
{
  "message": "Cart validation failed.",
  "errors": {
    "stock": ["Insufficient stock for item."]
  }
}
```

---

### Get Order by Reference
Retrieve order details by order reference number.

**Endpoint:** `GET /checkout/order/{reference}`

**URL Parameters:**
- `reference` (required, string) - Order reference number (e.g., "00000789")

**Example Request:**
```bash
GET /api/checkout/order/00000789
```

**Success Response (200):**
```json
{
  "order": {
    "id": 789,
    "reference": "00000789",
    "status": "awaiting-payment",
    "status_label": "Awaiting Payment",
    "currency_code": "USD",
    "sub_total": "$259.98",
    "discount_total": "$0.00",
    "tax_total": "$26.00",
    "shipping_total": "$10.00",
    "total": "$295.98",
    "placed_at": "2025-12-04T10:30:00+00:00",
    "notes": null,
    "lines": [...],
    "lines_count": 1,
    "shipping_address": { ... },
    "billing_address": { ... }
  }
}
```

**Error Response (404):**
```json
{
  "message": "Order not found."
}
```

---

## Common Response Codes

- `200 OK` - Request successful
- `201 Created` - Resource created successfully
- `400 Bad Request` - Invalid request parameters
- `404 Not Found` - Resource not found
- `422 Unprocessable Entity` - Validation failed
- `500 Internal Server Error` - Server error

---

## Example Usage Flow

### Complete Checkout Flow

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
# Response: { "id": "9d3f5c8a-1b2e-4f3d-8a9b-1c2d3e4f5a6b", ... }
```

### 4. Add Items to Cart
```bash
POST /api/cart/items
Content-Type: application/json

{
  "cart_id": "9d3f5c8a-1b2e-4f3d-8a9b-1c2d3e4f5a6b",
  "variant_id": 118,
  "quantity": 2
}
```

### 5. Update Quantity (Optional)
```bash
PATCH /api/cart/items/456
Content-Type: application/json

{
  "cart_id": "9d3f5c8a-1b2e-4f3d-8a9b-1c2d3e4f5a6b",
  "quantity": 3
}
```

### 6. Set Shipping Address
```bash
POST /api/checkout/shipping-address
Content-Type: application/json

{
  "cart_id": "9d3f5c8a-1b2e-4f3d-8a9b-1c2d3e4f5a6b",
  "first_name": "John",
  "last_name": "Doe",
  "line_one": "123 Main Street",
  "city": "New York",
  "state": "NY",
  "postcode": "10001",
  "country_id": 1,
  "contact_email": "john@example.com",
  "contact_phone": "+1234567890"
}
```

### 7. Set Billing Address
```bash
POST /api/checkout/billing-address
Content-Type: application/json

{
  "cart_id": "9d3f5c8a-1b2e-4f3d-8a9b-1c2d3e4f5a6b",
  "same_as_shipping": true
}
```

### 8. Review Checkout Summary
```bash
GET /api/checkout/summary?cart_id=9d3f5c8a-1b2e-4f3d-8a9b-1c2d3e4f5a6b
```

### 9. Complete Checkout
```bash
POST /api/checkout/complete
Content-Type: application/json

{
  "cart_id": "9d3f5c8a-1b2e-4f3d-8a9b-1c2d3e4f5a6b",
  "payment_method": "card"
}
# Response: { "message": "Order created successfully.", "order": { "reference": "00000789", ... } }
```

### 10. Get Order Confirmation
```bash
GET /api/checkout/order/00000789
```

---

## Notes

- Store the `cart_id` (UUID) returned from cart creation on the client side
- Pass `cart_id` with all subsequent cart and checkout operations
- Cart calculations (totals, taxes, shipping) are automatically performed on each operation
- Shipping address must be set before billing address can use "same_as_shipping"
- Both shipping and billing addresses are required before completing checkout
- Order reference is auto-generated upon checkout completion
- Language support available through `lang_id` parameter for products
- All price values are pre-formatted with currency symbols
- Payment processing integration should be added separately based on your payment gateway
