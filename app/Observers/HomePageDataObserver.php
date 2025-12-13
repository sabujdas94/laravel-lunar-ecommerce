<?php

namespace App\Observers;


use Lunar\Models\Product;
use App\Services\DataVersion;

class HomePageDataObserver
{
    public function saved(Product $product)
    {
        DataVersion::bump('products');
    }

    public function deleted(Product $product)
    {
        DataVersion::bump('products');
    }
}
