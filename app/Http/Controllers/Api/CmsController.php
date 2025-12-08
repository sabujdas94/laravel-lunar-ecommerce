<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Models\Partner;
use App\Models\PromoPopup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CmsController extends Controller
{
    /**
     * Get all active sliders.
     *
     * @return JsonResponse
     */
    public function sliders(): JsonResponse
    {
        $sliders = Slider::active()
            ->ordered()
            ->get()
            ->map(function ($slider) {
                return [
                    'id' => $slider->id,
                    'image' => $slider->image ? asset('storage/' . $slider->image) : null,
                    'heading' => $slider->heading,
                    'sub_heading' => $slider->sub_heading,
                    'button1_label' => $slider->button1_label,
                    'button1_url' => $slider->button1_url,
                    'button2_label' => $slider->button2_label,
                    'button2_url' => $slider->button2_url,
                    'tag' => $slider->tag,
                    'tag_style' => $slider->tag_style,
                    'sort_order' => $slider->sort_order,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $sliders,
        ]);
    }

    /**
     * Get all active partners.
     *
     * @return JsonResponse
     */
    public function partners(): JsonResponse
    {
        $partners = Partner::active()
            ->ordered()
            ->get()
            ->map(function ($partner) {
                return [
                    'id' => $partner->id,
                    'name' => $partner->name,
                    'logo' => $partner->logo ? asset('storage/' . $partner->logo) : null,
                    'website_url' => $partner->website_url,
                    'sort_order' => $partner->sort_order,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $partners,
        ]);
    }

    /**
     * Get the current active promo popup.
     *
     * @return JsonResponse
     */
    public function promoPopup(): JsonResponse
    {
        $popup = PromoPopup::getCurrent();

        if (!$popup) {
            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'No active promo popup available',
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $popup->id,
                'title' => $popup->title,
                'image' => $popup->image ? asset('storage/' . $popup->image) : null,
                'banner_link' => $popup->banner_link,
            ],
        ]);
    }

    /**
     * Get all home page data including sliders, partners, promo popup, and collections.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function homePageData(Request $request): JsonResponse
    {
        $languageCode = $request->query('lang', 'en');

        $sliders = Slider::active()
            ->ordered()
            ->get()
            ->map(function ($slider) {
                return [
                    'id' => $slider->id,
                    'image' => $slider->image ? asset('storage/' . $slider->image) : null,
                    'heading' => $slider->heading,
                    'sub_heading' => $slider->sub_heading,
                    'button1_label' => $slider->button1_label,
                    'button1_url' => $slider->button1_url,
                    'button2_label' => $slider->button2_label,
                    'button2_url' => $slider->button2_url,
                    'tag' => $slider->tag,
                    'tag_style' => $slider->tag_style,
                    'sort_order' => $slider->sort_order,
                ];
            });

        $partners = Partner::active()
            ->ordered()
            ->get()
            ->map(function ($partner) {
                return [
                    'id' => $partner->id,
                    'name' => $partner->name,
                    'logo' => $partner->logo ? asset('storage/' . $partner->logo) : null,
                    'website_url' => $partner->website_url,
                    'sort_order' => $partner->sort_order,
                ];
            });

        $popup = PromoPopup::getCurrent();
        $popupData = null;
        if ($popup) {
            $popupData = [
                'id' => $popup->id,
                'title' => $popup->title,
                'image' => $popup->image ? asset('storage/' . $popup->image) : null,
                'banner_link' => $popup->banner_link,
            ];
        }

        // Add collections
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

        return response()->json([
            'success' => true,
            'data' => [
                'sliders' => $sliders,
                'partners' => $partners,
                'popup' => $popupData,
                'shop_by_category' => $collectionResponse,
            ],
        ]);
    }
}
