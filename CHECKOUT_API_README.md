# Checkout API - Quick Start Guide

## Overview
The Checkout API provides a complete e-commerce checkout flow, allowing you to:
- Set shipping and billing addresses
- Review order summary
- Complete checkout and create orders
- Retrieve order details

## Prerequisites
- A valid cart with items (see Cart API)
- Cart UUID from cart creation

## Checkout Flow

### Step 1: Set Shipping Address
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

### Step 2: Set Billing Address
**Option A - Same as Shipping:**
```bash
POST /api/checkout/billing-address
Content-Type: application/json

{
  "cart_id": "9d3f5c8a-1b2e-4f3d-8a9b-1c2d3e4f5a6b",
  "same_as_shipping": true
}
```

**Option B - Different Address:**
```bash
POST /api/checkout/billing-address
Content-Type: application/json

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
  "contact_email": "jane@example.com"
}
```

### Step 3: Review Checkout Summary
```bash
GET /api/checkout/summary?cart_id=9d3f5c8a-1b2e-4f3d-8a9b-1c2d3e4f5a6b
```

Response includes:
- Cart items with prices
- Shipping address
- Billing address
- Totals (subtotal, tax, shipping, total)

### Step 4: Complete Checkout
```bash
POST /api/checkout/complete
Content-Type: application/json

{
  "cart_id": "9d3f5c8a-1b2e-4f3d-8a9b-1c2d3e4f5a6b",
  "payment_method": "card"
}
```

**Returns:**
- Order reference number (e.g., "00000789")
- Complete order details
- Order status

### Step 5: Get Order Details (Optional)
```bash
GET /api/checkout/order/00000789
```

## Available Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/checkout/shipping-address` | Set/update shipping address |
| POST | `/api/checkout/billing-address` | Set/update billing address |
| GET | `/api/checkout/summary` | Get checkout summary |
| POST | `/api/checkout/complete` | Complete checkout & create order |
| GET | `/api/checkout/order/{reference}` | Get order by reference |

## Validation Requirements

### Before Completing Checkout:
1. ✅ Cart must have at least one item
2. ✅ Shipping address must be set
3. ✅ Billing address must be set
4. ✅ All address fields must be valid

### Address Required Fields:
- `first_name`, `last_name`
- `line_one` (street address)
- `city`, `postcode`
- `country_id`
- `contact_email`

## Error Handling

### Common Errors:

**Cart not found (404):**
```json
{
  "message": "Cart not found."
}
```

**Cart is empty (400):**
```json
{
  "message": "Cart is empty."
}
```

**Missing shipping address (400):**
```json
{
  "message": "Shipping address is required."
}
```

**Missing billing address (400):**
```json
{
  "message": "Billing address is required."
}
```

**Shipping address not set (when using same_as_shipping):**
```json
{
  "message": "Shipping address must be set first."
}
```

## Order Statuses

After checkout completion, orders are created with status:
- **awaiting-payment** (default) - Order created, waiting for payment confirmation
- **payment-received** - Payment confirmed
- **payment-offline** - Offline payment method
- **dispatched** - Order shipped

## Payment Integration

The current implementation creates orders with `awaiting-payment` status. To integrate payment:

1. Implement payment gateway (Stripe, PayPal, etc.) before calling `/checkout/complete`
2. After successful payment, update order status to `payment-received`
3. Store payment method in order meta

Example payment flow:
```javascript
// 1. Process payment with gateway
const payment = await processPayment(amount);

// 2. Complete checkout
if (payment.success) {
  const order = await fetch('/api/checkout/complete', {
    method: 'POST',
    body: JSON.stringify({
      cart_id: cartId,
      payment_method: 'card'
    })
  });
}
```

## Testing

### Test with cURL:

```bash
# 1. Set shipping address
curl -X POST http://localhost/api/checkout/shipping-address \
  -H "Content-Type: application/json" \
  -d '{
    "cart_id": "your-cart-uuid",
    "first_name": "John",
    "last_name": "Doe",
    "line_one": "123 Main St",
    "city": "New York",
    "state": "NY",
    "postcode": "10001",
    "country_id": 1,
    "contact_email": "john@example.com"
  }'

# 2. Set billing address (same as shipping)
curl -X POST http://localhost/api/checkout/billing-address \
  -H "Content-Type: application/json" \
  -d '{
    "cart_id": "your-cart-uuid",
    "same_as_shipping": true
  }'

# 3. Get summary
curl "http://localhost/api/checkout/summary?cart_id=your-cart-uuid"

# 4. Complete checkout
curl -X POST http://localhost/api/checkout/complete \
  -H "Content-Type: application/json" \
  -d '{
    "cart_id": "your-cart-uuid",
    "payment_method": "card"
  }'
```

## Notes

- All addresses are stored with the cart and transferred to the order
- Addresses can be updated multiple times before checkout completion
- Once checkout is completed, the cart is marked as completed
- Use the order reference to track and retrieve order details
- Cart UUID must be passed with all checkout operations
- Country IDs come from the `lunar_countries` table

## See Also

- [Complete API Documentation](API_DOCUMENTATION.md)
- [Cart API Documentation](API_DOCUMENTATION.md#cart-api)
- [Product API Documentation](API_DOCUMENTATION.md#products-api)
