<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'place'       => 'required|string|max:255',
            'price'       => 'nullable|numeric|min:0',
            'is_free'     => 'required|boolean',
            'capacity'    => 'required|integer|min:1',
            'category_id' => 'required|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status'      => 'required|in:actif,archive,archivé',
        ];
    }
}