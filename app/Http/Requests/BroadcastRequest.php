<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BroadcastRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'movie_id' => 'required|uuid|exists:movies,id',
            'broadcasted_at' => 'required|date_format:Y-m-d H:i:s',
            'channel' => 'required|string',
        ];
    }
}
