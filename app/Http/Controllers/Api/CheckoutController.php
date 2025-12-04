<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Cart;
use Lunar\Models\CartAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    /**
     * Add or update shipping address to cart.
     * POST /api/checkout/shipping-address
     */
    public function setShippingAddress(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'cart_id' => 'required|string',
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'line_one' => 'required|string|max:255',
            'line_two' => 'nullable|string|max:255',
            'line_three' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'postcode' => 'required|string|max:255',
            'country_id' => 'required|integer|exists:lunar_countries,id',
            'company_name' => 'nullable|string|max:255',
            'delivery_instructions' => 'nullable|string|max:500',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:255',
        ]);

        $cart = Cart::findByUuid($validated['cart_id']);

        if (!$cart) {
            return response()->json(['message' => 'Cart not found.'], 404);
        }

        // Remove cart_id from validated data
        $addressData = collect($validated)->except('cart_id')->toArray();
        $addressData['type'] = 'shipping';
        $addressData['cart_id'] = $cart->id;

        // Update or create shipping address
        $shippingAddress = $cart->addresses()->where('type', 'shipping')->first();
        
        if ($shippingAddress) {
            $shippingAddress->update($addressData);
        } else {
            $addressData['cart_id'] = $cart->id;
            $shippingAddress = CartAddress::create($addressData);
        }

        $cart->refresh();
        $cart->calculate();

        return response()->json([
            'message' => 'Shipping address set successfully.',
            'address' => $this->formatAddress($shippingAddress),
        ]);
    }

    /**
     * Add or update billing address to cart.
     * POST /api/checkout/billing-address
     */
    public function setBillingAddress(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'cart_id' => 'required|string',
            'same_as_shipping' => 'nullable|boolean',
            'first_name' => 'required_if:same_as_shipping,false|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'line_one' => 'required_if:same_as_shipping,false|string|max:255',
            'line_two' => 'nullable|string|max:255',
            'line_three' => 'nullable|string|max:255',
            'city' => 'required_if:same_as_shipping,false|string|max:255',
            'state' => 'nullable|string|max:255',
            'postcode' => 'required_if:same_as_shipping,false|string|max:255',
            'country_id' => 'required_if:same_as_shipping,false|integer|exists:lunar_countries,id',
            'company_name' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:255',
        ]);

        $cart = Cart::findByUuid($validated['cart_id']);

        if (!$cart) {
            return response()->json(['message' => 'Cart not found.'], 404);
        }

        // Handle "same as shipping" scenario
        if ($validated['same_as_shipping'] ?? false) {
            $shippingAddress = $cart->addresses()->where('type', 'shipping')->first();
            
            if (!$shippingAddress) {
                return response()->json([
                    'message' => 'Shipping address must be set first.'
                ], 400);
            }

            $addressData = $shippingAddress->toArray();
            $addressData['type'] = 'billing';
            $addressData['cart_id'] = $cart->id;
            unset($addressData['id'], $addressData['created_at'], $addressData['updated_at']);
        } else {
            $addressData = collect($validated)->except('cart_id', 'same_as_shipping')->toArray();
            $addressData['type'] = 'billing';
            $addressData['cart_id'] = $cart->id;
        }

        // Update or create billing address
        $billingAddress = $cart->addresses()->where('type', 'billing')->first();
        
        if ($billingAddress) {
            $billingAddress->update($addressData);
        } else {
            $addressData['cart_id'] = $cart->id;
            $billingAddress = CartAddress::create($addressData);
        }

        $cart->refresh();
        $cart->calculate();

        return response()->json([
            'message' => 'Billing address set successfully.',
            'address' => $this->formatAddress($billingAddress),
        ]);
    }

    /**
     * Get checkout summary (cart with addresses).
     * GET /api/checkout/summary
     */
    public function summary(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'cart_id' => 'required|string',
        ]);

        $cart = Cart::findByUuid($validated['cart_id']);

        if (!$cart) {
            return response()->json(['message' => 'Cart not found.'], 404);
        }

        $cart->load(['lines.purchasable.product', 'addresses.country']);
        $cart->calculate();

        $shippingAddress = $cart->addresses()->where('type', 'shipping')->first();
        $billingAddress = $cart->addresses()->where('type', 'billing')->first();

        $lines = $cart->lines->map(function ($line) {
            $purchasable = $line->purchasable;
            
            return [
                'id' => $line->id,
                'variant_id' => $line->purchasable_id,
                'quantity' => $line->quantity,
                'unit_price' => $line->unitPrice?->formatted ?? null,
                'sub_total' => $line->subTotal?->formatted ?? null,
                'total' => $line->total?->formatted ?? null,
                'product' => [
                    'name' => $purchasable?->product?->translateAttribute('name') ?? 'Unknown',
                    'sku' => $purchasable?->sku ?? null,
                    'thumbnail' => $purchasable?->getThumbnailImage() ?? null,
                ],
            ];
        });

        return response()->json([
            'id' => $cart->getIdentifier(),
            'currency_code' => $cart->currency?->code ?? null,
            'sub_total' => $cart->subTotal?->formatted ?? null,
            'tax_total' => $cart->taxTotal?->formatted ?? null,
            'shipping_total' => $cart->shippingTotal?->formatted ?? null,
            'total' => $cart->total?->formatted ?? null,
            'lines' => $lines,
            'lines_count' => $cart->lines->count(),
            'shipping_address' => $shippingAddress ? $this->formatAddress($shippingAddress) : null,
            'billing_address' => $billingAddress ? $this->formatAddress($billingAddress) : null,
        ]);
    }

    /**
     * Create order from cart (complete checkout).
     * POST /api/checkout/complete
     */
    public function complete(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'cart_id' => 'required|string',
            'payment_method' => 'nullable|string|in:cash-on-delivery',
            // Customer info
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:255',
            // Address
            'address' => 'required|array',
            'address.line_one' => 'required|string|max:255',
            'address.line_two' => 'nullable|string|max:255',
            'address.line_three' => 'nullable|string|max:255',
            'address.city' => 'required|string|max:255',
            'address.state' => 'nullable|string|max:255',
            'address.postcode' => 'required|string|max:255',
            'address.country_id' => 'required|integer|exists:lunar_countries,id',
            'address.company_name' => 'nullable|string|max:255',
            'address.delivery_instructions' => 'nullable|string|max:500',
        ]);

        $cart = Cart::findByUuid($validated['cart_id']);

        if (!$cart) {
            return response()->json(['message' => 'Cart not found.'], 404);
        }

        // Validate cart has items
        if ($cart->lines()->count() === 0) {
            return response()->json([
                'message' => 'Cart is empty.'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Create or update customer
            $customer = null;
            if ($request->user()) {
                // If user is authenticated, find or create customer
                $customer = \Lunar\Models\Customer::whereHas('users', function($query) use ($request) {
                    $query->where('users.id', $request->user()->id);
                })->first();

                if (!$customer) {
                    $customer = \Lunar\Models\Customer::create([
                        'first_name' => $validated['first_name'],
                        'last_name' => $validated['last_name'] ?? '',
                        'meta' => [
                            'contact_email' => $validated['contact_email'] ?? null,
                            'contact_phone' => $validated['contact_phone'] ?? null,
                        ],
                    ]);
                    $customer->users()->attach($request->user()->id);
                } else {
                    // Update existing customer
                    $customer->update([
                        'first_name' => $validated['first_name'],
                        'last_name' => $validated['last_name'] ?? '',
                        'meta' => array_merge(
                            $customer->meta ? (array)$customer->meta : [],
                            [
                                'contact_email' => $validated['contact_email'] ?? null,
                                'contact_phone' => $validated['contact_phone'] ?? null,
                            ]
                        ),
                    ]);
                }
            } else {
                // Guest checkout - create customer without user association
                $customer = \Lunar\Models\Customer::create([
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'] ?? null,
                    'meta' => [
                        'contact_email' => $validated['contact_email'] ?? null,
                        'contact_phone' => $validated['contact_phone'] ?? null,
                    ],
                ]);
            }

            // Associate customer with cart
            $cart->customer()->associate($customer);

            // Set address on cart (both shipping and billing use same address)
            $addressData = $validated['address'];
            $addressData['cart_id'] = $cart->id;
            $addressData['first_name'] = $validated['first_name'];
            $addressData['last_name'] = $validated['last_name'] ?? '';
            $addressData['contact_email'] = $validated['contact_email'] ?? null;
            $addressData['contact_phone'] = $validated['contact_phone'] ?? null;

            $cart->setShippingAddress($addressData);
            $cart->setBillingAddress($addressData);

            $shippingOptions = \Lunar\Facades\ShippingManifest::getOptions($cart);

            $cart->setShippingOption($shippingOptions->first());

            $driver = \Lunar\Facades\Payments::driver($validated['payment_method']);
            $driver->cart($cart);
            $order = $driver->authorize();

            DB::commit();

            return response()->json([
                'message' => 'Order created successfully.',
                'order_id' => $order->orderId,
            ], 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Cart validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } 
        catch (\Lunar\Exceptions\Carts\CartException $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } 
        catch (\Exception $e) {
            DB::rollBack();            
            return response()->json([
                'message' => 'Failed to create order.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get order details.
     * GET /api/checkout/order/{reference}
     */
    public function getOrder(string $reference): JsonResponse
    {
        $order = \Lunar\Models\Order::where('reference', $reference)->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        $order->load([
            'lines.purchasable.product',
            'shippingAddress',
            'billingAddress'
        ]);

        $lines = $order->lines->map(function ($line) {
            $purchasable = $line->purchasable;
            
            return [
                'id' => $line->id,
                'variant_id' => $line->purchasable_id,
                'quantity' => $line->quantity,
                'unit_price' => $line->unit_price?->formatted ?? null,
                'sub_total' => $line->sub_total?->formatted ?? null,
                'total' => $line->total?->formatted ?? null,
                'product' => [
                    'name' => $purchasable?->product?->translateAttribute('name') ?? 'Unknown',
                    'sku' => $purchasable?->sku ?? null,
                    'thumbnail' => $purchasable?->getThumbnailImage() ?? null,
                ],
            ];
        });

        return response()->json([
            'order' => [
                'id' => $order->id,
                'reference' => $order->reference,
                'status' => $order->status,
                'status_label' => $order->status_label,
                'customer_reference' => $order->customer_reference,
                'currency_code' => $order->currency_code,
                'sub_total' => $order->sub_total?->formatted ?? null,
                'discount_total' => $order->discount_total?->formatted ?? null,
                'tax_total' => $order->tax_total?->formatted ?? null,
                'shipping_total' => $order->shipping_total?->formatted ?? null,
                'total' => $order->total?->formatted ?? null,
                'placed_at' => $order->placed_at?->toIso8601String(),
                'notes' => $order->notes,
                'lines' => $lines,
                'lines_count' => $order->lines->count(),
                'shipping_address' => $this->formatAddress($order->shippingAddress),
                'billing_address' => $this->formatAddress($order->billingAddress),
            ],
        ]);
    }

    /**
     * Format address for API response.
     */
    private function formatAddress($address): array
    {
        if (!$address) {
            return [];
        }

        return [
            'id' => $address->id,
            'first_name' => $address->first_name,
            'last_name' => $address->last_name,
            'company_name' => $address->company_name,
            'line_one' => $address->line_one,
            'line_two' => $address->line_two,
            'line_three' => $address->line_three,
            'city' => $address->city,
            'state' => $address->state,
            'postcode' => $address->postcode,
            'country' => $address->country?->name ?? null,
            'country_id' => $address->country_id,
            'contact_email' => $address->contact_email,
            'contact_phone' => $address->contact_phone,
            'delivery_instructions' => $address->delivery_instructions ?? null,
        ];
    }
}
