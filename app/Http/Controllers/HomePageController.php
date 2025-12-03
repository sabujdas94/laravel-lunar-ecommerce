<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomePageController extends Controller
{
    public function collection(Request $request)
    {
        $languageCode = $request->query('lang', 'en');

        $cacheKey = "homepage:collections:lang:{$languageCode}";

        $data = Cache::rememberForever($cacheKey, function () use ($languageCode) {
            $language = \Lunar\Models\Language::where('code', $languageCode)->first();

            $scCollectionGroup = \Lunar\Models\CollectionGroup::query()
                ->with('collections')
                ->with('collections.media')
                ->with('collections.children')
                ->where('handle', 'shop-by-category')
                ->first();

            $collectionResponse = [];

            if ($scCollectionGroup?->collections) {
                foreach ($scCollectionGroup->collections as $collection) {
                    $url = $collection->localeUrl($languageCode)->first();
                    $name = $collection->translateAttribute('name', $language?->code);

                    $children = [];
                    foreach ($collection->children as $child) {
                        $childName = $child->translateAttribute('name', $language?->code);
                        $childUrl = $child->localeUrl($languageCode)->first();
                        $children[] = [
                            'name' => $childName,
                            'slug' => $childUrl ? $childUrl->slug : $child?->defaultUrl?->slug,
                        ];
                    }

                    $collectionResponse[] = [
                        'attribute_data' => $name,
                        'slug' => $url ? $url->slug : $collection?->defaultUrl?->slug,
                        'thumbnail' => $collection->getThumbnailImage(),
                        'children' => $children,
                    ];
                }
            }

            return [
                'shop_by_category' => $collectionResponse,
            ];
        });

        return response()->json([$data['shop_by_category']]);
    }
}
