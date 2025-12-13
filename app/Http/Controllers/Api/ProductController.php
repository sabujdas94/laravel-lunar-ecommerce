<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Lunar\Models\Product;
use Lunar\Models\Language;
use App\Services\ProductService;
use App\Http\Requests\GetProductsByTagRequest;

class ProductController extends Controller
{
    /**
     * Return product details found by URL slug.
     *
     * Example: GET /api/product/{slug}?lang_id=1
     */
    public function showByUrl(Request $request, string $slug)
    {
        // Resolve language (fallback to id 1 if not provided)
        $langId = $request->query('lang_id') ?? 1;
        $language = Language::find($langId);
        $langCode = $language?->code ?? 'en';

        // Find the product by url/slug (search the urls relation)
        $product = Product::query()
            ->with(['collections', 'tags', 'prices', 'variants', 'images', 'urls'])
            ->whereHas('urls', function ($q) use ($slug) {
                $q->where('slug', $slug);
            })
            ->first();

        if (! $product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        // product name/description translations
        $name = $product->translateAttribute('name', $langCode);
        $description = $product->translateAttribute('description', $langCode);
        $specification = $product->translateAttribute('detailed-specification', $langCode);

        // price — take the first price model if present
        $priceModel = $product->prices?->first();
        $price = null;
        $comparePrice = null;

        if ($priceModel) {
            $price = $priceModel->price->formatted($language->lang_code, $priceModel->price->currency->code);

            $comparePrice = $priceModel->compare_price ?
                $priceModel->compare_price->formatted($language->lang_code, $priceModel->price->currency->code) : null;
        }

        // collections and tags
        $collections = $product->collections->map(function ($col) use ($langCode) {
            return [
                'name' => $col->translateAttribute('name', $langCode),
                'slug' => $col->localeUrl($langCode)->first()?->slug ?? $col?->defaultUrl?->slug,
            ];
        })->values();

        $tags = $product->tags->map(function ($tag) {
            return $tag->value;
        })->values();

        // Group options by type (e.g., Size, Color)
        $variantOptions = $product->variants
            ->flatMap(fn($variant) => $variant->values)
            ->groupBy('product_option_id')
            ->map(function ($group) use ($langCode) {
                $option = $group->first()->option;
                return [
                    'name'   => $option->translate('name', $langCode),
                    'values' => $group->map(function ($value) use ($langCode) {
                        return [
                            'id' => $value->id,
                            'name' => $value->translate('name', $langCode),
                        ];
                    })->unique('id')->values()
                ];
            })
            ->values();

        // Build variant combinations with stock
        $variantCombinations = $product->variants->map(function ($variant) use ($language) {
            $firstPrice = $variant->prices?->first();
            $variantPrice = null;
            if ($firstPrice) {
                $variantPrice = $firstPrice->price->formatted($language->lang_code, $firstPrice->price->currency->code);
            }

            // Map the option values for this specific variant
            $optionValueIds = $variant->values->pluck('id')->values();

            return [
                'variant_id' => $variant->id,
                'sku' => $variant->sku,
                'price' => $variantPrice,
                'stock' => $variant->stock,
                'backorder' => $variant->backorder,
                'option_value_ids' => $optionValueIds,
                'thumbnail' => $variant->getThumbnailImage(),
            ];
        })->values();

        $images = $product->images->map(function ($img) {
            return [
                'path' => $img->getUrl(),
            ];
        })->values();

        // choose a URL to return (first matching locale url or default)
        $urlObj = $product->urls?->first() ?? $product?->defaultUrl;
        $slugToReturn = $urlObj?->slug ?? null;

        return response()->json([
            'url' => $slugToReturn,
            'name' => $name,
            'description' => $description,
            'specification' => $specification,
            'price' => $price,
            'comparePrice' => $comparePrice,
            'collections' => $collections,
            'tags' => $tags,
            'variant_options' => $variantOptions,
            'variant_combinations' => $variantCombinations,
            'thumbnail' => $product->getThumbnailImage(),
            'images' => $images,
        ]);
    }

    /**
     * Return products by tag.
     *
     * Example: GET /api/products-by-tag?tag=sale,new&lang_id=1
     */
    public function getByTag(GetProductsByTagRequest $request)
    {
        $validated = $request->validated();
        $tags = $validated['tag'];
        $langId = $validated['lang_id'] ?? 1;
        $limit = $request->input('limit', 8);

        $productService = new ProductService();
        $formattedProducts = $productService->getProductsByTags($tags, $langId, $limit);
        if ($formattedProducts->isEmpty()) {
            return response()->json(['message' => 'No products found for the specified tags.'], 404);
        }

        return response()->json($formattedProducts);
    }
}
