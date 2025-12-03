<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Lunar\Models\Product;
use Lunar\Models\Language;
class ShopPageController extends Controller
{
    public function getProducts(Request $request)
    {
        $langId = $request->lang_id ?? 1;
        $language = Language::find($langId);
        $langCode = $language->code;

        $perPage = (int) $request->input('per_page', 12);

        $products = Product::with(['collections', 'tags'])
            ->with('urls')
            ->with('prices')
            ->where("status", "published")
            ->paginate($perPage);

        return $products->through(function ($product) use ($langCode, $language) {

            $name = $product->translateAttribute('name', $langCode);

            $priceModel = $product->prices?->first();

            $basePrice = $priceModel->price->formatted($language->lang_code, $priceModel->price->currency->code);
            $comparePrice = $priceModel->compare_price ?
                $priceModel->compare_price->formatted($language->lang_code, $priceModel->price->currency->code) : null;

            $collections = $product->collections->map(function ($col) use ($langCode) {
                return $col->translateAttribute('name', $langCode);
            });

            $tags = $product->tags->map(function ($tag) {
                return $tag->value;
            })->values();

            $thumbnail = $product->getThumbnailImage();

            $url = $product->localeUrl($langCode)->first();

            return [
                'url' => $url ? $url->slug : $product?->defaultUrl?->slug,
                'name' => $name,
                'price' => $basePrice,
                'comparePrice' => $comparePrice,
                'collections' => $collections,
                'tags' => $tags,
                'thumbnail' => $thumbnail,
            ];
        });
    }
}
