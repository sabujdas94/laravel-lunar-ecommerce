<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetProductsByTagRequest extends FormRequest
{
    /**
     * Authorize the request.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     * Normalizes comma-separated `tag` query into an array.
     */
    protected function prepareForValidation()
    {
        if ($this->has('tag') && is_string($this->tag)) {
            $tags = array_filter(array_map('trim', explode(',', $this->tag)));
            $this->merge(['tag' => $tags]);
        }
    }

    /**
     * The validation rules.
     */
    public function rules()
    {
        return [
            'tag' => ['required', 'array'],
            'tag.*' => ['string'],
            'lang_id' => ['required', 'integer'],
            'limit' => ['required', 'integer'],
        ];
    }
}
