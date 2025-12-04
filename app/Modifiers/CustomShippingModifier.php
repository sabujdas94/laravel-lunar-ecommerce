<?php

namespace App\Modifiers;

use Lunar\Base\ShippingModifier;
use Lunar\DataTypes\Price;
use Lunar\DataTypes\ShippingOption;
use Lunar\Facades\ShippingManifest;
use Lunar\Models\Contracts\Cart;
use Lunar\Models\Currency;
use Lunar\Models\TaxClass;

class CustomShippingModifier extends ShippingModifier
{
    public function handle(Cart $cart, \Closure $next)
    {
        // Get the tax class
        $taxClass = TaxClass::first();

        // Or add multiple options, it's your responsibility to ensure the identifiers are unique
        ShippingManifest::addOptions(collect([
            new ShippingOption(
                name: 'Basic Delivery',
                description: 'A basic delivery option',
                identifier: 'BASDEL',
                price: new Price(6000, $cart->currency, 1),
                taxClass: $taxClass
            ),
            // new ShippingOption(
            //     name: 'Express Delivery',
            //     description: 'Express delivery option',
            //     identifier: 'EXDEL',
            //     price: new Price(1000, $cart->currency, 1),
            //     taxClass: $taxClass
            // )
        ]));
        
        return $next($cart);
    }
}