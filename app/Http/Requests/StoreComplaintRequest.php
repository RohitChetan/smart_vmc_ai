<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description' => [
                'required',
                'string',
                'min:5',
                'max:5000',
            ],

            'category_id' => [
                'nullable',
                'integer',
                'exists:categories,id',
            ],

            'latitude' => [
                'required',
                'numeric',
                'between:-90,90',
            ],

            'longitude' => [
                'required',
                'numeric',
                'between:-180,180',
            ],

            'location_accuracy' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'media' => [
                'nullable',
                'array',
                'max:5',
            ],

            'media.*' => [
                'file',
                'mimes:jpg,jpeg,png,webp,mp4,mov',
                'max:51200',
            ],
        ];
    }
}