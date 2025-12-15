<?php

namespace App\Services;

use Lunar\Models\Product;
use Lunar\Models\Language;

class ProductService
{
    /**
     * Get products by tags.
     *
     * @param array $tags
     * @param int $langId
     * @return \Illuminate\Support\Collection
     */
    public function getProductsByTags(array $tags, int $langId = 1, int $limit = 8)
    {
        $language = Language::find($langId);
        $langCode = $language?->code ?? 'en';

        // Find products that have any of the specified tags
        $products = Product::query()
            ->with(['collections', 'tags', 'prices', 'variants', 'images', 'urls'])
            ->whereHas('tags', function ($q) use ($tags) {
                $q->whereIn('value', $tags);
            })
            ->limit($limit)
            ->latest()
            ->get();

        // Format the products
        return $products->map(function ($product) use ($langCode, $language) {
            // product name/description translations
            $name = $product->translateAttribute('name', $langCode);
            // $description = $product->translateAttribute('description', $langCode);

            // price — take the first price model if present
            $priceModel = $product->prices?->first();
            $price = null;
            $comparePrice = null;
            if ($priceModel) {
                $price = $priceModel->price->formatted($language->lang_code, $priceModel->price->currency->code);

                $comparePrice = $priceModel->compare_price ?
                    $priceModel->compare_price->formatted($language->lang_code, $priceModel->price->currency->code) : null;
            }

            // collections
            $collections = $product->collections->map(function ($col) use ($langCode) {
                return [
                    'name' => $col->translateAttribute('name', $langCode),
                    'slug' => $col->localeUrl($langCode)->first()?->slug ?? $col?->defaultUrl?->slug,
                ];
            })->values();

            // tags
            $tags = $product->tags->map(function ($tag) {
                return $tag->value;
            })->values();

            // choose a URL to return (first matching locale url or default)
            $urlObj = $product->urls?->first() ?? $product?->defaultUrl;
            $slugToReturn = $urlObj?->slug ?? null;

            return [
                'url' => $slugToReturn,
                'name' => $name,
                // 'description' => $description,
                'price' => $price,
                'comparePrice' => $comparePrice,
                'collections' => $collections,
                'tags' => $tags,
                'thumbnail' => $product->getThumbnailImage(),
            ];
        });
    }
}