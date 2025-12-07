<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * List orders for authenticated user.
     * GET /api/user/orders
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Pagination
        $perPage = (int) $request->query('per_page', 15);
        $page = (int) $request->query('page', 1);

        $orders = \Lunar\Models\Order::query()
            ->where('user_id', $user->id)
            ->withCount('lines')
            ->orderBy('placed_at', 'desc')
            ->paginate($perPage);

        $list = $orders->getCollection()->map(function ($order) {
            return [
                'id' => $order->id,
                'reference' => "#ORD-{$order->reference}",
                'status_label' => $order->status_label ?? null,
                'total' => $order->total?->formatted ?? null,
                'placed_at' => $order->placed_at?->format('M d, Y') ?? null,
                'lines_count' => $order->lines_count ?? 0,
            ];
        });

        return response()->json([
            'data' => $list,
            'meta' => [
                'current_page' => $orders->currentPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
                'last_page' => $orders->lastPage(),
            ],
        ]);
    }

    /**
     * Show a specific order for authenticated user.
     * GET /api/user/orders/{order}
     */
    public function show($orderIdentifier): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $order = \Lunar\Models\Order::query()
            ->where('user_id', $user->id)
            ->where('id', $orderIdentifier)
            ->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        $lines = $order->lines->map(function ($line) {
            // Check if purchasable is a ShippingOption (DataType, not a model)
            if ($line->type === 'shipping') {
                return [
                    'id' => $line->id,
                    'type' => 'shipping',
                    'description' => $line->description,
                    'quantity' => $line->quantity,
                    'unit_price' => $line->unit_price?->formatted ?? null,
                    'sub_total' => $line->sub_total?->formatted ?? null,
                    'total' => $line->total?->formatted ?? null,
                ];
            }

            // For product lines, access purchasable safely
            $purchasable = $line->purchasable;
            $product = $purchasable?->product;

            return [
                'id' => $line->id,
                'type' => $line->type ?? 'product',
                'variant_id' => $line->purchasable_id,
                'quantity' => $line->quantity,
                'unit_price' => $line->unit_price?->formatted ?? null,
                'sub_total' => $line->sub_total?->formatted ?? null,
                'total' => $line->total?->formatted ?? null,
                'product' => [
                    'name' => $product?->translateAttribute('name') ?? 'Unknown',
                    'sku' => $purchasable?->sku ?? null,
                    'thumbnail' => $purchasable?->getThumbnailImage() ?? null,
                ],
            ];
        });

        return response()->json([
            'order' => [
                'id' => $order->id,
                'reference' => "#ORD-{$order->reference}",
                'status' => $order->status,
                'status_label' => $order->status_label,
                'customer_reference' => $order->customer_reference,
                'currency_code' => $order->currency_code,
                'sub_total' => is_object($order->sub_total) && method_exists($order->sub_total, 'formatted') ? $order->sub_total->formatted : $order->sub_total,
                'tax_total' => is_object($order->tax_total) && method_exists($order->tax_total, 'formatted') ? $order->tax_total->formatted : $order->tax_total,
                'shipping_total' => is_object($order->shipping_total) && method_exists($order->shipping_total, 'formatted') ? $order->shipping_total->formatted : $order->shipping_total,
                'total' => is_object($order->total) && method_exists($order->total, 'formatted') ? $order->total->formatted : $order->total,
                'placed_at' => $order->placed_at?->toIso8601String(),
                'notes' => $order->notes ?? null,
                'lines' => $lines,
                'lines_count' => $order->lines->count(),
                'shipping_address' => method_exists($this, 'formatAddress') ? $this->formatAddress($order->shippingAddress) : [],
                'billing_address' => method_exists($this, 'formatAddress') ? $this->formatAddress($order->billingAddress) : [],
            ],
        ]);
    }

    /**
     * Format address for API response (copied from CheckoutController)
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
