<?php

namespace App\Providers;

use App\Modifiers\CustomShippingModifier;
use Illuminate\Support\ServiceProvider;

class CustomShippingProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(\Lunar\Base\ShippingModifiers $shippingModifiers)
    {
        $shippingModifiers->add(
            CustomShippingModifier::class
        );
    }
}
