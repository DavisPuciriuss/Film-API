<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MovieRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:100',
            'rating' => 'required|numeric|min:0|max:10',
            'age_restriction' => 'required|in:None,7+,12+,16+',
            'description' => 'required|string|max:500',
            'released_at' => 'required|date_format:Y-m-d H:i:s',
        ];
    }
}
