<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;
use Lunar\Models\Cart as LunarCart;

class Cart extends LunarCart
{
    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'uuid' => 'string',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        parent::booted();

        // Automatically generate UUID before creating a new cart
        static::creating(function ($cart) {
            $cart->uuid = $cart->uuid ?: Str::uuid()->toString();
        });
    }

    /**
     * Get the cart by UUID or ID.
     */
    public static function findByUuid(string $identifier): ?static
    {
        return static::query()
            ->where('uuid', $identifier)
            ->orWhere('id', $identifier)
            ->first();
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Get the identifier for the cart (prefers UUID over ID).
     */
    public function getIdentifier(): string
    {
        return $this->uuid ?? (string) $this->id;
    }
}
