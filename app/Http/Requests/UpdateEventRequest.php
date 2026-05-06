<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'start_date'  => 'sometimes|date',
            'end_date'    => 'sometimes|date|after_or_equal:start_date',
            'place'       => 'sometimes|string|max:255',
            'price'       => 'nullable|numeric|min:0',
            'is_free'     => 'sometimes|boolean',
            'capacity'    => 'sometimes|integer|min:1',
            'category_id' => 'sometimes|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status'      => 'sometimes|in:actif,archive,archivé',
        ];
    }
}