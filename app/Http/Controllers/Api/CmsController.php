<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Models\Partner;
use App\Models\PromoPopup;
use Illuminate\Http\JsonResponse;

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
}
