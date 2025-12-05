<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Get all addresses for the authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        // Get customer associated with user
        $customer = $user->customers()->first();
        
        if (!$customer) {
            return response()->json([
                'data' => []
            ]);
        }

        // Get all addresses for the customer
        $addresses = $customer->addresses()->get()->map(function ($address) {
            return [
                'id' => $address->id,
                'title' => $address->title,
                'first_name' => $address->first_name,
                'last_name' => $address->last_name,
                'company_name' => $address->company_name,
                'line_one' => $address->line_one,
                'line_two' => $address->line_two,
                'line_three' => $address->line_three,
                'city' => $address->city,
                'state' => $address->state,
                'postcode' => $address->postcode,
                'country_id' => $address->country_id,
                'country' => $address->country ? [
                    'id' => $address->country->id,
                    'name' => $address->country->name,
                    'iso2' => $address->country->iso2,
                    'iso3' => $address->country->iso3,
                ] : null,
                'delivery_instructions' => $address->delivery_instructions,
                'contact_email' => $address->contact_email,
                'contact_phone' => $address->contact_phone,
                'shipping_default' => $address->shipping_default,
                'billing_default' => $address->billing_default,
                'created_at' => $address->created_at,
                'updated_at' => $address->updated_at,
            ];
        });

        return response()->json([
            'data' => $addresses
        ]);
    }
}
